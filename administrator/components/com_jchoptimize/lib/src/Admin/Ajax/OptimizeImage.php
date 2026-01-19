<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 * @package   jchoptimize/core
 * @author    Samuel Marshall <samuel@jch-optimize.net>
 * @copyright Copyright (c) 2022 Samuel Marshall / JCH Optimize
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 *  If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace JchOptimize\Core\Admin\Ajax;

use _JchOptimizeVendor\V91\GuzzleHttp\Client;
use _JchOptimizeVendor\V91\GuzzleHttp\Exception\ConnectException;
use _JchOptimizeVendor\V91\GuzzleHttp\Exception\RequestException;
use _JchOptimizeVendor\V91\GuzzleHttp\Pool;
use _JchOptimizeVendor\V91\GuzzleHttp\Psr7\MultipartStream;
use _JchOptimizeVendor\V91\GuzzleHttp\Psr7\Request;
use _JchOptimizeVendor\V91\GuzzleHttp\Psr7\Utils as GuzzlePsr7Utils;
use _JchOptimizeVendor\V91\GuzzleHttp\RequestOptions;
use _JchOptimizeVendor\V91\Psr\Http\Client\ClientInterface;
use Exception;
use Generator;
use JchOptimize\Core\Admin\API\FulfillImageOptimization;
use JchOptimize\Core\Admin\API\MessageEventFactory;
use JchOptimize\Core\Admin\API\MessageEventInterface;
use JchOptimize\Core\Admin\API\ProcessImagesByFolders;
use JchOptimize\Core\Admin\API\ProcessImagesByUrls;
use JchOptimize\Core\Admin\API\ProcessImagesQueueInterface;
use JchOptimize\Core\Helper;
use JchOptimize\Core\Registry;
use Throwable;

use function array_map;
use function array_merge;
use function class_exists;
use function count;
use function defined;
use function file_exists;
use function set_time_limit;
use function sleep;
use function ucfirst;

defined('_JCH_EXEC') or die('Restricted access');

class OptimizeImage extends Ajax
{
    public static string $backup_folder_name = 'jch_optimize_backup_images';

    private MessageEventInterface $messageEventObj;
    private FulfillImageOptimization $fulfillImageOptimization;

    protected function __construct()
    {
        parent::__construct();

        $this->messageEventObj = MessageEventFactory::create(
            $this->input->getString('evtMsg'),
            $this->input->get('browserId')
        );

        $this->fulfillImageOptimization = new FulfillImageOptimization(
            $this->messageEventObj,
            $this->logger,
            $this->adminHelper,
            $this->paths
        );

        set_time_limit(0);
    }

    public function run(): void
    {
        try {
            $this->messageEventObj->initialize();

            $loops = 0;
            while ($loops < 10) {
                try {
                    $message = $this->messageEventObj->receive($this->input);
                    if (is_object($message)) {
                        if ($message->type == 'optimize') {
                            $this->optimize($message->data);
                            break;
                        }

                        if ($message->type == 'disconnected') {
                            $this->messageEventObj->disconnect();
                            break;
                        }
                    }
                    $loops++;
                } catch (Exception $e) {
                    $this->messageEventObj->send("PHP error: {$e->getMessage()}", 'apiError');
                    break;
                }
            }
            echo "Script completed!\n";
        } catch (Throwable $e) {
            echo "Error: {$e->getMessage()}\n";
        }
    }

    public function optimize(object $data): void
    {
        if (isset($data->subdirs)) {
            $subDirs = array_map([$this, 'resolveDirectories'], $data->subdirs);
        }

        if (!empty($subDirs)) {
            $this->input->set('subdirs', $subDirs);
        }

        $options = new Registry($data->params, '/');

        if (isset($data->filepack)) {
            $files = [];
            foreach ($data->filepack as $file) {
                $files[] = $file->path;
                if (isset($file->path) && (isset($file->width) || isset($file->height))) {
                    $shortFileName = $this->adminHelper->createClientFileName($file->path);
                    $options->set("resize/$shortFileName/width", (int)$file->width ?: 0);
                    $options->set("resize/$shortFileName/height", (int)$file->height ?: 0);
                }
            }
            $this->input->set('files', $files);
        }

        /** @var Client&ClientInterface $client */
        $client = $this->getContainer()->get(ClientInterface::class);

        $pool = new Pool($client, $this->getFilePackageRequests($options), [
            'concurrency' => (int)$this->getContainer()->get(Registry::class)->get('pro_api_concurrency', 5),
            'options' => [
                RequestOptions::SYNCHRONOUS => false,
                RequestOptions::TIMEOUT => (int)$this->params->get('api_connection_timeout', 30),
            ],
            'fulfilled' => [$this->fulfillImageOptimization, 'handle'],
            'rejected' => [$this, 'rejectedRequests']
        ]);

        $promise = $pool->promise();

        $promise->wait();

        $this->messageEventObj->send($this->paths->getLogsPath(), 'complete');
        sleep(5);
        $this->messageEventObj->disconnect();
    }

    private function resolveDirectories(string $directory): string
    {
        return $this->adminHelper->normalizePath(
            Helper::appendTrailingSlash($this->paths->rootPath()) . Helper::removeLeadingSlash($directory)
        );
    }

    public function rejectedRequests(RequestException|ConnectException $exception, $index): void
    {
        foreach ($index as $file) {
            $fileName = $this->adminHelper->maskFileName($file);
            $message = $fileName . ': Request failed with message: ' . $exception->getMessage();
            $this->messageEventObj->send($message, 'requestRejected');
        }
    }

    public function getFilePackageRequests(Registry $options): Generator
    {
        $totalFilesFound = 0;
        $mode = $this->input->get('mode');

        /** @see ProcessImagesByUrls */
        /** @see ProcessImagesByFolders */
        $imageProcessorClass = '\JchOptimize\Core\Admin\API\ProcessImages' . ucfirst($mode);

        if (class_exists($imageProcessorClass)) {
            /** @var ProcessImagesQueueInterface $imageProcessor */
            $imageProcessor = new $imageProcessorClass($this->getContainer(), $this->messageEventObj);

            while ($imageProcessor->hasPendingImages()) {
                $files = $imageProcessor->getFilePackages();

                if (empty($files['images'])) {
                    continue;
                }

                $this->packageCroppedImages($options, $files['images']);
                $uploadFiles = [];
                $noImagesInPackage = count($files['images']);
                $totalFilesFound += count($files['images']);
                $this->messageEventObj->send((string)$noImagesInPackage, 'addFileCount');

                foreach ($files['images'] as $i => $file) {
                    try {
                        $contents = GuzzlePsr7Utils::tryFopen($file, 'r');
                    } catch (Exception) {
                        $contents = '';
                    }

                    if (file_exists($file)) {
                        $uploadFiles[] = [
                            'name' => 'files[' . $i . ']',
                            'contents' => $contents,
                            'filename' => $this->adminHelper->createClientFileName($file)
                        ];
                    }
                }

                if (isset($files['url'])) {
                    $options->set('url', $files['url']);
                }

                $data = [
                    'name' => 'data',
                    'contents' => $options->toString()
                ];

                $body = array_merge($uploadFiles, [$data]);

                yield $files['images'] => new Request(
                    'POST',
                    'https://api3.jch-optimize.net/api/optimize-images',
                    [],
                    new MultipartStream($body)
                );
            }
        } else {
            $this->logger?->error('Image Processor Class not found');
        }

        if ($totalFilesFound === 0) {
            $this->messageEventObj->send('0', 'addFileCount');
        }
    }

    private function packageCroppedImages(Registry $options, array $images): void
    {
        $croppedImages = $options->get('cropgravity', []);

        foreach ($images as $image) {
            foreach ($croppedImages as $croppedImage) {
                if (
                    !empty($croppedImage->url)
                    && Helper::findExcludes([$croppedImage->url], $image)
                    && isset($croppedImage->gravity)
                    && isset($croppedImage->cropwidth)
                ) {
                    $shortFileName = $this->adminHelper->createClientFileName($image);
                    $options->set("resize/$shortFileName/crop", true);
                    $options->set("resize/$shortFileName/gravity", $croppedImage->gravity);
                    $options->set("resize/$shortFileName/width", $croppedImage->cropwidth);
                }
            }
        }

        $options->remove('cropgravity');
    }
}

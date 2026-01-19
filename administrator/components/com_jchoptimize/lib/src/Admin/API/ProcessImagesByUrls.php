<?php

namespace JchOptimize\Core\Admin\API;

use _JchOptimizeVendor\V91\GuzzleHttp\Psr7\UriResolver;
use _JchOptimizeVendor\V91\Joomla\DI\Container;
use _JchOptimizeVendor\V91\Psr\Http\Message\UriInterface;
use Exception;
use JchOptimize\Container\ContainerFactory;
use JchOptimize\Core\Admin\AdminHelper;
use JchOptimize\Core\Admin\HtmlCrawler;
use JchOptimize\Core\Cdn\Cdn;
use JchOptimize\Core\Combiner;
use JchOptimize\Core\Html\FilesManager;
use JchOptimize\Core\Html\HtmlProcessor;
use JchOptimize\Core\Platform\PathsInterface;
use JchOptimize\Core\Registry;
use JchOptimize\Core\SystemUri;
use JchOptimize\Core\Uri\UriConverter;
use JchOptimize\Core\Uri\UriNormalizer;
use JchOptimize\Core\Uri\Utils;

use function array_filter;
use function array_map;
use function array_merge;
use function array_shift;
use function array_unique;
use function array_values;
use function count;
use function file_exists;
use function filesize;
use function in_array;
use function preg_match;

class ProcessImagesByUrls extends AbstractProcessImages
{
    private array $htmlArray;

    private array $images = [];

    private ?UriInterface $uri = null;

    private array $processedImages = [];

    public function __construct(Container $container, MessageEventInterface $messageEventObj)
    {
        parent::__construct($container, $messageEventObj);

        $this->crawlHtmls();
    }

    public function getFilePackages(): array
    {
        [$files, $totalFileSize] = $this->initializeFileArray();

        do {
            if (empty($this->images) && count($this->htmlArray) > 0) {
                $this->images = $this->getImagesFromPendingHtml();
            }

            do {
                if (empty($this->images)) {
                    break;
                }

                $image = array_shift($this->images);
                $fileSize = filesize($image);

                if ($fileSize > $this->maxUploadFilesize) {
                    $this->messageEventObj->send(
                        'Skipping ' . $this->adminHelper->maskFileName($image) . ': Too large!'
                    );

                    continue;
                }

                $totalFileSize += $fileSize;

                if ($totalFileSize > $this->maxUploadFilesize) {
                    $this->prevFiles[] = $image;
                    $this->prevFileSize = $fileSize;

                    break;
                }

                $files['images'][] = $image;
                $this->processedImages[] = $image;
            } while (count($files['images']) < $this->getMaxFileUploads());

            if (count($files['images']) > 0) {
                $files['url'] = (string)$this->uri;

                return $files;
            }
        } while (count($this->images) > 0 || count($this->htmlArray) > 0);

        return $files;
    }

    private function crawlHtmls(): void
    {
        $htmlCrawler = $this->getContainer()->get(HtmlCrawler::class);
        $htmlCrawler->setEventLogging(true, $this->messageEventObj);
        $pathsUtils = $this->getContainer()->get(PathsInterface::class);

        $options = [
            'base_url' => (string)$this->params->get('pro_api_base_url', SystemUri::siteBaseFull($pathsUtils)),
            'crawl_limit' => (int)$this->params->get('pro_api_crawl_limit', 15)
        ];

        try {
            $this->htmlArray = $htmlCrawler->getCrawledHtmls($options);
        } catch (Exception $e) {
            $this->htmlArray = [];
        }
    }

    protected function getImagesFromPendingHtml(): array
    {
        $container = ContainerFactory::create();
        $this->setParamsForApiImages($container);

        $aHtml = $this->getPendingHtmlArray();
        $aHtmlImages = $this->getImagesInHtml($container, $aHtml['html']);
        $aCssImages = $this->getImagesInCss($container);

        $images = array_merge($aHtmlImages, $aCssImages);
        $images = array_unique(array_filter($images));

        //Get the absolute file path of images on filesystem
        $uri = Utils::uriFor($aHtml['url']);
        $images = array_map(function ($a) use ($uri, $container) {
            $uri = UriResolver::resolve($uri, UriNormalizer::normalize(Utils::uriFor($a)));
            $cdn = $container->get(Cdn::class);
            $pathsUtils = $container->get(PathsInterface::class);

            return UriConverter::uriToFilePath($uri, $pathsUtils, $cdn);
        }, $images);

        $images = array_filter($images, function ($a) {
            return preg_match('#' . AdminHelper::$optimizeImagesFileExtRegex . '#i', $a)
                && !in_array($a, $this->processedImages)
                && @file_exists($a);
        });

        //If option set, remove images already optimized
        if ($this->params->get('ignore_optimized', '1')) {
            $images = $this->adminHelper->filterOptimizedFiles($images);
        }

        $images = array_values(array_unique($images));

        if (!empty($images)) {
            $this->uri = $uri;
        }

        return $images;
    }

    protected function getImagesInHtml(Container $container, string $html): array
    {
        $htmlProcessor = $container->getNewInstance(HtmlProcessor::class);
        $htmlProcessor->setHtml($html);

        return $htmlProcessor->processImagesForApi();
    }

    protected function getImagesInCss(Container $container): array
    {
        try {
            $htmlProcessor = $container->get(HtmlProcessor::class);
            $htmlProcessor->processCombineJsCss();
            $oFilesManager = $container->get(FilesManager::class);
            $aCssLinks = $oFilesManager->aCss;
            $oCombiner = $container->get(Combiner::class);
            $aResult = $oCombiner->combineFiles($aCssLinks[0]);
            $aCssImages = array_unique(array_filter($aResult->getImages(), function (mixed $a) {
                return $a instanceof UriInterface;
            }));
        } catch (Exception) {
            $aCssImages = [];
        }

        return $aCssImages;
    }

    protected function setParamsForApiImages(Container $container): void
    {
        $params = $container->get(Registry::class);
        $params->set('combine_files_enable', '1');
        $params->set('combine_files', '1');
        $params->set('javascript', '0');
        $params->set('css', '1');
        $params->set('css_minify', '0');
        $params->set('excludeCss', []);
        $params->set('excludeCssComponents', []);
        $params->set('replaceImports', '1');
        $params->set('phpAndExternal', '1');
        $params->set('inlineScripts', '1');
        $params->set('lazyload_enable', '0');
        $params->set('cookielessdomain_enable', '0');
        $params->set('optimizeCssDelivery_enable', '0');
        $params->set('csg_enable', '0');
    }

    private function getPendingHtmlArray(): array
    {
        return array_shift($this->htmlArray);
    }

    public function hasPendingImages(): bool
    {
        return !empty($this->htmlArray) || !empty($this->images);
    }
}

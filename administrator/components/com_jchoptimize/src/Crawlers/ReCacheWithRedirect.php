<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 * @package   jchoptimize/joomla-platform
 * @author    Samuel Marshall <samuel@jch-optimize.net>
 * @copyright Copyright (c) 2022 Samuel Marshall / JCH Optimize
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 *  If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace CodeAlfa\Component\JchOptimize\Administrator\Crawlers;

use _JchOptimizeVendor\V91\GuzzleHttp\Exception\RequestException;
use _JchOptimizeVendor\V91\Psr\Http\Message\ResponseInterface;
use _JchOptimizeVendor\V91\Psr\Http\Message\UriInterface;
use _JchOptimizeVendor\V91\Psr\Log\LoggerAwareInterface;
use _JchOptimizeVendor\V91\Psr\Log\LoggerAwareTrait;
use _JchOptimizeVendor\V91\Psr\Log\LoggerInterface;
use _JchOptimizeVendor\V91\Spatie\Crawler\CrawlObservers\CrawlObserver;
use JchOptimize\Core\Platform\PathsInterface;
use JchOptimize\Core\SystemUri;
use Joomla\Application\Web\WebClient;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Application\ConsoleApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Database\DatabaseInterface;

use function count;
use function defined;
use function fastcgi_finish_request;
use function headers_sent;
use function ignore_user_abort;
use function ob_end_clean;
use function ob_end_flush;
use function strlen;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die('Restricted Access');

// phpcs:enable PSR1.Files.SideEffects

class ReCacheWithRedirect extends CrawlObserver implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected bool $redirected = false;

    public function __construct(
        protected CMSApplication|ConsoleApplication $app,
        LoggerInterface $logger,
        protected PathsInterface $paths,
        protected ?string $redirectUrl = null
    ) {
        $this->setLogger($logger);
        if ($this->redirectUrl === null) {
            $this->redirectUrl = Route::_('index.php?option=com_jchoptimize', false, 0, true);
        }
    }

    /**
     * @inheritDoc
     *
     * @return void
     */
    public function crawled(UriInterface $url, ResponseInterface $response, ?UriInterface $foundOnUrl = null): void
    {
        if (!$this->redirected) {
            $this->app->enqueueMessage(
                Text::_('COM_JCHOPTIMIZE_RECACHE_STARTED'),
                'success'
            );//Redirect without closing to allow recache to continue asynchronously.
            $this->redirect();

            $this->redirected = true;
        }
    }

    /**
     * @inheritDoc
     *
     * @return void
     */
    public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null
    ): void {
        $message = '';
        //Build error msg
        if ($requestException->hasResponse()) {
            $response = $requestException->getResponse();

            if ($response) {
                $message = 'Status code: ' . $response->getStatusCode();
                if ($response->getReasonPhrase()) {
                    $message .= ' - ' . $response->getReasonPhrase();
                }
            }
        } else {
            $message = 'Connection issues.';
        }

        if ((string)$url == SystemUri::siteBaseFull($this->paths) && $this->app instanceof CMSApplication) {
            $this->app->enqueueMessage(Text::_('COM_JCHOPTIMIZE_RECACHE_FAILED') . ' ' . $message, 'error');
            $this->app->redirect($this->redirectUrl);
        }

        $this->logger->error($message . ': ' . $url);
    }

    private function redirect(): void
    {
        ignore_user_abort(true);

        $app = $this->app;
        if (!$app instanceof CMSApplication) {
            return;
        }

        // If headers already went out, don't touch session—just do a JS redirect and bail.
        if (headers_sent()) {
            echo '<script>document.location.href=' . json_encode($this->redirectUrl) . ";</script>\n";
            return;
        }

        // 1) Persist messages and CLOSE the session BEFORE any output.
        $messageQueue = $app->getMessageQueue();

        $session = $app->getSession(); // Joomla\Session\SessionInterface
        // Start proactively to avoid a lazy start later (which would fail after we echo).
        if (method_exists($session, 'isStarted') && !$session->isStarted()) {
            $session->start();
        }

        if (!empty($messageQueue)) {
            $session->set('application.queue', $messageQueue);
        }

        // Release the session lock so the "background" work can keep running
        // without blocking other requests for this user.
        // Use native close to be extra sure no late handlers try to write again.
        if (method_exists($session, 'close')) {
            $session->close();
        } else {
            @session_write_close();
        }

        // 2) Kill any active output buffers from earlier code to prevent accidental output.
        while (ob_get_level() > 0) {
            @ob_end_clean();
        }

        // 3) Handle the Trident + non-ASCII edge case with a tiny HTML wrapper.
        if ($app->client->engine == WebClient::TRIDENT && !$app::isAscii($this->redirectUrl)) {
            $html = '<!doctype html><html><head>';
            $html .= '<meta http-equiv="content-type" content="text/html; charset=' . $app->charSet . '">';
            $html .= '<script>document.location.href=' . json_encode($this->redirectUrl) . ';</script>';
            $html .= '</head><body></body></html>';

            // No extra headers here—just send body and finish.
            echo $html;

            // Try to finish the response cleanly.
            if (function_exists('fastcgi_finish_request')) {
                fastcgi_finish_request();
            } else {
                @flush();
            }
            return;
        }

        // 4) Build a tiny body and send a proper 303 with no-cache headers.
        $body = 'Redirecting...';

        // Avoid content-length mismatches with compression—set it only if zlib is off.
        $zlibOn = ini_get('zlib.output_compression');
        $app->setBody($body);
        $app->setHeader('Status', '303', true);
        $app->setHeader('Location', $this->redirectUrl, true);
        $app->setHeader('Expires', 'Wed, 17 Aug 2005 00:00:00 GMT', true);
        $app->setHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT', true);
        $app->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0', false);
        $app->setHeader('Pragma', 'no-cache', true);
        $app->setHeader('Connection', 'close', true);
        if (!$zlibOn) {
            $app->setHeader('Content-Length', (string)strlen($body), true);
        }

        $app->sendHeaders();
        echo $body;

        // 5) From this point on, do not touch the session or emit more output.
        // Disconnect DB to free up connection for the crawler run.
        try {
            Factory::getContainer()->get(DatabaseInterface::class)->disconnect();
        } catch (\Throwable $e) {
            // swallow; not critical
        }

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } else {
            @flush();
        }
        // continue long work here (crawler) with no session held and response already gone
    }
}

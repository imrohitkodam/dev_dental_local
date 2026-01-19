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

namespace CodeAlfa\Component\JchOptimize\Administrator\Model;

use _JchOptimizeVendor\V91\Psr\Log\LoggerInterface;
use CodeAlfa\Component\JchOptimize\Administrator\Crawlers\ReCacheWithRedirect as ReCacheCrawler;
use JchOptimize\Core\Platform\PathsInterface;
use JchOptimize\Core\Registry;
use JchOptimize\Core\Spatie\Crawler;
use JchOptimize\Core\Spatie\CrawlQueues\CacheCrawlQueue;
use JchOptimize\Core\SystemUri;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Application\ConsoleApplication;
use Joomla\CMS\MVC\Model\BaseModel;

use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die('Restricted Access');
// phpcs:enable PSR1.Files.SideEffects

class ReCacheModel extends BaseModel
{
    private CacheCrawlQueue $crawlQueue;

    private Registry $params;

    private PathsInterface $pathsUtils;

    private LoggerInterface $logger;

    public function reCache(CMSApplication|ConsoleApplication|null $app, ?string $redirectUrl = null): void
    {
        $baseUrl = SystemUri::siteBaseFull($this->pathsUtils);
        $crawlLimit = (int)$this->state->get('recache_crawl_limit', 500);
        $concurrency = (int)$this->state->get('recache_concurrency', 20);
        $maxDepth = (int)$this->state->get('recache_max_depth', 5);

        Crawler::create($baseUrl)
            ->setCrawlQueue($this->crawlQueue)
            ->setCrawlObserver(new ReCacheCrawler($app, $this->logger, $this->pathsUtils, $redirectUrl))
            ->setTotalCrawlLimit($crawlLimit)
            ->setConcurrency($concurrency)
            ->setMaximumDepth($maxDepth)
            ->startCrawling($baseUrl);
    }

    public function setParams(Registry $params): void
    {
        $this->params = $params;
    }

    public function setPathsUtils(PathsInterface $pathsUtils): void
    {
        $this->pathsUtils = $pathsUtils;
    }

    public function setCrawlQueue(CacheCrawlQueue $crawlQueue): void
    {
        $this->crawlQueue = $crawlQueue;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    protected function populateState(): void
    {
        $this->setState('params', $this->params);
    }
}

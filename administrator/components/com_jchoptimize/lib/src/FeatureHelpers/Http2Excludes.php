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

namespace JchOptimize\Core\FeatureHelpers;

use _JchOptimizeVendor\V91\Joomla\DI\Container;
use _JchOptimizeVendor\V91\Psr\Http\Message\UriInterface;
use JchOptimize\Core\Helper;
use JchOptimize\Core\Preloads\Http2Preload;
use JchOptimize\Core\Preloads\Preload;
use JchOptimize\Core\Preloads\PreloadsCollection;
use JchOptimize\Core\Registry;
use JchOptimize\Core\Uri\Utils;

use function defined;

defined('_JCH_EXEC') or die('Restricted access');

class Http2Excludes extends AbstractFeatureHelper
{
    private PreloadsCollection $imagePreloads;

    private PreloadsCollection $fontPreloads;

    private PreloadsCollection $stylePreloads;

    private PreloadsCollection $scriptPreloads;

    public function __construct(Container $container, Registry $params, private Http2Preload $http2Preload)
    {
        parent::__construct($container, $params);

        $this->imagePreloads = new PreloadsCollection();
        $this->fontPreloads = new PreloadsCollection();
        $this->stylePreloads = new PreloadsCollection();
        $this->scriptPreloads = new PreloadsCollection();
    }

    public function addHttp2Includes(): void
    {
        if (!$this->http2Preload->enabled()) {
            return;
        }

        /** @var array{url?:string, anonymous?:string, use-credentials?: string}[] $includeFiles */
        $includeFiles = $this->params->get('pro_http2_include', []);

        if (empty($includeFiles)) {
            return;
        }

        foreach ($includeFiles as $includeFile) {
            //For backwards compatibility
            if (!isset($includeFile['url'])) {
                $oldValue = $includeFile;
                $includeFile = [];
                $includeFile['url'] = $oldValue;
            }

            $uri = Utils::uriFor($includeFile['url']);

            $type = match (Utils::fileExtension($uri)) {
                'js' => 'script',
                'css' => 'style',
                'woff', 'woff2', 'ttf' => 'font',
                'webp', 'gif', 'jpg', 'jpeg', 'png' => 'image',
                default => '',
            };

            if ($type && $this->http2Preload->isUriValid($uri)) {
                $attributes = [];

                if (isset($includeFile['anonymous'])) {
                    $attributes['crossorigin'] = 'anonymous';
                } elseif (isset($includeFile['use-credentials'])) {
                    $attributes['crossorigin'] = 'use-credentials';
                }

                $preparedUri = $this->http2Preload->prepareUriForPreload($uri);
                $preload = new Preload($preparedUri, $type, $attributes);
                $this->{"{$preload->getAs()}Preloads"}->attach($preload);
            }
        }
    }

    public function findHttp2Excludes(UriInterface $uri): bool
    {
        if (Helper::findExcludes($this->params->get('pro_http2_exclude', []), (string)$uri)) {
            return true;
        }

        return false;
    }

    public function getImagePreloads(): PreloadsCollection
    {
        return $this->imagePreloads;
    }

    public function getFontPreloads(): PreloadsCollection
    {
        return $this->fontPreloads;
    }

    public function getStylePreloads(): PreloadsCollection
    {
        return $this->stylePreloads;
    }

    public function getScriptPreloads(): PreloadsCollection
    {
        return $this->scriptPreloads;
    }
}

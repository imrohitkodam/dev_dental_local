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
use _JchOptimizeVendor\V91\Laminas\Stdlib\SplPriorityQueue;
use _JchOptimizeVendor\V91\Psr\Http\Message\UriInterface;
use CodeAlfa\Minify\Js;
use JchOptimize\Core\FileInfo;
use JchOptimize\Core\Helper;
use JchOptimize\Core\Html\CacheManager;
use JchOptimize\Core\Html\Elements\Script;
use JchOptimize\Core\Html\HtmlElementBuilder;
use JchOptimize\Core\Html\HtmlManager;
use JchOptimize\Core\Preloads\Http2Preload;
use JchOptimize\Core\Registry;
use SplObjectStorage;

use function defined;

use const JCH_DEBUG;

defined('_JCH_EXEC') or die('Restricted access');

class DynamicJs extends AbstractFeatureHelper
{
    /**
     * @var SplPriorityQueue<FileInfo|Script, int>
     */
    private SplPriorityQueue $criticalJs;

    private array $dynamicJsInfoArray = [];

    private array $optimizedDynamicScripts = [];

    private bool $enable;


    public function __construct(
        Container $container,
        Registry $params,
        private CacheManager $cacheManager,
        private HtmlManager $htmlManager,
    ) {
        parent::__construct($container, $params);

        $this->enable = (bool)$this->params->get('pro_reduce_unused_js_enable', '0');
        $this->criticalJs = new SplPriorityQueue();
    }

    public function appendCriticalJsToHtml(): void
    {
        if ($this->enable) {
            foreach (clone $this->criticalJs as $fileInfoOrScriptObj) {
                /** @var Script $criticalJsScript */
                $criticalJsScript = $this->getCriticalScript($fileInfoOrScriptObj)->class('jchoptimize-critical-js');

                if ($this->params->get('loadAsynchronous', '0') && $criticalJsScript->getType() != 'module') {
                    $criticalJsScript->defer();
                }

                $section = $this->params->get('bottom_js', '0') ? 'body' : 'head';
                $this->htmlManager->appendChildToHtml($criticalJsScript->render(), $section);

                if ($this->params->get('preload_criticalJs', '0')) {
                    $this->preloadCriticalScript($criticalJsScript);
                }
            }
        }
    }

    public function addCriticalJsFromScriptOrFileInfo(Script|FileInfo $fileInfoOrScriptObj): bool
    {
        if ($fileInfoOrScriptObj instanceof Script) {
            if ($fileInfoOrScriptObj->getType() == 'module') {
                if ($this->isCriticalModule($fileInfoOrScriptObj)) {
                    $this->criticalJs->insert($fileInfoOrScriptObj, 0);
                    return true;
                }

                return false;
            }
            $fileInfo = new FileInfo($fileInfoOrScriptObj);
        } else {
            $fileInfo = $fileInfoOrScriptObj;
        }

        if ($this->isCriticalJs($fileInfo)) {
            if ($this->elementInFileInfoDeferred($fileInfo)) {
                $this->criticalJs->insert($fileInfo, 0);
            } else {
                $this->criticalJs->insert($fileInfo, 1);
            }

            return true;
        }

        return false;
    }

    private function preloadCriticalScript(Script $script): void
    {
        $http2Preload = $this->getContainer()->get(Http2Preload::class);
        if ($http2Preload instanceof Http2Preload) {
            if ($script->getType() != 'module') {
                $http2Preload->preload($script->getSrc(), 'script');
            }
        }
    }

    private function getCriticalScript(FileInfo|Script $fileInfoOrScriptObj): Script
    {
        if ($fileInfoOrScriptObj instanceof Script) {
            return $fileInfoOrScriptObj;
        }

        $cacheObj = $this->cacheManager->getCombinedFiles([$fileInfoOrScriptObj], $criticalJsId, 'js');
        $criticalJsUrl = $this->htmlManager->buildUrl($criticalJsId, 'js', $cacheObj);

        $script = $this->htmlManager->addDataFileToElement(
            HtmlElementBuilder::script()->src($criticalJsUrl),
            $fileInfoOrScriptObj
        );

        if ($this->elementInFileInfoDeferred($fileInfoOrScriptObj)) {
            $script->defer();
        }
        return $script;
    }

    public function prepareJsDynamicUrls(SplObjectStorage $deferredScriptStorage): void
    {
        if (empty($this->dynamicJsInfoArray) && $deferredScriptStorage->count() == 0) {
            return;
        }

        if (!empty($this->dynamicJsInfoArray)) {
            foreach ($this->dynamicJsInfoArray as $jsInfos) {
                $this->optimizeAndQueueDynamicScripts($jsInfos);
            }
        }

        $this->optimizeAndQueueDeferredScripts($deferredScriptStorage);
        $this->appendCriticalJsToHtml();
        $this->appendDynamicScriptsToHtml();
    }

    /**
     * @param FileInfo[] $jsInfosArray
     * @param bool $deferred
     * @return void
     */
    private function optimizeAndQueueDynamicScripts(array $jsInfosArray, bool $deferred = false): void
    {
        $jsToCombineInfo = [];

        foreach ($jsInfosArray as $jsInfo) {
            if ($jsInfo instanceof FileInfo) {
                if ($this->addCriticalJsFromScriptOrFileInfo($jsInfo)) {
                    continue;
                }
                $jsToCombineInfo[] = $jsInfo;
            }
        }

        if (!empty($jsToCombineInfo)) {
            $cacheObj = $this->cacheManager->getAppendedFiles([], $jsToCombineInfo, $dynamicJsId);
            $script = HtmlElementBuilder::script()
                ->src($this->htmlManager->buildUrl($dynamicJsId, 'js', $cacheObj));

            if (count($jsToCombineInfo) == 1) {
                $this->optimizedDynamicScripts[] = $this->htmlManager->addDataFileToElement(
                    $script,
                    $jsToCombineInfo[0]
                )->attribute('type', 'jchoptimize-text/javascript');
            } else {
                $this->optimizedDynamicScripts[] = $script->attribute('type', 'jchoptimize-text/javascript');
            }
        }
    }

    private function processModules(Script $module): void
    {
        $module->type('jchoptimize-text/module');
        $this->optimizedDynamicScripts[] = $module;
    }

    public function appendDynamicScriptsToHtml(): void
    {
        foreach ($this->optimizedDynamicScripts as $script) {
            $this->htmlManager->appendChildToHTML($script, 'body');
        }
    }

    public function addDynamicJsInfoArray(array $dynamicJsInfoArray): DynamicJs
    {
        $this->dynamicJsInfoArray[] = $dynamicJsInfoArray;

        return $this;
    }

    private function optimizeAndQueueDeferredScripts(SplObjectStorage $deferredScriptStorage): void
    {
        /** @var Script $deferredScript */
        foreach ($deferredScriptStorage as $deferredScript) {
            if ($this->addCriticalJsFromScriptOrFileInfo($deferredScript)) {
                continue;
            }

            if ($deferredScript->hasAttribute('nomodule')) {
                $fileInfo = new FileInfo($deferredScript);
                $cacheObj = $this->cacheManager->getAppendedFiles(
                    [],
                    [$fileInfo],
                    $dynamicNomoduleId
                );
                $this->optimizedDynamicScripts[] = $this->htmlManager->addDataFileToElement(
                    HtmlElementBuilder::script()
                    ->src($this->htmlManager->buildUrl($dynamicNomoduleId, 'js', $cacheObj)),
                    $fileInfo
                )->type('jchoptimize-text/nomodule');
            } elseif ($deferredScript->getType() == 'module') {
                $this->processModules($deferredScript);
            } else {
                $this->optimizeAndQueueDynamicScripts([new FileInfo($deferredScript)], true);
            }
        }
    }

    private function isCriticalJs(FileInfo $jsInfos): bool
    {
        return ($jsInfos->hasUri()
                    && (Helper::findExcludes(
                        Helper::getArray($this->params->get('pro_criticalJs', [])),
                        (string)$jsInfos->getUri()
                    ) || (
                            JCH_DEBUG && Helper::findExcludes(
                                Helper::getArray($this->params->get('criticalJs_configure_helper', [])),
                                (string)$jsInfos->getUri()
                            )
                        )
                    )
                ) || (($content = $jsInfos->getContent()) !== ''
                    && (Helper::findExcludes(
                        Helper::getArray($this->params->get('pro_criticalScripts', [])),
                        Js::optimize($content)
                    ) || (
                            JCH_DEBUG && Helper::findExcludes(
                                Helper::getArray($this->params->get('criticalScripts_configure_helper', [])),
                                Js::optimize($content)
                            )
                        )
                    )
                );
    }

    private function isCriticalModule(Script $module): bool
    {
        return (($uri = $module->getSrc()) instanceof UriInterface
           && (Helper::findExcludes(
               Helper::getArray($this->params->get('pro_criticalModules', [])),
               (string) $uri
           ) || (
                    JCH_DEBUG && Helper::findExcludes(
                        Helper::getArray($this->params->get('criticalModules_configure_helper', [])),
                        (string) $uri
                    )
                )
           )
        ) || ($module->hasChildren() && ($content = $module->getChildren()[0]) != ''
               && (Helper::findExcludes(
                   Helper::getArray($this->params->get('pro_criticalModulesScripts', [])),
                   Js::optimize($content)
               ) || (
                        JCH_DEBUG && Helper::findExcludes(
                            Helper::getArray($this->params->get('criticalModulesScripts_configure_helper', [])),
                            Js::optimize($content)
                        )
                    )
               )
        );
    }

    private function elementInFileInfoDeferred(FileInfo $fileInfo): bool
    {
        if ($fileInfo->hasUri()) {
            $element = $fileInfo->getElement();

            return $element->hasAttribute('defer') || $element->hasAttribute('async');
        }

        return false;
    }
}

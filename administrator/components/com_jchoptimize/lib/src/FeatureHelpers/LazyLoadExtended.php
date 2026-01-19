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
use _JchOptimizeVendor\V91\Laminas\EventManager\Event;
use _JchOptimizeVendor\V91\Psr\Http\Message\UriInterface;
use JchOptimize\Core\Css\Callbacks\CorrectUrls;
use JchOptimize\Core\Css\Components\CssRule;
use JchOptimize\Core\Css\Components\CssSelector;
use JchOptimize\Core\Css\Components\CssSelectorList;
use JchOptimize\Core\Css\Components\CssUrl;
use JchOptimize\Core\Css\CssComponents;
use JchOptimize\Core\Css\ModifyCssUrlsProcessor;
use JchOptimize\Core\Css\Parser as CssParser;
use JchOptimize\Core\FileInfo;
use JchOptimize\Core\Helper;
use JchOptimize\Core\Html\CacheManager;
use JchOptimize\Core\Html\ElementObject;
use JchOptimize\Core\Html\Elements\Audio;
use JchOptimize\Core\Html\Elements\Video;
use JchOptimize\Core\Html\HtmlElementBuilder;
use JchOptimize\Core\Html\HtmlElementInterface;
use JchOptimize\Core\Html\HtmlManager;
use JchOptimize\Core\Html\Parser;
use JchOptimize\Core\Platform\PathsInterface;
use JchOptimize\Core\Registry;
use JchOptimize\Core\SystemUri;

use function array_map;
use function defined;
use function json_encode;
use function trim;

defined('_JCH_EXEC') or die('Restricted access');

class LazyLoadExtended extends AbstractFeatureHelper implements ModifyCssUrlsProcessor
{
    /**
     * @var array $cssBgImagesSelectors The selectors for CSS rules found with background images
     */
    private array $cssBgImagesSelectors = [];

    private ?CssUrl $lazyLoadedCssUrl = null;

    private ?correctUrls $correctUrls = null;

    private bool $lazyLoaded = false;

    public function __construct(
        Container $container,
        Registry $params,
        private CacheManager $cacheManager,
        private PathsInterface $pathsUtils
    ) {
        parent::__construct($container, $params);
    }

    public function lazyLoadAudioVideo(Audio|Video $element): void
    {
        if ($element instanceof Video) {
            $poster = $element->getPoster();

            if ($poster instanceof UriInterface) {
                //If poster value invalid just remove it
                if ((string)$poster != '' && $poster->getPath() != SystemUri::currentBasePath()) {
                    $element->data('poster', $poster);
                }

                $element->remove('poster');
            }
        }

        if ($element->hasAttribute('autoplay')) {
            $element->data('autoplay');
            $element->remove('autoplay');
        }

        $element->preload('none');
        $element->class('jch-lazyload');
    }

    public function lazyLoadBgImages(HtmlElementInterface $element): void
    {
        if ($element->hasAttribute('style')) {
            $style = $element->getStyle();

            $cssRule = new CssRule();
            $cssRule->setDeclarationList($style);
            $cssUrl = $cssRule->getCssUrls()[0];
            $this->lazyLoadCssUrls($cssRule);

            if ($cssRule->isModified() && $cssUrl instanceof CssUrl) {
                $url = (string)$cssUrl->getUri();
                $element->data('bg', $url);
                $element->class('jch-lazyload');
            }

            $element->style($cssRule->getDeclarationList());
        }
    }

    public static function getLazyLoadClassOrId(HtmlElementInterface $element): array
    {
        $classOrIds = [];

        if ($element->hasAttribute('class')) {
            $classOrIds = array_merge($classOrIds, $element->getClass());
        }

        if ($element->hasAttribute('id')) {
            $classOrIds[] = $element->getId();
        }

        return $classOrIds;
    }

    public function setupLazyLoadExtended(Parser $parser, string $section, bool $lazyLoad): void
    {
        if (
            (
                $section == 'below_fold'
                && $lazyLoad
                && $this->params->get('pro_lazyload_iframe', '0')
            )
            ||
            (
                $section == 'above_fold'
                && $lazyLoad
                && $this->params->get('use_youtube_facade')
            )
        ) {
            $iFrameElement = new ElementObject();
            $iFrameElement->setNamesArray(['iframe']);
            $iFrameElement->addNegAttrCriteriaRegex('data-src');
            $iFrameElement->addPosAttrCriteriaRegex('src');
            $parser->addElementObject($iFrameElement);
            unset($iFrameElement);
        }

        if (
            ($section == 'above_fold' && $this->params->get('pro_lcp_images_enable', '0'))
            || ($section == 'below_fold' && $this->params->get('pro_lazyload_bgimages', '0'))
            || $this->params->get('pro_next_gen_images', '1')
        ) {
            $bgElement = new ElementObject();
            $bgElement->voidElementOrStartTagOnly = true;
            $bgElement->addPosAttrCriteriaRegex('style*=' . CssParser::cssUrlToken());
            $parser->addElementObject($bgElement);
            unset($bgElement);
        }

        if (
            ($section == 'above_fold' && $this->params->get('pro_lcp_images_enable', '0'))
            || ($section == 'below_fold' && $this->params->get('pro_lazyload_audiovideo', '0'))
        ) {
            $audioVideoElement = new ElementObject();
            $audioVideoElement->setNamesArray(['video', 'audio']);
            $parser->addElementObject($audioVideoElement);
            unset($audioVideoElement);
        }
    }

    public function lazyLoadCssBackgroundImages(Event $event): void
    {
        if (
            $this->params->get('lazyload_enable', '0')
            && $this->params->get('pro_lazyload_bgimages', '0')
        ) {
            $cssSelectors = array_unique($this->cssBgImagesSelectors);
            //Remove any pseudo-classes or pseudo-elements from the selector
            $cssSelectors = array_map(
                fn (CssSelectorList $a) => $a->removeLastDescendantNonFunctionalPseudoSelectors()->render(),
                $cssSelectors
            );
            $jsSelectors = json_encode($cssSelectors);

            $script = <<<HTML
<script>
const jchLazyLoadSelectors = {$jsSelectors};
</script>
HTML;
            $htmlManager = $event->getTarget();

            if ($htmlManager instanceof HtmlManager) {
                $htmlManager->appendChildToHTML($script, 'body');
            }
        }
    }

    private function lazyLoadCssUrls(CssRule $cssRule): void
    {
        if (
            str_contains($cssRule->getDeclarationList(), "background")
            && preg_match('#' . CssParser::cssUrlToken() . '#', $cssRule->getDeclarationList())
        ) {
            $cssRule->modifyCssUrls($this);
        }
    }

    public function handleCssBgImages(CorrectUrls $correctUrls, CssRule $cssRule, bool &$lazyLoaded = false): void
    {
        $this->correctUrls = $correctUrls;
        $this->lazyLoadCssUrls($cssRule);
        $lazyLoaded = $this->lazyLoaded;
    }

    public function processCssUrls(CssUrl $cssUrl): ?CssUrl
    {
        $cssUri = $cssUrl->getUri();
        //Exclude LCP images
        if (
            Helper::findMatches(
                Helper::getArray($this->params->get('pro_lcp_images', [])),
                (string)$cssUri
            )
        ) {
            return $cssUrl;
        }

        //Don't need to lazy-load data-image
        if (
            $this->params->get('pro_lazyload_bgimages', '0')
            && $cssUri->getScheme() != 'data'
            && $cssUri->getPath() != '/'
            && $cssUri->getPath() != ''
            //skip excluded images
            && !Helper::findExcludes(
                Helper::getArray($this->params->get('excludeLazyLoad', [])),
                (string)$cssUri
            )
            && !Helper::findExcludes(
                Helper::getArray($this->params->get('pro_excludeLazyLoadFolders', [])),
                (string)$cssUri
            )
        ) {
            $this->lazyLoadedCssUrl = $cssUrl;

            return null;
        }

        return $cssUrl;
    }

    private function getJsLazyLoadAssets(): array
    {
        $assets = [];

        $assets[] = new FileInfo(
            HtmlElementBuilder::script()->src(
                $this->pathsUtils->mediaUrl() . '/lazysizes-config/ls.loader.js?' . JCH_VERSION
            )
        );
        $assets[] = new FileInfo(
            HtmlElementBuilder::script()->src(
                $this->pathsUtils->mediaUrl() . '/lazysizes/plugins/unveilhooks/ls.unveilhooks.min.js?' . JCH_VERSION
            )
        );
        $assets[] = new FileInfo(
            HtmlElementBuilder::script()->src(
                $this->pathsUtils->mediaUrl() . '/lazysizes/lazysizes.min.js?' . JCH_VERSION
            )
        );

        if ($this->params->get('pro_lazyload_bgimages', '0')) {
            $assets[] = new FileInfo(
                HtmlElementBuilder::script()->src(
                    $this->pathsUtils->mediaUrl() . '/js/core/bg-images.lazysizes.js?' . JCH_VERSION
                )
            );
        }

        return $assets;
    }

    public function loadLazyLoadAssets(Event $event): void
    {
        if (
            $this->params->get('lazyload_enable', '0')
            && ($this->params->get('pro_lazyload_bgimages', '0')
                || $this->params->get('pro_lazyload_audiovideo', '0'))
        ) {
            $jsLazyLoadAssets = $this->getJsLazyLoadAssets();
            $cacheObj = $this->cacheManager->getCombinedFiles($jsLazyLoadAssets, $lazyLoadCacheId, 'js');
            /** @var HtmlManager $htmlManager */
            $htmlManager = $event->getTarget();
            $uri = $htmlManager->buildUrl($lazyLoadCacheId, 'js', $cacheObj);
            $htmlManager->appendChildToHTML(
                $htmlManager->getNewJsLink($uri, false, true),
                'body'
            );
        }
    }

    private function createLazyLoadedDeclarations(CssUrl $cssUrl): string
    {
        $important = $cssUrl->getImportantContext() ? ' !important' : '';

        return  "background-image: {$cssUrl->render()}{$important};";
    }

    private function amendLazyLoadedParentSelectors(CssSelectorList $parentSelectorList): string
    {
        if ($this->hasMultipleSelectors($parentSelectorList)) {
            return '';
        }

        /** @var CssSelector $selector */
        $selector = $this->getSingleSelector($parentSelectorList);

        if ($selector->hasNonFunctionalPseudoSelector()) {
            $amendedSelector = clone $selector;
            return $amendedSelector->removeLastDescendantNonFunctionalPseudoSelectors()->render();
        }

        return $parentSelectorList->render();
    }

    private function amendLazyLoadedParentDeclarations(
        CssSelectorList $parentSelectorList,
        string $parentDeclarationList
    ): string {
        if ($this->hasMultipleSelectors($parentSelectorList)) {
            $childCssRule = new CssRule($parentSelectorList->render(), $parentDeclarationList);

            return $childCssRule->render();
        }

        /** @var CssSelector $selector */
        $selector = $this->getSingleSelector($parentSelectorList);

        if ($selector->hasNonFunctionalPseudoSelector()) {
            $childCssRule = new CssRule(
                '&' . $selector->renderLastDescendantNonFunctionalPseudoSelector(),
                $parentDeclarationList
            );

            return $childCssRule->render();
        }

        return $parentDeclarationList;
    }

    private function createLazyLoadedSelector(CssSelectorList $parentSelectorList): string
    {
        if ($this->hasMultipleSelectors($parentSelectorList)) {
            $amendedSelectorList = clone $parentSelectorList;
            $amendedSelectorList->addClass('jch-lazyloaded');

            return $amendedSelectorList->render();
        }

        /** @var CssSelector $selector */
        $selector = $this->getSingleSelector($parentSelectorList);

        if ($selector->hasNonFunctionalPseudoSelector()) {
            return '&.jch-lazyloaded' . $selector->renderLastDescendantNonFunctionalPseudoSelector();
        }

        return '&.jch-lazyloaded';
    }

    private function getSingleSelector(CssSelectorList $parentSelectorList)
    {
        $selectors = $parentSelectorList->getSelectors();
        $selectors->rewind();

        return $selectors->current();
    }

    private function hasMultipleSelectors(CssSelectorList $parentSelectorList): bool
    {
        return $parentSelectorList->getSelectors()->count() > 1;
    }

    public function addCssBgImagesSelectors(array $bgImageSelectors): void
    {
        $this->cssBgImagesSelectors = array_merge($this->cssBgImagesSelectors, $bgImageSelectors);
    }

    public function postProcessModifiedCssComponent(CssComponents $cssComponent): void
    {
        if ($cssComponent instanceof CssRule) {
            $cssRule = $cssComponent;

            if ($cssRule->isModified()) {
                $parentSelectorList = CssSelectorList::load($cssRule->getSelectorList());
                $parentDeclarationList = $cssRule->getDeclarationList();

                if ($parentSelectorList->getSelectors()->count() > 0) {
                    if (trim($parentDeclarationList) == '') {
                        $clonedParentSelectorList = clone $parentSelectorList;
                        $cssRule->setSelectorList($clonedParentSelectorList->addClass('jch-lazyloaded'));
                        $cssRule->setDeclarationList($this->createLazyLoadedDeclarations($this->lazyLoadedCssUrl));
                    } else {
                        $cssRule->setSelectorList($this->amendLazyLoadedParentSelectors($parentSelectorList))
                            ->setDeclarationList(
                                $this->amendLazyLoadedParentDeclarations($parentSelectorList, $parentDeclarationList)
                            );

                        $lazyLoadRule = new CssRule(
                            $this->createLazyLoadedSelector($parentSelectorList),
                            $this->createLazyLoadedDeclarations($this->lazyLoadedCssUrl)
                        );

                        $cssRule->appendDeclarationList($lazyLoadRule->render());
                    }

                    $this->correctUrls->getCacheObject()->addBgSelectors($parentSelectorList);
                    $this->lazyLoaded = true;
                }
            }
        }
    }
}

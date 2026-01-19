<?php

namespace YOOtheme\Joomla;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\Input\Input;
use YOOtheme\Application;
use YOOtheme\Arr;
use YOOtheme\Http\Exception;
use YOOtheme\Http\Request;
use YOOtheme\Http\Response;
use YOOtheme\Metadata;
use YOOtheme\Path;
use YOOtheme\Url;

class Platform
{
    /**
     * Handle application routes.
     */
    public static function handleRoute(Application $app, CMSApplication $joomla, Input $input)
    {
        if ($input->getCmd('option') !== 'com_ajax' || !$input->get('p')) {
            return;
        }

        $response = null;

        // disable cache
        $joomla->set('caching', 0);

        // default format
        $input->def('format', 'raw');

        // get response
        $joomla->registerEvent('onAfterDispatch', function () use ($app, &$response, $input) {
            // On administrator routes com_login is rendered for guest users
            if ($input->getCmd('option') !== 'com_ajax') {
                return;
            }

            $response = $app->run(false);
        });

        // send response
        $joomla->registerEvent('onAfterRender', function () use ($joomla, &$response) {
            if (!$response) {
                return;
            }

            $isHtml = strpos($response->getContentType(), 'html');

            if (!$isHtml) {
                // disable gzip for none html responses like binary images
                $joomla->set('gzip', false);
            }

            if (version_compare(JVERSION, '4.0', '>')) {
                $joomla->allowCache(true);
                $joomla->setResponse($isHtml ? $response->write($joomla->getBody()) : $response);
                return;
            }

            // send headers
            if (!headers_sent()) {
                $response->sendHeaders();
            }

            // set body for none html responses
            if (!$isHtml) {
                $joomla->setBody($response->getBody());
            }

            // set cms headers (fix issue when headers_sent() is still false)
            if (!headers_sent()) {
                $joomla->allowCache(true);
                $joomla->setHeader('Cache-Control', $response->getHeaderLine('Cache-Control'));
                $joomla->setHeader('Content-Type', $response->getContentType());
            }
        });
    }

    /**
     * Handle application errors.
     *
     * @param Request    $request
     * @param Response   $response
     * @param \Exception $exception
     *
     * @throws \Exception
     *
     * @return Response
     */
    public static function handleError(Request $request, $response, $exception)
    {
        if ($exception instanceof Exception) {
            if (str_starts_with($request->getHeaderLine('Content-Type'), 'application/json')) {
                return $response->withJson($exception->getMessage());
            }

            return $response
                ->write($exception->getMessage())
                ->withHeader('Content-Type', 'text/plain');
        }

        throw $exception;
    }

    /**
     * Callback to register assets.
     *
     * @param Metadata $metadata
     * @param Document $document
     */
    public static function registerAssets(Metadata $metadata, Document $document)
    {
        if (version_compare(JVERSION, '4.0', '<')) {
            static::registerAssetsLegacy($metadata, $document);
            return;
        }

        $wa = $document->getWebAssetManager();

        // Ensure WebAssetManager is not locked
        // This might happen if a view is rendered after the documents head has been rendered (e.g. HikaShop renders multiple Views)
        if (\Closure::bind(fn() => $this->locked, $wa, $wa)()) {
            return;
        }

        foreach ($metadata->all('style:*') as $style) {
            if ($style->href) {
                $attrs = Arr::omit($style->getAttributes(), ['version', 'href', 'rel', 'defer']);

                if ($style->defer && $document instanceof HtmlDocument) {
                    $attrs = array_merge($attrs, [
                        'rel' => 'preload',
                        'as' => 'style',
                        'onload' => "this.onload=null;this.rel='stylesheet'",
                    ]);
                }
                $wa->registerAndUseStyle(
                    $style->getName(),
                    static::toRelativeUrl($style->href),
                    ['version' => $style->version],
                    $attrs,
                );
            } elseif ($value = $style->getValue()) {
                $wa->addInlineStyle($value, [], Arr::omit($style->getAttributes(), ['version']));
            }
        }

        foreach ($metadata->all('script:*') as $script) {
            if ($script->src) {
                $wa->registerAndUseScript(
                    $script->getName(),
                    static::toRelativeUrl($script->src),
                    ['version' => $script->version],
                    Arr::omit($script->getAttributes(), ['version', 'src']),
                );
            } elseif ($value = $script->getValue()) {
                $wa->addInlineScript($value, [], Arr::omit($script->getAttributes(), ['version']));
            }
        }
    }

    protected static function toRelativeUrl($url)
    {
        $url = Path::resolveAlias($url);

        if (Path::isBasePath(JPATH_ROOT, $url)) {
            return Path::relative(JPATH_ROOT, $url);
        }

        return $url;
    }

    /**
     * Callback to register assets (Joomla 3.x).
     *
     * @param Metadata $metadata
     * @param Document $document
     */
    protected static function registerAssetsLegacy(Metadata $metadata, Document $document)
    {
        foreach ($metadata->all('style:*') as $style) {
            if ($style->href) {
                $attrs = Arr::omit($style->getAttributes(), ['version', 'href', 'rel', 'defer']);

                if ($style->defer && $document instanceof HtmlDocument) {
                    $document->addHeadLink(
                        htmlentities(Url::to($style->href, ['ver' => $style->version])),
                        'preload',
                        'rel',
                        [
                            'as' => 'style',
                            'onload' => "this.onload=null;this.rel='stylesheet'",
                        ] + $attrs,
                    );
                } else {
                    $document->addStyleSheet(
                        htmlentities(Url::to($style->href)),
                        ['version' => $style->version],
                        $attrs,
                    );
                }
            } elseif ($value = $style->getValue()) {
                $document->addStyleDeclaration($value);
            }
        }

        foreach ($metadata->all('script:*') as $script) {
            if ($script->src) {
                $document->addScript(
                    htmlentities(Url::to($script->src)),
                    ['version' => $script->version],
                    Arr::omit($script->getAttributes(), ['version', 'src']),
                );
            } elseif ($value = $script->getValue()) {
                if ($document instanceof HtmlDocument) {
                    $document->addCustomTag((string) $script->withAttribute('version', ''));
                } else {
                    $document->addScriptDeclaration($value);
                }
            }
        }
    }
}

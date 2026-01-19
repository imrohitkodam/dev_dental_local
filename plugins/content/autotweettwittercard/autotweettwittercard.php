<?php

/*
 * @package     Perfect Publisher
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2025 Extly, CB. All rights reserved.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

defined('_JEXEC') || exit;

if (!defined('AUTOTWEET_API') && !@include_once(JPATH_ADMINISTRATOR.'/components/com_autotweet/api/autotweetapi.php')) {
    return;
}

require_once __DIR__ . '/twittercard.php';

final class PlgContentAutotweetTwitterCard extends \Joomla\CMS\Plugin\CMSPlugin
{
    public const OPT_OFF = 0;

    public const OPT_IMG_PRIO_INTRO = 2;

    public const OPT_IMG_PRIO_FULL = 3;

    public const OPT_IMG_PRIO_CLASS = 4;

    public const OPT_IMG_PRIO_CUSTOM = 5;

    public const OPT_DESC_META = 1;

    public const OPT_DESC_INTRO = 2;

    public const OPT_DESC_SITE = 3;

    public const OPT_DESC_CUSTOM = 4;

    private $contentItem;

    private $twitterCard;

    private $classNames;

    private $ogTagsProcessed = false;

    public function onContentPrepare($context, &$article, &$params, $limitstart = 0)
    {
        if ($this->ogTagsProcessed) {
            return;
        }

        $input = \Joomla\CMS\Factory::getApplication()->input;
        $component = $input->get('option');

        $includedComponents = $this->params->get('included_components', 'com_content,com_autotweet');
        $includedComponents = explode(',', str_replace(['\n', ' '], [',', ''], $includedComponents));

        if (!in_array($component, $includedComponents, true)) {
            $this->ogTagsProcessed = true;

            return;
        }

        $id = (int) $input->get('id');

        // Content - Front
        $id = $id ?: (int) $input->get('a_id');

        if ($id === 0) {
            $this->ogTagsProcessed = true;

            return;
        }

        if ($articleHelper = OGArticleFactory::getHelper($component, $context, $article)) {
            $this->contentItem = $articleHelper->getArticle();
        }

        if ($this->params->get('smart-loader', 1)) {
            $ogSmartLoader = new OGSmartLoader($this->contentItem);

            if ('com_autotweet' === $component) {
                $this->contentItem = $ogSmartLoader->getItemByPost($id);
            } else {
                $this->contentItem = $ogSmartLoader->getItemByUrl();
            }
        }

        if ($this->contentItem->title) {
            $this->insertTwitterCard();
        }

        $this->ogTagsProcessed = true;
    }

    /**
     * insertTwitterCard.
     *
     * @return void
     */
    private function insertTwitterCard()
    {
        $twittercardHelper = new TwittercardHelper();
        $twittercardHelper->title = OGHelper::title($this->params, $this->contentItem);
        $twittercardHelper->type = $this->type();
        $twittercardHelper->description = OGHelper::description($this->params, $this->contentItem);
        $twittercardHelper->author = OGHelper::author($this->params, $this->contentItem);
        $twittercardHelper->imgSrc = OGHelper::image($this->params, $this->contentItem);

        // No image yet, but we have the default image
        if (empty($twittercardHelper->imgSrc)) {
            $imageUrl = EParameter::getComponentParam(CAUTOTWEETNG, 'default_image', '');
            $twittercardHelper->imgSrc = RouteHelp::getInstance()->getAbsoluteUrl($imageUrl, true);
        }

        if (!empty($twittercardHelper->imgSrc)) {
            $twittercardHelper->imgAlt = OGHelper::imageAlt($this->params, $this->contentItem);
        }

        $twittercardHelper->insertTags();
    }

    /**
     * type.
     *
     * @return string
     */
    private function type()
    {
        $type = $this->params->get('og-type', 'summary_large_image');

        if ((empty($type)) || ('custom' === $type)) {
            return $this->params->get('og-type-custom', 'summary_large_image');
        }

        return $type;
    }
}

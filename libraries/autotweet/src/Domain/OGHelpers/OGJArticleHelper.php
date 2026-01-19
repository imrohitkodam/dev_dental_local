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

final class OGJArticleHelper
{
    private $articleObj;

    public function __construct($article)
    {
        $oGItem = new OGItem();

        $oGItem->title = $article->title;
        $oGItem->metadesc = $article->metadesc;
        $oGItem->introtext = $article->introtext;
        $oGItem->publish_up = $article->publish_up;
        $oGItem->modified = $article->modified;
        $oGItem->created_by = $article->created_by;

        $oGItem->category_title = $this->categoryTitle($article);
        $oGItem->description = $this->description($article);
        $oGItem->isPublished = $this->isPublished($article);
        $oGItem->isPublic = $this->isPublic($article);
        $oGItem->url = $this->url($article);
        $oGItem->tags = $this->tags($article);

        $oGItem->imageArray = $this->imagesInContent($article);
        $oGItem->firstContentImage = $this->firstImageInContent($oGItem->imageArray);
        $oGItem->firstContentImageAlt = $this->firstImageAltInContent($oGItem->imageArray);

        $articleImages = $this->articleImages($article);
        $oGItem->introImage = $this->introImage($articleImages);
        $oGItem->introImageAlt = $this->introImageAlt($articleImages);
        $oGItem->fullTextImage = $this->fullTextImage($articleImages);
        $oGItem->fullTextImageAlt = $this->fullTextImageAlt($articleImages);

        $oGItem->image = $this->image($oGItem->introImage, $oGItem->fullTextImage, $oGItem->firstContentImage);
        $oGItem->imageAlt = $this->imageAlt($oGItem->introImageAlt, $oGItem->fullTextImageAlt, $oGItem->firstContentImageAlt);

        $this->articleObj = $oGItem;
    }

    public function __get($name)
    {
        return $this->{$name};
    }

    public function getArticle()
    {
        return $this->articleObj;
    }

    public function categoryTitle($article)
    {
        return empty($article->category_title) ? null : $article->category_title;
    }

    public function image($introImage, $fullTextImage, $firstContentImage)
    {
        if ($introImage) {
            return $introImage;
        }

        if ($fullTextImage) {
            return $fullTextImage;
        }

        return $firstContentImage;
    }

    public function imageAlt($introImageAlt, $fullTextImageAlt, $firstContentImageAlt)
    {
        if ($introImageAlt) {
            return $introImageAlt;
        }

        if ($fullTextImageAlt) {
            return $fullTextImageAlt;
        }

        return $firstContentImageAlt;
    }

    public function imagesInContent($article)
    {
        $content = $this->content($article);

        $images = [];

        if ((empty($content)) || (!class_exists('DOMDocument'))) {
            return $images;
        }

        $domDocument = new DOMDocument();
        $domDocument->recover = true;
        $domDocument->strictErrorChecking = false;
        @$domDocument->loadHTML($content);

        foreach ($domDocument->getElementsByTagName('img') as $domNodeList) {
            $images[] = [
                'src' => $domNodeList->getAttribute('src'),
                'class' => $domNodeList->getAttribute('class'),
                'alt' => $domNodeList->getAttribute('alt'),
            ];
        }

        return $images;
    }

    public function firstImageInContent(array $images)
    {
        if ($images === []) {
            return null;
        }

        return $images[0]['src'] ?? null;
    }

    public function firstImageAltInContent(array $images)
    {
        if ($images === []) {
            return null;
        }

        return $images[0]['alt'] ?? null;
    }

    public function content($article)
    {
        if (!empty($article->text)) {
            return $article->text;
        }

        if (!empty($article->fulltext)) {
            return $article->fulltext;
        }

        if (!empty($article->introtext)) {
            return $article->introtext;
        }

        return null;
    }

    public function isEmptyContent($article)
    {
        return empty($this->content($article));
    }

    public function description($article)
    {
        $descText = empty($article->text) ? null : $article->text;
        $description = empty($article->text) ? null : $article->text;

        if (!empty($article->introtext)) {
            $descText = $article->introtext;
        } elseif (!empty($article->metadesc)) {
            $descText = $article->metadesc;
        }

        $descNeedles = [
            "\n",
            "\r",
            '"',
            "'",
        ];

        str_replace($descNeedles, ' ', $description);
        $description = substr(htmlspecialchars(strip_tags($descText), \ENT_QUOTES | \ENT_SUBSTITUTE), 0, 250);

        return $description;
    }

    public function isPublic($article)
    {
        if (empty($article->access)) {
            return false;
        }

        $access = $article->access;
        $isPublic = (bool) $access;

        return $isPublic;
    }

    public function isPublished($article)
    {
        $isPublState = (bool) $article->state;

        if (!$isPublState) {
            return false;
        }

        $publishUp = empty($article->publish_up) ? null : $article->publish_up;
        $publishDown = empty($article->publish_down) ? null : $article->publish_down;

        if (empty($publishUp)) {
            return false;
        }

        $date = \Joomla\CMS\Factory::getDate();
        $currentDate = $date->toSql();

        if (($publishUp > $currentDate)) {
            return false;
        }

        if ($publishDown < $currentDate && '0000-00-00 00:00:00' !== $publishDown && !empty($publishDown)) {
            return false;
        }

        return true;
    }

    public function isArticle()
    {
        $hasID = !empty($this->articleObj->id);
        $hasTitle = !empty($this->articleObj->title);
        return $hasID && $hasTitle;
    }

    public function url($article)
    {
        JLoader::register('\Joomla\CMS\Table\Category', JPATH_PLATFORM.'/joomla/database/table/category.php');

        $cats = plgAutotweetBase::getContentCategories($article->catid);
        $cat_ids = $cats[0];
        $catNames = $cats[1];
        $catAlias = $cats[2];

        // Use main category for article url
        $cat_slug = $article->catid.':'.TextUtil::convertUrlSafe($catAlias[0]);
        $id_slug = $article->id.':'.TextUtil::convertUrlSafe($article->alias);

        // Create internal url for Joomla core content article
        JLoader::import('components.com_content.helpers.route', JPATH_ROOT);
        $url = ContentHelperRoute::getArticleRoute($id_slug, $cat_slug);
        $instance = RouteHelp::getInstance();

        // Optimized
        // $url = $routeHelp->getAbsoluteUrl($url);
        $url = \Joomla\CMS\Uri\Uri::getInstance()->toString();

        return $url;
    }

    private function introImage($articleImages)
    {
        if (!empty($articleImages->image_intro)) {
            return $articleImages->image_intro;
        }

        return null;
    }

    private function introImageAlt($articleImages)
    {
        if (!empty($articleImages->image_intro_alt)) {
            return $articleImages->image_intro_alt;
        }

        return null;
    }

    private function fullTextImage($articleImages)
    {
        if (!empty($articleImages->image_fulltext)) {
            return $articleImages->image_fulltext;
        }

        return null;
    }

    private function fullTextImageAlt($articleImages)
    {
        if (!empty($articleImages->image_fulltext_alt)) {
            return $articleImages->image_fulltext_alt;
        }

        return null;
    }

    private function articleImages($article)
    {
        if (!isset($article->images)) {
            return null;
        }

        return json_decode($article->images);
    }

    private function isExtensionInstalled($option)
    {
        $db = \Joomla\CMS\Factory::getDbo();
        $query = 'SELECT extension_id AS id, element AS "option", params, enabled FROM '.$db->quoteName('#__extensions').' WHERE '.$db->quoteName('type').'= '.$db->quote('component').' AND '.$db->quoteName('element').'= '.$db->quote($option);
        $db->setQuery($query);
        $result = $db->loadObject();
        return (bool) $result;
    }

    private function tags($article)
    {
        $metatagString = empty($article->metakey) ? null : $article->metakey;

        if (empty($metatagString)) {
            return null;
        }

        $tags = explode(',', $metatagString);

        foreach ($tags as $key => $value) {
            $tagsArray[] = trim(str_replace(' ', '', $value));
        }

        return $tagsArray;
    }

    private function articleSlug($article)
    {
        $slug = $article->id.':'.$this->articleAlias($article);

        return $slug;
    }

    private function articleAlias($article)
    {
        $alias = $article->alias;

        if (empty($alias)) {
            $db = \Joomla\CMS\Factory::getDBO();
            $query = 'SELECT a.alias FROM '.$db->quoteName('#__content').' AS '.$db->quoteName('a').' WHERE a.id='.$db->quote($article->id);
            $db->setQuery($query);
            $result = $db->loadObject();
            $alias = empty($result->alias) ? $article->title : $result->alias;
        }

        $alias = TextUtil::convertUrlSafe($alias);

        return $alias;
    }

    private function categoryAlias($article)
    {
        $db = \Joomla\CMS\Factory::getDBO();
        $query = 'SELECT c.alias FROM '.$db->quoteName('#__categories').' AS '.$db->quoteName('c').' WHERE c.id='.$db->quote($article->catid);
        $db->setQuery($query);
        $result = $db->loadObject();
        $alias = $result->alias;
        $alias = TextUtil::convertUrlSafe($alias);

        return $alias;
    }
}

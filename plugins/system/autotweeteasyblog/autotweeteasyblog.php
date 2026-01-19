<?php

/*
 * @package     Perfect Publisher
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2021 Extly, CB. All rights reserved.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

defined('_JEXEC') || exit;

require_once JPATH_ADMINISTRATOR.'/components/com_autotweet/api/autotweetapi.php';

// EasyBlog extension plugin for AutoTweet.

/**
 * PlgSystemAutotweetEasyblog.
 *
 * @since       1.0
 */
class PlgSystemAutotweetEasyblog extends PlgAutotweetBase
{
    // Typeinfo

    // Not used at the moment
    public const TYPE_ARTICLE = 1;
    public const TYPE_COMMENT = 2;

    public const ARTICLE_STATE_UNPUBLISHED = 0;
    public const ARTICLE_STATE_PUBLISHED = 1;
    public const ARTICLE_STATE_PENDING = 4;

    protected $on_event_oncomment = false;

    protected $template_oncomment;

    // Const TYPE_COMMENT = 2;
    protected $categories = '';

    protected $excluded_categories = '';

    protected $post_modified = 0;

    protected $show_category = 0;

    protected $show_hash = 0;

    protected $use_text = 0;

    protected $use_text_count;

    protected $static_text = '';

    protected $static_text_pos = 1;

    protected $static_text_source = 0;

    // Used for tags
    protected $metakey_count = 1;

    protected $easyBlogVersion;

    /**
     * PlgSystemAutotweetEasyblog.
     *
     * @param string &$subject Param
     * @param object $params   Param
     */
    public function __construct(&$subject, $params)
    {
        parent::__construct($subject, $params);

        $pluginParams = $this->pluginParams;

        // Plugin specific params
        $this->categories = $pluginParams->get('categories', '');
        $this->excluded_categories = $pluginParams->get('excluded_categories', '');
        $this->post_modified = (int) $pluginParams->get('post_modified');
        $this->show_category = (int) $pluginParams->get('show_category');
        $this->show_hash = (int) $pluginParams->get('show_hash');
        $this->use_text = (int) $pluginParams->get('use_text');
        $this->use_text_count = (int) $pluginParams->get('use_text_count75', SharingHelper::MAX_CHARS_TITLE);
        $this->static_text = $pluginParams->get('static_text', '');
        $this->static_text_pos = (int) $pluginParams->get('static_text_pos', 1);
        $this->static_text_source = (int) $pluginParams->get('static_text_source');

        $this->on_event_oncomment = (int) $pluginParams->get('on_event_oncomment', 0);
        $this->template_oncomment = $pluginParams->get('template_oncomment', '[username]: [comment] on [title]');

        // Used for tags
        $this->metakey_count = (int) $pluginParams->get('metakey_count', 1);
    }

    /**
     * Checks for new articles.
     *
     * @return bool
     */
    public function onAfterRoute()
    {
        if ('com_easyblog' === JFactory::getApplication()->input->get('option')) {
            // Publish article (backend)
            if (('publish' === JFactory::getApplication()->input->get('task', '')) && ('blogs' === JFactory::getApplication()->input->get('view', ''))) {
                $this->_initEasyBlog();
                $cid = JFactory::getApplication()->input->get('cid', [], 'array');
                $article = EasyBlogHelper::getTable('Blog');

                foreach ($cid as $id) {
                    $article->load($id);

                    // Post allowed?
                    if (self::ARTICLE_STATE_UNPUBLISHED === (int) $article->published) {
                        $this->postMessage($article);
                    }
                }
            }
        }

        return true;
    }

    /**
     * onAfterEasyBlogSave.
     *
     * @param object &$article An object
     * @param bool   $isNew    If the content is just about to be created
     *
     * @return bool
     */
    public function onAfterEasyBlogSave(&$article, $isNew)
    {
        $this->retrieveAdvancedAttrs();
        $this->saveAdvancedAttrs($article->id);

        // Approvals are allowed modifications
        $input = new JInput($_REQUEST);
        $task = $input->get('task');

        if ((!$this->post_modified) && (('pending.approve' === $task) || ('approve' === $task))) {
            $this->post_modified = true;
        }

        // Content article
        if ((($isNew)
            || ($this->post_modified)
            || (($this->advanced_attrs) && ((self::POSTTHIS_YES === (int) $this->advanced_attrs->postthis) || (self::POSTTHIS_IMMEDIATELY === (int) $this->advanced_attrs->postthis))))
            && (self::ARTICLE_STATE_PUBLISHED === (int) $article->published)) {
            //  || ($article->published == self::ARTICLE_STATE_PENDING)
            $this->postMessage($article);
        }

        return true;
    }

    /**
     * onAfterCommentSave.
     *
     * @param object $comment An object
     *
     * @return bool
     */
    public function onAfterCommentSave($comment)
    {
        if (!$this->on_event_oncomment) {
            return;
        }

        $this->comment = $comment;

        $article = EB::table('Post');
        $article->load($comment->post_id, true);

        $this->postMessage($article, self::TYPE_COMMENT);

        return true;
    }

    /**
     * getExtendedData.
     *
     * @param string $id             param
     * @param string $typeinfo       param
     * @param string &$native_object Param
     *
     * @return array
     */
    public function getExtendedData($id, $typeinfo, &$native_object)
    {
        $engine = JPATH_ADMINISTRATOR.'/components/com_easyblog/includes/easyblog.php';

        if (!file_exists($engine)) {
            return;
        }

        require_once $engine;
        $article = EB::table('Post');
        $article->load($id, true);

        // Get categories
        $cats = $this->getCategories($article->category_id);
        $catIds = $cats[0];
        $catNames = $cats[1];

        // Use title or text as twitter message
        $message = $this->message;
        $title = $article->title;
        $articleText = $this->getFulltext($article);
        $text = $this->getMessagetext($this->use_text, $this->use_text_count, $message, $articleText);

        // Tags and static text
        $tags = implode(',', $this->getTags($id));

        if ((self::STATIC_TEXT_SOURCE_STATIC === (int) $this->static_text_source) || ((self::STATIC_TEXT_SOURCE_METAKEY === (int) $this->static_text_source) && (empty($tags)))) {
            $title = $this->addStatictext($this->static_text_pos, $title, $this->static_text);
            $text = $this->addStatictext($this->static_text_pos, $text, $this->static_text);
        } elseif (self::STATIC_TEXT_SOURCE_METAKEY === (int) $this->static_text_source) {
            $this->addHashtags($this->getHashtags($tags, $this->metakey_count));
        }

        // Title
        $categoriesResult = $this->addCategories($this->show_category, $catNames, $title, 0);
        $title = $categoriesResult['text'];

        // Text
        $categoriesResult = $this->addCategories($this->show_category, $catNames, $text, $this->show_hash);
        $text = $categoriesResult['text'];

        if (!empty($categoriesResult['hashtags'])) {
            $this->addHashtags($categoriesResult['hashtags']);
        }

        // Return values
        $data = [
            'title' => $title,
            'text' => $text,
            'hashtags' => $this->renderHashtags(),
            'fulltext' => $articleText,
            'catids' => $catIds,
            'cat_names' => $catNames,
            'author' => $this->getAuthor($article),
            'is_valid' => true,
        ];

        return $data;
    }

    /**
     * _initEasyBlog.
     *
     * @return bool
     */
    protected function _initEasyBlog()
    {
        // EasyBlog
        if (!class_exists('EasyBlogHelper')) {
            JLoader::register('EasyBlogHelper', JPATH_ROOT.'/components/com_easyblog/helpers/helper.php');
            JLoader::load('EasyBlogHelper');
        }

        $this->easyBlogVersion = EasyBlogHelper::getLocalVersion();
    }

    /**
     * postMessage.
     *
     * @param object $article  the item object
     * @param int    $typeinfo Type
     *
     * @return void
     */
    protected function postMessage($article, $typeinfo = self::TYPE_ARTICLE)
    {
        $cats = $this->getCategories($article->category_id);
        $catIds = $cats[0];

        $isIncluded = $this->isCategoryIncluded($catIds);
        $isExcluded = $this->isCategoryExcluded($catIds);

        if ((!$isIncluded) || ($isExcluded)) {
            return true;
        }

        // Create url
        $url = 'index.php?option=com_easyblog&view=entry&id='.$article->id;
        $needles = [
            'view' => 'entry',
            'id' => $article->id,
        ];
        $itemId = $this->getItemId('com_easyblog', $needles);

        if ($itemId) {
            $url .= '&Itemid='.$itemId;
        }

        if (JFactory::getApplication()->isClient('site')) {
            // New router
            if (class_exists('EBR')) {
                $url = EBR::_($url);
            } else {
                // Old router
                $router50 = JPATH_ROOT.'/components/com_easyblog/helpers/router.php';

                if ((!class_exists('EasyBlogRouter')) && (file_exists($router50))) {
                    JLoader::register('EasyBlogRouter', $router50);
                }

                if (class_exists('EasyBlogRouter')) {
                    $url = EasyBlogRouter::_($url);
                }
            }
        }

        // Get image url
        $fulltext = $this->getFulltext($article);

        $image = null;

        if ((isset($article->image)) && (!empty($article->image))) {
            $image = json_decode($article->image);

            if ((isset($image->url)) && (!empty($image->url))) {
                $image = $image->url;
            }
        }

        // Get the first image from the text
        if (empty($image)) {
            $image = $this->getImageFromText($fulltext);
        }

        $native_object = null;

        $title = $article->title;
        $publish_up = $article->publish_up;

        if (self::TYPE_COMMENT === (int) $typeinfo) {
            $title = $this->template_oncomment;
            $title = str_replace('[username]', JFactory::getUser()->username, $title);
            $title = str_replace('[comment]', $this->comment->comment, $title);
            $title = str_replace('[title]', $article->title, $title);

            $publish_up = $this->comment->created;
        }

        $this->postStatusMessage($article->id, $publish_up, $title, $typeinfo, $url, $image, $native_object);
    }

    /**
     * getFulltext.
     *
     * @param object $article An object
     *
     * @return string
     */
    private function getFulltext($article)
    {
        $texts = [];

        if (isset($article->intro)) {
            $texts[] = $article->intro;
        }

        if (isset($article->content)) {
            $texts[] = $article->content;
        }

        if (isset($article->post->content)) {
            $texts[] = $article->post->content;
        }

        $full_content = implode(' ', $texts);

        return $full_content;
    }

    /**
     * getCategories.
     *
     * @param array $article_cat param
     *
     * @return array
     */
    private function getCategories($article_cat)
    {
        $cat_ids = [];
        $catNames = [];

        $current = $article_cat;

        while ($category = $this->_loadCategory($current)) {
            $cat_ids[] = $category->id;
            $catNames[] = $category->title;

            $current = $category->parent_id;
        }

        return [
            $cat_ids,
            $catNames,
        ];
    }

    /**
     * _loadCategory.
     *
     * @param int $catid param
     *
     * @return object
     */
    private function _loadCategory($catid)
    {
        $_db = JFactory::getDbo();
        $query = $_db->getQuery(true);

        $query->select('id');
        $query->select('title');
        $query->select('parent_id');

        $query->from('#__easyblog_category');

        $query->where('id = '.$_db->quote($catid));
        $_db->setQuery($query);

        return $_db->loadObject();
    }

    /**
     * getTags.
     *
     * @param int $aid param
     *
     * @return array
     */
    private function getTags($aid)
    {
        $table_tag = '#__easyblog_tag';
        $table_posttag = '#__easyblog_post_tag';

        $db = JFactory::getDBO();
        $query = 'SELECT t.title FROM '
                .$db->quoteName($table_tag).' AS t, '
                .$db->quoteName($table_posttag).' AS p'
                .' WHERE p.post_id = '.(int) $aid
                .' AND p.tag_id = t.id';
        $db->setQuery($query);
        $tags = $db->loadColumn();

        return $tags;
    }

    /**
     * getAuthor.
     *
     * @param object &$article Param
     *
     * @return string
     */
    private function getAuthor(&$article)
    {
        $uid = $article->created_by;
        $user = JFactory::getUser($uid);

        return $user->username;
    }
}

<?php
/**
 * @package         SourceCoast Extensions
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('sourcecoast.openGraph');

class OpenGraphPlugin extends JPlugin
{
    var $pluginName;
    var $extensionName;
    var $supportedObjects;
    var $jfbcOgObjectModel;
    var $supportedComponents;
    var $supportedAutopostLabel;
    var $supportedAutopostTypes;
    var $jfbcLibrary;
    var $object;
    var $db;
    var $setsDefaultTags;

    private $openGraphLibrary;

    function __construct(&$subject, $config)
    {
        $this->pluginName = $config['name'];
        $this->extensionName = $config['name']; // Should be overridden by the plugin itself.
        if (class_exists('JFBCFactory'))
        {
            JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_jfbconnect/models');
            $this->jfbcOgObjectModel = JModelLegacy::getInstance('OpenGraphObject', 'JFBConnectModel');
            $this->jfbcLibrary = JFBCFactory::provider('facebook');
        }

        if (class_exists('OpenGraphLibrary'))
            $this->openGraphLibrary = OpenGraphLibrary::getInstance();

        $this->hasDefaultTags = false;
        $this->db = JFactory::getDBO();

        parent::__construct($subject, $config);

        $this->init();
    }

    protected function init()
    {
    }

    /******* Triggers ******/
    public function onOpenGraphGetPlugins()
    {
        return $this;
    }

    public function onOpenGraphAfterDispatch()
    {
        if ($this->inSupportedComponent())
        {
            $juri = JURI::getInstance();
            $url = $juri->toString();
            $queryVars = $this->getUrlVars($url);
            $this->object = $this->findObjectType($queryVars);

            // Set the OG tags if this plugin has an object type set *or* will set OG tags even without objects set by the admin
            if ($this->object || $this->setsDefaultTags)
                $this->setOpenGraphTags();

            if ($this->object)
                $this->setTypeTag();
        }
    }

    // Get the URL query variables for the passed in URL.
    // The caller *MUST* filter any variables it uses from the return
    public function getUrlVars($url)
    {
        $router = JRouter::getInstance('site');
        $origVars = $router->getVars();
        $router->setVars(array(), false);
        // DO NOT use JURI::getInstance! Re-routing on the same instance causes big issues
        $juri = new JURI($url);
        // Odd hack to prevent the parsing of the URL to redirect to the https version in certain circumstances
        $jConfig = JFactory::getConfig();
        $forceSSL = $jConfig->get('force_ssl');
        $jConfig->set('force_ssl', 0);
        $queryVars = $router->parse($juri);
        $jConfig->set('force_ssl', $forceSSL);
        // Reset the router back to it's original state
        $router->setVars($origVars);
        return $queryVars;
    }


    public function onOpenGraphFindObjectType($url)
    {
        $queryVars = $this->getUrlVars($url);
        return $this->findObjectType($queryVars);
    }

    public function onOpenGraphGetBestImage($article)
    {
        return $this->getBestImage($article);
    }

    public function onOpenGraphGetBestText($article)
    {
        return $this->getBestText($article);
    }

    /******** End triggers ********/

    /******** Object Calls ********/
    protected function getObjects($type)
    {
        // Can we make this more efficient to load all the plugin objects once, and then just pick off the 'name' types
        // when we need them?
        return $this->jfbcOgObjectModel->getPluginObjects($this->pluginName, $type);
    }

    protected function addSupportedObject($systemName, $displayName)
    {
        $this->supportedObjects[$systemName] = $displayName;
    }

    private function inSupportedComponent()
    {
        // If none are defined, plugin always fired
        if (!$this->supportedComponents)
            return true;

        if (in_array(JFactory::getApplication()->input->getCmd('option'), $this->supportedComponents))
            return true;

        return false;
    }

    protected function addOpenGraphTag($name, $value, $isFinal)
    {
        $this->openGraphLibrary->addOpenGraphTag($name, $value, $isFinal, PRIORITY_NORMAL, "Open Graph - " . ucfirst($this->pluginName)." Plugin");
    }

    protected function skipOpenGraphTag($name)
    {
        $this->openGraphLibrary->blockTag($name, $this->pluginName);
    }

    protected function getDefaultObject($name)
    {
        $object = new ogObject();
        $object->loadDefaultObject($this->pluginName, $name);

        return $object;
    }

    // Setup any extra Open Graph tags specific to this object (title, image, description, video, etc)
    protected function setOpenGraphTags()
    {
        // Should be overridden by the plugin
    }

    protected function setTypeTag()
    {
        $this->addOpenGraphTag('type', $this->object->getObjectPath(), true);
    }

    protected function getBestImage($article) { return null; }

    protected function getBestText($article) { return null; }

    /******** Get Images and Descriptions ********/

    protected function getFirstCategoryText($category, $numCharacters = 100, $socialGraphFirstText = '1')
    {
        $categoryText = '';
        if (isset($category->description))
            $categoryText = $this->getSelectedText($category->description, $socialGraphFirstText, $numCharacters);
        return $categoryText;
    }

    protected function getFirstArticleText($article, $numCharacters = 100, $socialGraphFirstText = '1')
    {
        $articleText = '';
        if (isset($article->introtext) && trim(strip_tags($article->introtext)) != "")
        {
            $articleText = $article->introtext;
        } else if (isset($article->text) && trim(strip_tags($article->text)) != "")
        {
            $articleText = $article->text;
        } else if (isset($article->fulltext) && trim(strip_tags($article->fulltext)) != "")
        {
            $articleText = $article->fulltext;
        }

        $articleText = $this->getSelectedText($articleText, $socialGraphFirstText, $numCharacters);

        return $articleText;
    }

    protected function getSelectedText($contentText, $socialGraphFirstText, $numCharacters)
    {
        $articleText = JFBConnectUtilities::trimNBSP($contentText);
        $articleText = strip_tags($articleText);
//        $articleText = preg_replace('/\s+/', ' ', $articleText);
        JFBConnectUtilities::stripSystemTags($articleText, 'JFBC');
        JFBConnectUtilities::stripSystemTags($articleText, 'JLinked');
        JFBConnectUtilities::stripSystemTags($articleText, 'SC');
        JFBConnectUtilities::stripSystemTags($articleText, 'loadposition');
        $articleText = str_replace(array('{K2Splitter}','{', '}'), '', $articleText);
        $articleText = trim($articleText);

        $addEllipsis = false;
        if ($socialGraphFirstText == '1') //Add first X characters
        {
            $addEllipsis = strlen($articleText) > $numCharacters;

            if (function_exists('mb_substr'))
                $articleText = mb_substr($articleText, 0, $numCharacters, 'UTF-8');
            else
                $articleText = substr($articleText, 0, $numCharacters);
        }
        else if ($socialGraphFirstText == '2') //Add first X words. Split by whitespace and preserve what whitespace there was
        {
            if (function_exists('mb_ereg_search_init') && function_exists('mb_ereg_search_pos') && function_exists('mb_ereg_search_getregs'))
            {
                // Note: mb method does not use pattern delimiters of slashes before and after
                $parts = $this->mb_explode('(\s+)', $articleText, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
            }
            else
            {
                $parts = preg_split('/(\s+)/', $articleText, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
            }

            $numWordParts = $numCharacters * 2 - 1; //Number of words + same amount of spaces in between - edge
            $selParts = array_slice($parts, 0, $numWordParts);
            $articleText = implode('', $selParts);
            $addEllipsis = count($parts) > $numWordParts;

        } else
            $articleText = '';

        if ($addEllipsis)
            $articleText .= '...';

        return $articleText;
    }

    /**
     * https://github.com/vanderlee/PHP-multibyte-functions/blob/master/functions/mb_explode.php
     * A cross between mb_split and preg_split, adding the preg_split flags
     * to mb_split.
     * @param string $pattern
     * @param string $string
     * @param int $limit
     * @param int $flags
     * @return array
     */
    protected function mb_explode($pattern, $string, $limit = -1, $flags = 0)
    {
        $strlen = strlen($string);  // bytes!
        if (!$strlen) {
            return array('');
        }
        mb_ereg_search_init($string);
        $lengths = array();
        $position = 0;
        while (($array = mb_ereg_search_pos($pattern)) !== false) {
            // capture split
            $lengths[] = array($array[0] - $position, false, null);
            // move position
            $position = $array[0] + $array[1];
            // capture delimiter
            $regs = mb_ereg_search_getregs();
            $lengths[] = array($array[1], true, isset($regs[1]) && $regs[1]);
            // Continue on?
            if ($position >= $strlen) {
                break;
            }
        }
        // Add last bit, if not ending with split
        $lengths[] = array($strlen - $position, false, null);
        // Substrings
        $parts = array();
        $position = 0;
        $count = 1;
        foreach ($lengths as $length) {
            $is_delimiter = $length[1];
            $is_captured = $length[2];
            if ($limit > 0 && !$is_delimiter && ($length[0] || ~$flags & PREG_SPLIT_NO_EMPTY) && ++$count > $limit) {
                if ($length[0] > 0 || ~$flags & PREG_SPLIT_NO_EMPTY) {
                    $parts[] = $flags & PREG_SPLIT_OFFSET_CAPTURE ? array(mb_strcut($string, $position), $position) : mb_strcut($string, $position);
                }
                break;
            } elseif ((!$is_delimiter || ($flags & PREG_SPLIT_DELIM_CAPTURE && $is_captured)) && ($length[0] || ~$flags & PREG_SPLIT_NO_EMPTY)) {
                $parts[] = $flags & PREG_SPLIT_OFFSET_CAPTURE ? array(mb_strcut($string, $position, $length[0]), $position) : mb_strcut($string, $position, $length[0]);
            }
            $position += $length[0];
        }
        return $parts;
    }


    protected function getFirstImage($article)
    {
        if (isset($article->text))
            $articleText = $article->text;
        else
            $articleText = $article->introtext . $article->fulltext;

        $fullImagePath = $this->getFirstImageFromText($articleText);
        return $fullImagePath;
    }

    protected function getFirstImageFromText($text)
    {
        $fullImagePath = '';
        if (preg_match_all('/<img [^>]*src=["|\']([^"|\']+)/i', $text, $matches))
        {
            $fullImagePath = $this->getImageLink($matches[1][0]);
        }
        return $fullImagePath;
    }

    protected function getImageLink($path)
    {
        if ($path)
        {
            $juri = JURI::getInstance();
            $basePath = str_replace(array($juri->getScheme() . "://", $juri->getHost()), "", $juri->base());

            if (strpos($path, '//') === 0)
            {
                return $juri->getScheme() . ':' . $path;
            } else if (strpos($path, $basePath) === 0)
            {
                $path = substr($path, strlen($basePath));
                $path = $juri->base() . $path;
            } else if (strpos($path, "http") !== 0)
                $path = $juri->base() . $path;
        }
        return $path;
    }

    /******** Autopost ********/
    public function openGraphProfilesGetPlugins()
    {
        if($this->supportedAutopostTypes)
            return $this;
    }

    public function openGraphUpdatePending($articleId, $link, $ext)
    {
        // Override by plugin
    }

    public function onContentBeforeSave($context, $article, $isNew)
    {
        // Override by plugin
    }

    public function onContentChangeState($context, $pks, $value)
    {
        // Override by plugin
    }

    public function getAutopostMessage($objectType, $id)
    {
        // Override by plugin
        return '';
    }

    public function isArticlePublishPending($publish_up)
    {
        $currentDate = JFactory::getDate();
        $publishDate = new JDate($publish_up);

        $interval = $publishDate->diff($currentDate);

        return ($interval->invert == 1);
    }

    public function isArticleSpecial($accessType)
    {
        return $accessType == '3' || $accessType == '6'; //Special or Super User
    }

    public function getPublishedState($itemId, $layout)
    {
        /* We don't check the channel, so new channels that are created don't have
        updated content posted to them
        At the time of initial publishing, if content hasn't been autoposted before, it will go to all channels configured
        */
        $ext = $this->supportedComponents[0];
        $query = $this->db->getQuery(true);
        $query->select('*')
            ->from($this->db->qn('#__jfbconnect_autopost_activity'))
            ->where($this->db->qn('item_id') . '=' . $this->db->q($itemId) . ' AND ' .
                $this->db->qn('layout') . '=' . $this->db->q($layout) . ' AND ' .
                $this->db->qn('ext') . '=' . $this->db->q($ext) . ' AND ' .
                $this->db->qn('status') . '<>' . $this->db->q(0)
            );
        $this->db->setQuery($query);
        $activity = $this->db->loadObject();
        return $activity;
    }

    public function autopublish($ogType, $articleId, $link, $isPending = false, $language = null)
    {
        //Need to make an AJAX call to front-end to get the SEF URL
        $http = JHttpFactory::getHttp();

        $langParam = "";
        if (!is_null($language))
        {
            $langCodes   = JLanguageHelper::getLanguages('lang_code');
            $sefLang = $langCodes[$language]->sef;
            $langParam = '&lang=' . $sefLang;
        }

        $sef = $http->get(JUri::root() . 'index.php?option=com_jfbconnect' . $langParam . '&task=ajax.sef&url=' . base64_encode($link));

        if($sef->code == 303)
        {
            $redirect = $sef->headers['Location'];
            $http2 = JHttpFactory::getHttp();
            $sef = $http2->get($redirect);
        }

        if ($sef && $sef->code == 200)
            $link = $sef->body;
        else
            $link = JUri::root() . $link;

        $objectType = $this->getLayout($ogType);

        $activity = $this->getPublishedState($articleId, $objectType);

        if($activity && $activity->status == 2)
        {
            $this->removePublish($ogType, $articleId);
            $activity = null;
        }
        if($activity == null)
        {
            // Get Mappings from Autopost table for the given object
            $query = $this->db->getQuery(true);
            $query->select('*')
                ->from($this->db->qn('#__jfbconnect_autopost'))
                ->where('(' . $this->db->qn('opengraph_type') . '=' . $this->db->q($this->pluginName) . ')', 'OR');
            if($ogType)
            {
                if(is_array($ogType))
                {
                    foreach($ogType as $type)
                        $query->where('(' . $this->db->qn('opengraph_type') . '=' . $this->db->q($type->id) . ')');
                }
                else
                    $query->where('(' . $this->db->qn('opengraph_type') . '=' . $this->db->q($ogType->id) . ')');
            }


            $this->db->setQuery($query);
            $mappings = $this->db->loadObjectList();

            $message = htmlspecialchars_decode($this->getAutopostMessage($objectType, $articleId));

            //Perform the post and save result
            $mappingsPerformed = array();
            foreach ($mappings as $mapping)
            {
                $channelId = $mapping->channel_id;
                if(!in_array($channelId, $mappingsPerformed))
                {
                    $this->performPost($channelId, $objectType, $articleId, $message, $link, $mapping->id, $isPending);
                    $mappingsPerformed[] = $channelId;
                }
            }
        }
    }

    public function updatePending($ogType, $articleId, $link)
    {
        //Delete pending and perform auto-publish again
        $this->removePublish($ogType, $articleId);
        $this->autopublish($ogType, $articleId, $link);
    }

    public function removePublish($ogType, $articleId)
    {
        $objectType = $this->getLayout($ogType);
        $ext = $this->supportedComponents[0];
        $query = $this->db->getQuery(true);
        $query->delete($this->db->qn("#__jfbconnect_autopost_activity"))
            ->where($this->db->qn('item_id') . '=' . $this->db->q($articleId) . ' AND ' .
                $this->db->qn('layout') . '=' . $this->db->q($objectType) . ' AND ' .
                $this->db->qn('ext') . '=' . $this->db->q($ext) . ' AND ' .
                $this->db->qn('status') . '=' . $this->db->q(2)
            );
        $this->db->setQuery($query);
        $this->db->execute();
    }

    private function getLayout($ogType)
    {
        if($ogType)
        {
            if(is_array($ogType))
                $objectType = $ogType[0]->system_name;
            else
                $objectType = $ogType->system_name;
        }
        else
        {
            $ext = $this->supportedComponents[0];
            if($ext=='com_content')
                $objectType = 'article';
            else if($ext == 'com_k2')
                $objectType = 'item';
            else if($ext == 'com_easyblog')
                $objectType = 'post';
            else //JomSocial
                $objectType = JRequest::getCmd('view');
        }


        return $objectType;
    }

    public function hasAllowedAccessType($channel, $articleId)
    {
        return true;
    }

    public function checkAccessType($channel, $article)
    {
        if($article)
        {
            $query = $this->db->getQuery(true)
                ->select($this->db->quoteName('a.id', 'value') . ', ' . $this->db->quoteName('a.ordering', 'value'))
                ->from($this->db->quoteName('#__viewlevels', 'a'))
                ->where($this->db->quoteName('id') . '=' . $this->db->quote($article->access));
            $this->db->setQuery($query);
            $articleValue = $this->db->loadObject();

            $query = $this->db->getQuery(true)
                ->select($this->db->quoteName('a.id', 'value') . ', ' . $this->db->quoteName('a.ordering', 'value'))
                ->from($this->db->quoteName('#__viewlevels', 'a'))
                ->where($this->db->quoteName('id') . '=' . $this->db->quote($channel->options->get('autopost_access_type')));
            $this->db->setQuery($query);
            $channelValue = $this->db->loadObject();

            return ($articleValue <= $channelValue);
        }
        return false;
    }

    public function performPost($id, $layout, $articleId, $message, $link, $mappingId, $isPending)
    {
        JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/tables/');
        $row = JTable::getInstance('Channel', 'Table');
        $row->load($id);

        if($row->published)
        {
            $options = new JRegistry();
            $options->loadObject($row->attribs);
            $provider = JFBCFactory::provider($row->provider);
            $channel = $provider->channel($row->type, $options);

            if($this->hasAllowedAccessType($channel, $articleId))
            {
                $post = new JRegistry();
                $post->set('message', $message);
                $post->set('link', $link);

                $channel->post($post, AP_TYPE_AUTOPUBLISH, $isPending, $mappingId, $articleId, $this->supportedComponents[0], $layout);
            }
        }
    }
}
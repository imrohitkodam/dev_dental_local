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

define('SOURCECOAST_JFBCONNECT', 'JFBConnect');
define('SOURCECOAST_JLINKED', 'JLinked');
define('CARRIAGE_RETURN', chr(13));
define('PRIORITY_HIGH', 2);
define('PRIORITY_NORMAL', 1);
define('PRIORITY_LOW', 0);

class OpenGraphLibrary
{
    private static $libraryInstance;

    private $openGraphTags; //array of OpenGraphTag objects that need to be added to content
    private $skippedGraphTags; //array of OpenGraphTag objects that we have not added, since an isFinal was encountered
    private $overlappingTags; //array of OpenGraphTag objects that were encountered from other extensions and removed

    private $blockedTags; //array of Tag names to skip, since other extensions may add them, or user wants to skip them

    function __construct()
    {
        $this->openGraphTags = array();
        $this->skippedGraphTags = array();
        $this->overlappingTags = array();
        $this->blockedTags = array();
    }

    public function __get($name)
    {
        switch ($name)
        {
            case 'tagsInserted':
                return $this->openGraphTags;
            case 'tagsSkipped' :
                return $this->skippedGraphTags;
            case 'tagsRemoved' :
                return $this->overlappingTags;
            case 'tagsBlocked' :
                return $this->blockedTags;
        }
    }

    public static function getInstance()
    {
        if (!isset(self::$libraryInstance))
        {
            self::$libraryInstance = new OpenGraphLibrary();
        }

        return self::$libraryInstance;
    }

    public function blockTag($tagName, $origin)
    {
        $tag = OpenGraphUtilities::getTagName($tagName); //Fully qualify the name
        if(!in_array($tag, $this->blockedTags))
        {
            $this->blockedTags[] = $tag;
            $this->skipOpenGraphTag($tag, $origin);
        }
    }

    public function addBlockedTags($blockedFields)
    {
        $blockedFields = $this->prepareSkipTags($blockedFields);
        foreach($blockedFields as $blockedTag)
            $this->blockTag($blockedTag, 'JFBCSystem');
    }

    public function buildCompleteOpenGraphList()
    {
        $openGraphTags = '';
        $twitterTags = '';

        foreach ($this->openGraphTags as $ogTag)
        {
            if(!in_array($ogTag->name, $this->blockedTags))
                $openGraphTags .= $ogTag->toString($this->blockedTags);
        }

        //add twitter cards support
        //check if twitter cards support is enabled
        //set required tags if not found in $openGraphTags.
        //cannot set required tags if the tag is included in the skip tags
        $twitter_card_enabled = JFBCFactory::config()->get('social_graph_twitter_cards_enabled');
        if($twitter_card_enabled)
        {
            $card_type = JFBCFactory::config()->get('social_graph_twitter_cards_types');
            if(strpos($openGraphTags, 'twitter:card') === false && !in_array('twitter:card', $this->blockedTags))
                $twitterTags .= '<meta name="twitter:card" content="'.$card_type.'"/>' . CARRIAGE_RETURN;

            if(strpos($openGraphTags, 'twitter:site') === false && !in_array('twitter:site', $this->blockedTags))
            {
                $twitter_site = JFBCFactory::config()->get('social_graph_twitter_cards_twitter_site');
                $twitter_site = OpenGraphUtilities::getTwitterName($twitter_site);
                $twitterTags .= '<meta name="twitter:site" content="'.$twitter_site.'"/>' . CARRIAGE_RETURN;
            }

            if(strpos($openGraphTags, 'twitter:creator') === false && $card_type == 'summary_large_image' && !in_array('twitter:creator', $this->blockedTags))
            {
                $twitter_creator = JFBCFactory::config()->get('social_graph_twitter_cards_twitter_creator');
                $twitter_creator = OpenGraphUtilities::getTwitterName($twitter_creator);
                if(!empty($twitter_creator)) //twitter:creator is not required
                    $twitterTags .= '<meta name="twitter:creator" content="'.$twitter_creator.'"/>' . CARRIAGE_RETURN;
            }
        }

        $openGraphTags = $twitterTags.$openGraphTags;
        return $openGraphTags;
    }

    public function prepareSkipTags($skipGraphFields)
    {
        $tags = array();
        $skipTags = explode(',', $skipGraphFields);
        foreach ($skipTags as $tag)
        {
            if(!empty($tag))
                $tags[] = OpenGraphUtilities::getTagName($tag);
        }
        return $tags;
    }

    public function removeOverlappingTags($contents)
    {
        foreach ($this->openGraphTags as $ogTag)
        {
            if (!$ogTag->allowsMultiple && get_class($ogTag) != 'SkippedOpenGraphTag')
            {
                // Removed the /s switch from the grep in v5.1.0 below to prevent multi-line scanning
                // Maybe add back later, but want to be more careful since we now look for /> instead of just >

                $regex = '/<meta (property|name)="' . $ogTag->name . '".+?\/>/i';
                $overlappingFound = preg_match($regex, $contents, $matches);
                $contents = preg_replace($regex, '', $contents);

                if ($overlappingFound)
                {
                    $openGraphTag = new OpenGraphTag($ogTag->name, "", false, $ogTag->priority, '3rd Party Extension');
                    $this->overlappingTags[] = $openGraphTag;
                }
            }
        }
        return $contents;
    }

    public function addOpenGraphTag($name, $value, $isFinal, $priority = PRIORITY_NORMAL, $origin = 'jfbconnect')
    {
        $this->addTwitterTags($name, $value, $priority, $origin);
        $this->addOpenGraphTagEx($name, $value, $isFinal, $priority, $origin);
    }

    /*
     * This method adds a tag to the list of all open graph tags
     * we will eventually add to the page content.
     *
     * - Once isFinal is true, if we encounter this again, do not use new value
     * - If isFinal is false, keep updating value (unless multiple values are accepted)
     *
     * If {SCOpenGraph} is found, then use if isOverrideable is true
     * - content plugin - auto-generate description
     * - users add this manually to articles, etc
     */
    public function addOpenGraphTagEx($name, $value, $isFinal, $priority = PRIORITY_NORMAL, $origin = 'jfbconnect')
    {
        $isUsingNewValue = true;

        $name = OpenGraphUtilities::getTagName($name);

        if ($value == '' || !$value)
        {
            $isUsingNewValue = false;
        }
        else if (array_key_exists($name, $this->openGraphTags))
        {
            $existingTag = $this->openGraphTags[$name];

            if ($existingTag->allowsMultiple) // Just add value, disregards isFinal
            {
                $this->openGraphTags[$name]->addValue($value, $isFinal, $priority);
            }
            else if ($existingTag->isFinal) // We are not adding it
            {
                $this->addSkippedTag($name, $value, $isFinal, $priority, $origin);
                $isUsingNewValue = false;
            }
            else // Reset the value. Does not create new object to replace?
            {
                $existingTag = $this->openGraphTags[$name];
                $this->addSkippedTag($name, $existingTag->value[0], false, $priority, $existingTag->origin);
                $this->openGraphTags[$name]->setValues($value, $isFinal, $priority);
                $this->openGraphTags[$name]->origin = $origin;
            }
        }
        else // Does not exist, so just add it
        {
            $openGraphTag = new OpenGraphTag($name, $value, $isFinal, $priority, $origin);
            $this->openGraphTags[$name] = $openGraphTag;
        }

        return $isUsingNewValue;
    }

    /*
    * This method will add the tag so it is skipped when coming up with the
    * final list of OpenGraph tags to add. This should be called from the
    * plugins and only used when we "trust" that the component is adding og tags
    * correctly
    *
    * - Reset as skipped object if a regular one already exists.
    * - If it does not exist yet, then add it as final and it will be skipped later
    */
    public function skipOpenGraphTag($name, $origin)
    {
        $name = OpenGraphUtilities::getTagName($name);
        $this->openGraphTags[$name] = new SkippedOpenGraphTag($name, "SKIPPED", true, PRIORITY_HIGH, $origin);
        return true;
    }

    private function addDefaultGraphTag($name, $value, $priority)
    {
        if (!array_key_exists($name, $this->openGraphTags))
        {
            $this->addOpenGraphTagEx($name, $value, false, $priority, 'Auto-generated');
        }
        else
        {
            $this->addSkippedTag($name, $value, false, $priority, 'Auto-generated');
        }
    }

    private function addSkippedTag($name, $value, $isFinal, $priority, $origin)
    {
        $openGraphTag = new OpenGraphTag($name, $value, $isFinal, $priority, $origin);
        $this->skippedGraphTags[] = $openGraphTag;
    }

    /*
     * Expects a list of "key=value" strings. Adds them into the list as non-final
     * Should handle {SCOpenGraph} tags first before automatically generating. They have priority.
     */
    public function addOpenGraphEasyTags($easyTagsList)
    {
        foreach ($easyTagsList as $graphField)
        {
            $this->addTagFromString($graphField, false, PRIORITY_HIGH, 'EasyTag');
        }
    }

    /*
     * Expects a default list of carriage-returned fields that are "key=value" format
     */
    public function addDefaultSettingsTags($defaultSettingsTagList)
    {
        $fields = explode(CARRIAGE_RETURN, $defaultSettingsTagList);
        foreach ($fields as $graphField)
        {
            $this->addTagFromString($graphField, true, PRIORITY_LOW, 'Default Setting');
        }
    }

    public function addAutoGeneratedTags($locale)
    {
        $url = OpenGraphUtilities::getCurrentUrlTag();
        $this->addDefaultGraphTag('og:url', $url, PRIORITY_NORMAL);

        $title = OpenGraphUtilities::getCurrentPageTitleTag();
        $this->addDefaultGraphTag('og:title', $title, PRIORITY_NORMAL);
        $this->addDefaultGraphTag('twitter:title', $title, PRIORITY_NORMAL);

        $desc = OpenGraphUtilities::getCurrentDescriptionTag();
        $this->addDefaultGraphTag('og:description', $desc, PRIORITY_NORMAL);
        $this->addDefaultGraphTag('twitter:description', $desc, PRIORITY_NORMAL);

        $type = OpenGraphUtilities::getCurrentPageTypeTag();
        $this->addDefaultGraphTag('og:type', $type, PRIORITY_NORMAL);

        $appId = OpenGraphUtilities::getCurrentAppId();
        $this->addDefaultGraphTag('fb:app_id', $appId, PRIORITY_NORMAL);

        $locale = OpenGraphUtilities::getCurrentLocaleTag($locale);
        $this->addDefaultGraphTag('og:locale', $locale, PRIORITY_NORMAL);

        $siteName = OpenGraphUtilities::getCurrentSiteNameTag();
        $this->addDefaultGraphTag('og:site_name', $siteName, PRIORITY_NORMAL);
    }

    private function addTagFromString($graphFieldStr, $isDefault, $priority, $origin)
    {
        $keyValue = explode('=', $graphFieldStr, 2);
        if (count($keyValue) == 2)
        {
            $graphName = strtolower(trim($keyValue[0]));
            $graphValue = JFBConnectUtilities::trimNBSP($keyValue[1]);
            //$graphValue = trim($keyValue[1]);

            $this->addTwitterTags($graphName, $graphValue, $priority, $origin);

            if ($isDefault)
                $this->addDefaultGraphTag($graphName, $graphValue, $priority);
            else
                $this->addOpenGraphTagEx($graphName, $graphValue, false, $priority, $origin);
        }
    }

    private function addTwitterTags($graphName, $graphValue, $priority, $origin)
    {
        $twitter_card_enabled = JFBCFactory::config()->get('social_graph_twitter_cards_enabled');

        //Add twitter title and description if encounterning og versions
        if(($graphName == 'og:title' || $graphName == 'title') && $twitter_card_enabled)
            $this->addOpenGraphTagEx('twitter:title', $graphValue, false, $priority, $origin);
        else if(($graphName == 'og:description' || $graphName == 'description') && $twitter_card_enabled)
            $this->addOpenGraphTagEx('twitter:description', $graphValue, false, $priority, $origin);
    }
}

class OpenGraphTag
{
    var $name;
    var $value;
    var $isFinal;
    var $allowsMultiple;
    var $priority;
    var $origin;

    function __construct($name, $value, $isFinal, $priority, $origin)
    {
        $this->isFinal = $isFinal;
        $this->name = OpenGraphUtilities::getTagName($name);
        $this->priority = $priority;
        $this->origin = $origin;
        $this->addValue($value, $isFinal, $priority);

        // og:image and any custom namespace tags are allowed multiple times
        $this->allowsMultiple = (($name == 'og:image' || $name == 'twitter:image') && JFBCFactory::config()->get('social_graph_multiple_images')) ||
            (strpos($name, 'og:') === false && strpos($name, 'fb:') === false && strpos($name, 'twitter:') === false)
        ;
    }

    function addValue($value, $isFinal, $priority)
    {
        $cleanValue = $this->getCleanValue($value);
        if ($priority == PRIORITY_HIGH && is_array($this->value))
        {
            array_unshift($this->value, $cleanValue); //prepend high priority items
        }
        else
        {
            $this->value[] = $cleanValue;
        }
        $this->isFinal = $isFinal;
    }

    function setValues($value, $isFinal, $priority)
    {
        if (count($this->value) == 1 && $this->priority < $priority)
        {
            $this->priority = $priority;
            $this->value[0] = $this->getCleanValue($value);
            $this->isFinal = $isFinal;
        }
    }

    function toString($skipGraphFields=array())
    {
        $graphValue = '';
        $twitter_card_enabled = JFBCFactory::config()->get('social_graph_twitter_cards_enabled');
        $card_type = JFBCFactory::config()->get('social_graph_twitter_cards_types');

        foreach ($this->value as $tagValue)
        {
            // Basic Twitter Card support
            // ie. twitter:card, twitter:app:country
            if(strpos($this->name, 'twitter') !== false && !in_array($this->name, $skipGraphFields))
            {
                if($twitter_card_enabled)
                {
                    if(in_array($this->name, array('twitter:site', 'twitter:creator')) && strpos($tagValue, '@') === false) //add @ if not found
                        $tagValue = '@'.$tagValue;

                    $graphValue .= '<meta name="'.$this->name.'" content="' . $tagValue . '"/>' . CARRIAGE_RETURN;
                }
                continue;
            }

            // Check and set the base URL for images if a relative path is given
            if ($this->name == 'og:image' || $this->name =='twitter:image')
            {
                if (strpos($tagValue, 'http') !== 0)
                {
                    $tagValue = JUri::root() . ltrim($tagValue, '/');
                    $tagValue = JFBConnectUtilities::forceScheme($tagValue);
                }
            }

            // Basic Twitter Card support
            //if($this->name == 'og:title' && !in_array('twitter:title', $skipGraphFields) && $twitter_card_enabled)
            //    $graphValue .= '<meta name="twitter:title" content="' . $tagValue . '"/>' . CARRIAGE_RETURN;
            //else if($this->name == 'og:description' && !in_array('twitter:description', $skipGraphFields) && $twitter_card_enabled)
            //    $graphValue .= '<meta name="twitter:description" content="' . $tagValue . '"/>' . CARRIAGE_RETURN;
            /*else */if($this->name == 'og:image' && strpos($graphValue, 'og:image')===false && !in_array('twitter:image', $skipGraphFields) && in_array($card_type, array('summary', 'summary_large_image', 'player')) && $twitter_card_enabled)
            $graphValue .= '<meta name="twitter:image" content="' . $tagValue . '"/>' . CARRIAGE_RETURN;

            //Add twitter first, so only first image is added to twitter
            $graphValue .= '<meta property="' . $this->name . '" content="' . $tagValue . '"/>' . CARRIAGE_RETURN;

            if($this->name == 'og:image' && JFBCFactory::config()->get('social_graph_image_size'))
            {
                list($width, $height) = getimagesize($tagValue);
                $graphValue .= '<meta property="og:image:width" content="' . $width . '"/>' . CARRIAGE_RETURN;
                $graphValue .= '<meta property="og:image:height" content="' . $height . '"/>' . CARRIAGE_RETURN;
            }
        }

        return $graphValue;
    }

    private function getCleanValue($value)
    {
        $value = trim($value);
        $value = str_replace('"', "'", $value);
        return $value;
    }
}

class SkippedOpenGraphTag extends OpenGraphTag
{
    function __construct($name, $value, $isFinal, $priority, $origin)
    {
        parent::__construct($name, $value, true, PRIORITY_HIGH, $origin);
        $this->allowsMultiple = false;
    }

    function toString($skipGraphFields=array())
    {
        return '';
    }
}

class OpenGraphUtilities
{
    /**
     * @return bool
     */
    public static function isHomepage()
    {
        $app = JFactory::getApplication();
        $menu = $app->getMenu();
        return ($menu->getActive() == $menu->getDefault());
    }

    public static function getTagName($name)
    {
        $name = strtolower(trim($name));
        if (strpos($name, 'ns:') === 0)
        {
            $appConfig = JFBCFactory::config()->get('autotune_app_config');
            $namespace = $appConfig['namespace'];
            $name = $namespace . ":" . $name;
            $name = str_replace($namespace . ":ns:", $namespace . ":", $name);
        }

        if (strpos($name, ':') === false)
        {
            if ($name == 'admins' || $name == 'app_id')
                $name = 'fb:' . $name;
            else
                $name = 'og:' . $name;
        }
        return $name;
    }

    public static function getCurrentUrlTag()
    {
        //Check the header array before it's rendered.
        //If something has set the rel=canonical tag, use that as the og:url tag instead of generating one on our own.
        $document = JFactory::getDocument();
        if($document->getType() == 'html')
        {
            $headData = $document->getHeadData();
            if(isset($headData['links']))
            {
                $links = $headData['links'];
                foreach ($links as $link => $linkAttribs)
                {
                    if($linkAttribs['relation'] == 'canonical' && $linkAttribs['relType'] == 'rel')
                    {
                        //Canonical can have relative URL. We need absolute URL for og:url tag
                        if(strpos($link, JURI::root())!==0)
                        {
                            $path = JURI::root(true);
                            //If canonical starts with the relative path subfolder, strip it off so we can add the full base path
                            if(substr($link, 0, strlen($path)+1) == $path.'/')
                                $link = substr($link, strlen($path)+1);
                            $link = JURI::root() . $link;
                        }
                        return $link;
                    }
                }
            }
        }

        //If no canonical is found, generate the url
        $url = JFBConnectUtilities::getStrippedUrl();
        return $url;
    }

    public static function getCurrentPageTitleTag()
    {
        $doc = JFactory::getDocument();
        $title = $doc->getTitle();
        return $title;
    }

    public static function getCurrentDescriptionTag()
    {
        $doc = JFactory::getDocument();
        $desc = $doc->getDescription();
        return $desc;
    }

    public static function getCurrentPageTypeTag()
    {
        $isHomePage = OpenGraphUtilities::isHomepage();
        if ($isHomePage)
            $type = "website";
        else
            $type = "article";
        return $type;
    }

    public static function getCurrentAppId()
    {
        if (JFBConnectUtilities::areJFBConnectTagsEnabled())
        {
            $appId = JFBCFactory::provider('facebook')->appId;
            if ($appId != '')
            {
                return $appId;
            }
        }
        return '';
    }

    public static function getCurrentLocaleTag($locale)
    {
        $locale = strtolower($locale);
        return $locale;
    }

    public static function getCurrentSiteNameTag()
    {
        $config = JFactory::getConfig();
        $siteName = $config->get("sitename");
        return $siteName;
    }

    public static function getTwitterName($tagName)
    {
        if(!empty($tagName) && strpos($tagName, '@') === false) //add @ if not found
            $tagName = '@'.$tagName;

        return $tagName;
    }
}
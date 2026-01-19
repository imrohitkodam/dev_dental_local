<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */
if (!(defined('_JEXEC') || defined('ABSPATH')))
{
    die('Restricted access');
};

class JFBCFactory
{
    public static function initializeJoomla()
    {
        require_once JPATH_SITE . '/components/com_jfbconnect/autoloader.php';
        JFBConnectAutoloader::register('Joomla');

        JLoader::register('JFBCOAuth1Client', JPATH_SITE . '/components/com_jfbconnect/libraries/joomla/oauth1/client.php');
        JLoader::register('JFBConnectAuthenticationOauth2', JPATH_SITE . '/components/com_jfbconnect/libraries/authentication/oauth2.php');

        include_once(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/models/usermap.php');

        jimport('joomla.filesystem.folder');
        JPluginHelper::importPlugin('jfbconnect');
    }

    public static function initializeWordpress()
    {
        require_once ABSPATH . 'wp-content/plugins/jfbconnect/jfbconnect/component/frontend/autoloader.php';

        JFBConnectAutoloader::register('Wordpress');
        require_once ABSPATH . 'wp-content/plugins/jfbconnect/jfbconnect/wordpress/shims/registry.php';

//        JLoader::register('JFBCOAuth1Client', JPATH_SITE . '/components/com_jfbconnect/libraries/joomla/oauth1/client.php');
//        JLoader::register('JFBConnectAuthenticationOauth2', JPATH_SITE . '/components/com_jfbconnect/libraries/authentication/oauth2.php');

//        include_once(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/models/usermap.php');

//        jimport('joomla.filesystem.folder');
    }

    public static function provider($name)
    {
        $name = strtolower($name);

        static $providers = array();
        if (!isset($providers[$name]))
        {
            $className = 'JFBConnectProvider' . ucfirst($name);

            if (class_exists($className))
            {
                // Do not clean this up. It's ugly, but needs to be to prevent recursive loading
                $providers[$name] = new $className();
                $providers[$name]->setupAuthentication();
            }
        }
        if (isset($providers[$name]))
            return $providers[$name];
        else
            return null;
    }


    public static function getAllProviders()
    {
        static $allProviders;
        if (!isset($allProviders))
        {
            $allProviders = array();

            $directory = dirname(__FILE__) . '/provider/';
            $files = JFBCFactory::getProviderFilenames($directory);

            //Plugin files
            $app = JFactory::getApplication();
            $pluginProviders = $app->triggerEvent('onGetSocialLoginProviders');
            foreach($pluginProviders as $provider)
            {
                $directory = JPATH_SITE . '/plugins/jfbconnect/provider_'.$provider.'/provider/';
                $pluginProviderFiles = JFBCFactory::getProviderFilenames($directory);
                $files = array_merge($files, $pluginProviderFiles);
            }

            sort($files);
            foreach ($files as $f)
            {
                $p = self::provider($f);
                if ($p)
                    $allProviders[] = $p;
            }
        }
        return $allProviders;
    }

    public static function getProviderFilenames($directory)
    {
        $iterator = new DirectoryIterator ($directory);
        $files = array();

        if(JFolder::exists($directory))
        {
            foreach ($iterator as $info)
            {
                if ($info->isFile() && $info->getExtension() == 'php')
                {
                    $name = str_replace(".php", "", $info->__toString());
                    $files[] = $name;
                }
            }
        }
        return $files;
    }


    public static function library($path)
    {
        static $loaded;
        if (!isset($loaded[$path]))
        {
            $parts = explode('.', $path);
            $path = implode('/', $parts);
            $className = 'JFBConnect' . implode('', array_map('ucfirst', $parts));
            require_once(JPATH_SITE . '/components/com_jfbconnect/libraries/' . $path . '.php');
            $loaded[$path] = new $className();
        }
        return $loaded[$path];
    }

    public static function model($name)
    {
        JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_jfbconnect/models');
        $model = JModelLegacy::getInstance($name, 'JFBConnectModel');
        return $model;
    }

    public static function config()
    {
        static $configModel = null;
        if (!isset($configModel))
        {
            require_once(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/models/config.php');
            $configModel = new JFBConnectModelConfig();
        }
        return $configModel;
    }

    public static function cache()
    {
        static $cache = null;
        if (!isset($cache))
        {
            require_once('cache.php');
            $cache = new JFBConnectCache();
        }
        return $cache;
    }

    public static function easytags()
    {
        static $tags = null;
        if (!isset($tags))
        {
            require_once('easytags.php');
            $tags = new JFBConnectEasytags();
            $tags->initialize();
        }
        return $tags;
    }

    /**
     * Method to log issues to file and display then on frontend
     * @param string $msg - The message it self to be logged or displayed.
     * @param string $type - The joomla message type (message | warning | notice | error)
     * @param boolean $debug
     * @return void
     */
    public static function log($msg = '', $type = 'message', $debug = false)
    {
        static $debugC = null;
        if (!isset($debugC))
        {
            require_once(JPATH_SITE . '/components/com_jfbconnect/libraries/debug.php');
            $debugC = new JFBConnectDebug();
        }

        $debugC->add($msg, $type, $debug);
    }

    // Return an instance of the usermap model, always creating it
    public static function usermap()
    {
        $userMapModel = new JFBConnectModelUserMap();
        return $userMapModel;
    }

    // Special call to grab the provider folders.
    // Can't use getAllProviders as folders contain the widgets, whereas the .php file is used for authentication
    public static function getAllWidgetProviderNames()
    {
        $providers = JFolder::folders(JPATH_SITE . '/components/com_jfbconnect/libraries/provider/');

        $app = JFactory::getApplication();
        $pluginProviders = $app->triggerEvent('onGetSocialLoginProviders');
        $providers = array_merge($providers, $pluginProviders);
        sort($providers);
        return $providers;
    }

    public static function widget($providerName, $name, $params = "")
    {
        $className = 'JFBConnectProvider' . ucfirst($providerName) . 'Widget' . ucfirst($name);
        $provider = self::provider($providerName);
        if (class_exists($className))
            $widget = new $className($provider, $params);
        else
            $widget = null;
        return $widget;
    }

    public static function getAllWidgets($provider)
    {
        static $allWidgets;

        if (!$provider || $provider == 'provider')
            return array();

        if (!isset($allWidgets))
            $allWidgets = array();

        if (!isset($allWidgets[$provider]))
        {
            $widgetFolder = dirname(__FILE__) . '/provider/' . $provider . '/widget/';
            $widgets = JFBCFactory::getWidgets($provider, $widgetFolder);
            $allWidgets[$provider] = $widgets;

            if(empty($widgets))
            {
                $app = JFactory::getApplication();
                $pluginProviders = $app->triggerEvent('onGetSocialLoginProviders');
                foreach($pluginProviders as $providerName)
                {
                    if($provider == $providerName)
                    {
                        $widgetFolder = JPATH_SITE . '/plugins/jfbconnect/provider_'.$providerName.'/provider/'.$providerName.'/widget/';
                        $allWidgets[$providerName] = JFBCFactory::getWidgets($providerName, $widgetFolder);
                    }
                }
            }
        }
        return $allWidgets[$provider];
    }

    public static function getWidgets($provider, $widgetFolder)
    {
        $widgets = array();
        if(JFolder::exists($widgetFolder))
        {
            $iterator = new DirectoryIterator ($widgetFolder);
            foreach ($iterator as $info)
            {
                if ($info->isFile() && $info->getExtension() == 'xml')
                {
                    $widgets[] = self::widget($provider, str_replace(".xml", "", $info->getFilename()));
                }
            }
        }
        return $widgets;
    }

    public static function getAllAutopostExtensions()
    {
        static $allAutopostExt;

        if (!isset($allAutopostExt))
        {
            $allAutopostExt = array();

            $app = JFactory::getApplication();
            JPluginHelper::importPlugin('opengraph');
            $ogPlugins = $app->triggerEvent('openGraphProfilesGetPlugins');

            foreach ($ogPlugins as $plugin)
            {
                if ($plugin->supportedAutopostTypes)
                    $allAutopostExt = array_merge($allAutopostExt, $plugin->supportedComponents);
            }
        }
        return $allAutopostExt;
    }

    public static function getLoginButtons($params = null)
    {
        // Any provider can actually be used here. All roads lead to the same login widget
        return JFBCFactory::widget('facebook', 'login', $params)->render();
    }

    public static function getReconnectButtons($params = null)
    {
        $params['show_reconnect'] = 'true';
        return JFBCFactory::widget('facebook', 'login', $params)->render();
    }

    /***
     * Adds a stylesheet to the list of inclusions that need to be added to the page
     * Managed this way as some are added before the page is rendered, and some after
     * @var path to filename relative to /components/com_jfbconnect/
     */
    static protected $css = array();

    public static function addStylesheet($name)
    {
        if (!in_array($name, self::$css))
            self::$css[] = $name;
    }

    public static function getStylesheets()
    {
        return self::$css;
    }

}
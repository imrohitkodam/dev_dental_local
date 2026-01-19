<?php

/*
 * @package     XT Transitional Package from FrameworkOnFramework
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2024 Extly, CB. All rights reserved.
 *              Based on Akeeba's FrameworkOnFramework
 * @license     https://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

// Protect from unauthorized access
defined('XTF0F_INCLUDED') || exit;

/**
 * FrameworkOnFramework dispatcher class
 *
 * FrameworkOnFramework is a set of classes which extend Joomla! 1.5 and later's
 * MVC framework with features making maintaining complex software much easier,
 * without tedious repetitive copying of the same code over and over again.
 *
 * @since    1.0
 */
class XTF0FDispatcher extends XTF0FUtilsObject
{
    public $component;

    public $view;

    public $layout;

    /**
     * @var string
     */
    public $_originalPhpScript;

    /** @var string The name of the default view, in case none is specified */
    public $defaultView = 'cpanel';

    /** @var array Configuration variables */
    protected $config = [];

    /** @var \Joomla\CMS\Input\Input Input variables */
    protected $input = [];

    // Variables for XTF0F's transparent user authentication. You can override them
    // in your Dispatcher's __construct() method.

    /** @var int The Time Step for the TOTP used in XTF0F's transparent user authentication */
    protected $fofAuth_timeStep = 6;

    /** @var string The key for the TOTP, Base32 encoded (watch out; Base32, NOT Base64!) */
    protected $fofAuth_Key = null;

    /** @var array Which formats to be handled by transparent authentication */
    protected $fofAuth_Formats = ['json', 'csv', 'xml', 'raw'];

    /**
     * Should I logout the transparently authenticated user on logout?
     * Recommended to leave it on in order to avoid crashing the sessions table.
     *
     * @var bool
     */
    protected $fofAuth_LogoutOnReturn = true;

    /** @var array Which methods to use to fetch authentication credentials and in which order */
    protected $fofAuth_AuthMethods = [
        /* HTTP Basic Authentication using encrypted information protected
         * with a TOTP (the username must be "_fof_auth") */
        'HTTPBasicAuth_TOTP',
        /* Encrypted information protected with a TOTP passed in the
         * _fofauthentication query string parameter */
        'QueryString_TOTP',
        /* HTTP Basic Authentication using a username and password pair in plain text */
        'HTTPBasicAuth_Plaintext',
        /* Plaintext, JSON-encoded username and password pair passed in the
         * _fofauthentication query string parameter */
        'QueryString_Plaintext',
        /* Plaintext username and password in the _fofauthentication_username
         * and _fofauthentication_username query string parameters */
        'SplitQueryString_Plaintext',
    ];

    /** @var bool Did we successfully and transparently logged in a user? */
    private $_fofAuth_isLoggedIn = false;

    /** @var string The calculated encryption key for the _TOTP methods, used if we have to encrypt the reply */
    private $_fofAuth_CryptoKey = '';

    /**
     * Public constructor
     *
     * @param array $config The configuration variables
     */
    public function __construct($config = [])
    {
        // Cache the config
        $this->config = $config;

        // Get the input for this MVC triad
        $this->input = array_key_exists('input', $config) ? $config['input'] : self::getJoomlaInput();

        // Get the default values for the component name
        $this->component = $this->input->getCmd('option', 'com_foobar');

        // Load the component's fof.xml configuration file
        $xtf0FConfigProvider = new XTF0FConfigProvider();
        $this->defaultView = $xtf0FConfigProvider->get($this->component.'.dispatcher.default_view', $this->defaultView);

        // Get the default values for the view name
        $this->view = $this->input->getCmd('view', null);

        if (empty($this->view)) {
            // Do we have a task formatted as controller.task?
            $task = $this->input->getCmd('task', '');

            if (!empty($task) && (false !== strstr($task, '.'))) {
                [$this->view, $task] = explode('.', $task, 2);
                $this->input->set('task', $task);
            }
        }

        if (empty($this->view)) {
            $this->view = $this->defaultView;
        }

        $this->layout = $this->input->getCmd('layout', null);

        // Overrides from the config
        if (array_key_exists('option', $config)) {
            $this->component = $config['option'];
        }

        if (array_key_exists('view', $config)) {
            $this->view = empty($config['view']) ? $this->view : $config['view'];
        }

        if (array_key_exists('layout', $config)) {
            $this->layout = $config['layout'];
        }

        $this->input->set('option', $this->component);
        $this->input->set('view', $this->view);
        $this->input->set('layout', $this->layout);

        if (array_key_exists('authTimeStep', $config)) {
            $this->fofAuth_timeStep = empty($config['authTimeStep']) ? 6 : $config['authTimeStep'];
        }
    }

    /**
     * Get a static (Singleton) instance of a particular Dispatcher
     *
     * @param string $option The component name
     * @param string $view   The View name
     * @param array  $config Configuration data
     *
     * @staticvar  array  $instances  Holds the array of Dispatchers XTF0F knows about
     *
     * @return XTF0FDispatcher
     */
    public static function &getAnInstance($option = null, $view = null, $config = [])
    {
        static $instances = [];

        $hash = $option.$view;

        if (!array_key_exists($hash, $instances)) {
            $instances[$hash] = self::getTmpInstance($option, $view, $config);
        }

        return $instances[$hash];
    }

    /**
     * Gets a temporary instance of a Dispatcher
     *
     * @param string $option The component name
     * @param string $view   The View name
     * @param array  $config Configuration data
     *
     * @return XTF0FDispatcher
     */
    public static function &getTmpInstance($option = null, $view = null, $config = [])
    {
        if (array_key_exists('input', $config)) {
            if ($config['input'] instanceof \Joomla\CMS\Input\Input) {
                $input = $config['input'];
            } else {
                if (!is_array($config['input'])) {
                    $config['input'] = (array) $config['input'];
                }

                $config['input'] = array_merge($_REQUEST, $config['input']);
                $input = new \Joomla\CMS\Input\Input($config['input']);
            }
        } else {
            $input = self::getJoomlaInput();
        }

        $config['option'] = $option ?? $input->getCmd('option', 'com_foobar');
        $config['view'] = $view ?? $input->getCmd('view', '');

        $input->set('option', $config['option']);
        $input->set('view', $config['view']);

        $config['input'] = $input;

        $className = ucfirst(str_replace('com_', '', $config['option'])).'Dispatcher';

        if (!class_exists($className)) {
            $componentPaths = XTF0FPlatform::getInstance()->getComponentBaseDirs($config['option']);

            $searchPaths = [
                $componentPaths['main'],
                $componentPaths['main'].'/dispatchers',
                $componentPaths['admin'],
                $componentPaths['admin'].'/dispatchers',
            ];

            if (array_key_exists('searchpath', $config)) {
                array_unshift($searchPaths, $config['searchpath']);
            }

            $filesystem = XTF0FPlatform::getInstance()->getIntegrationObject('filesystem');

            $path = $filesystem->pathFind(
                $searchPaths, 'dispatcher.php'
            );

            if ($path) {
                require_once $path;
            }
        }

        if (!class_exists($className)) {
            $className = 'XTF0FDispatcher';
        }

        $instance = new $className($config);

        return $instance;
    }

    /**
     * The main code of the Dispatcher. It spawns the necessary controller and
     * runs it.
     *
     * @return null|\Exception
     *
     * @throws Exception
     */
    public function dispatch()
    {
        $xtf0FPlatform = XTF0FPlatform::getInstance();

        if (!$xtf0FPlatform->authorizeAdmin($this->input->getCmd('option', 'com_foobar'))) {
            return $xtf0FPlatform->raiseError(403, JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
        }

        $this->transparentAuthentication();

        // Merge English and local translations
        $xtf0FPlatform->loadTranslations($this->component);

        $canDispatch = true;

        if ($xtf0FPlatform->isCli()) {
            $canDispatch = $canDispatch && $this->onBeforeDispatchCLI();
        }

        $canDispatch = $canDispatch && $this->onBeforeDispatch();

        if (!$canDispatch) {
            // We can set header only if we're not in CLI
            if (!$xtf0FPlatform->isCli()) {
                $xtf0FPlatform->setHeader('Status', '403 Forbidden', true);
            }

            return $xtf0FPlatform->raiseError(403, JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
        }

        // Get and execute the controller
        $option = $this->input->getCmd('option', 'com_foobar');
        $view = $this->input->getCmd('view', $this->defaultView);
        $task = $this->input->getCmd('task', null);

        if (empty($task)) {
            $task = $this->getTask($view);
        }

        // Pluralise/sungularise the view name for typical tasks
        if (in_array($task, ['edit', 'add', 'read'])) {
            $view = XTF0FInflector::singularize($view);
        } elseif ($task == 'browse') {
            $view = XTF0FInflector::pluralize($view);
        }

        $this->input->set('view', $view);
        $this->input->set('task', $task);

        $config = $this->config;
        $config['input'] = $this->input;

        $xtf0FController = XTF0FController::getTmpInstance($option, $view, $config);
        $status = $xtf0FController->execute($task);

        if (!$this->onAfterDispatch()) {
            // We can set header only if we're not in CLI
            if (!$xtf0FPlatform->isCli()) {
                $xtf0FPlatform->setHeader('Status', '403 Forbidden', true);
            }

            return $xtf0FPlatform->raiseError(403, JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
        }

        $format = $this->input->get('format', 'html', 'cmd');
        $format = empty($format) ? 'html' : $format;

        if ($xtf0FController->hasRedirect()) {
            $xtf0FController->redirect();
        }

        return null;
    }

    /**
     * Executes right before the dispatcher tries to instantiate and run the
     * controller.
     *
     * @return bool Return false to abort
     */
    public function onBeforeDispatch()
    {
        return true;
    }

    /**
     * Sets up some environment variables, so we can work as usually on CLI, too.
     *
     * @return bool Return false to abort
     */
    public function onBeforeDispatchCLI()
    {
        JLoader::import('joomla.environment.uri');
        JLoader::import('joomla.application.component.helper');

        // Trick to create a valid url used by JURI
        $this->_originalPhpScript = '';

        // We have no Application Helper (there is no Application!), so I have to define these constants manually
        $option = $this->input->get('option', '', 'cmd');

        if ($option) {
            $componentPaths = XTF0FPlatform::getInstance()->getComponentBaseDirs($option);

            if (!defined('JPATH_COMPONENT')) {
                define('JPATH_COMPONENT', $componentPaths['main']);
            }

            if (!defined('JPATH_COMPONENT_SITE')) {
                define('JPATH_COMPONENT_SITE', $componentPaths['site']);
            }

            if (!defined('JPATH_COMPONENT_ADMINISTRATOR')) {
                define('JPATH_COMPONENT_ADMINISTRATOR', $componentPaths['admin']);
            }
        }

        return true;
    }

    /**
     * Executes right after the dispatcher runs the controller.
     *
     * @return bool Return false to abort
     */
    public function onAfterDispatch()
    {
        // If we have to log out the user, please do so now
        if ($this->fofAuth_LogoutOnReturn && $this->_fofAuth_isLoggedIn) {
            XTF0FPlatform::getInstance()->logoutUser();
        }

        return true;
    }

    /**
     * Transparently authenticates a user
     *
     * @return void
     */
    public function transparentAuthentication()
    {
        // Only run when there is no logged in user
        if (!XTF0FPlatform::getInstance()->getUser()->guest) {
            return;
        }

        // @todo Check the format
        $format = $this->input->getCmd('format', 'html');

        if (!in_array($format, $this->fofAuth_Formats)) {
            return;
        }

        foreach ($this->fofAuth_AuthMethods as $fofAuth_AuthMethod) {
            // If we're already logged in, don't bother
            if ($this->_fofAuth_isLoggedIn) {
                continue;
            }

            // This will hold our authentication data array (username, password)
            $authInfo = $this->processMethod($fofAuth_AuthMethod);

            // No point trying unless we have a username and password
            if (!is_array($authInfo)) {
                continue;
            }

            $this->_fofAuth_isLoggedIn = XTF0FPlatform::getInstance()->loginUser($authInfo);
        }
    }

    /**
     * Main function to detect if we're running in a CLI environment and we're admin
     *
     * @return array isCLI and isAdmin. It's not an associtive array, so we can use list.
     */
    public static function isCliAdmin()
    {
        static $isCLI = null;
        static $isAdmin = null;

        if (null === $isCLI && null === $isAdmin) {
            $isCLI = XTF0FPlatform::getInstance()->isCli();
            $isAdmin = XTF0FPlatform::getInstance()->isBackend();
        }

        return [$isCLI, $isAdmin];
    }

    /**
     * Tries to guess the controller task to execute based on the view name and
     * the HTTP request method.
     *
     * @param string $view The name of the view
     *
     * @return string The best guess of the task to execute
     */
    protected function getTask($view)
    {
        // Get a default task based on plural/singular view
        $request_task = $this->input->getCmd('task', null);
        $task = XTF0FInflector::isPlural($view) ? 'browse' : 'edit';

        // Get a potential ID, we might need it later
        $id = $this->input->get('id', null, 'int');

        if (0 == $id) {
            $ids = $this->input->get('ids', [], 'array');

            if (!empty($ids)) {
                $id = array_shift($ids);
            }
        }

        // Check the request method

        if (!isset($_SERVER['REQUEST_METHOD'])) {
            $_SERVER['REQUEST_METHOD'] = 'GET';
        }

        $requestMethod = strtoupper($_SERVER['REQUEST_METHOD']);

        switch ($requestMethod) {
            case 'POST':
            case 'PUT':
                if (null !== $id) {
                    $task = 'save';
                }

                break;

            case 'DELETE':
                if (0 != $id) {
                    $task = 'delete';
                }

                break;

            case 'GET':
            default:
                // If it's an edit without an ID or ID=0, it's really an add
                if (('edit' === $task) && (0 == $id)) {
                    $task = 'add';
                }

                // If it's an edit in the frontend, it's really a read
                elseif (('edit' === $task) && XTF0FPlatform::getInstance()->isFrontend()) {
                    $task = 'read';
                }

                break;
        }

        return $task;
    }

    private function processMethod($method)
    {
        $authInfo = null;

        switch ($method) {
            case 'HTTPBasicAuth_TOTP':

                if (empty($this->fofAuth_Key)) {
                    break;
                }

                if (!isset($_SERVER['PHP_AUTH_USER'])) {
                    break;
                }

                if (!isset($_SERVER['PHP_AUTH_PW'])) {
                    break;
                }

                if ('_fof_auth' != $_SERVER['PHP_AUTH_USER']) {
                    break;
                }

                $encryptedData = $_SERVER['PHP_AUTH_PW'];

                $authInfo = $this->_decryptWithTOTP($encryptedData);
                break;

            case 'QueryString_TOTP':
                $encryptedData = $this->input->get('_fofauthentication', '', 'raw');

                if (empty($encryptedData)) {
                    break;
                }

                $authInfo = $this->_decryptWithTOTP($encryptedData);
                break;

            case 'HTTPBasicAuth_Plaintext':
                if (!isset($_SERVER['PHP_AUTH_USER'])) {
                    break;
                }

                if (!isset($_SERVER['PHP_AUTH_PW'])) {
                    break;
                }

                $authInfo = [
                    'username'	 => $_SERVER['PHP_AUTH_USER'],
                    'password'	 => $_SERVER['PHP_AUTH_PW'],
                ];
                break;

            case 'QueryString_Plaintext':
                $jsonencoded = $this->input->get('_fofauthentication', '', 'raw');

                if (empty($jsonencoded)) {
                    break;
                }

                $authInfo = json_decode($jsonencoded, true);

                if (!is_array($authInfo)) {
                    $authInfo = null;
                } elseif (!array_key_exists('username', $authInfo) || !array_key_exists('password', $authInfo)) {
                    $authInfo = null;
                }

                break;

            case 'SplitQueryString_Plaintext':
                $authInfo = [
                    'username'	 => $this->input->get('_fofauthentication_username', '', 'raw'),
                    'password'	 => $this->input->get('_fofauthentication_password', '', 'raw'),
                ];

                if (empty($authInfo['username'])) {
                    $authInfo = null;
                }

                break;

            default:
                break;
        }

        return $authInfo;
    }

    /**
     * Decrypts a transparent authentication message using a TOTP
     *
     * @param string $encryptedData The encrypted data
     *
     * @codeCoverageIgnore
     *
     * @return array The decrypted data
     */
    private function _decryptWithTOTP($encryptedData)
    {
        if (empty($this->fofAuth_Key)) {
            $this->_fofAuth_CryptoKey = null;

            return null;
        }

        $xtf0FEncryptTotp = new XTF0FEncryptTotp($this->fofAuth_timeStep);
        $period = $xtf0FEncryptTotp->getPeriod();
        $period--;

        for ($i = 0; $i <= 2; $i++) {
            $time = ($period + $i) * $this->fofAuth_timeStep;
            $otp = $xtf0FEncryptTotp->getCode($this->fofAuth_Key, $time);
            $this->_fofAuth_CryptoKey = hash('sha256', $this->fofAuth_Key.$otp);

            $aes = new XTF0FEncryptAes($this->_fofAuth_CryptoKey);
            $ret = $aes->decryptString($encryptedData);
            $ret = rtrim($ret, "\000");

            $ret = json_decode($ret, true);

            if (!is_array($ret)) {
                continue;
            }

            if (!array_key_exists('username', $ret)) {
                continue;
            }

            if (!array_key_exists('password', $ret)) {
                continue;
            }

            // Successful decryption!
            return $ret;
        }

        // Obviously if we're here we could not decrypt anything. Bail out.
        $this->_fofAuth_CryptoKey = null;

        return null;
    }

    /**
     * Creates a decryption key for use with the TOTP decryption method
     *
     * @param int $time The timestamp used for TOTP calculation, leave empty to use current timestamp
     *
     * @codeCoverageIgnore
     *
     * @return string THe encryption key
     */
    private function _createDecryptionKey($time = null)
    {
        $xtf0FEncryptTotp = new XTF0FEncryptTotp($this->fofAuth_timeStep);
        $otp = $xtf0FEncryptTotp->getCode($this->fofAuth_Key, $time);

        $key = hash('sha256', $this->fofAuth_Key.$otp);

        return $key;
    }

    private static function getJoomlaInput()
    {
        if (version_compare(JVERSION, '4', '<')) {
            // Joomla 3 code
            jimport('joomla.filter.input');

            $input = JFactory::getApplication()->input;
            $data = $input->serialize();
            $jinput = new \Joomla\CMS\Input\Input([]);
            $jinput->unserialize($data);

            return $jinput;
        }

        $input = Joomla\CMS\Factory::getApplication()->input;
        $data = $input->getArray();
        $jinput = new \Joomla\CMS\Input\Input($data);

        return $jinput;
    }
}

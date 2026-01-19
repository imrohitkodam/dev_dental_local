<?
/**
 * Siteground Joomla Cache Plugin
 *
 * Flushes varnish cache when certain data change actions are invoked. Disables varnish cache
 * on SiteGround servers when users are logged in.
 *
 * @author	   George Penkov
 * @category   Siteground Joomla Plugins
 * @package    Siteground Joomla Cache Plugin
 */

defined('_JEXEC') or die();
JLoader::import('joomla.plugin.plugin');
JLoader::import('joomla.error.log');

/**
 * jSGCache Plugin
 *
 * @package	jSGCache
 * @subpackage	System
*/


class plgSystemjSGCache extends JPlugin
{
	/**
	 * JRegistry parameter object for the plugin.
	 * @var JRegistry
	 */
	public $params = null;
	/**
	 * Web path to the joomla application.
	 * @var string
	 */
	private $_applicationPath = null;
	/**
	 * Global cache flag.
	 * @var bool
	 */
	private $_cacheEnabled = false;
	/**
	 * Auto flush flag
	 * @var bool
	 */
	private $_autoflush = false;
	/**
	 * 3rd party auto flush flag
	 * @var bool
	 */
	private $_autoflush3rdParty = false;
	/**
	 * Client side auto flush flag
	 * @var bool
	 */
	private $_autoflushClientSide = false;
	/**
	 * Which tasks to ignore in the flush monitor
	 * @var array
	 */
	private static $_ignoreTasks = array('login','logout','user.login','user.logout');

	/**
	 * Constructor
	 *
     * @access public
	 * @param   object  &$subject  The object to observe
	 */
	public function __construct( &$subject )
	{
		if(isset($_GET['sgCacheCheck']) && $_GET['sgCacheCheck'] == md5('joomlaCheck'))
			die('OK');
		parent::__construct( $subject );
		$plugin = JPluginHelper::getPlugin('system','jSGCache');
		$this->params= new JRegistry();
		$this->params->loadString($plugin->params, 'JSON');

		$this->_cacheEnabled = $this->params->get('cache_enabled');
		if ($this->_cacheEnabled === null)
			$this->_cacheEnabled == 1;

		$this->_autoflush = $this->params->get('autoFlush');
		if ($this->_autoflush === null)
			$this->_autoflush = 1;
		$this->_autoflush3rdParty = $this->params->get('autoFlush-ThirdParty');
		if ($this->_autoflush3rdParty === null)
			$this->_autoflush3rdParty = 1;
		$this->_autoflushClientSide = $this->params->get('autoFlush-ClientSide');
		if ($this->_autoflushClientSide === null)
			$this->_autoflushClientSide = 0;
	}

    /**
     * Heartbeat cache checking function. Will also monitor $_GET for the jSGCache parameter
     * (pressing the purge cache button in admin)
     *
     *
     * @access public
     * @return null
     */
	public function onAfterInitialise()
	{
		if (!$this->_cacheEnabled || $this->_isBlacklisted($this->_applicationPath))
		{
			JResponse::setHeader('X-Cache-Enabled','False',true);
			return;
		}

		if ($this->_cacheEnabled)
		{
			JResponse::allowCache(true);
			JResponse::setHeader('X-Cache-Enabled','True',true);
		}

		//Init the application url
		$this->_applicationPath = str_replace(array('administrator/index.php','index.php'),'',str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']));

		//Check for any admin action and proceed to flushMonitor and 3rd party plugins
		if ( isset($_POST['task']) || isset($_GET['task']) || isset($_GET['cart_virtuemart_product_id']))
		{
			$this->_flushMonitor();
			if ($this->_autoflush3rdParty)
				$this->_monitorThirdPartyPlugins();
		}

 		//Check if we have a logged in user and enable cache bypass cookie 'task' => string 'user.login'
 		$user = JFactory::getUser();
 		if (!$user->guest  || (isset($_POST['task']) && preg_match('/login/i', $_POST['task'])))
 		{
 			$_POST[JSession::getFormToken()] = 1; //Force the correct token, since the login box on the page is cached with the 1st visitors' token
 			//Enable the cache bypass for logged users by setting a cache bypass cookie
 			setcookie('jSGCacheBypass',1,time() + 6000,'/');
 		}

 		if ($user->guest || (isset($_POST['task']) && $_POST['task'] == 'user.logout'))
 		{
 			//Remove the bypass cookie if not a logged user
 			if (isset($_COOKIE['jSGCacheBypass']))
 				setcookie('jSGCacheBypass',0, time() - 3600,'/');
 		}

		// Handle purge button press when get has jSGCache=purge, but only in admin with a logged user
		if(isset($_GET['jSGCache']) && $_GET['jSGCache'] == 'purge' && JFactory::getApplication()->isAdmin() && !$user->guest )
			$this->_purgeCache(true);
	}

    /**
     * Admin panel icon display
     *
     * @access public
     * @param string $context
     * @return array
     */
	public function onGetIcons( $context )
	{
		return array(array(
			'link'=>'?jSGCache=purge',
			'image'=>'refresh',
			'text'=>JText::_('Purge jSGCache'),
			'id'=>'jSGCache'
		));
	}

    /**
     * Calls the cache server to purge the cache
     *
     * @access public
     * @param string|bool $message Message to be displayed if purge is successful. If this param is false no output would be done
     * @return null
     */
	private function _purgeCache( $message = true )
	{
    	$purgeRequest = $this->_applicationPath . '(.*)';

		// Construct the PURGE request
		$hostname = str_replace( 'www.', '', $_SERVER['HTTP_HOST'] );
		$purge_method = "PURGE";
		
		$cacheServerSocket = fsockopen($hostname, 80, $errno, $errstr, 2);

		if(!$cacheServerSocket)
		{
      		JError::raise(E_ERROR,500,JText::_('Connection to cache server failed!'));
      		JError::raise(E_ERROR,500,JText::_($errstr ($errno)));
      		return;
     	}

		$request = "$purge_method {$purgeRequest} HTTP/1.0\r\nHost: {$_SERVER['SERVER_NAME']}\r\nConnection: Close\r\n\r\n";

		if (preg_match('/^www\./',$_SERVER['SERVER_NAME']))
		{ 
			$domain_no_www = preg_replace('/^www\./', '', $_SERVER['SERVER_NAME']);
			$request2 = "$purge_method {$purgeRequest} HTTP/1.0\r\nHost: {$domain_no_www}\r\nConnection: Close\r\n\r\n";
		} 
		else
			$request2 = "$purge_method {$purgeRequest} HTTP/1.0\r\nHost: www.{$_SERVER['SERVER_NAME']}\r\nConnection: Close\r\n\r\n";

      	fwrite($cacheServerSocket, $request);
      	$response = fgets($cacheServerSocket);
      	fclose($cacheServerSocket);

		$cacheServerSocket = fsockopen($hostname, 80, $errno, $errstr, 2);
		fwrite($cacheServerSocket, $request2);
		fclose($cacheServerSocket);

		if($message !== false)
		{
  			if(preg_match('/200/',$response))
  			{
  				if ($message === true)
    					JFactory::getApplication()->enqueueMessage(JText::_('SG Cache Successfully Purged!'));
  				else
  					JFactory::getApplication()->enqueueMessage(JText::_( $message ));
  			}
 			else
 			{
    				JError::raise(E_NOTICE,501, JText::_('SG Cache: Purge was not successful!'));
    				JError::raise(E_NOTICE,501, jText::_('Error: ' . $response));
			}
		}
	}

	/**
	 * Check if url is in caching blacklist
	 *
	 * @param string $applicationPath
	 *
	 * @return bool
	 */
	private function _isBlacklisted($applicationPath)
	{
		$blacklistArray = explode("\n",$this->params->get('blacklist'));

		$blacklistRegexArray = array();
		$indexIsBlacklisted = false;
		foreach($blacklistArray as $key=>$row)
		{
			$row = trim($row);

			if ($row != '/' && $quoted = preg_quote($row,'/'))
				$blacklistRegexArray[$key] = $quoted;

			if ($row == '/')
				$indexIsBlacklisted = true;
		}

		if ($indexIsBlacklisted && $_SERVER['REQUEST_URI'] == $applicationPath)
			return true;

		if (empty($blacklistRegexArray))
			return false;

		$blacklistRegex = '/('.implode('|',$blacklistRegexArray) . ')/i';

		return preg_match($blacklistRegex, $_SERVER['REQUEST_URI']);
	}

	/**
	 * 3rd party plugin monitor
	 *
	 * @access private
	 * @return null
	 */
	private function _monitorThirdPartyPlugins()
	{
		// Kunena & K2
		if ($this->params->get('autoFlush-ThirdParty') == 1 && isset($_POST['option']) &&
			($_POST['option']=='com_k2' || $_POST['option' ]== 'com_kunena'))
		{
			$this->_purgeCache(false);
		}


		// VirtueMart
		if ( (isset($_POST['option']) && $_POST['option'] == 'com_virtuemart') || 
			( isset($_GET['option']) && $_GET['option'] == 'com_virtuemart' ) ||  
			isset($_GET['cart_virtuemart_product_id']) )
		{
			if($this->params->get('autoFlush-ThirdParty') == 1)
				$this->_purgeCache(false);
		}	
	}

	/**
	 * Action monitor
	 *
	 * @access private
	 * @return null
	 */
	private function _flushMonitor()
	{
		$user = JFactory::getUser();
    	if ((!JFactory::getApplication()->isAdmin() && !$this->_autoflushClientSide) || $user->guest)
    		return;

    	$autoflush = $this->params->get('autoFlush');
    	if ($autoflush === null)
    		$autoflush = 1;

    	if (isset($_POST['task']) && $_POST['task'] && !in_array($_POST['task'],self::$_ignoreTasks) && $autoflush == 1)
    		$this->_purgeCache(false);
	}
}

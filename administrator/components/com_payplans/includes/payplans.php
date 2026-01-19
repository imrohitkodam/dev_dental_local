<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/dependencies.php');
require_once(__DIR__ . '/api.php');
require_once(__DIR__ . '/compatibility.php');

use Foundry\Libraries\Pagination;
use Foundry\Libraries\Scripts;

class PP
{
	/**
	 * Accessing foundry library should be done here
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function fd()
	{
		static $fd = null;

		if (is_null($fd)) {
			PP::initFoundry();

			$fd = new FoundryLibrary('com_payplans', 'pp', 'PayPlans', '');
		}

		return $fd;
	}

	/**
	 * Check if foundry plugin enabled or not.
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function isFoundryEnabled()
	{
		static $isEnabled = null;

		if (is_null($isEnabled)) {
			$isEnabled = true;

			if (!JFile::exists(JPATH_LIBRARIES . '/foundry/foundry.php')) {
				$isEnabled = false;
			}

			if (!JPluginHelper::isEnabled('system', 'foundry')) {
				$isEnabled = false;
			}
		}

		return $isEnabled;
	}

	/**
	 * Method to display Joomla's core alert
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function raiseWarning($errCode, $msg)
	{
		if (class_exists('JError')) {
			return JError::raiseWarning($errCode, JText::_($msg));
		}

		return JFactory::getApplication()->enqueueMessage(JText::_($msg), 'error');
	}

	/**
	 * Magic method to load static objects
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function __callStatic($name, $arguments)
	{
		static $staticLibraries = [];

		// Load the library first
		PP::load($name);

		$className = 'PP' . ucfirst($name);

		if (method_exists($className, 'factory')) {
			$object = call_user_func_array(array($className, 'factory'), $arguments);

			return $object;
		}

		// For classes with $static variables, we assume that it should only be rendered once
		if (isset($className::$static) && $className::$static) {

			if (!isset($staticLibraries[$className])) {
				$staticLibraries[$className] = new $className();
			}

			return $staticLibraries[$className];
		}

		$staticLibraries[$className] = new $className();

		return $staticLibraries[$className];
	}

	/**
	 * Ajax library needs to be a single instance
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function ajax()
	{
		static $ajax = null;

		if (is_null($ajax)) {
			PP::load('ajax');

			$ajax = new PPAjax();
		}

		return $ajax;
	}

	/**
	 * Creates an instance of the database library
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function db()
	{
		PP::load('DB');

		$db = PPDb::getInstance();

		return $db;
	}

	/**
	 *
	 * @since	4.0.4
	 * @access	public
	 */
	public static function user($ids = null, $resetCache = false, $debug = false)
	{
		// Load the user library
		self::load('User');

		return PPUser::factory($ids, $resetCache, $debug);
	}

	/**
	 * Generic method to log data into a logging file (for debugging purposes only)
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function debug($data, $file)
	{
		ob_start();
		print_r($data);
		$contents = ob_get_contents();
		ob_end_clean();

		return JFile::write($file, $contents);
	}

	/**
	 * Renders the encryptor library
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function encryptor($reset = false)
	{
		static $instance = null;

		if ($instance !== null && $reset === false) {
			return $instance;
		}

		PP::load('encryptor');

		$config = PP::config();
		$key = PPJString::strtoupper($config->get('expert_encryption_key'));

		$instance = new PPEncryptor($key);

		return $instance;
	}

	/**
	 * Renders the event library
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function event($debug = false)
	{
		static $event = null;

		if (is_null($event)) {
			PP::load('event');

			$event = new PPEvent();
		}

		return $event;
	}

	/**
	 * Simple implementation to extract keywords from a string
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public static function extractKeyWords($string)
	{
		mb_internal_encoding('UTF-8');

		$stopwords = [];
		$string = preg_replace('/[\pP]/u', '', trim(preg_replace('/\s\s+/iu', '', mb_strtolower($string))));
		$matchWords = array_filter(explode(' ',$string), function ($item) use ($stopwords) { return !($item == '' || in_array($item, $stopwords) || mb_strlen($item) <= 2 || is_numeric($item));});
		$wordCountArr = array_count_values($matchWords);

		arsort($wordCountArr);
		return array_keys(array_slice($wordCountArr, 0, 10));
	}

	/**
	 * Includes a file given a particular namespace in POSIX format.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function import($namespace)
	{
		static $locations = [];

		if (!isset($locations[$namespace])) {
			$parts = explode(':', $namespace);

			// Non POSIX standard.
			if (count($parts) <= 1) {
				return false;
			}

			$base = $parts[0];

			// Default path
			$path = PP_SITE;

			if ($base == 'admin') {
				$path = PP_ADMIN;
			}

			// Replace / with proper directory structure.
			$path = $path . str_ireplace('/', DIRECTORY_SEPARATOR, $parts[1]) . '.php';

			include_once($path);

			$locations[$namespace] = true;
		}

		return true;
	}

	/**
	 * Initializes Foundry
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function initFoundry()
	{
		require_once(JPATH_LIBRARIES . '/foundry/foundry.php');
	}

	/**
	 * Initialize the scripts and stylesheets on the site
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function initialize($location = 'site')
	{
		// Determines if we should compile the javascripts on the site
		$config = PP::config();

		if (PP::isSiteAdmin()) {
			$app = JFactory::getApplication();
			$input = $app->input;
			$compile = $input->get('compile', false, 'bool');

			if ($compile) {

				// Determines if we need to minify the js
				$minify = $input->get('minify', false, 'bool');

				// Get section if not default one
				$section = $input->get('section', $location, 'cmd');

				// Get the compiler
				$compiler = PP::compiler();
				$result = [];

				// Compile with jquery.easyblog.js
				$result['standard'] = $compiler->compile($section, $minify);

				// Compile with jquery.js
				$result['basic'] = $compiler->compile($section, $minify, false);

				if ($result !== false) {
					header('Content-type: text/x-json; UTF-8');
					echo json_encode($result);
					exit;
				}
			}
		}

		static $loaded = [];

		if (!isset($loaded[$location])) {

			$app = JFactory::getApplication();
			$location = self::isFromAdmin() ? 'admin' : 'site';

			// @TODO: Replace this in the future
			$theme = 'wireframe';

			if ($location == 'admin') {
				$theme = 'default';
			}

			// Attach foundry scripts
			Scripts::init();

			// Attach the scripts
			$scripts = PP::scripts();
			$scripts->attach($location);

			// Attach css files

			// Only load the site stylesheet if needed to
			if ($location == 'site' && !$config->get('render_site_css')) {
				$loaded[$location] = true;

				return true;
			}

			$stylesheet = PP::stylesheet($location, $theme);
			$stylesheet->attach();

			$loaded[$location] = true;
		}

		return $loaded[$location];
	}

	/**
	 * Creates an instance of the info library from Foundry
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function info()
	{
		return PP::fd()->info();
	}
	

	/**
	 * Method to retrieve the routed url
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public static function getFrontendUrl($url)
	{
		static $router;

		// Only get the router once.
		if (!($router instanceof JRouter)) {
			// Get and configure the site router.
			$config = JFactory::getConfig();
			$router = JRouter::getInstance('site');
			$router->setMode($config->get('sef', 1));
		}

		// Build the relative route.
		$uri   = $router->build($url);
		$route = $uri->toString(['path', 'query', 'fragment']);
		$route = str_replace(JUri::base(true) . '/', '', $route);

		return $route;
	}

	/**
	 * Determines if a given string is a namespace on the filesystem
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function isNamespace($str)
	{
		// Explode the namespace
		$parts = explode(':', $str);

		if (count($parts) <= 1) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if this is from the Joomla backend
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function isFromAdmin()
	{
		$isFromAdmin = null;

		if (is_null($isFromAdmin)) {
			$isFromAdmin = PPCompat::isFromAdmin();
		}

		return $isFromAdmin;
	}

	/**
	 * Determines if the user is a super admin on the site.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function isSiteAdmin($id = null)
	{
		static $items = [];

		$user = JFactory::getUser($id);

		if (!isset($items[$user->id])) {
			$items[$user->id] = $user->authorise('core.admin');
		}

		return $items[$user->id] ? true : false;
	}

	/**
	 * Retrieves the current Joomla template being used
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function getJoomlaTemplate($client = 'site')
	{
		static $template = [];

		if (!array_key_exists($client, $template)) {

			$app = JFactory::getApplication();

			// Try to load the template from joomla cache since some 3rd party plugins can change the templates on the fly. #449
			if ($client == 'site' && !self::isFromAdmin()) {
				$template[$client] = $app->getTemplate();
			} else {

				$clientId = ($client == 'site') ? 0 : 1;

				$db = PP::db();

				$query	= 'SELECT template FROM `#__template_styles` AS s'
						. ' LEFT JOIN `#__extensions` AS e ON e.type = `template` AND e.element=s.template AND e.client_id=s.client_id'
						. ' WHERE s.client_id = ' . $db->quote($clientId) . ' AND home = 1';

				$db->setQuery( $query );

				$result = $db->loadResult();

				// Fallback template
				if (!$result) {
					$result = ($client == 'site') ? 'beez_20' : 'bluestork';
				}

				$template[$client] = $result;
			}
		}

		return $template[$client];
	}

	/**
	 * Retrieves the login link
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function getLoginLink($route = true, $xhtml = false)
	{
		$currentUrl = JURI::getInstance()->toString();
		$return = base64_encode($currentUrl);

		$link = 'index.php?option=com_users&task=login&' . PP::token() . '=1&return=' . $return;

		if ($route) {
			return JRoute::_($link, $xhtml);
		}

		return $link;
	}

	/**
	 * Retrieves the object's context.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function getObjectContext($object)
	{
		return PPJString::strtolower($object->getPrefix().'_'.$object->getName());
	}

	/**
	 * Retrieve's country id from PayPlans given the country iso code
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public static function getCountryIdByIso($isoCode)
	{
		if (!$isoCode) {
			return 0;
		}

		$table = PP::table('Country');
		$table->load([
			'isocode2' => $isoCode
		]);

		if ($table->country_id) {
			return $table->country_id;
		}

		return 0;
	}

	/**
	 * Retrieve's country name from PayPlans given the country id
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public static function getCountryNameById($countryId)
	{
		if (!$countryId) {
			return 0;
		}

		$table = PP::table('Country');
		$table->load([
			'country_id' => $countryId
		]);

		if ($table->title) {
			return $table->title;
		}

		return 0;
	}

	/**
	 * Retrieve's country id by verifying the country title
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function getCountryIdByTitle($countryTitle)
	{
		if (!$countryTitle) {
			return 0;
		}

		$table = PP::table('Country');
		$table->load([
			'title' => $countryTitle
		]);

		if ($table->country_id) {
			return $table->country_id;
		}

		return 0;
	}

	/**
	 * Retrieves the formatter object
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function getFormatter($class, $logClass)
	{
		$mappings = [
			'PayplansFormatterLibApp' => 'PayplansAppFormatter',
			'PayplansFormatterLibConfig' => 'PayplansConfigFormatter',
			'PayplansFormatterLibGroup' => 'PayplansGroupFormatter',
			'PayplansFormatterLibInvoice' => 'PayplansInvoiceFormatter',
			'PayplansFormatterLibOrder' => 'PayplansOrderFormatter',
			'PayplansFormatterLibPayment' => 'PayplansPaymentFormatter',
			'PayplansFormatterLibPlan' => 'PayplansPlanFormatter',
			'PayplansFormatterLibSubscription'	=> 'PayplansSubscriptionFormatter',
			'PayplansFormatterLibTransaction' 	=> 'PayplansTransactionFormatter',
			'PayplansFormatterLibUser' => 'PayplansUserFormatter',
			'PayplansFormatterEmail' => 'PayplansFormatter'
		];

		if (isset($mappings[$class])) {
			$class = $mappings[$class];
		}

		// For cron logs and email logs as they use PayplansFormatter class
		if ($class == 'PayplansFormatter' || $class == 'XiFormatter') {
			return new PayplansFormatter();
		}

		// Find lib class
		$libClass = str_replace('Formatter', '', $class);

		// if log-class extends PayplansAppFormatter
		if ($libClass == 'PayplansApp') {

			$customAppFormatter = $logClass . 'Formatter';

			if (class_exists($customAppFormatter, true) && is_subclass_of($customAppFormatter, 'PayplansAppFormatter')) {
				$class = $customAppFormatter;
				return new $class();
			}

			return new PayplansAppFormatter();
		}

		// If an app renders it's own formatter, the class should already exist by now
		if (class_exists($class,true)) {
			return new $class();
		}

		// Try to get the formatter for this class
		$logFormatter = PP::logFormatter();
		$formatter = $logFormatter->getFormatter($libClass);

		if ($formatter !== false) {
			return new $class();
		}

		// If all else fails, just use the default formatter
		return new PayplansFormatter();
	}

	/**
	 * Get statuses available on the site given the entity of the item.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function getStatuses($entity)
	{
		static $statuses = null;

		// Instead of using reflection class to get constants, we define them here once.
		if (is_null($statuses)) {

			$statuses['subscription'] = [
				'none' => PP_NONE,
				'active' => PP_SUBSCRIPTION_ACTIVE,
				'hold' => PP_SUBSCRIPTION_HOLD,
				'expired' => PP_SUBSCRIPTION_EXPIRED
			];

			$statuses['invoice'] = [
				'none' => PP_NONE,
				'confirmed' => PP_INVOICE_CONFIRMED,
				'paid' => PP_INVOICE_PAID,
				'refunded' => PP_INVOICE_REFUNDED
			];

			$statuses['order'] = [
				'none' => PP_NONE,
				'confirmed' => PP_ORDER_CONFIRMED,
				'paid' => PP_ORDER_PAID,
				'refunded' => PP_ORDER_HOLD,
				'expired' => PP_ORDER_EXPIRED
			];
		}

		$entity = strtolower($entity);

		return $statuses[$entity];
	}

	/**
	 * Renders Joomla's Global Configuration library
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function jconfig()
	{
		static $config = false;

		if (!$config) {
			$config = JFactory::getConfig();
		}

		return $config;
	}

	/**
	 * Creates a new modifier
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function createModifier(PPInvoice $invoice, $amount, $percentage = false, $type = '', $message = '', $frequency = PP_MODIFIER_FREQUENCY_ONE_TIME, $serial = PP_MODIFIER_PERCENT_DISCOUNT)
	{
		$modifier = PP::modifier();
		$modifier->amount = $amount;
		$modifier->percentage = $percentage;
		$modifier->invoice_id = $invoice->getId();
		$modifier->user_id = $invoice->getBuyer(true)->getId();
		$modifier->type = $type;
		$modifier->frequency = $frequency;
		$modifier->serial = $serial;
		$modifier->message = $message;

		return $modifier;
	}

	/**
	 * Creates a new transaction
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function createTransaction($invoice = null, $payment = null, $transactionId = 0, $subscriptionId = 0, $parentId = 0, $params = null)
	{
		$transaction = PP::transaction();

		if ($payment && ($payment instanceof PPPayment)) {
			$transaction->user_id = $payment->getBuyer();
			$transaction->payment_id = $payment->getId();
		}

		if ($invoice && ($invoice instanceof PPInvoice)) {
			$transaction->invoice_id = $invoice->getId();
		}

		if ($params) {
			$params = new JRegistry($params);

			$transaction->params = $params->toString();
		}

		$transaction->gateway_txn_id = $transactionId;
		$transaction->gateway_subscr_id = $subscriptionId;
		$transaction->gateway_parent_txn = $parentId;

		return $transaction;
	}

	/**
	 * Renders Payplans Configuration
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function config($reload = false)
	{
		static $config = false;

		if ($config && !$reload) {
			return $config;
		}

		$model = PP::model('Config');
		$configData = $model->getConfig();

		// Merge the configurations
		$defaultConfigPath = PP_DEFAULTS . '/config.json';
		$defaultConfigContents = file_get_contents($defaultConfigPath);
		$config = new JRegistry($defaultConfigContents);

		$siteConfig = new JRegistry($configData);

		// Merge the stored configuration with the default configuration
		$config->merge($siteConfig);

		// Merge joomla's configuration
		$jConfig = JFactory::getConfig();
		$config->merge($jConfig);

		// Let plugin modify config
		$args = [&$config];

		// PP::event()->trigger('onPayplansConfigLoad', $args);

		return $config;
	}

	/**
	 * If the current user is a super admin, allow them to change the environment via the query string
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function checkEnvironment()
	{
		if (!PP::isSiteAdmin()) {
			return;
		}

		$app = JFactory::getApplication();
		$environment = $app->input->get('pp_env', '', 'word');
		
		$allowed = [
			'production', 
			'development'
		];

		// Nothing has changed
		if (!$environment || !in_array($environment, $allowed)) {
			return;
		}

		// We also need to update the database value
		$config = PP::table('Config');
		$config->load([
			'key' => 'environment'
		]);

		$config->key = 'environment';
		$config->value = $environment;
		$config->store();

		PP::info()->set('Updated system environment to <b>' . $environment . '</b> mode', 'success');
		return $app->redirect('index.php?option=com_payplans');
	}

	/**
	 * Get the user id stored in the session for proxy purchases
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function getUserIdFromSession()
	{
		$session = PP::session();
		$id = (int) $session->get('REGISTRATION_NEW_USER_ID');

		return $id;
	}

	/**
	 * Creates a new dummy user on the site if it doesn't exist yet.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	static public function getDummyUserId()
	{
		static $userId = null;

		if (is_null($userId)) {

			$model = PP::model('User');
			$dummy = $model->getDummyUser();

			// If it doesn't exist, create the dummy user
			if (!$dummy) {
				$dummy = $model->createDummyUser();
			}

			$userId = (int) $dummy->id;
		}

		return $userId;
	}

	/**
	 * Remove out comments from an SQL query
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function filterComments($sql)
	{
		return preg_replace("!/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/!s","",$sql);
	}

	/**
	 * Loads the Form instances
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function form($type)
	{
		PP::load('Form');

		$form = new PPForm($type);

		return $form;
	}

	/**
	 * Determines if the current Joomla install is J4.0
	 *
	 * @since	4.2
	 * @access	public
	 */
	public static function isJoomla4()
	{
		static $isJoomla4 = null;

		if (is_null($isJoomla4)) {
			$currentVersion = self::getJoomlaVersion();
			$isJoomla4 = version_compare($currentVersion, '4.0') !== -1;

			return $isJoomla4;
		}

		return $isJoomla4;
	}

	/**
	 * Loads a library from the system
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function load($library)
	{
		// We do not need to use PPJString here because files are not utf-8 anyway.
		$library = strtolower($library);
		$obj = false;

		$path = PP_LIB . '/' . $library . '/' . $library . '.php';
		include_once($path);
	}

	/**
	 * Loads a library from the system
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function lock($name, $timeout = 0)
	{
		PP::load('lock');

		static $libraries = [];

		if (!isset($libraries[$name])) {
			$lock = new PPLock($name, $timeout);

			$libraries[$name] = $lock;
		}

		return $libraries[$name];
	}

	/**
	 * Retrieve JTable instance
	 *
	 * @since 	4.0.0
	 * @access	public
	 **/
	public static function table($name, $prefix = 'PayPlansTable')
	{
		PP::import('admin:/tables/table');

		$table = PayPlansTable::getInstance($name, $prefix);

		return $table;
	}

	/**
	 * Simple way to minify css codes
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function minifyCSS($css)
	{
		$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
		$css = str_replace(': ', ':', $css);
		$css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);

		return $css;
	}

	/**
	 * Retrieves the model for Komento
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function model($name, $config = [])
	{
		static $models = [];

		$key = md5(strtolower($name));

		// Determines if we should run the initialize state for the model
		$initializeStates = \FH::normalize($config, 'initState', false);

		if ($initializeStates) {
			unset($config['initState']);
		}

		if (!isset($models[$key])) {
			PP::import('admin:/includes/model');

			$className = 'PayPlansModel' . ucfirst($name);

			// Include the model file. This is much quicker than doing JLoader::import
			if (!class_exists($className)) {
				$path = PP_MODELS . '/' . strtolower($name) . '.php';
				require_once($path);
			}

			$config = array_merge($config, [
				'fd' => PP::fd()
			]);

			$models[$key] = new $className($config);
		}

		// Initialize state when needed to
		if (isset($models[$key]) && $models[$key] && $initializeStates && method_exists($models[$key], 'initStates')) {
			$models[$key]->initStates();
		}

		return $models[$key];
	}

	/**
	 * Creates a new view instance if it doesn't exist yet
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function view($name, $backend = true)
	{
		static $views = array();

		$className = 'PayPlansView' . ucfirst($name);
		$index = md5($className);

		if (!isset($views[$index])) {

			if (!class_exists($className)) {
				$path = $backend ? PP_ADMIN : PP_SITE;

				$doc = JFactory::getDocument();
				$path .= '/views/' . strtolower( $name ) . '/view.' . $doc->getType() . '.php';

				if (!JFile::exists($path)) {
					return false;
				}

				// Include the view
				require_once($path);
			}

			if (!class_exists($className)) {
				JError::raiseError(500, JText::sprintf('View class not found: %1s', $className));
				return false;
			}

			$views[$index] = new $className(array());
		}

		return $views[$index];
	}

	/**
	 * Creates a new stylesheet instance
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function stylesheet($location = 'site')
	{
		PP::load('Stylesheet');

		$stylesheet = new PPStyleSheet($location);

		return $stylesheet;
	}

	/**
	 * Create a new statistics object
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function statistics()
	{
		PP::load('statistics');

		$statistics = new PPStatistics();

		return $statistics;
	}

	/**
	 * Generates the CSRF token from Joomla
	 * 
	 * DEPRECATED. Use Foundry's form.token helper.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function token()
	{
		return JFactory::getSession()->getFormToken();
	}

	/**
	 * Single point of entry for static calls.
	 *
	 * @since	3.7
	 * @access	public
	 */
	public static function call($className, $method, $args = [])
	{
		$item = strtolower($className);
		$obj = false;

		$path = PP_LIB . '/' . $item . '/' . $item . '.php';

		require_once($path);

		$class = 'PP' . ucfirst($className);

		// Ensure that $args is an array.
		$args = PP::makeArray($args);

		return call_user_func_array(array($class, $method), $args);
	}

	/**
	 * Converts an argument into an array.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function makeArray($item, $delimeter = null)
	{
		// If this is already an array, we don't need to do anything here.
		if (is_array($item)) {
			return $item;
		}

		// Test if source is a SocialRegistry/JRegistry object
		if ($item instanceof PPRegistry || $item instanceof JRegistry) {
			return $item->toArray();
		}

		// Test if source is an object.
		if (is_object($item)) {
			return PPArrayHelper::fromObject($item);
		}

		if (is_integer($item)) {
			return [$item];
		}

		// Test if source is a string.
		if (is_string($item)) {
			if ($item == '') {
				return [];
			}

			// Test for comma separated values.
			if (!is_null($delimeter) && stristr($item, $delimeter) !== false) {
				$data = explode($delimeter, $item);
				return $data;
			}

			// Test for JSON array string
			$pattern = '#^\s*//.+$#m';
			$item = trim(preg_replace($pattern, '', $item));
			if ((substr($item, 0, 1) === '[' && substr($item, -1, 1) === ']')) {
				return json_decode($item);
			}

			// Test for JSON object string, but convert it into array
			if ((substr($item, 0, 1) === '{' && substr($item, -1, 1) === '}')) {
				$result = json_decode($item);
				return PPArrayHelper::fromObject($result);
			}

			return [$item];
		}

		return false;
	}

	/**
	 * Utility to mark exit
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	static public function markExit($msg = 'NO_MESSAGE')
	{
		// if not already set
		 if (defined('PAYPLANS_EXIT') == false) {
			define('PAYPLANS_EXIT',$msg);
			return true;
		}

		//already set
		return false;
	}

	/**
	 * Rearranges a list of modifiers
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function rearrageModifiers($modifiers)
	{
		$results = [];

		// arrage according to their serial
		$arrangeOrder = [];

		foreach ($modifiers as $modifier) {
			$arrangeOrder[$modifier->getSerial()][] = $modifier;
		}

		$arranged = [];

		foreach (self::$serials as $serial) {
			if (!isset($arrangeOrder[$serial])) {
				continue;
			}

			$arranged = array_merge($arranged, $arrangeOrder[$serial]);
		}

		return $arranged;
	}

	/**
	 * Redirects to a given link
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function redirect($link, $message = '', $class = '')
	{
		$app = JFactory::getApplication();

		if ($message) {
			$message = JText::_($message);
		}

		if (self::isJoomla4()) {
			if ($message) {
				$app->enqueueMessage($message, $class);
			}

			$app->redirect($link);
			return $app->close();
		}

		$app->redirect($link, $message, $class);
		return $app->close();
	}

	/**
	 * Renders the resolver library to resolve namespaes
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function resolver()
	{
		static $resolver = false;

		if (!$resolver) {
			PP::load('resolver');

			$resolver = new PPResolver();
		}

		return $resolver;
	}

	/**
	 * Renders the rewriter library. It needs to be a singleton instance
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function rewriter()
	{
		static $lib = null;

		if (is_null($lib)) {
			PP::load('rewriter');

			$lib = new PPRewriter();
		}

		return $lib;
	}

	/**
	 * Rewrites a given content with the rewriter
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function rewriteContent($content, $obj, $newlineToBreak = false)
	{
		if (!$content) {
			return $content;
		}

		if ($newlineToBreak) {
			$content = nl2br($content);
		}

		$rewriter = PP::rewriter();
		$content = $rewriter->rewrite($content, $obj);

		return $content;
	}

	/**
	 * Resolve a given POSIX path.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function resolve($path)
	{
		if (strpos($path, ':/') === false) {
			return false;
		}

		$parts = explode(':/', $path);

		// Get the protocol.
		$protocol = $parts[0];

		// Get the real path.
		$path = $parts[1];

		if ($protocol == 'modules') {
			return PP::call('Modules', 'resolve', $path);
		}

		if ($protocol == 'themes') {
			return PP::call('Themes', 'resolve', $path);
		}

		if ($protocol == 'ajax') {
			return PP::call('Ajax', 'resolveNamespace', $path);
		}

		if ($protocol == 'site' || $protocol == 'admin') {
			$key = 'PP_' . strtoupper($protocol);
			$basePath = constant($key);

			return $basePath . '/' . $path;
		}

		return false;
	}

	/**
	 * Renders a login page if necessary. If this is called via an ajax method, it will trigger a dialog instead.
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public static function requireLogin($redirect = '')
	{
		$my = JFactory::getUser();

		// User is logged in, allow them to proceed
		if (!$my->guest) {
			return true;
		}

		$app = JFactory::getApplication();

		// Get the current URI which you trying to access
		$currentUri = PPR::getCurrentURI();
		$returnURL = '';

		if ($currentUri) {
			$returnURL = '&return=' . base64_encode($currentUri);
		}

		$defaultLoginURL = "index.php?option=com_users&view=login" . $returnURL;
		$defaultLoginURL = JRoute::_($defaultLoginURL, false);

		// redirect to the custom redirection URL else redirect to Joomla login page
		$redirect = $redirect ? $redirect : $defaultLoginURL;

		return $app->redirect($redirect);
	}

	/**
	 * Retrieves the current version of Payplans installed.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function getLocalVersion()
	{
		static $version = false;

		if ($version === false) {
			$file = PP_ADMIN . '/payplans.xml';

			$contents = file_get_contents($file);
			$parser = simplexml_load_string($contents);

			$version = $parser->xpath('version');
			$version = (string) $version[0];
		}

		return $version;
	}

	/**
	 * Retrieves the current installed Joomla version
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function getJoomlaVersion($long = false)
	{
		if ($long) {
			return JVERSION;
		}

		$version = explode('.' , JVERSION);
		return $version[0] . '.' . $version[1];
	}

	/**
	 * Retrieves the current installed Joomla version
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function getJoomlaCodename()
	{
		$versionName = 'joomla15';
		$version = self::getJoomlaVersion();

		if ($version >= '1.6') {
			$versionName = 'joomla30';
			return $versionName;
		}

		return $versionName;
	}

	/**
	 * Retrieves date library
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function date($date = 'now', $offset = null)
	{
		if (is_object($date) && get_class($date) == 'PPDate') {
			return $date;
		}

		// load library
		PP::load('Date');

		$date = new PPDate($date, $offset);

		return $date;
	}

	/**
	 * Cumpute the date that has locale and return GMT datetime string.
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public static function convertToGMTDate($dateStringWithOffset)
	{
		if (!$dateStringWithOffset || $dateStringWithOffset == '0000-00-00 00:00:00') {
			return $dateStringWithOffset;
		}

		$tz = PP::date()->getTimezone();
		$date = PP::date($dateStringWithOffset, $tz);

		return $date->toSQL();
	}

	/**
	 * Retrieves the base URL of the site
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function getBaseUrl()
	{
		$baseUrl = rtrim( JURI::root() , '/' ) . '/index.php?option=com_payplans';

		$app = JFactory::getApplication();
		$config = PP::config();
		$jConfig = PP::jconfig();
		$uri = PP::getURI();
		$language = $uri->getVar('lang', 'none');
		$router = $app->getRouter();
		$baseUrl = rtrim(JURI::base(), '/') . '/index.php?option=com_payplans&lang=' . $language;

		$itemId = $app->input->get('Itemid') ? '&Itemid=' .  $app->input->get('Itemid') : '';

		if (PPRouter::getMode() == PP_JROUTER_MODE_SEF && JPluginHelper::isEnabled("system" , "languagefilter")) {

			$sefs = JLanguageHelper::getLanguages('sef');
			$lang_codes = JLanguageHelper::getLanguages('lang_code');

			$plugin = JPluginHelper::getPlugin('system', 'languagefilter');
			$params = new JRegistry();
			$params->loadString(empty($plugin) ? '' : $plugin->params);
			$removeLangCode = is_null($params) ? 'null' : $params->get('remove_default_prefix', false);

			$rewrite = $jConfig->get('sef_rewrite');

			$path = $uri->getPath();
			$parts = explode('/', $path);

			if ($removeLangCode === 1) {

				// the current view language
				$currentViewLang = JFactory::getLanguage()->getTag();

				// the default site language
				$defaultSiteLang = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');

				$defaultSefLang = $lang_codes[$defaultSiteLang]->sef;
				$currentSefLang = $lang_codes[$currentViewLang]->sef;

				if ($defaultSefLang == $currentSefLang) {
					$language = '';
				} else {
					$language = $currentSefLang;
				}

			} else {

				$base = str_ireplace(JURI::root(true), '', $uri->getPath());
				$path = $rewrite ? $base : PPJString::substr($base , 10);
				$path = trim( $path , '/' );
				$parts = explode( '/' , $path );

				if ($parts) {
					// First segment will always be the language filter.
					$language = reset( $parts );
				} else {
					$language = '';
				}

			}

			if ($language) {
				$language .= '/';
			}

			if ($rewrite) {
				$baseUrl = rtrim(JURI::base(), '/') . '/' . $language . '?option=com_payplans';
			} else {
				$baseUrl = rtrim(JURI::base(), '/') . '/index.php/' . $language . '?option=com_payplans';
			}
		}

		return $baseUrl . $itemId;
	}

	/**
	 * Converts an argument into an object
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function makeObject($item, $debug = false)
	{
		// If this is already an object, skip this
		if (is_object($item)) {
			return $item;
		}

		if (is_array($item)) {
			return (object) $item;
		}

		if (strlen($item) < 1024 && is_file($item)) {
			jimport('joomla.filesystem.file');
			$item = file_get_contents($item);
		}

		$json = PP::json();

		// Test if source is a string.
		if ($json->isJsonString($item)) {

			if ($debug) {
				$obj = $json->decode($item);
				var_dump($item, $obj);
				exit;
			}

			// Trim the string first
			$item = trim($item);

			$obj = $json->decode($item);

			if (!is_null($obj)) {
				return $obj;
			}

			$obj = new stdClass();
			return $obj;
		}

		return false;
	}

	/**
	 * Converts an array to string
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function makeString($val, $join = '')
	{
		if (is_string($val)) {
			return $val;
		}

		return implode($join, $val);
	}

	/**
	 * Converts an argument into a json string. If argument is a string, it wouldn't be processed.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function makeJSON($item)
	{
		if (is_string($item)) {
			return $item;
		}

		return json_encode($item);
	}

	/**
	 * Allows caller to pass in an array of data to normalize the data
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function normalize($data, $key, $default = null)
	{
		if (!$data) {
			return $default;
		}

		// $key cannot be an array
		if (is_array($key)) {
			$key = $key[0];
		}

		// Object datatype
		if (is_object($data) && isset($data->$key)) {
			return $data->$key;
		}

		// Array datatype
		if (is_array($data) && isset($data[$key])) {
			return $data[$key];
		}

		return $default;
	}

	/**
	 * Allows caller to pass in an array of data to normalize the data
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function normalizeCardNumber($cardNumber)
	{
		$number = trim(str_ireplace(' ', '', $cardNumber));

		return $number;
	}

	/**
	 * Allows caller to pass in an array of data to normalize the data
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function normalizeCardExpiry($month, $year)
	{
		$month = substr($month, 0, 2);
		$month = str_pad($month, 2, '0', STR_PAD_LEFT);
		
		$year = substr($year, -2);

		$expiryDate = $month . $year;

		return $expiryDate;
	}

	/**
	 * Retrieves the current currency used
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function getCurrency($isocode = null)
	{
		static $currencies = null;

		if (is_null($currencies)) {
			$model = PP::model('Currency');
			$currencies = $model->getAllCurrency();

		}

		if (is_null($isocode)) {
			return $currencies;
		}

		return $currencies[$isocode];
	}

	/**
	 * Retrieve company logo
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public static function getCompanyLogo()
	{
		static $logo = null;

		if (is_null($logo)) {
			$config = PP::config();
			$logoPath = $config->get('companyLogo', '');

			if (!$logoPath) {
				$logoPath = '/media/com_payplans/images/logo-payplans-text.png';
			}

			$logo = rtrim(JURI::root(), '/') . $logoPath;
		}

		return $logo;
	}

	/**
	 * Retrieve payment logo
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public static function getPaymentProviderLogo($app)
	{
		$logoPath = '';

		if ($app) {

			$model = PP::model('App');
			$appManifest = $model->getApp($app, 'gateway');

			if (isset($appManifest->icon) && $appManifest->icon) {
				$logoPath = '/media/com_payplans/images/payment-logo/'.$appManifest->icon;
			}
		}

		if (!$logoPath) {
			$logoPath = '/media/com_payplans/images/payment-logo/default-payment.svg';
		}

		$logo = rtrim(JURI::root(), '/') . $logoPath;

		return $logo;
	}

	/**
	 * Given a list of objects, get the id of the objects. Must be PPAbstract object
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function getIds($objects)
	{
		if (!$objects) {
			return false;
		}

		$ids = [];
		
		foreach ($objects as $object) {
			if (is_object($object) && method_exists($object, 'getId')) {
				$ids[] = (int) $object->getId();
			}
		}

		return $ids;
	}

	/**
	 * Render editor (deprecated function, please use PPCompat::getEditor())
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public static function getEditor($type = 'tinymce')
	{
		// Fall back to 'none' editor if the specified plugin is not enabled
		jimport('joomla.plugin.helper');
		$editorType = JPluginHelper::isEnabled('editors', $type) ? $type : 'none';

		if ($editorType == 'composer') {
			$editorType = $jConfig->get('editor');
		}

		if (JVERSION < 4) {
			$editor = JFactory::getEditor($editorType);

			if ($editorType == 'none') {
				JHtml::_('behavior.core');
			}
		} else {
			$editor = Joomla\CMS\Editor\Editor::getInstance($editorType);
		}

		return $editor;
	}

	/**
	 * Returns a query variable by name.
	 *
	 * @since   4.0
	 * @access  public
	 */
	public static function getURI($requestPath = false)
	{
		$uri = JUri::getInstance();

		// Gets the full request path.
		if ($requestPath) {
			$uri = $uri->toString(['path', 'query']);
		}

		return $uri;
	}

	/**
	 * Given the key for the request string, convert the key into an id
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function getIdFromInput($key)
	{
		$input = JFactory::getApplication()->input;
		$key = $input->get($key, '', 'default');

		if (!$key) {
			return $key;
		}

		$id = (int) PP::encryptor()->decrypt($key);

		return $id;
	}

	/**
	 * Return apps belong to specify type. E.g. Payment apps.
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public static function getApps($type)
	{
		static $_apps = [];

		if (! isset($_apps[$type])) {

			// TODO: retrieve apps based on the type.
			$_apps[$type] = [];
		}

		return $_apps[$type];
	}

	/**
	 * Given the id, return the encrypted key
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function getKeyFromId($id)
	{
		$encryptor = PP::encryptor();
		return $encryptor->encrypt($id);
	}

	/**
	 * Retrieves the menu id for a specific view
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public static function getMenuIdForView($view)
	{
		static $menus = null;
		static $siteMenus = [];
		static $cache = [];

		if (is_null($menus)) {
			$model = PP::model('Menu');
			$menus = $model->getMenuItems();

			if ($menus) {
				foreach ($menus as &$menu) {
					$tmp = str_ireplace('index.php?option=com_payplans', '', $menu->link);

					parse_str($tmp, $segments);

					$view = $segments['view'];
					$siteMenus[$view] = (int) $menu->id;
				}
			}
		}

		// If there is no site menus, return false
		if (!$siteMenus) {
			$cache[$view] = false;
		}

		// If they are already cached, return the cached copy
		if (isset($cache[$view])) {
			return $cache[$view];
		}

		// If we can't find any, just return the first item
		if (!isset($siteMenus[$view])) {
			$cache[$view] = reset($siteMenus);
		}

		if (isset($siteMenus[$view])) {
			$cache[$view] = $siteMenus[$view];
		}

		return $cache[$view];
	}

	/**
	 * Get the exclude template query for the specific view
	 *
	 * @since	4.2
	 * @access	public
	 */
	public static function getExcludeTplQuery($view, $exclude = false)
	{
		// This means that the template can be excluded immediately
		if ($exclude) {
			return '&tmpl=component';
		}

		// This means that need to check whether the view is allowed to exclude template or not based on its setting
		if (!$exclude) {
			$config = PP::config();

			if ($config->get($view . '_display_fullscreen', false)) {
				return '&tmpl=component';
			}
		}

		return '';
	}

	/**
	 * Given the encrypted key, return the id
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function getIdFromKey($key, $fromSearch = false)
	{
		$encryptor = PP::encryptor();
		$id = (int) $encryptor->decrypt($key, $fromSearch);

		return $id;
	}

	/**
	 * Reads a XML file.
	 *
	 * @since   4.0
	 * @access  public
	 */
	public static function getXml($data, $isFile = true)
	{
		$class = 'SimpleXMLElement';

		if (class_exists('JXMLElement')) {
			$class = 'JXMLElement';
		}

		if ($isFile) {
			// Try to load the XML file
			$xml = simplexml_load_file($data, $class);

		} else {
			// Try to load the XML string
			$xml = simplexml_load_string($data, $class);
		}

		if ($xml === false) {
			foreach (libxml_get_errors() as $error) {
				echo "\t", $error->message;
			}
		}

		return $xml;
	}

	public function setMessaage($message, $type = PP_MSG_INFO)
	{
		PP::view()->setMessage($message, $type);
	}

	 /**
	 * Generates a hash on a string.
	 *
	 * @since   1.0
	 * @access  public
	 */
	public static function getHash($str)
	{
		if (JVERSION < 4) {
			return JApplication::getHash($str);
		}

		return JApplicationHelper::getHash($str);
	}

	/**
	 * Retrieve Joomla article content
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public static function getCustomInvoiceContent($invoice)
	{
		$config = PP::config();

		$invoiceSource = $config->get('invoice_source');
		$articleId = $config->get('invoice_joomla_article');

		if ($invoiceSource != 'custom' || !$articleId || !$invoice) {
			return false;
		}

		$contents = self::getJoomlaArticleContent($articleId);

		if ($contents) {

			// Replace the token to proper value
			$rewriter = PP::rewriter();
			$contents = $rewriter->rewrite($contents, $invoice);
		}

		return $contents;
	}	

	/**
	 * Retrieve Joomla article content
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public static function getJoomlaArticleContent($articleId)
	{
		if (!$articleId) {
			return false;
		}

		$article = JTable::getInstance('Content');
		$state = $article->load($articleId);

		if (!$state) {
			return false;
		}

		// Determine if the site enable multilingual language
		if (JLanguageAssociations::isEnabled()) {

			// Retrieve all the association article data e.g. English, French and etc
			$termsAssociated = JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $articleId);

			// Determine the current site language
			$currentLang = JFactory::getLanguage()->getTag();

			// Only come inside this checking if the current site language not match with the selected article language
			// And see whether this tearmAssociated got detect got other association article or not
			if (isset($termsAssociated) && $currentLang !== $article->language && array_key_exists($currentLang, $termsAssociated)) {

				foreach ($termsAssociated as $term) {

					// Retrieve the associated article id
					if ($term->language == $currentLang) {
						$articleId = explode(':', $term->id);
						$articleId = $articleId[0];
						break;
					}
				}
			}

			// Reload the new associated article id
			$state = $article->load($articleId);
		}

		// Only assign the Joomla article content here if the article exist
		if ($state) {
			$contents = $article->introtext . $article->fulltext;
		}

		return $contents;
	}

	/**
	 * Render the modules library
	 *
	 * @since	4.1
	 * @access	public
	 */
	public static function modules($module)
	{
		require_once(__DIR__ . '/modules/modules.php');

		$lib = new PPModules($module);
		return $lib;
	}

	/**
	 * Determine if the animated icons is enabled for checkout process
	 *
	 * @since	4.2
	 * @access	public
	 */
	public static function isAnimatedIconsEnabled()
	{
		$config = PP::config();

		if ($config->get('checkout_use_animated')) {
			return true;
		}

		return false;
	}

	/**
	 * Generates the animated image codes used on the page
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public static function getAnimatedImageHtml($lottieFileName, $defaultHtml, $lottieOptions = [])
	{
		static $lottieRendered = false;

		static $items = [];

		if (!isset($items[$lottieFileName])) {
			$config = PP::config();

			$items[$lottieFileName] = $defaultHtml;

			// If configured to use the non animated version, always return the non animated url
			if (!$config->get('checkout_use_animated')) {
				return $items[$lottieFileName];
			}

			// If configured to use animated version
			$lottieUrl = JURI::root() . 'media/com_payplans/images/lottie/' . $lottieFileName . '.json';

			$template = PP::getJoomlaTemplate();
			$override = JPATH_ROOT . '/templates/' . $template . '/html/com_payplans/images/lottie/' . $lottieFileName . '.json';
			
			// Use the template override if there is
			if (file_exists($override)) {
				$lottieUrl = JURI::root() . 'templates/' . $template . '/html/com_payplans/images/lottie/' . $lottieFileName . '.json';
			}

			$lottieDefaultOptions = [
				'autoplay' => true,
				'renderer' => 'svg',
				'loop' => true
			];

			$lottieOptions = array_merge($lottieDefaultOptions, $lottieOptions);

			$theme = PP::themes();
			$theme->set('lottieUrl', $lottieUrl);
			$theme->set('rendered', $lottieRendered);
			$theme->set('options', $lottieOptions);
			$theme->set('lottieFileName', $lottieFileName);

			$output = $theme->output('site/lottie/svg');
				
			// We know that we need to render the animated lottie files, so we need to add their JS at least once
			$lottieRendered = true;

			$items[$lottieFileName] = $output;
		}

		return $items[$lottieFileName];
	}

	/**
	 * Method to return user language 
	 *
	 * @since   4.2.0
	 * @access  public
	 */
	public static function getUserLanguage(PPUser $ppuser)
	{
		// default language tag
		$languageTag = JFactory::getLanguage()->getTag();

		if (isset($ppuser->user->params) && $ppuser->user->params) {
			$userParams = PP::json()->decode($ppuser->user->params);

			if (isset($userParams->language) && $userParams->language) {
				$languageTag = $userParams->language;
			}
		}

		return $languageTag;
	}

	/**
	 * Normalize directory separator
	 *
	 * @since   4.2.0
	 * @access  public
	 */
	public static function normalizeSeparator($path)
	{
		$path = str_ireplace(['\\' ,'/' ] , '/' , $path);

		return $path;
	}

	/**
	 * Allow callers to set meta data
	 *
	 * @since   4.2.8
	 * @access  public
	 */
	public static function setMeta($type = '', $id = '')
	{
		$doc = JFactory::getDocument();
		$active = JFactory::getApplication()->getMenu()->getActive();
		$meta = false;

		// Checkout page
		if ($type == PP_META_TYPE_CHECKOUT && $id) {

			$plan = PP::plan($id);
			$description = strip_tags($plan->getDescription());

			$doc->setMetadata('description', $description);
		}

		// no index checkout, payment and invoice thanks page
		if (in_array($type, [PP_META_TYPE_CHECKOUT, PP_META_TYPE_PAYMENT, PP_META_TYPE_THANKS])) {
			$doc->setMetadata('robots', 'noindex, nofollow');
		}

		if ($active && !$type) {
			$params = $active->getParams();

			$description = $params->get('menu-meta_description', '');
			$keywords = $params->get('menu-meta_keywords', '');
			$robots = $params->get('robots', '');

			if (!empty($description) || !empty($keywords) || !empty($robots)) {
				$meta = new stdClass();
				$meta->description = PP::string()->escape($description);
				$meta->keywords = $keywords;
				$meta->robots = $robots;
			}
		}

		if (!$meta) {
			return;
		}

		if ($meta->keywords) {
			$doc->setMetadata('keywords', $meta->keywords);
		}

		if ($meta->description) {
			$doc->setMetadata('description', $meta->description);
		}

		if ($meta->robots) {
			$doc->setMetadata('robots', $meta->robots);
		}
	}

	/**
	 * Delete the files that uploaded on the custom detail of its type and id given
	 *
	 * @since	4.2.8
	 * @access	public
	 */
	public static function deleteCustomDetailFiles($type, $id)
	{
		$path = JPATH_ROOT . '/media/com_payplans/attachments/customdetails/'. $type . '/' . $id;

		if (!JFolder::exists($path)) {
			return;
		}

		JFolder::delete($path);
	}

	/**
	 * Storing the files that uploaded on the custom detail into the site
	 *
	 * @since	4.2.8
	 * @access	public
	 */
	public static function saveCustomDetailFiles($data, $type, $id)
	{
		if (empty($data)) {
			return;
		}

		$config = self::config();

		$uploadLimit = $config->get('custom_details_file_limit');
		$maxSize = $config->get('custom_details_file_maxsize');
		$maxSizeInByte = $maxSize * 1024 * 1024;

		foreach ($data as $key => $files) {
			$folderPath = JPATH_ROOT . '/media/com_payplans/attachments/customdetails/'. $type . '/' . $id . '/' . $key;

			if (!JFolder::exists($folderPath)) {
				JFolder::create($folderPath);
			}

			foreach ($files as $file) {
				if (!$file['tmp_name'] || !$file['name']) {
					continue;
				}

				// Ensure that the file does not exceed the max size limit
				if ($maxSize && $file['size'] > $maxSizeInByte) {
					$message = JText::sprintf('COM_PP_CUSTOM_DETAILS_FILE_FIELD_MAXSIZE_EXCEEDED', $file['name'], $maxSize);

					throw new Exception($message, 400);
				}

				$total = count(JFolder::files($folderPath));

				// Ensure that the total files in the folder does not exceed the limit
				if ($uploadLimit && $total >= $uploadLimit) {
					$message = JText::sprintf('COM_PP_CUSTOM_DETAILS_FILE_FIELD_UPLOAD_LIMIT_REACHED', $uploadLimit);

					throw new Exception($message, 400);
				}

				$name = $file['name'];
				$extension = JFile::getExt($name);
				$tmp = $name;

				$path = $folderPath . '/' . $name;
				$i = 1;

				do {
					$exists = JFile::exists($path);

					if ($exists) {
						// Remove the extension first
						$tmp = str_replace('.' . $extension, '', $tmp);

						// Generate an unique name if is exists
						$name = $tmp . '_' . $i++ . '.' . $extension;
					}

					// Update the path
					$path = $folderPath . '/' . $name;
				} while ($exists);

				JFile::upload($file['tmp_name'], $path);
			}
		}
	}

	/**
	 * Retrieves all the files that the user uploaded for the custom detail
	 *
	 * @since	4.2.8
	 * @access	public
	 */
	public static function getCustomDetailFiles($type, $id)
	{
		$folderPath = JPATH_ROOT . '/media/com_payplans/attachments/customdetails/' . $type . '/' . $id;

		if (!JFolder::exists($folderPath)) {
			return array();
		}

		// The file custom details
		$customDetails = JFolder::folders($folderPath);

		$files = [];

		foreach ($customDetails as $customDetail) {
			$path = $folderPath . '/' . $customDetail;
			$files_ = JFolder::files($path);

			if (!empty($files_)) {
				$files[$customDetail] = $files_;
			}
		}

		return $files;
	}

	/**
	 * Retrieve date format
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public static function getDateFormat()
	{
		static $dateFormat = null;

		if (is_null($dateFormat)) {
			$config = PP::config();
			$dateFormat = $config->get('date_format', '');

			//need to check for timestamp with date
			$showTime = $config->get('show_time', false);

			if ($showTime) {

				// we consider default timezone format to be H:i 
				$timeFormat = "H:i";
				
				// combining dateformat with time format
				$dateFormat = $dateFormat.' '.$timeFormat;
			}
		}
		
		return $dateFormat;
	}

	/**
	 * Loads the default languages for Payplans
	 *
	 * @since	4.2.10
	 * @access	public
	 */
	public static function loadLanguages($path = JPATH_ROOT)
	{
		return FH::loadLanguage('com_payplans', $path);
	}

	/**
	 * Parses a csv file to array of data
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function parseCSV($file, $firstRowName = true, $firstColumnKey = true)
	{
		$exists = JFile::exists($file);

		if (!$exists) {
			return [];
		}

		$handle = fopen($file, 'r');

		$line = 0;
		$columns = array();

		$data = array();

		while (($row = fgetcsv($handle)) !== false) {

			if ($row[0] == null) { // ignore blank lines
   				continue;
			}

			if ($firstRowName && $line === 0) {
				$columns = $row;

			} else {
				$tmp = [];

				if ($firstRowName) {

					foreach ($row as $i => $v) {
						$tmp[$columns[$i]] = $v;
					}

				} else {
					$tmp = $row;
				}

				if ($firstColumnKey) {
					
					if ($firstRowName) {
						$data[$tmp[$columns[0]]] = $tmp;
					
					} else {
						$data[$tmp[0]] = $tmp;
					}

				} else {
					$data[] = $tmp;
				}
			}

			$line++;
		}

		fclose($handle);
		return $data;
	}

	/**
	 * Format date in yyyy-mm-dd
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function formatDate($date, $dateFormatInput = '', $dateFormatOutput = 'Y-m-d')
	{
		// You can pass in what date format input to this variable dateFormatInput

		// if the input date format same with the output then use the output date format
		if (!$dateFormatInput) {
			$dateFormatInput = $dateFormatOutput;
		}

		$tmp = explode(' ', $date); // remove the time segments based on space character

		$dateInput = $tmp[0]; //day

		// check if date is valid or not
		$tempDate = explode('-', $dateInput);

		// $tempDate[0] = month, $tempDaea[1] = date, $tempDate[2] = year
		$valid = checkdate($tempDate[1], $tempDate[2], $tempDate[0]);   // checkdate(month, day, year)

		if (!$valid) {
			return false;
		}

		$date = DateTime::createFromFormat($dateFormatInput, $dateInput)->format($dateFormatOutput);

		$date = PP::date($date);
		return $date;
	}

	/**
	 * Determine if the price give is free or not
	 *
	 * @since	5.0.1
	 * @access	public
	 */
	public static function isFree($price)
	{
		$zero = floatval(0);
		$price = floatval($price);

		return $price === $zero;
	}
}
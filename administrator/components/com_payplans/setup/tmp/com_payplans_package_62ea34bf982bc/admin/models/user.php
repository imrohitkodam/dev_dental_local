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

use Joomla\CMS\Form\Form;

PP::import('admin:/includes/model');

class PayplansModelUser extends PayPlansModel
{
	protected $recordId ;

	public $crossTableNetwork = [ 
		"subscription"=> ['subscription'],
		"users"  => ['users']
	];

	public $innerJoinCondition	= [
		'tbl-subscription' => ' `#__payplans_subscription` as cross_subscription on cross_subscription.user_id = tbl.id'
	];

	public $filterMatchOpeartor = [
		'username' => ['LIKE'],
		'usertype' => ['='],
		'cross_subscription_plan_id' => ['='],
		'cross_subscription_status' => ['=']
	];

	public static $loadedUsers = [];
	public $ordering = null;

	public function __construct()
	{
		parent::__construct('user');
	}

	/**
	 * Create dummy user record for simplified checkout experience
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function createDummyUser()
	{
		jimport('joomla.mail.helper');
		jimport('joomla.user.helper');

		$config = JFactory::getConfig();
		$params = JComponentHelper::getParams('com_users');

		$model = PPUsersModelRegistration::load();
		$data = (array) $model->getData();

		// Prepare the data for the user object.
		$username = 'Not_Registered';
		$email = 'not@registered.com';
		$password = JUserHelper::genRandomPassword();

		$data['name'] = $username;
		$data['username'] = $username;
		$data['email'] = $email;
		$data['password'] = $password;
		$data['block'] = 1;
		$data['activation'] = PP::getHash(JUserHelper::genRandomPassword());

		$user = new JUser;

		if (!$user->bind($data)) {
			return false;
		}

		// Load the users plugin group.
		JPluginHelper::importPlugin('user');

		// Store the data.
		if (!$user->save()) {
			return false;
		}

		return $user;
	}

	/**
	 * Create dummy user record for simplified checkout experience
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function createUser($invoice, $account = [], $preferences = [])
	{
		jimport('joomla.mail.helper');
		jimport('joomla.user.helper');
		
		$config = PP::config();
		$params = JComponentHelper::getParams('com_users');

		// Load com_users language files
		JFactory::getLanguage()->load('com_users');

		$model = PPUsersModelRegistration::load();
		$data = (array) $model->getData();

		// Get the user data
		$userData = [
			'username' => $this->normalize($account, 'username', ''),
			'name' => $this->normalize($account, 'name', ''),
			'email' => $this->normalize($account, 'email', ''),
			'password' => $this->normalize($account, 'password', ''),
			'clear_password' => $this->normalize($account, 'password', '')
		];

		// Set the user language
		if (isset($account['language'])) {
			$userData['params'] = [
				'language' => $this->normalize($account, 'language', '')
			];
		}

		$plan = $invoice->getPlan();
		$requireActivation = $this->requireEmailVerification($plan);
		$requireActiveSub = $this->requireActiveSubscription();

		if ($requireActivation || $requireActiveSub) {
			$data['block'] = 1;
			$data['activation'] = PP::getHash(JUserHelper::genRandomPassword());
		}

		$userData = array_merge($userData, $data);

		$user = new JUser;

		if (!$user->bind($userData)) {
			return false;
		}

		// Load the users plugin group.
		JPluginHelper::importPlugin('user');

		// Store the data.
		if (!$user->save()) {
			$this->setError($user->getError());
			return false;
		}

		// There is a possibilites where user didn't set any password during registration.
		// When password is empty, Joomla will automatically generate a random password for the user.
		$notifyPassword = null;

		if (!$userData['clear_password']) {
			$userData['clear_password'] = $user->password_clear;
			$notifyPassword = true;
		}

		// Create a new record for PayPlans to store any other params
		$table = PP::table('User');
		$table->load([
			'user_id' => $user->id
		]);

		$table->user_id = $user->id;
		$table->address = $this->normalize($account, 'address', '');
		$table->city = $this->normalize($account, 'city', '');
		$table->state = $this->normalize($account, 'state', '');
		$table->country = $this->normalize($account, 'country', 0);
		$table->zipcode = $this->normalize($account, 'zip', '');
		$table->preference = json_encode($preferences);
		$table->store();

		$user = PP::user($user->id);
		$this->notify($user, $userData, $notifyPassword);

		// Automatically log the user into the system only when;
		// 1. The autologin setting is enabled,
		// 2. Registration type is built-in (auto),
		// 3. Registration doesn't need account_verification (auto);
		if ($config->get('autologin') && $config->get('registrationType') == 'auto' && $config->get('account_verification') == 'auto') {
			$user->login($userData['username'], $userData['clear_password']);

			$jTable = JUser::getTable();
			$jTable->load($user->id);

			// need to bind the user last visit date for the new user
			$user->lastvisitDate = $jTable->lastvisitDate;
		}

		// set the user credentials on the session in order to login manually after done the payment.
		if ($requireActiveSub) {

			$username = base64_encode($userData['username']);
			$password = base64_encode($userData['clear_password']);

			// set this credentials session
			// need to use this session after done the payment
			$session = PP::session();
			$session->set('COM_PAYPLANS_AUTHENTICATION_USERNAME', $username);
			$session->set('COM_PAYPLANS_AUTHENTICATION_PASSWORD', $password);

		}

		return $user;
	}

	/**
	 * Notify the user when their account is created
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function notify($user, $data, $sendPassword = null)
	{
		$config = PP::config();
		$jconfig = PP::jconfig();
		$verificationType = $config->get('account_verification');

		if (is_null($sendPassword)) {
			$sendPassword = $config->get('send_password');
		}

		// Compile the notification mail values.
		$data['siteUrl'] = JURI::root();
		$data['siteName'] = $jconfig->get('sitename');
		$data['includePassword'] = $sendPassword;
		$data['activate'] = false;
		$data['activation'] = PP::normalize($data, 'activation', false);

		// Default template
		$namespace = 'emails/registration/default';

		$subject = JText::sprintf('COM_PP_EMAIL_REGISTRATION_SUBJECT', $data['siteName']);

		// Set the link to activate the user account.
		if (($verificationType == 'admin' || $verificationType == 'user') && $data['activation']) {
			$uri = JURI::getInstance();
			$base = $uri->toString(['scheme', 'user', 'pass', 'host', 'port']);

			$data['activate'] = $base . JRoute::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'], false);

			$namespace = 'emails/registration/activation';

			if ($verificationType == 'admin') {
				$namespace = 'emails/registration/moderation';
			}
		}

		$mailer = PP::mailer();
		$state = $mailer->send($user->getEmail(), $subject, $namespace, $data);

		// Notify admin about user registration
		if ($config->get('notify_admin')) {
			$namespace = 'emails/registration/notify_admin';
			$subject = JText::sprintf('COM_PP_EMAIL_REGISTRATION_NOTIFY_ADMIN_SUBJECT', $data['siteName']);

			// Get admin emails
			$emails = $mailer->getAdminEmails();

			foreach ($emails as $email) {
				$data['admin_user'] = $this->getUserNameFromEmail($email);
				$mailer->send($email, $subject, $namespace, $data);
			}
		}

		return $state;
	}

	/**
	 * Determines if a specific column for the users table exists on the site
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function exists($column, $value)
	{
		$db = PP::db();

		$query = [];
		$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__users');
		$query[] = 'WHERE ' . $db->qn($column) . '=' . $db->Quote($value);

		$db->setQuery($query);
		$exists = $db->loadResult() > 0 ? true : false;

		return $exists;
	}

	/**
	 * Initialize default states
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function initStates()
	{
		parent::initStates();

		$planId = $this->getUserStateFromRequest('plan_id', 0, 'int');
		$this->setState('plan_id', $planId);

		// Override ordering column to use id by default
		$ordering = $this->getUserStateFromRequest('ordering', 'id', 'string');
		$this->setState('ordering', $ordering);
	}

	/**
	 * Initialize a user record
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function initializeUser($data)
	{
		$db = PP::db();
		$data = (object) $data;

		return $db->insertObject('#__payplans_user', $data, 'user_id');
	}

	/**
	 * Determines if a user exists in our own user table
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isUserExists($id)
	{
		$db = PP::db();
		$query = [];
		$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__payplans_user');
		$query[] = 'WHERE ' . $db->qn('user_id') . '=' . $db->Quote($id);

		$db->setQuery($query);

		$exists = $db->loadResult() > 0 ? true : false;

		return $exists;
	}

	/**
	 * Retrieves a list of joomla groups
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getJoomlaGroups()
	{
		$db = PP::db();

		$sql = 'SELECT a.id AS value, a.title AS name, COUNT(DISTINCT b.id) AS level' .
			' FROM #__usergroups AS a' .
			' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt' .
			' GROUP BY a.id' .
			' ORDER BY a.lft ASC';
		$db->setQuery($sql);
		$groups = $db->loadObjectList('value');
		
		// filter groups
		// unset groups which are core.admin
		$cloneGroups = $groups;
		foreach ($cloneGroups as $group) {
			
			if (!class_exists('JAccessRules')) {
				jimport('joomla.access.rules');
			}
			
			// if its admin group
			if (JAccess::getAssetRules(1)->allow('core.admin', $group->value)) {
				unset($groups[$group->value]);
			}
		}
		
		return $groups;
	}

	/**
	 * Retrieves a list of user groups a user belongs to
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getUserGroups($userId)
	{
		// Load our own db.
		$db = PP::db();

		$query = [];
		$query[] = 'SELECT b.' . $db->nameQuote('group_id') . ' AS ' . $db->nameQuote('id') . ', a.' . $db->nameQuote('title');
		$query[] = 'FROM ' . $db->nameQuote('#__usergroups') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->nameQuote('#__user_usergroup_map') . ' AS b';
		$query[] = 'ON a.' . $db->nameQuote('id') . ' = b.' . $db->nameQuote('group_id');
		$query[] = 'WHERE b.' . $db->nameQuote('user_id') . ' = ' . $db->Quote($userId);

		$query = implode(' ' , $query);
		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$groups = [];

		foreach($result as $row) {
			// Do not use title because by default, JUser::groups array format is id => id, NOT id => title
			// Because we set it as title here, it affected the original JUser object for the current logged in user
			// And hence forth causes some plugin to have issues
			// $groups[$row->id]  = $row->title;
			$groups[$row->id] = $row->id;
		}

		return $groups;
	}

	/**
	 * Retrieve lists of usergroups available on the site
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function getAllUserGroups()
	{
		$db	= PP::db();
		$query = [];

		$query[] = 'SELECT a.*, COUNT(DISTINCT(b.`id`)) AS level FROM ' . $db->qn('#__usergroups') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->qn('#__usergroups') . ' AS b';
		$query[] = 'ON a.`lft` > b.`lft`';
		$query[] = 'AND a.`rgt` < b.`rgt`';
		$query[] = 'GROUP BY a.`id`, a.`title`, a.`lft`, a.`rgt`, a.`parent_id`';
		$query[] = 'ORDER BY a.`lft` ASC';

		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Retrieves a list of site admins from the site
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getSiteAdmins($sendMail = false)
	{
		if (!class_exists('JAccessRules')) {
			jimport('joomla.access.rules');
		}

		$rules = JAccess::getAssetRules(1);
		$groups = $rules->getData();
		$adminGroups = array_keys($groups['core.admin']->getData());

		$db = PP::db();
		$query = [];
		$query[] = 'SELECT * FROM ' . $db->qn('#__users');
		$query[] = 'WHERE ' . $db->qn('block') . '=' . $db->Quote(0);
		$query[] = 'AND ' . $db->qn('id') . ' IN(';
		$query[] = 'SELECT ' . $db->qn('user_id') . ' FROM ' . $db->qn('#__user_usergroup_map') . ' WHERE ' . $db->qn('group_id') . ' IN(' . implode(',', $adminGroups) . ')';
		$query[] = ')';

		if ($sendMail) {
			$query[] = 'AND ' . $db->qn('sendEmail') . '=' . $db->Quote(1);
		}

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$admins = [];

		foreach ($result as $admin) {
			$admin = PP::user($admin->id);
			$admins[] = $admin;
		}

		return $admins;
	}

	/**
	 * Retrieve users from the site with states
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getItems()
	{
		$planId = $this->getState('plan_id');
		$subStatus = $this->getState('subscription_status');
		$usertype = $this->getState('usertype');

		$db = $this->db;

		$query = [];

		$query[] = 'SELECT DISTINCT(a.`id`) AS `user_id`, b.`params`, b.`address`, b.`state`, b.`city`,b.`country`,b.`zipcode`,b.`preference` FROM ' . $db->qn('#__users') . ' AS a';
		$query[] = 'LEFT JOIN ' . $db->qn('#__payplans_user') . ' AS b';
		$query[] = 'ON a.' . $db->qn('id') . ' = b.' . $db->qn('user_id');
		$query[] = 'LEFT JOIN ' . $db->qn('#__payplans_subscription') . ' AS c';
		$query[] = 'ON a.' . $db->qn('id') . ' = c.' . $db->qn('user_id');
		$query[] = 'LEFT JOIN ' . $db->qn('#__payplans_order') . ' AS e';
		$query[] = 'ON a.' . $db->qn('id') . ' = e.' . $db->qn('buyer_id');

		$wheres = [];

		$search = $this->getState('search');

		if ($search) {
			$search = PPJString::strtolower($search);

			$searchQuery = [];
			$searchQuery[] = 'LOWER(a.' . $db->qn('username') . ') LIKE ' . $db->Quote('%' . $search . '%');
			$searchQuery[] = 'LOWER(a.' . $db->qn('name') . ') LIKE ' . $db->Quote('%' . $search . '%');
			$searchQuery[] = 'LOWER(a.' . $db->qn('email') . ') LIKE ' . $db->Quote('%' . $search . '%');
			$searchQuery[] = 'LOWER(a.' . $db->qn('id') . ') LIKE ' . $db->Quote('%' . $search . '%');

			$wheres[] = implode(' OR ', $searchQuery);
		}

		if ($planId && $planId !== -1) {
			$wheres[] = 'c.`plan_id`=' . $db->Quote($planId) . ' AND e.`status` != 0 ';
		}

		// Filter without plans
		if ($planId && $planId === -1) {
			$wheres[] = 'a.`id`NOT IN (SELECT DISTINCT d.`user_id` FROM `#__payplans_subscription` AS d INNER JOIN `#__payplans_order` AS o ON o.`buyer_id` = d.`user_id` WHERE o.`status` != 0)';
		}

		$where = '';

		if (count($wheres) > 0) {
			$where = ' WHERE ';
			$where .= (count($wheres) == 1) ? $wheres[0] : implode(' and ', $wheres);
		}

		$query = implode(' ', $query);
		$query .= $where;

		$this->setTotal($query, true);

		// Ordering
		$ordering = $this->getState('ordering');
		$direction = $this->getState('direction');

		$orderingQuery = 'ORDER BY a.' . $db->qn('id') . ' DESC';

		if ($ordering && $direction) {
			$orderingQuery = 'ORDER BY a.' . $db->qn($ordering) . ' ' . $direction;
		}

		$query .= $orderingQuery;

		$result	= $this->getData($query);

		if (!$result) {
			return $result;
		}

		$users = [];

		foreach ($result as $row) {
			$user = PP::user($row);
			$users[] = $user;
		}

		return $users;
	}

	/**
	 * Builds FROM tables list for the query
	 */
	protected function _buildQueryFrom(&$query)
	{
		// Xi: ticket #1789
		// to get multiple records
		if(!is_numeric($this->recordId)){
			$query->from('`#__users` AS tbl');

			return;
		}

		// Xi: ticket #1789
		// To get one user we have to write query like this
		//SELECT tbl.*
		//FROM (
		//	SELECT tmpjuser.*, ppuser.*
		//	FROM
		//		(SELECT  joomla_user_fields) AS tmpjuser
		//	LEFT JOIN
		//		(SELECT  payplans_user_fields) AS ppuser
		//	ON (tmpjuser.id = ppuser.user_id)
		//
		//) AS tbl

		$q1 = $this->getQuery(true);
		$q2 = $this->getQuery(true);
		$q3 = $this->getQuery(true);

		$q1->select(' j.`id` AS user_id')
		   ->select(' j.`name` AS realname')
		   ->select(' j.`username` AS username')
		   ->select(' j.`email` As email')
		   ->select(' j.`registerDate` AS registerDate')
		   ->select(' j.`lastvisitDate` AS lastvisitDate')
		   ->from('`#__users` AS j')
		   ->where('j.`id`='.$this->recordId);

		$q2->select(' t.`address` ')
			  ->select(' t.`state` ')
			  ->select(' t.`city` ')
			  ->select(' t.`country` ')
			  ->select(' t.`zipcode` ')
			  ->select(' t.`preference` ')
			  ->select(' t.`params` ')
			  ->select('t.`user_id` AS puser_id')
			  ->from('#__payplans_user AS t')
			  ->where('t.`user_id`='.$this->recordId);

		 $q3->select('tmpjuser.*, ppuser.*')
			->from('('.$q1.') AS tmpjuser')
			->leftJoin('('.$q2.') AS ppuser ON (tmpjuser.user_id = ppuser.puser_id)');

		$query->from('('.$q3.') AS tbl');

	}


	/**
	 * Builds a generic ORDER BY clasue based on the model's state
	 */
	// Xi: ticket #1789
	protected function _buildQueryOrder(&$query)
	{

		$order = $this->getState('filter_order');
		if(!isset($order) || empty($order)){
			$order = 'id';
		}

		$direction  = strtoupper($this->getState('filter_order_Dir'));
		if(!isset($direction) || empty($direction)){
			$direction = "ASC";
		}

		// if there are multiple records to fetch
		// then we have only one table which is joomla_user table
		// XiTODO: alias
		if(!is_numeric($this->recordId)){
			return $query->order("$order $direction");
		}
	}


	protected function _buildQueryFields(&$query)
	{
		// when we collect multiple records of users then we use only joomla_user table.
		// There are various functions which are working on name of alias.
		//
		if(!is_numeric($this->recordId)){
			$query->select(' tbl.`id` AS user_id')
				   ->select(' tbl.`name` AS realname')
				   ->select(' tbl.`username` AS username')
				   ->select(' tbl.`email` AS email')
				   ->select(' tbl.`registerDate` AS registerDate')
				   ->select(' tbl.`lastvisitDate` AS lastvisitDate');
				   return ;
		}

		$query->select('tbl.*');
	}

	//added filter for user so it is necessary to override _buildQueryFilter function here
	//so that proper query can be build corresponding to applied filter
	protected function _buildQueryFilter(&$query, $key, $value,&$temp)
	{
		// Only add filter if we are working on bulk records
		if($this->getId()){
			return $this;
		}

		if (isset($this->filterMatchOpeartor[$key])) {
			throw new Exception('OPERATOR FOR $key IS NOT AVAILABLE FOR FILTER');	
		}
		
		if (is_array($value)) {
			throw new Exception(JText::_('COM_PAYPLANS_VALUE_FOR_FILTERS_MUST_BE_AN_ARRAY'));
		}

		$cloneOP = $this->filterMatchOpeartor[$key];
		$cloneValue = $value;

		while (!empty($cloneValue) && !empty($cloneOP)){
			$op  = array_shift($cloneOP);
			$val = array_shift($cloneValue);

			// discard empty values
			if (!isset($val) || '' == PPJString::trim($val))
				continue;

			if (stristr($key,"cross_")) {
				//seprate the variables
				$key = str_replace("cross_", "",$key); 			// key = cross_filtertable_fieldname
				$crosstable = strtok($key,'_');				  			// crosstable = filtertable
				$key = str_replace("{$crosstable}_", "",$key); 	// key = fieldname

				if (isset($this->crossTableNetwork[$crosstable])) {

					$travesingTables = $this->crossTableNetwork[$crosstable];
					$prevTable = "tbl";
					foreach ($travesingTables as $traversed) {
						
						if (!isset($temp["{$prevTable}-{$traversed}"])) {
							$temp["{$prevTable}-{$traversed}"] = "";
						}

						if ($crosstable == $traversed) {	
							$corssValue = "'$val'";
							
							if (PPJString::strtoupper($op) == 'LIKE') {
								$corssValue = "'%{$val}%'";

							}

						$temp["{$prevTable}-{$traversed}"] .= " AND cross_{$crosstable}.`$key` $op $corssValue ";
						$prevTable = $traversed;
						continue;

						}

						$temp["{$prevTable}-{$traversed}"] .= "";
						$prevTable = $traversed;
					}
				}
				continue;
				//CROSS FILTERING ENDS HERE
			}

			if (PPJString::strtoupper($op) != 'LIKE') {
				if ($key == 'usertype') {
					//this subquery will fetch all the users with the desired usertype
					// Xi: ticket #1789
					$query->where("  `tbl`.`id` IN( SELECT map.`user_id`
									FROM `#__usergroups` as groups, `#__user_usergroup_map` as map
									WHERE ( map.group_id = groups.id AND groups.title = '$val')) ");
						continue;
					}

				$query->where("`$key` $op '$val'");
				continue;
			}

			// filter according to username, realname and email
			if ($key == 'username') {
				$nameKey = 'realname';

				if (!is_numeric($this->recordId)) {
					$nameKey = 'name';
				}

				$query->where("( `$key` $op '%{$val}%' || `$nameKey` $op '%{$val}%' || `email` $op '%{$val}%' )");
			} else {
				$query->where("`$key` $op '%{$val}%'");
			}
		}
	}

	public function save($data, $pk = null, $new = false)
	{
		$new = $this->getTable()->load($pk)? false : true;
		return parent::save($data, $pk, $new);
	}

	/**
	 * Logs a user into the site
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function login($username, $password)
	{
		$credentials = [
			'username' => $username,
			'password' => $password
		];

		$app = JFactory::getApplication();
		$state = $app->login($credentials);

		if (!$state) {
			$this->setError('JGLOBAL_AUTH_INVALID_PASS');
			return false;
		}

		return $state;
	}

	public function loadRecords($queryFilters = [], $queryClean = [], $emptyRecord = false, $orderby = null)
	{
		//it is required to decide which query to execute in FROM
		// for single record or multiple record
		if (!empty($queryFilters)) {
			foreach ($queryFilters as $key => $value) {
				// Xi: ticket #1789
				if ($key === 'id') {
					$this->recordId = $value;
				}
			}
		}

		$query = $this->getQuery();

		//there might be no table and no query at all
		if ($query === null )
			return null;

		//Support Query Filters, and query cleanup
		$tmpQuery = clone ($query);

		foreach ($queryClean as $clean) {
			$tmpQuery->clear(PPJString::strtolower($clean));
		}

		foreach ($queryFilters as $key => $value) {
			// Xi: ticket #1789
			// Imp : Do NOT update 'id' in case of multiple record, as it will get `id`
			if (is_numeric($this->recordId)) {
				// for single records
				//support id too, replace with actual name of key
				$key = ($key === 'id') ? $this->getTable()->getKeyName() : $key;
			}

			// V.IMP : if any filter sends user_id or realname statically
			// then convert user_id into id and realname into name
			if (!is_numeric($this->recordId)) {
				$key = ($key === 'user_id') ? 'id' : $key;
				$key = ($key === 'realname') ? 'name' : $key;
			}

			// only one condition for this key
			if (is_array($value) == false) {
				$tmpQuery->where("`tbl`.`$key` =".$this->_db->Quote($value));
				continue;
			}

			// multiple keys are there
			foreach ($value as $condition) {

				// not properly formatted
				if (is_array($condition) == false) {
					continue;
				}

				// first value is condition, second one is value
				list($operator, $val) = $condition;
				$tmpQuery->where("`tbl`.`$key` $operator ".$val);
			}

		}

		//we want returned record indexed by columns
		$this->_recordlist = $this->db->setQuery($tmpQuery)->loadObjectList($this->getTable()->getKeyName());

		//handle if some one required empty records, only if query records were null
		if ($emptyRecord && empty($this->_recordlist)) {
			$this->_recordlist = $this->getEmptyRecord();
		}

		$data = $this->_recordlist;

		//get usertype of the user and append it with the data
		$this->getUsertype($data);

		//after executing query clean cache
		//Ticket #2956
		$this->recordId = null;

		return $data;
	}

	public function getQuery()
	{
		//create a new query
		$this->_query = $this->db->getQuery(true);

		// Query builder will ensure the query building process
		// can be overridden by child class
		if ($this->_buildQuery($this->_query)) {
			return $this->_query;
		}

		//in case of errors return null
		//XITODO : Generate a 500 Error Here
		return null;
	}

	protected function getUsertype(&$users)
	{
		$user_ids = array_keys($users);

		//when there is nothing in users
		if(empty($user_ids)){
			return $users;
		}

		$query = $this->db->getQuery(true);

		//if only single record exists
		if (count($users) == 1) {
			$query->where(' usergroupmap.user_id = '.array_shift($user_ids));
		}

		else {
			//in case of multiple users, user_usergroup_map table
			//contains multiple records for a single user thats why
			//group by with user_id is required
			$query->where(' usergroupmap.user_id IN ('.implode(',', $user_ids).') ')->group(' usergroupmap.user_id ');
		}

		$query->select('group_concat(groups.`title`) as usertype, usergroupmap.`user_id` as user_id')
			  ->from('`#__user_usergroup_map` as usergroupmap , `#__usergroups` as groups')
			  ->where(' usergroupmap.group_id = groups.id ');

		$userGroups[] = $this->db->setQuery($query)->loadObjectList('user_id');

		$groups = array_shift($userGroups);
		foreach ($users as $user) {
			$user->usertype = $groups[$user->user_id]->usertype;
		}
	}

	/**
	 * Retrieves a list of user data based on the given ids.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getUsersMeta($ids = [])
	{
		$loaded = [];
		$new = [];

		if (!empty($ids)) {

			foreach ($ids as $id) {

				if (is_numeric($id)) {

					if (isset(self::$loadedUsers[$id])) {
						$loaded[] = self::$loadedUsers[$id];
					} else {
						$new[] = $id;
					}
				}
			}
		}

		// Only fetch for new items that isn't stored on the cache
		if ($new) {

			foreach ($new as $id) {
				self::$loadedUsers[$id] = false;
			}

			$db = PP::db();

			$query = "SELECT a.*";
			$query .= ", b.`params` as `parameters`";
			$query .= ", b.`address`, b.`state`, b.`city`, b.`country`, b.`zipcode`, b.`preference`";


			$query .= " FROM `#__users` as a";
			$query .= " LEFT JOIN `#__payplans_user` as b ON a.`id` = b.`user_id`";

			if (count($new) > 1) {
				$query .= " WHERE a.`id` IN (" . (implode(', ', $new)) . ")";
				// $sql->where('a.id' , $new , 'IN');
			} else {
				$query .= " WHERE a.`id` = " . $new[0];
				// $sql->where('a.id' , $new[0]);
			}

			if (!empty($this->ordering)) {
				$query .= " ORDER BY " . $this->ordering['ordering'] . " " . $this->ordering['direction'];
			}

			// to compatible with aggregation function the 'ONLY_FULL_GROUP_BY' standard.
			// $sql->group('a.id');
			$query .= " GROUP BY a.`id`";

			$db->setQuery($query);

			$users = $db->loadObjectList();

			if ($users) {

				// cache user metas
				// ES::cache()->cacheUsersMeta($users);

				foreach ($users as $user) {
					$loaded[] = $user;
					self::$loadedUsers[$user->id] = $user;
				}
			}
		}

		$return = [];

		if ($loaded) {

			foreach ($loaded as $user) {
				if (isset($user->id)) {
					$return[] = $user;
				}
			}
		}

		return $return;
	}

	/**
	 * Finds dummy user id to associate invoice with for simplified checkout experience
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getDummyUser()
	{
		$db = PP::db();
		$query = [];

		$query[] = 'SELECT * FROM ' . $db->qn('#__users');
		$query[] = 'WHERE ' . $db->qn('username') . '=' . $db->Quote('Not_Registered');

		$db->setQuery($query);
		$dummy = $db->loadObject();

		return $dummy;
	}

	/**
	 * Retrieve record to export to CSV
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function getDataToExport($options = [])
	{
		$config = PP::config();
		$db = PP::db();

		$query = [];
		$query[] = "SELECT " . $db->nameQuote('a.id') . ", " . $db->nameQuote('a.name') . ", " . $db->nameQuote('a.username') . ", " . $db->nameQuote('a.email') . ", " . $db->nameQuote('a.registerDate') .", " . $db->nameQuote('a.lastvisitDate') . ", " . $db->nameQuote('u.params');

		if ($config->get('show_address')) {
			$query[] = ", ". $db->nameQuote('u.address') . ", " . $db->nameQuote('u.city') . ", " . $db->nameQuote('u.state') . ", " . $db->nameQuote('u.zipcode');
		}

		$query[] = ", " . $db->nameQuote('u.country');

		if ($config->get('show_billing_details')) {
			$query[] = ", " . $db->nameQuote('u.preference');
		}

		$query[] = "from `#__users` as a";
		$query[] = "inner join `#__payplans_subscription` as s on s.user_id = a.id";
		$query[] = "inner join `#__payplans_user` as u on u.user_id = a.id";

		// exclude not_registred user
		$query[] = "AND a.username != 'Not_Registered'";

		if (isset($options['plans']) && $options['plans']) {
			$query[] = "AND s.plan_id in (".implode(',', $options['plans']).")";
		}

		if (isset($options['status']) && $options['status']) {
			if (!is_array($options['status'])) {
				$options['status'] = (array) $options['status'];
			}

			$query[] = "AND s.status in (".implode(',',$options['status']).")";
		}

		if (isset($options['dateFrom']) && $options['dateFrom']) {
			$query[] = "AND s.expiration_date >= " . $db->Quote($options['dateFrom']);
		}

		if (isset($options['dateTo']) && $options['dateTo']) {
			$query[] = "AND s.expiration_date <= " . $db->Quote($options['dateTo']);
		}

		$query[] = "group by a.id LIMIT " . $options['limit'];

		$query = implode(' ', $query);
		$db->setQuery($query);
		$results = $db->loadObjectList();

		// Get the user custom details
		$customDetailsmodel = PP::model('CustomDetails');
		$customDetails = $customDetailsmodel->getCustomDetailsFields('user');

		foreach ($results as $result) {

			// Get user country
			if (isset($result->country)) {
				$result->country = PP::getCountryNameById($result->country);
			}

			// Set user Billing details on export data
			if ($config->get('show_billing_details')) {
				$billingDetailKeys = ['business_name', 'tin', 'business_address', 'business_city', 'business_state', 'business_zip', 'shipping_address'];

				$billingDetails = json_decode($result->preference, true);

				foreach ($billingDetailKeys as $key) {

					if ($key === 'business_country') {
						$result->$key = isset($billingDetails[$key]) ? PP::getCountryNameById($billingDetails[$key]) : '';
					} else {
						$result->$key = isset($billingDetails[$key]) ? $billingDetails[$key] : ''	;
					}
					
				}
			}

			// Set user custom details on data
			if ($customDetails) {

				if ($result->params) {
					$params = json_decode($result->params, true);
					
					foreach ($customDetails as $key => $value) {
						
						if (!empty($value['options'])) {

							$result->$key = isset($params[$key]) ? (isset($value['options'][$params[$key]]) ? $value['options'][$params[$key]] : '') : '';

						} else {
							$result->$key = isset($params[$key]) ? $params[$key] : '';	
						}	
					}
				}
			}

			unset($result->params);
			unset($result->preference);
		}

		return $results;
	}

	/**
	 * Determines if e-mail verifications are required
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function requireEmailVerification(PPPlan $plan)
	{
		$config = PP::config();
		$type = $config->get('account_verification');

		if ($type === 'user' || $type === 'admin') {
			return true;
		}

		return false;
	}

	/**
	 * Determines if user need to have a active subscription
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function requireActiveSubscription()
	{
		$config = PP::config();
		$type = $config->get('account_verification');

		if ($type === 'active_subscription') {
			return true;
		}

		return false;
	}

	/**
	 * Retrieve user id from username
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getUserIdFromUsername($username)
	{
		$query = 'SELECT `id` FROM `#__users`';
		$query .= ' WHERE `username` = ' . $this->db->Quote($username);

		$this->db->setQuery($query);
		$userId = $this->db->loadResult();

		return $userId;
	}

	/**
	 * Retrieve user id from email
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getUserIdFromEmail($email)
	{
		$query = 'SELECT `id` FROM `#__users`';
		$query .= ' WHERE `email` = ' . $this->db->Quote($email);

		$this->db->setQuery($query);
		$userId = $this->db->loadResult();

		return $userId;
	}

	/**
	 * Retrieve user name from email
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getUserNameFromEmail($email)
	{
		$query = 'SELECT `username` FROM `#__users`';
		$query .= ' WHERE `email` = ' . $this->db->Quote($email);

		$this->db->setQuery($query);
		$userName = $this->db->loadResult();

		return $userName;
	}

	/**
	 * Validates an e-mail address
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function validateEmail($email, $excludeUserId = null)
	{
		jimport('joomla.mail.helper');

		if (!JMailHelper::isEmailAddress($email)) {
			$this->setError(JText::_('COM_PP_INVALID_EMAIL_ADDRESS'));
			return false;
		}

		$regex = '/^([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]+)$/i';

		// Ensure that this is a valid email address as there is a Joomla bug where e.g. john@gmail counted as a valid email
		if (!preg_match($regex, $email)) {
			$this->setError(JText::_('COM_PP_INVALID_EMAIL_ADDRESS'));
			return false;
		}

		// Check if there are any same e-mail address
		$db = PP::db();

		$query = [];
		$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__users');
		$query[] = 'WHERE `email`=' . $db->Quote($email);

		if ($excludeUserId) {
			$query[] = 'AND `id` !=' . $db->Quote($excludeUserId);
		}

		$db->setQuery($query);
		$exists = $db->loadResult() > 0;

		if ($exists) {
			$this->setError(JText::_('COM_PP_EMAIL_ALREADY_REGISTERED'));
			return false;
		}

		return true;
	}

	/**
	 * Validates a username
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function validateUsername($username, $excludeUserId = null)
	{
		// Check if there are any same e-mail address
		$db = PP::db();

		$query = [];
		$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__users');
		$query[] = 'WHERE `username`=' . $db->Quote($username);

		if ($excludeUserId) {
			$query[] = 'AND `id` !=' . $db->Quote($excludeUserId);
		}

		$db->setQuery($query);
		$exists = $db->loadResult() > 0;

		if ($exists) {
			$this->setError(JText::_('COM_PP_USERNAME_UNAVAILABLE'));
			return false;
		}
		
		return true;
	}

	/**
	 * Get active subscription from given date range
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getRegistrationStat(PPdate $firstDate, PPDate $lastDate)
	{
		$db = $this->db;

		$rules = JAccess::getAssetRules(1);
		$groups = $rules->getData();
		$adminGroups = array_keys($groups['core.admin']->getData());

		$query = 'SELECT count(*) as count, date(registerDate) FROM `#__users`';
		$query .= ' WHERE `registerDate` >= ' . $db->Quote($firstDate->toMySQL());
		$query .= ' AND `registerDate` <= ' . $db->Quote($lastDate->toMySQL());
		
		// Exclude not_registred and admin users
		$query .= ' AND `username` != "Not_Registered"';
		$query .= 'AND  `id` NOT IN (';
		$query .= 'SELECT `user_id` FROM `#__user_usergroup_map` WHERE `group_id`  IN(' . implode(',', $adminGroups) . ')';
		$query .= ')';

		$query .= ' GROUP BY date(`registerDate`)';

		$db->setQuery($query);

		return $db->loadObjectList('date(registerDate)');
	}
	
	/**
	 * Import User subscription
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function importUser($data, $fields, $planId, $importSubscriptionStatus = '', $importSubscriptionStartDate = '', $importSubscriptionExpirationDate = '', $importSubscriptionNote = '')
	{
		// Construct the data
		$formattedData = array();

		foreach ($fields as $key => $field) {

			// There might an instance where certain column is missing in the csv
			if (!isset($data[$key])) {
				continue;
			}

			// If this is not an integer, we know this is joomla column
			if ($field && !(int) $field) {
				$formattedData[$field] = $data[$key];
				continue;
			}
		}

		$return = new stdClass();
		$return->state = false;
		$return->newUser = false;
		$return->message = '';

		$importUsername = $this->normalize($formattedData, 'username', '');
		$importFullname = $this->normalize($formattedData, 'fullname', '');
		$importEmail = $this->normalize($formattedData, 'email', '');
		$importPassword = $this->normalize($formattedData, 'password', '');

		$businessName = $this->normalize($formattedData, 'business_name', '');
		$businessTin = $this->normalize($formattedData, 'tin', '');
		$businessAddress = $this->normalize($formattedData, 'business_address', '');
		$businessCity = $this->normalize($formattedData, 'business_city', '');
		$businessState = $this->normalize($formattedData, 'business_state', '');
		$businessZip = $this->normalize($formattedData, 'business_zip', '');
		$businessCountry = $this->normalize($formattedData, 'business_country', '');

		// Retrieve user id from email and use to determine whether this email account register on the site yet
		$userId = $this->getUserIdFromEmail($importEmail);

		// import user data
		$userData = [];

		// user preferences data e.g. business details
		$preferences = [];
		$preferences['business_name'] = $businessName;
		$preferences['tin'] = $businessTin;
		$preferences['business_address'] = $businessAddress;
		$preferences['business_city'] = $businessCity;
		$preferences['business_state'] = $businessState;
		$preferences['business_zip'] = $businessZip;

		// Convert the country title to id
		if ($businessCountry) {
			$businessCountry = PP::getCountryIdByTitle($businessCountry);
		}

		$preferences['business_country'] = $businessCountry;

		// Need to create new user account first if the user don't have an account on the site yet
		if (!$userId) {

			$userData['username'] = $importUsername;
			$userData['name'] = $importFullname;
			$userData['email'] = $importEmail;
			$userData['password'] = $importPassword;
			$userData['clear_password'] = $importPassword;

			// Retrieve the default new user registration user group
			$usersConfig = JComponentHelper::getParams('com_users');
			$group = [$usersConfig->get('new_usertype')];

			$userData['groups'] = $group;

			$user = new JUser;
			$state = $user->bind($userData);

			if (!$state) {
				$return->message = JText::sprintf('COM_PP_USER_IMPORT_FAILED_USERS', $importEmail, 'User bind issue');
				return $return;
			}

			// Load the users plugin group.
			JPluginHelper::importPlugin('user');

			$state = $user->save();

			if (!$state) {
				$return->message = JText::sprintf('COM_PP_USER_IMPORT_FAILED_USERS', $importEmail, $user->getError());
				return $return;
			}

			$userId = $user->id;
			$return->newUser = true;			
		}

		// Create a new record for PayPlans to store any other params
		$userTbl = PP::table('User');
		$userTbl->load([
			'user_id' => $userId
		]);

		$userAddress = $this->normalize($formattedData, 'address', '');
		$userCity = $this->normalize($formattedData, 'city', '');
		$userState = $this->normalize($formattedData, 'state', '');
		$userZip = $this->normalize($formattedData, 'zip', '');
		$userCountry = $this->normalize($formattedData, 'country', 0);

		// If the user country doesn't have any data, then retrieve it from the business country data
		if (!$userCountry && $businessCountry) {
			$userCountry = $businessCountry;
		}

		$userTbl->address = $userAddress;
		$userTbl->city = $userCity;
		$userTbl->state = $userState;
		$userTbl->zipcode = $userZip;
		$userTbl->country = $userCountry;

		// update business custom field data
		$userTbl->preference = json_encode($preferences);
		$state = $userTbl->store();
		
		if (!$state) {
			$return->message = JText::sprintf('COM_PP_USER_IMPORT_FAILED_USERS', $importEmail, $userTbl->getError());
			return $return;
		}

		// assign plan for the user now
		// Retrieve the subscription start and expiration date
		$csvSubscriptionDate = $this->normalize($formattedData, 'subscription_date', '');
		$csvSubscriptionExpirationDate = $this->normalize($formattedData, 'subscription_exp_date', '');

		// If the CSV file doesn't including for those subscription start and expiration date then we need to check for the optional option
		if (!$csvSubscriptionDate && $importSubscriptionStartDate) {
			$csvSubscriptionDate = $importSubscriptionStartDate;
		}

		if (!$csvSubscriptionExpirationDate && $importSubscriptionExpirationDate) {
			$csvSubscriptionExpirationDate = $importSubscriptionExpirationDate;
		}

		$csvSubscriptionDate = PP::formatDate($csvSubscriptionDate);
		$csvSubscriptionExpirationDate = PP::formatDate($csvSubscriptionExpirationDate);

		// If subscription dates not exist then do nothing
		if (!$csvSubscriptionDate) {
			$return->message = JText::_('COM_PP_USER_IMPORT_FAILED_INVALID_SUBSCRIPTION_START_DATE_FORMAT');
			return $return;
		}

		if (!$csvSubscriptionExpirationDate) {
			$return->message = JText::_('COM_PP_USER_IMPORT_FAILED_INVALID_SUBSCRIPTION_EXPIRATION_DATE_FORMAT');
			return $return;
		}

		if (!$csvSubscriptionDate && !$csvSubscriptionExpirationDate) {
			$return->message = JText::_('COM_PP_USER_IMPORT_FAILED_MISSING_SUBSCRIPTION_DATE');
			return $return;
		}

		//  If subscription expiration date is greater than the subscription date then skip this
		if (($csvSubscriptionDate && $csvSubscriptionExpirationDate) && ($csvSubscriptionExpirationDate->toMySQL() < $csvSubscriptionDate->toMySQL())) {
			$return->message = JText::_('COM_PP_USER_IMPORT_FAILED_INVALID_SUBSCRIPTION_STARTEND_DATE');
			return $return;
		}

		$plan = PP::plan($planId);

		// Create subscription
		$order = $plan->subscribe($userId);
		$state = $plan->save();

		if (!$state) {
			$return->message = JText::sprintf('COM_PP_USER_IMPORT_FAILED_DURING_SAVE_PLAN', $plan->getError()->text);
			return $return;
		}

		// Add invoice
		$invoice = $order->createInvoice();

		// Retrieve the subscription status
		$csvSubscriptionStatus = $this->normalize($formattedData, 'subscription_status', 'None');

		// If the CSV file doesn't including for the subscription status column then we need to check for the optional option
		if ($importSubscriptionStatus != 'inherit') {
			$csvSubscriptionStatus = $importSubscriptionStatus;
		}

		if (in_array($csvSubscriptionStatus, array('Active', 'Expired', 'Inactive'))) {
			// Apply 100% discount on invoice
			$modifier = PP::modifier();

			$modifier->message = 'COM_PAYPLANS_APPLY_PLAN_ON_USER_MESSAGE';
			$modifier->invoice_id = $invoice->getId();
			$modifier->user_id = $invoice->getBuyer()->getId();
			$modifier->type = 'apply_plan';
			$modifier->amount = -100;
			$modifier->percentage = true;
			$modifier->frequency = PP_MODIFIER_FREQUENCY_ONE_TIME;
			$modifier->serial = PP_MODIFIER_FIXED_DISCOUNT;
			$modifier->save();

			$invoice->refresh()->save();

			// Create a transaction with 0 amount since the plan is applied by the admin
			$transaction = PP::transaction();
			$transaction->user_id = $invoice->getBuyer()->getId();
			$transaction->invoice_id = $invoice->getId();
			$transaction->amount = $invoice->getTotal();
			$transaction->message = 'COM_PAYPLANS_TRANSACTION_CREATED_FOR_APPLY_PLAN_TO_USER';
			$transaction->save();

		} else {
			$invoice->confirm(0);
		}

		// Retrieve the subscription from the order
		$subscription = $order->getSubscription();

		// Load the subscription object
		$subscription = PP::subscription($subscription->getId());

		// Update the subscription date
		if ($csvSubscriptionDate) {
			$subscription->setSubscriptionDate($csvSubscriptionDate);	
		}

		// Update the subscription expiration date
		if ($csvSubscriptionExpirationDate) {
			$subscription->setExpirationDate($csvSubscriptionExpirationDate);
		}

		// Retrieve the subscription note
		$csvSubscriptionNote = $this->normalize($formattedData, 'subscription_notes', '');

		// If the CSV file doesn't including for the subscription note column then we need to check for the optional option
		// If the subscription note field has value, then we must always respect this value.
		if (!$csvSubscriptionNote || $importSubscriptionNote) {
			$csvSubscriptionNote = $importSubscriptionNote;
		}

		if ($csvSubscriptionNote) {
			$params = $subscription->getParams();
			$params->set('notes', $csvSubscriptionNote);
			
			$subscription->setParams($params);			
		}

		// Update subscription status
		if ($csvSubscriptionStatus === 'Expired') {
			$subscription->setStatus(PP_SUBSCRIPTION_EXPIRED);
		}

		// If subscription inactive means it's hold
		if ($csvSubscriptionStatus === 'Inactive') {
			$subscription->setStatus(PP_SUBSCRIPTION_HOLD);
		}

		$state = $subscription->save();

		if (!$state) {
			$return->message = JText::_('COM_PP_USER_IMPORT_FAILED_DURING_UPDATE_SUBSCRIPTION');
			return $return;
		}

		$importMsg = $return->newUser ? 'COM_PP_USER_IMPORT_SUCCESS_IMPORT_NEWUSER' : 'COM_PP_USER_IMPORT_SUCCESS_IMPORT_EXISTSUSER';
		$return->message = JText::sprintf($importMsg, $importEmail);
		$return->state = true;

		return $return;
	}
}

class PayplansModelformUser extends PayPlansModelform {}


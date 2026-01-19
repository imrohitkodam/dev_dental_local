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

PP::import('site:/views/views');

class PayplansViewUser extends PayPlansSiteView
{
	public function display($tpl = null)
	{
		JHtml::_('behavior.core');

		ES::initialize();

		// Get User list selection option
		$listOption = $this->input->get('listOption', 'everyone', 'default');

		// Get logged in user
		$user = PP::user();

		if (!$user->getId()) {
			$listOption = "everyone";
		}

		$model = new PayplansModelFriendUsers();
		$model->initStates();

		// get users
		$users = $model->getItems($listOption);

		// get pagination
		$pagination = $model->getPagination();

		$states = $this->getStates(['search', 'plan_id', 'limit', 'ordering', 'direction'], $model);

		$this->set('states', $states);
		$this->set('users', $users);
		$this->set('pagination', $pagination);

		return parent::display('site/user/default/default');
	}

	/**
	 * This is only used for the model on the back end to retrieve available states
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getStates($availableStates = [], $model = null)
	{
		if (is_null($model)) {
			$model = PP::model($this->getName());
		}

		$states = new stdClass();

		foreach ($availableStates as $state) {
			$states->$state = $model->getState($state);
		}

		return $states;
	}
}


PP::import('admin:/includes/model');

class PayplansModelFriendUsers extends PayPlansModel
{
	protected $recordId ;
	public $ordering = null;

	public function __construct()
	{
		parent::__construct('FriendUsers');
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

		// Override ordering column to use id by default
		$ordering = $this->getUserStateFromRequest('ordering', 'id', 'string');
		$this->setState('ordering', $ordering);
	}

	public function getItems($listOption = 'everyone') 
	{
		$user = PP::user();

		$db = PP::db();

		$search = $this->getState('search');

		$wheres = [];

		$query = [];
		$query[] = 'SELECT a.`id` AS `user_id` FROM ' . $db->qn('#__users') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->qn('#__payplans_user') . ' AS b';
		$query[] = 'ON a.' . $db->qn('id') . ' = b.' . $db->qn('user_id');


		// filters es friends
		if ($listOption == 'friends') {

			// reset the variable
			$query = [];
			$query[] = 'SELECT a.`id` AS `user_id` FROM `#__users` AS a';
			$query[] = ' INNER JOIN `#__social_friends` as f ON a.id = IF(f.`target_id` = ' . $db->Quote($user->getId()) . ', f.`actor_id`, f.`target_id`) and f.`state` = 1';
		}

		// filters es followers
		if ($listOption == 'followers') {

			// reset the variable
			$query = [];
			$query[] = 'SELECT a.`id` AS `user_id` FROM `#__users` AS a';
			$query[] = ' INNER JOIN `#__social_subscriptions` as f ON a.`id` = f.`user_id` and f.uid = ' . $db->Quote($user->getId());
		}

		// we do not want blocked users
		$wheres[] = 'a.`block` = 0';

		if ($search) {
			$search = PPJString::strtolower($search);

			$searchQuery = array();
			$searchQuery[] = 'LOWER(a.' . $db->qn('username') . ') LIKE ' . $db->Quote('%' . $search . '%');
			$searchQuery[] = 'LOWER(a.' . $db->qn('name') . ') LIKE ' . $db->Quote('%' . $search . '%');
			$searchQuery[] = 'LOWER(a.' . $db->qn('email') . ') LIKE ' . $db->Quote('%' . $search . '%');
			$searchQuery[] = 'LOWER(a.' . $db->qn('id') . ') LIKE ' . $db->Quote('%' . $search . '%');
			
			$wheres[] = implode(' OR ', $searchQuery);
		}


		$where = '';
		if (count($wheres) > 0) {
			$where = ' where ';
			$where .= (count($wheres) == 1) ? $wheres[0] : implode(' and ', $wheres);
		}

		$query = implode(' ', $query);
		$query .= $where;

		$this->setTotal($query, true);

		// Ordering
		$ordering = $this->getState('ordering');
		$direction = $this->getState('direction');

		$orderingQuery = ' ORDER BY a.' . $db->qn('id') . ' DESC';

		if ($ordering && $direction) {
			$orderingQuery = ' ORDER BY a.' . $db->qn($ordering) . ' ' . $direction;
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


}


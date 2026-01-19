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

class PayPlansViewPlan extends PayPlansAdminView
{
	public function __construct()
	{
		parent::__construct();
		
		$this->checkAccess('plans');
	}

	public function display($tpl = null)
	{
		$this->heading('Plans');

		$this->addHelpButton('https://stackideas.com/docs/payplans/administrators/plans/plan-creation');

		JToolbarHelper::addNew('plan.new');
		JToolbarHelper::publishList('plan.publish');
		JToolbarHelper::unpublishList('plan.unpublish');
		JToolbarHelper::deleteList(JText::_('COM_PP_PLAN_DELETE_PLANS_CONFIRMATION'), 'plan.delete');

		JToolbarHelper::custom('plan.copy', 'copy.png', 'copy_f2.png', 'COM_PAYPLANS_TOOLBAR_COPY', true);

		$model = PP::model('Plan', [
			'initState' => true
		]);

		$rows = $model->getItems();

		$plans = [];

		if ($rows) {
			foreach ($rows as $row) {

				$plan = PP::plan();
				$plan->bind($row);

				$plan->currency = $plan->getCurrency();
				$plan->price = $plan->getPrice();

				$plan->groups = [];

				if ($this->config->get('useGroupsForPlan')) {
					$groups = $plan->getGroups();

					if ($groups) {
						foreach ($groups as $groupId) {
							$plan->groups[] = PP::group($groupId);
						}
					}
				}

				$plans[] = $plan;
			}
		}

		$statLib = PP::statistics()->getAdapter('plan');
		$stats = $statLib->getSubscriptionStats();

		$pagination = $model->getPagination();
		$states = $this->getStates([
			'search', 
			'published', 
			'visible', 
			'ordering', 
			'direction', 
			'limit', 
			'group_id'
		]);

		if ($states->visible === '') {
			$states->visible = 'all';
		}

		if ($states->published === '') {
			$states->published = 'all';
		}

		// Save ordering
		$saveOrder = $states->ordering == 'ordering' && $states->direction == 'asc';

		$this->set('states', $states);
		$this->set('plans', $plans);
		$this->set('pagination', $pagination);
		$this->set('stats', $stats);
		$this->set('saveOrder', $saveOrder);

		return parent::display('plan/default/default');
	}

	/**
	 * Activates the current subscription
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function form($tpl = null)
	{
		$this->addHelpButton('https://stackideas.com/docs/payplans/administrators/plans/plan-creation');
		
		$model = PP::model('Plan');
		$renderEditor = $this->config->get('layout_plan_description_use_editor');

		$planId = $this->input->get('id', null, 'int');
		$planId = ($planId === null) ? $model->getState('id') : $planId;

		$activeTab = $this->input->get('active', 'details', 'word');

		// editing
		JToolbarHelper::apply('plan.apply');
		JToolbarHelper::save('plan.save');
		JToolbarHelper::save2new('plan.saveNew');
		JToolbarHelper::cancel('plan.cancel');

		// setup heading.
		if ($planId) {
			$this->heading('EDIT_PLAN');
		} else {
			$this->heading('NEW_PLAN');
		}

		$editor = PPCompat::getEditor();
		$plan = PP::plan($planId);

		//display all core apps
		$appModel = PP::model('App');
		$apps = $appModel->loadRecords();

		$planGroups = [];

		if ($this->config->get('useGroupsForPlan') && $plan->getId()) {
			$planGroups = $plan->getGroups();
		}

		// Retrieve a list of log for this subscription
		$logModel = PP::model('Log');
		$options = [
			'object_id' => $planId, 
			'class' => 'plan', 
			'level' => 'all'
		];

		$logs = $logModel->getItems($options);
		$pagination = $logModel->getPagination();

		$expirationTypes = $this->getExpirationTypes();
		$badgePositions = $this->getBadgePositions();
		$childPlansDisplay = $this->getDisplayChildPlanOn();

		// used in logs theme files
		$renderFilterBar = false;
		$ordering = $logModel->getState('ordering');
		$filter_order_Dir = $logModel->getState('filter_order_Dir');

		$planPermission = $plan->getPlanPermission();

		$this->set('activeTab', $activeTab);
		$this->set('editor', $editor);
		$this->set('expirationTypes', $expirationTypes);
		$this->set('badgePositions', $badgePositions);
		$this->set('childPlansDisplay', $childPlansDisplay);
		$this->set('plan', $plan);
		$this->set('planGroups', $planGroups);
		$this->set('logs', $logs);
		$this->set('pagination', $pagination);
		$this->set('renderFilterBar', $renderFilterBar);
		$this->set('filter_order', $ordering);
		$this->set('filter_order_Dir', $filter_order_Dir);
		$this->set('renderEditor', $renderEditor);
		$this->set('isEdit', $planId);
		$this->set('planPermission', $planPermission);

		return parent::display('plan/form/default');
	}


	/**
	 * get the available expiration types for plan
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	private function getExpirationTypes()
	{
		$data = [
			'forever' => [
				'title' => 'COM_PP_PLAN_TIME_EXPIRATION_FOREVER', 
				'value' => 'forever', 
				'for' => 'data-expire-forever'
			],
			'fixed' => [
				'title' => 'COM_PP_PLAN_TIME_EXPIRATION_FIXED', 
				'value' => 'fixed', 
				'for' => 'data-expire-fixed'
			],
			'recurring' => [
				'title' => 'COM_PP_PLAN_TIME_EXPIRATION_RECURRING', 
				'value' => 'recurring', 
				'for' => 'data-expire-recurring'
			],
			'recurring_trial_1' => [
				'title' => 'COM_PP_PLAN_TIME_EXPIRATION_RECURRING_TRIAL_1', 
				'value' => 'recurring_trial_1', 
				'for' => 'data-expire-recurring-trial-1'
			],
			'recurring_trial_2' => [
				'title' => 'COM_PP_PLAN_TIME_EXPIRATION_RECURRING_TRIAL_2', 
				'value' => 'recurring_trial_2', 
				'for' => 'data-expire-recurring-trial-2'
			]
		];

		$types = [];

		foreach ($data as $key => $item) {
			$obj = new stdClass();

			$obj->title = JText::_($item['title']);
			$obj->value = $item['value'];
			$obj->for = $item['for'];

			$types[] = $obj;
		}

		return $types;
	}

	/**
	 * get the available badge position
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	private function getBadgePositions()
	{
		$data = [
			'right' => 'COM_PP_PLAN_EDIT_PLAN_BADGE_POSITION_TOP_RIGHT',
			'left' => 'COM_PP_PLAN_EDIT_PLAN_BADGE_POSITION_TOP_LEFT',
			'center' => 'COM_PP_PLAN_EDIT_PLAN_BADGE_POSITION_TOP_CENTER'
		];

		$positions = [];

		foreach ($data as $key => $title) {
			$obj = new stdClass();

			$obj->title = JText::_($title);
			$obj->value = $key;

			$positions[] = $obj;
		}

		return $positions;
	}

	/**
	 * get the available badge position
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	private function getDisplayChildPlanOn()
	{
		$data = [
			PP_CONST_ANY => 'COM_PP_PARENTCHILD_PLAN_EDIT_DISPLAY_PLAN_ANY_PLAN',
			PP_CONST_ALL => 'COM_PP_PARENTCHILD_PLAN_EDIT_DISPLAY_PLAN_ALL_PLANS',
			PP_CONST_NONE => 'COM_PP_PARENTCHILD_PLAN_EDIT_DISPLAY_PLAN_NONE_OF_PLANS'
		];

		$value = [];

		foreach ($data as $key => $title) {
			$obj = new stdClass();

			$obj->title = JText::_($title);
			$obj->value = $key;

			$value[] = $obj;
		}

		return $value;
	}

	/**
	 * Post processing for updating ordering.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function saveorder()
	{
		return $this->redirect('index.php?option=com_payplans&view=plan');
	}
}

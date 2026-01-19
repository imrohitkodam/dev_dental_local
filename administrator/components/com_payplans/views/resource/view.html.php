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

class PayplansViewResource extends PayPlansAdminView
{
	public function display($tpl = null)
	{
		$this->heading('Resources');

		JToolbarHelper::deleteList('Are you sure you want to delete the selected resources?', 'resource.delete');

		$view = $this->input->get('view', '', 'cmd');
		$renderFilterBar = true;

		// Only render the filter bar in the log listing page.
		if ($view !== 'log') {
			$renderFilterBar = false;
		}

		$model = PP::model('Resource');
		$model->initStates();

		$rows = $model->getItems();
		$resources = [];

		if ($rows) {
			foreach ($rows as $row) {
				$row->user = PP::user($row->user_id);
					
				$subscriptions = $row->subscription_ids;
				$subscriptions = ltrim($subscriptions, ',');
				$subscriptions = rtrim($subscriptions, ',');

				$subscriptions = explode(',', $subscriptions);

				$row->subscriptions = [];

				if ($subscriptions) {
					foreach ($subscriptions as $subscriptionId) {
						$subscription = PP::subscription($subscriptionId);

						$row->subscriptions[] = $subscription;
					}
				}

				$resources[] = $row;
			}
		}
		// Get states used in this list
		$states = $this->getStates(['search', 'paid_date', 'app_id', 'status', 'ordering', 'direction', 'limit']);

		$this->set('editable', true);
		$this->set('form', true);
		$this->set('sortable', true);
		$this->set('resources', $resources);
		$this->set('pagination', $model->getPagination());
		$this->set('limitstart', $model->getState('limitstart'));
		$this->set('states', $states);
		$this->set('renderFilterBar', $renderFilterBar);

		// When accessing from resource page, we always allow to perform actions.
		$this->set('editable', true);

		return parent::display('resource/default/default');
	}

	/**
	 * Renders the edit form for resource
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function form()
	{
		$id = $this->input->get('id', 0, 'int');

		JToolbarHelper::apply('resource.apply');
		JToolbarHelper::save('resource.save');
		JToolbarHelper::cancel();

		$this->heading('Manage Resource');

		$resource = PP::table('Resource');
		$resource->load($id);

		$user = PP::user($resource->user_id);
		$from = $this->input->get('from', '', 'default');

		$activeTab = $this->input->get('active', '', 'word');

		if ($from) {
			$from = rtrim(JURI::root(), '/') . base64_decode($from);
		}

		$tabs = [];
		$tabs[] = (object) [
			'title' => 'COM_PP_DETAILS',
			'active' => !$activeTab,
			'id' => 'details'
		];

		$this->set('from', $from);
		$this->set('user', $user);
		$this->set('resource', $resource);
		$this->set('tabs', $tabs);
		$this->set('activeTab', $activeTab);

		return parent::display('resource/form/default');
	}
}
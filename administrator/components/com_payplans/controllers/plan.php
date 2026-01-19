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

class PayplansControllerPlan extends PayPlansController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('plans');

		// Map the alias methods here.
		$this->registerTask('save', 'store');
		$this->registerTask('savenew', 'store');
		$this->registerTask('apply', 'store');

		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('unpublish', 'togglePublish');

		$this->registerTask('visible', 'toggleVisible');
		$this->registerTask('invisible', 'toggleVisible');

		$this->registerTask('close', 'cancel');
		$this->registerTask('remove', 'delete');
	}

	/**
	 * Display plan edit form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function form()
	{
		$id = $this->input->get('id', 0, 'int');

		$editLink = 'index.php?option=com_payplans&view=plan&layout=form';
		if ($id) {
			$editLink .= '&id=' . $id;
		}

		return $this->app->redirect($editLink);
	}

	/**
	 * Cancel process
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function cancel()
	{
		return $this->app->redirect('index.php?option=com_payplans&view=plan');
	}

	/**
	 * Method to decorate the data for passing in to bind
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function decorateData($values)
	{
		$config = PP::config();

		// decorate the data
		$data = [];
		$details = [];
		$params = [];

		$data['plan_id'] = isset($values['plan_id']) ? $values['plan_id'] : $this->input->get('plan_id', 0, 'int');
		$data['title'] = $values['title'];
		$data['published'] = $values['published'];
		$data['visible'] = $values['visible'];
		$data['description'] = $values['description'];
		// $data['planapps'] = isset($values['planapps']) ? $values['planapps'] : '';
		$data['groups'] = isset($values['groups']) ? $values['groups'] : '';

		// details
		$details['expirationtype'] = $values['expirationtype'];
		$details['expiration'] = $values['expiration'];
		$details['recurrence_count'] = $values['recurrence_count'];
		$details['price'] = floatval($values['price']);
		$details['trial_price_1'] = floatval($values['trial_price_1']);
		$details['trial_time_1'] = $values['trial_time_1'];
		$details['trial_price_2'] = floatval($values['trial_price_2']);
		$details['trial_time_2'] = $values['trial_time_2'];
		// currency should default to configuration
		$details['currency'] = $config->get('currency');
		// make sure the expiration for 'forever' is set to 0
		if ($details['expirationtype'] == 'forever') {
			$details['expiration'] = '000000000000';
		}

		$data['details'] = $details;

		// params
		$params['teasertext'] = $values['teasertext'];
		$params['redirecturl'] = $values['redirecturl'];
		$params['badgeVisible'] = $values['badgeVisible'];
		$params['badgePosition'] = $values['badgePosition'];
		$params['badgeTitle'] = $values['badgeTitle'];
		$params['badgeTitleColor'] = $values['badgeTitleColor'];
		$params['badgebackgroundcolor'] = $values['badgebackgroundcolor'];
		$params['planHighlighter'] = $values['planHighlighter'];
		$params['limit_count'] = $values['limit_count'];
		$params['scheduled'] = $values['scheduled'];
		$params['start_date'] = $values['start_date'];
		$params['end_date'] = $values['end_date'];
		$params['total_count'] = $values['total_count'];
		$params['moderate_subscription'] = $values['moderate_subscription'];
		$params['parentplans'] = PP::normalize($values, 'parentplans', '');
		$params['displaychildplanon'] = $values['displaychildplanon'];
		$params['expiration_date'] = $values['expiration_date'];
		$params['subscription_from'] = $values['subscription_from'];
		$params['subscription_to'] = $values['subscription_to'];
		$params['enable_fixed_expiration_date'] = $values['enable_fixed_expiration_date'];
		$params['show_billing'] = (float) $details['price'] === 0.00 ? $values['show_billing'] : 1;
		$params['planPermission'] = $values['planPermission'];

		$data['params'] = $params;

		return $data;
	}

	/**
	 * Method to process plan saving
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function store()
	{
		$id = $this->input->get('plan_id', 0, 'int');
		$data = $this->input->post->getArray();
		$plan = PP::plan($id);
		$otherQuery = $plan->getId() ? 'id=' . $plan->getId() : '';

		if (empty($data['title'])) {
			$this->info->set('COM_PP_EMPTY_TITLE', 'danger');

			return $this->redirectToView('plan', 'form', $otherQuery);
		}

		// Don't save the html tags in the plan title as it will create issue in further processing in the payment gateway.
		$data['title'] = strip_tags($data['title']);

		$data['description'] = $this->input->get('description', $data['description'], 'raw');

		// Decarate the data so that binding will work.
		$data = $this->decorateData($data);

		// Bind the data
		$plan->bind($data);

		// validation on recurring plan
		if ($plan->isRecurring()) {
			// Free plans can never be recurred
			if ($plan->isFree()) {
				$this->info->set('COM_PP_RECURRING_PLAN_NEVER_FREE', 'danger');

				return $this->redirectToView('plan', 'form', $otherQuery);
			}

			// Recurring plan can never be of lifetime expiration
			if ($plan->getRawExpiration() === '000000000000') {
				$this->info->set('COM_PP_RECURRING_PLAN_EXPIRATIONE_NEVER_LIFETIME', 'danger');

				return $this->redirectToView('plan', 'form', $otherQuery);
			}

			// In case Recurring + Trial plan, trial expiration time can not be life time
			if (in_array($plan->getExpirationType(), [PP_RECURRING_TRIAL_1, PP_RECURRING_TRIAL_2])) {

				if ($plan->getExpiration(PP_PRICE_RECURRING_TRIAL_1, true) === '000000000000' || ($plan->getExpirationType() === PP_RECURRING_TRIAL_2 && $plan->getExpiration(PP_PRICE_RECURRING_TRIAL_2, true) === '000000000000')) {
					$this->info->set('COM_PP_RECURRING_PLAN_TRIAL_EXPIRATIONE_NEVER_LIFETIME', 'danger');

					return $this->redirectToView('plan', 'form', $otherQuery);
				}
			}
		}
 
		$libObj = $plan->save();

		if ($libObj === false) {
			$error = $plan->getError();
			$this->info->set($error->text, 'danger');

			return $this->redirectToView('plan', 'form', $otherQuery);
		}

		$actionString = $id ? 'COM_PP_ACTIONLOGS_PLAN_UPDATED' : 'COM_PP_ACTIONLOGS_PLAN_CREATED';

		$actionlog = PP::actionlog();
		$actionlog->log($actionString, 'plan', [
			'planTitle' => $plan->getTitle(),
			'planLink' => 'index.php?option=com_payplans&view=plan&layout=form&id=' . $plan->getId(),
		]);

		$message = JText::_('COM_PAYPLANS_ITEM_SAVED_SUCCESSFULLY');
		$this->info->set($message, 'success');

		if ($this->task === 'saveNew') {
			return $this->redirectToView('plan', 'form');
		}

		if ($this->task === 'apply') {
			return $this->redirectToView('plan', 'form', 'id=' . $plan->getId());
		}

		return $this->redirectToView('plan');
	}

	/**
	 * Method to publish / unpublish
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function togglePublish()
	{
		$ids = $this->input->get('cid', [], 'array');

		if (!$ids) {
			$message = JText::_('COM_PP_INVALID_IDS');
			$this->info->set($message, 'danger');
			return $this->redirectToView('plan');
		}

		$task = $this->getTask();
		$actionlog = PP::actionlog();

		$state = $task === 'publish' ? 1 : 0;
		$actionString = $task === 'publish' ? 'COM_PP_ACTIONLOGS_PLAN_PUBLISHED' : 'COM_PP_ACTIONLOGS_PLAN_UNPUBLISHED';

		$model = PP::model('plan');
		$model->publish($ids, $state);

		foreach ($ids as $id) {
			$table = PP::table('Plan');
			$table->load($id);

			if (!$table->plan_id) {
				continue;
			}

			$actionlog->log($actionString, 'plan', [
				'planTitle' => $table->title,
				'planLink' => 'index.php?option=com_payplans&view=plan&layout=form&id=' . $table->plan_id
			]);
		}

		$msg = JText::_('COM_PP_PLAN_PUBLISHED_SUCCESSFULLY');

		if ($task !== 'publish') {
			$msg = JText::_('COM_PP_PLAN_UNPUBLISHED_SUCCESSFULLY');
		}

		$this->info->set($msg, 'success');
		return $this->redirectToView('plan'); 
	}

	/**
	 * Method to toggle plan's visibility
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function toggleVisible()
	{
		$ids = $this->input->get('cid', 0, 'int');

		if (!$ids) {
			$message = JText::_('COM_PP_INVALID_IDS');
			$this->info->set($message, 'danger');
			return $this->redirectToView('plan');
		}

		$task = $this->getTask();
		$state = $task == 'visible' ? 1 : 0;

		$actionlog = PP::actionlog();
		$actionString = $task == 'visible' ? 'COM_PP_ACTIONLOGS_PLAN_VISIBLE' : 'COM_PP_ACTIONLOGS_PLAN_INVISIBLE';

		foreach ($ids as $id) {
			$table = PP::table('plan');
			$table->load($id);
			$table->visible($state);

			$actionlog->log($actionString, 'plan', [
				'planTitle' => $table->title,
				'planLink' => 'index.php?option=com_payplans&view=plan&layout=form&id=' . $table->plan_id
			]);
		}

		$msg = JText::_('COM_PP_PLAN_VISIBLE_SUCCESSFULLY');

		if ($task != 'visible') {
			$msg = JText::_('COM_PP_PLAN_INVISIBLE_SUCCESSFULLY');
		}

		$this->info->set($msg, 'success');
		return $this->redirectToView('plan');
	}

	/**
	 * Method to process plan deletion
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function delete()
	{
		$ids = $this->input->get('cid', [], 'array');

		if (!$ids) {
			$message = JText::_('COM_PP_INVALID_IDS');
			$this->info->set($message, 'danger');
			return $this->redirectToView('plan');
		}

		$actionlog = PP::actionlog();

		foreach ($ids as $id) {
			$plan = PP::plan($id);
			$title = $plan->getTitle();
			$state = $plan->delete();

			if ($state === false) {
				$error = $plan->getError();

				$this->info->set($error->text, $error->type);
				return $this->redirectToView('plan');
			}

			$actionlog->log('COM_PP_ACTIONLOGS_PLAN_DELETED', 'plan', [
				'planTitle' => $title
			]);
		}

		$this->info->set(JText::_('COM_PP_PLAN_DELETED_SUCCESSFULLY'), 'success');
		return $this->redirectToView('plan');
	}

	/**
	 * Method to process plan copying
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function copy()
	{
		$ids = $this->input->get('cid', [], 'array');

		if (!$ids) {
			$message = JText::_('COM_PP_INVALID_IDS');
			$this->info->set($message, 'danger');
			return $this->redirectToView('plan');
		}

		$actionlog = PP::actionlog();

		foreach ($ids as $id) {
			$plan = PP::plan($id);
			$selectedPlanTitle = $plan->getTitle();
			$plan->setId(0);
			$plan->setTitle(JText::sprintf('COM_PP_COPY_OF', $plan->getTitle()));

			// reset ordering
			$plan->setOrdering(0);
			$state = $plan->save();

			if ($state === false) {

				$error = $plan->getError();
				$this->info->set($error->text, $error->type);

				return $this->redirectToView('plan');
			}

			$actionlog->log('COM_PP_ACTIONLOGS_PLAN_COPIED', 'plan', [
				'planTitle' => $selectedPlanTitle,
				'planLink' => 'index.php?option=com_payplans&view=plan&layout=form&id=' . $id,
				'newPlanTitle' => $plan->getTitle(),
				'newPlanLink' => 'index.php?option=com_payplans&view=plan&layout=form&id=' . $plan->getId()
			]);
		}

		$this->info->set(JText::_('COM_PP_PLAN_COPIED_SUCCESSFULLY'), 'success');
		return $this->redirectToView('plan');
	}

	public function recurrencevalidation()
	{
		return true;
	}

	/**
	 * Method to update the ordering of plan
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function saveorder()
	{
		// Check for request forgeries.
		$cid = $this->input->get('cid', [], 'array');
		$ordering = $this->input->get('order', [], 'array');

		if (!$cid) {
			$message = JText::_('COM_PP_INVALID_IDS');
			$this->info->set($message, 'danger');
			return $this->redirectToView('plan');
		}

		$model = PP::model('plan');

		for($i = 0; $i < count($cid); $i++) {

			$id = $cid[$i];
			$order = $ordering[$i];

			$model->updateOrdering($id, $order);
		}

		$this->info->set(JText::_('COM_PP_PLAN_ORDERED_SUCCESSFULLY'), 'success');
		return $this->redirectToView('plan');
	}

	/**
	 * Move up the ordering
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function moveUp()
	{
		$direction = $this->input->get('direction', 'asc');
		if ($direction == 'desc') {
			return $this->move(1);
		}

		return $this->move(-1);
	}

	/**
	 * Move down the ordering
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function moveDown()
	{
		$direction = $this->input->get('direction', 'asc');

		if ($direction == 'desc') {
			return $this->move(-1);
		}

		return $this->move(1);
	}

	/**
	 * Allow caller to move the ordering up/down 
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	private function move($index)
	{
		$layout = $this->input->get('layout', '', 'cmd');

		$ids = $this->input->get('cid', [], 'array');

		if (!$ids) {
			$message = JText::_('COM_PP_INVALID_IDS');
			$this->info->set($message, 'danger');
			return $this->redirectToView('plan');
		}

		foreach ($ids as $id) {
			$table = PP::table('plan');
			$table->load($id);

			$table->move($index);
		}

		$this->info->set(JText::_('COM_PP_PLAN_ORDERED_SUCCESSFULLY'), 'success');
		return $this->redirectToView('plan');
	}
}

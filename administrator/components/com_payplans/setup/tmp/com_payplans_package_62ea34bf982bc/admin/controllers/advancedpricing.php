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

class PayplansControllerAdvancedpricing extends PayPlansController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('plans');

		$this->registerTask('save', 'save');
		$this->registerTask('savenew', 'save');
		$this->registerTask('apply', 'save');

		$this->registerTask('close', 'cancel');

		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('unpublish', 'togglePublish');
	}

	/**
	 * Delete a list of advancedpricing from the site
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function delete()
	{
		$ids = $this->input->get('cid', 0, 'int');
		$actionlog = PP::actionlog();

		foreach ($ids as $id) {
			$table = PP::table('Advancedpricing');			
			$table->load($id);
			$title = $table->title;
			$table->delete();

			$actionlog->log('COM_PP_ACTIONLOGS_ADVANCED_PRICING_DELETED', 'advancedpricing', [
				'advPricingTitle' => $title
			]);
		}

		$this->info->set('COM_PP_ITEM_DELETED_SUCCESS', 'success');
		return $this->redirectToView('advancedpricing');
	}

	/**
	 * Cancel process
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function cancel()
	{
		return $this->app->redirect('index.php?option=com_payplans&view=advancedpricing');
	}

	/**
	 * Allow caller to toggle published state
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function togglePublish()
	{

		$ids = $this->input->get('cid', 0, 'int');
		$task = $this->getTask();

		$actionlog = PP::actionlog();
		$actionString = $task == 'publish' ? 'COM_PP_ACTIONLOGS_ADVANCED_PRICING_PUBLISHED' : 'COM_PP_ACTIONLOGS_ADVANCED_PRICING_UNPUBLISHED';

		foreach ($ids as $id) {
			$table = PP::table('Advancedpricing');
			$table->load($id);

			$table->$task();

			$actionlog->log($actionString, 'advancedpricing', [
				'advPricingTitle' => $table->title,
				'advPricingLink' => 'index.php?option=com_payplans&view=advancedpricing&layout=form&id=' . $table->advancedpricing_id
			]);
		}

		$message = $task == 'publish' ? 'COM_PP_ITEM_PUBLISHED_SUCCESSFULLY' : 'COM_PP_ITEM_UNPUBLISHED_SUCCESSFULLY';

		$this->info->set($message, 'success');
		return $this->redirectToView('advancedpricing');
	}

	/**
	 * Saves a advanced pricing item
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function save()
	{
		$id = $this->input->get('advancedpricing_id', 0, 'int');
		$data = $this->input->post->getArray();

		if (empty($data['title'])) {
			$this->info->set('COM_PP_TITLE_REQUIRED', 'danger');
			return $this->redirectToView('advancedpricing', 'form');
		}

		if (empty($data['units_min']) || empty($data['units_max'])) {
			$this->info->set('COM_PP_ADV_PRICING_MIN_MAX_INVALID', 'danger');
			return $this->redirectToView('advancedpricing', 'form');
		}

		$prices = $data['price'];
		$durations = $data['duration'];

		if (!$prices || !$durations) {
			$this->info->set('COM_PP_AT_LEAST_ONE_PRICESET_REQUIRED', 'danger');
			return $this->redirectToView('advancedpricing', 'form');
		}

		foreach ($prices as $price) {
			if ($price === '' || PP::isFree($price)) {
				$this->info->set('COM_PP_ADV_PRICING_FREE_DISALLOWED', 'danger');

				return $this->redirectToView('advancedpricing', 'form&id=' . $id);
			}
		}

		$table = PP::table('Advancedpricing');
		$table->load($id);
		$table->bind($data);

		$plans = $title = PP::normalize($data, 'plans', []);

		// Save plans
		$table->plans = json_encode($plans);

		// Save price set
		$priceSet = new stdClass();
		$priceSet->price = $prices;
		$priceSet->expiration_time = $durations;

		$table->params = json_encode($priceSet);

		if (!$id) {
			$table->created_date = PP::date()->toSql();
		}

		$state = $table->store();

		if ($state === false) {
			$this->info->set('COM_PP_MODIFIER_SAVED_FAILED', 'danger');
			return $this->redirectToView('advancedpricing', 'form');
		}

		$actionString = $id ? 'COM_PP_ACTIONLOGS_ADVANCED_PRICING_UPDATED' : 'COM_PP_ACTIONLOGS_ADVANCED_PRICING_CREATED';
		$message = $id ? 'COM_PP_ADV_PRICING_SAVED_SUCCESS' : 'COM_PP_ADV_PRICING_CREATED_SUCCESS';

		$actionlog = PP::actionlog();
		$actionlog->log($actionString, 'advancedpricing', [
				'advPricingTitle' => $table->title,
				'advPricingLink' => 'index.php?option=com_payplans&view=advancedpricing&layout=form&id=' . $table->advancedpricing_id
		]);

		$this->info->set($message, 'success');

		$task = $this->getTask();

		if ($task === 'apply') {
			return $this->redirectToView('advancedpricing', 'form', 'id=' . $table->getId());
		}

		if ($task === 'saveNew') {
			return $this->redirectToView('advancedpricing', 'form');
		}

		return $this->redirectToView('advancedpricing');
	}
}
<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class PayplansControllerPlanPriceVariations extends PayPlansController
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
	 * Delete a list of plan modifiers instance from the site
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function delete()
	{
		$ids = $this->input->get('cid', 0, 'int');
		$actionlog = PP::actionlog();

		foreach ($ids as $id) {
			$app = PP::app((int) $id);
			$title = $app->getTitle();
			$app->delete();

			$actionlog->log('COM_PP_ACTIONLOGS_PLAN_PRICE_VARIATION_DELETED', 'planpricevariations', array(
					'planPriceVariationTitle' => $title
			));
		}

		$this->info->set('COM_PP_PLAN_PRICE_VARIATION_DELETED_SUCCESS', 'success');
		return $this->redirectToView('planpricevariations');
	}

	/**
	 * Cancel process
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function cancel()
	{
		return $this->app->redirect('index.php?option=com_payplans&view=planpricevariations');
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
		$actionString = $task == 'publish' ? 'COM_PP_ACTIONLOGS_PLAN_PRICE_VARAIATIONS_PUBLISHED' : 'COM_PP_ACTIONLOGS_PLAN_PRICE_VARAIATIONS_UNPUBLISHED';

		foreach ($ids as $id) {
			$table = PP::table('App');
			$table->load($id);

			$table->$task();

			$actionlog->log($actionString, 'planpricevariations', array(
					'planPriceVariationTitle' => $table->title,
					'planPriceVariationLink' => 'index.php?option=com_payplans&view=planpricevariations&layout=form&id=' . $table->app_id
			));
		}

		$message = $task == 'publish' ? 'COM_PP_ITEM_PUBLISHED_SUCCESSFULLY' : 'COM_PP_ITEM_UNPUBLISHED_SUCCESSFULLY';

		$this->info->set($message, 'success');
		return $this->redirectToView('planpricevariations');
	}

	/**
	 * Saves a modifier
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function save()
	{
		$id = $this->input->get('app_id', 0, 'int');
		$data = $this->input->post->getArray();

		if (empty($data['title'])) {
			$this->info->set('COM_PP_TITLE_REQUIRED', 'danger');
			return $this->redirectToView('planpricevariations', 'form');
		}

		$app = PP::app($id);
		$app->bind($data);
		$app->type = 'planpricevariation';
		$app->group = 'core';

		// Insert the core params
		$coreParams = new JRegistry($data['core_params']);
		$app->setCoreParams($data['core_params']);

		// Insert the app params
		$data['app_params']['time_price'] = serialize($data['app_params']['time_price']);
		$appParams = $app->collectAppParams($data);
		$app->setAppParams($appParams);

		// Save the app
		$state = $app->save();

		$message = 'COM_PP_PLAN_PRICE_VARAIATION_CREATED_SUCCESS';
		$actionString = 'COM_PP_ACTIONLOGS_PLAN_PRICE_VARAIATION_CREATED';

		if ($state === false) {
			$this->info->set('COM_PP_PLAN_PRICE_VARAIATION_SAVED_FAILED', 'danger');

			return $this->redirectToView('planpricevariations', 'form');
		}

		if ($id) {
			$message = 'COM_PP_PLAN_PRICE_VARAIATION_SAVED_SUCCESS';
			$actionString = 'COM_PP_ACTIONLOGS_PLAN_PRICE_VARAIATIONS_UPDATED';
		}

		$actionlog = PP::actionlog();
		$actionlog->log($actionString, 'planpricevariations', array(
				'planPriceVariationTitle' => $app->getTitle(),
				'planPriceVariationLink' => 'index.php?option=com_payplans&view=planpricevariations&layout=form&id=' . $app->getId()
		));

		$this->info->set($message, 'success');

		$task = $this->getTask();

		if ($task == 'apply') {
			return $this->redirectToView('planpricevariations', 'form', 'id=' . $app->getId());
		}

		if ($task == 'saveNew') {
			return $this->redirectToView('planpricevariations', 'form');
		}

		return $this->redirectToView('planpricevariations');
	}

}

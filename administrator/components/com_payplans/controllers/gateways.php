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

class PayplansControllerGateways extends PayPlansController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('gateways');
		
		$this->registerTask('save', 'store');
		$this->registerTask('apply', 'store');
		
		$this->registerTask('remove', 'delete');
	}

	/**
	 * Allows remote caller to delete an app
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function delete()
	{
		$ids = $this->input->get('cid', [], 'int');
		$actionlog = PP::actionlog();

		foreach ($ids as $id) {
			$id = (int) $id;

			$app = PP::app($id);

			if (!$app->canDelete()) {
				$this->info->set('COM_PP_UNABLE_TO_DELETE_PAYMENT_METHOD', 'danger');
				return $this->redirectToView('gateways');
			}

			$title = $app->getTitle();

			$app->delete();

			$actionlog->log('COM_PP_ACTIONLOGS_PAYMENT_METHODS_DELETED', 'gateways', [
				'paymentTitle' => $title
			]);
		}

		$this->info->set('COM_PP_PAYMENT_METHODS_DELETED_SUCCESS', 'success');

		return $this->redirectToView('gateways');
	}

	/**
	 * Cancel process
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function cancel()
	{
		return $this->app->redirect('index.php?option=com_payplans&view=gateways');
	}

	/**
	 * Saves a payment gateway
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function store()
	{
		$id = $this->input->get('id', 0, 'int');
		$data = $this->input->post->getArray();
		$coreParams = $this->input->get('core_params', [], 'array');
		$appParams = $this->input->get('app_params', [], 'array');

		// process plan mapping
		if (isset($appParams['plan_product_mapping'])) {
			foreach ($appParams['plan_product_mapping'] as $key => $mapping) {
				if (!$mapping[0]) {
					unset($appParams['plan_product_mapping'][$key]);
				}
			}

			$data['app_params'] = $appParams;
		}

		if ($id) {
			$app = PP::app()->getAppInstance($id);

			$data['core_params'] = $app->collectCoreParams($data);
			$data['app_params']  = $app->collectAppParams($data);

			$actionString = 'COM_PP_ACTIONLOGS_PAYMENT_METHODS_UPDATED';
		} else {
			$app = PP::app();
			$actionString = 'COM_PP_ACTIONLOGS_PAYMENT_METHODS_CREATED';
		}

		$title = PP::normalize($data, 'title', '');

		if (!$title) {
			$this->info->set('COM_PP_PAYMENT_METHODS_TITLE_BLANK', 'danger');

			if ($app->getId()) {
				return $this->redirectToView('gateways', 'form', 'id=' . $app->getId());
			}

			return $this->redirectToView('gateways', 'create', 'element=' . $data['type']);
		}

		$app->setCoreParams($coreParams);
		$app->setAppParams($appParams);

		// All payment gateways should have an instance of "payment"
		if (!$id) {
			$data['group'] = 'payment';
		}

		$app->bind($data);

		try {
			$app->save();
		} catch (Exception $e) {
			$this->info->set($e->getMessage(), 'danger');

			return $this->redirectToView('gateways', 'form', 'id=' . $app->getId());
		}

		$actionlog = PP::actionlog();
		$actionlog->log($actionString, 'gateways', [
			'paymentTitle' => $app->getTitle(),
			'paymentLink' => 'index.php?option=com_payplans&view=gateways&layout=form&id=' . $app->getId()
		]);

		$this->info->set('COM_PP_PAYMENT_METHOD_UPDATED_SUCCESSFULLY', 'success');

		if ($this->getTask() === 'apply') {
			return $this->redirectToView('gateways', 'form', 'id=' . $app->getId());
		}
		return $this->redirectToView('gateways');
	}

	/**
	 * Save ordering
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
			$this->info->set($message);
			return $this->redirectToView('gateways');
		}

		$model = PP::model('gateways');

		for($i = 0; $i < count($cid); $i++) {

			$id = $cid[$i];
			$order = $ordering[$i];

			$model->updateOrdering($id, $order);
		}

		$this->info->set(JText::_('COM_PP_PLAN_ORDERED_SUCCESSFULLY'), 'success');
		return $this->redirectToView('gateways');
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

		if ($direction === 'desc') {
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

		if ($direction === 'desc') {
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
			$this->info->set($message);
			return $this->redirectToView('gateways');
		}

		$db = PP::db();

		foreach ($ids as $id) {
			$table = PP::table('app');
			$table->load($id);

			$where = $db->nameQuote('group') . ' = ' . $db->Quote('payment');

			$table->move($index, $where);
		}

		$this->info->set(JText::_('COM_PP_PLAN_ORDERED_SUCCESSFULLY'), 'success');
		return $this->redirectToView('gateways');
	}
}
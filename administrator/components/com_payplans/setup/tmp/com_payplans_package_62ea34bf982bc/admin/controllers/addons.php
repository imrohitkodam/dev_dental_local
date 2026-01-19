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

class PayplansControllerAddons extends PayPlansController
{
	protected $_defaultOrderingDirection = 'ASC';

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
	 * Deletes addon
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function delete()
	{
		$ids = $this->input->get('cid', 0, 'int');
		$actionlog = PP::actionlog();

		foreach ($ids as $id) {
			$discount = PP::addon((int) $id);
			$title = $discount->getTitle();
			$discount->delete();

			$actionlog->log('COM_PP_ACTIONLOGS_ADDONS_DELETED', 'addons', [
				'addOnTitle' => $title
			]);
		}

		$this->info->set('COM_PP_SELECTED_ADDONS_DELETED_SUCCESS', 'success');

		return $this->redirectToView('addons');
	}

	/**
	 * Cancel process
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function cancel()
	{
		return $this->app->redirect('index.php?option=com_payplans&view=addons');
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
		$actionString = $task == 'publish' ? 'COM_PP_ACTIONLOGS_ADDONS_PUBLISHED' : 'COM_PP_ACTIONLOGS_ADDONS_UNPUBLISHED';

		foreach ($ids as $id) {
			$table = PP::table('Addon');
			$table->load($id);

			$table->$task();

			$actionlog->log($actionString, 'addons', [
				'addOnTitle' => $table->title,
				'addOnLink' => 'index.php?option=com_payplans&view=addons&layout=form&id=' . $table->planaddons_id
			]);
		}

		$message = $task == 'publish' ? 'COM_PP_ITEM_PUBLISHED_SUCCESSFULLY' : 'COM_PP_ITEM_UNPUBLISHED_SUCCESSFULLY';

		$this->info->set($message, 'success');
		return $this->redirectToView('addons');
	}

	public function updateStatStatus()
	{
		$id = $this->input->get('id', 0, 'int');
		$status = $this->input->get('status', 0, 'int');

		if (!$id) {
			$message = JText::_('COM_PP_INVALID_IDS');;
			$this->view->setMessage($message, PP_MSG_WARNING);
			return $this->view->call(__FUNCTION__);
		}

		$table = PP::table('AddonStat');
		$table->load($id);

		$table->status = $status;
		$state = $table->store();

		$msg = JText::_('COM_PP_ADDONS_STAT_STATUS_UPDATE_SUCCESSFULLY');

		if (!$state) {
			$msg = JText::_('COM_PP_ADDONS_STAT_STATUS_UPDATE_FAILED');
		}

		$this->view->setMessage($msg, 'success');
		return $this->view->call(__FUNCTION__);
	}

	/**
	 * Saves the addon
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function save()
	{
		$id = $this->input->get('id', 0, 'int');
		$data = $this->input->post->getArray();

		// before we bind the data, we need to handle the dates. # 816
		if (isset($data['start_date']) && $data['start_date']) {
			$data['start_date'] = PP::convertToGMTDate($data['start_date']);
		}
		if (isset($data['end_date']) && $data['end_date']) {
			$data['end_date'] = PP::convertToGMTDate($data['end_date']);
		}

		$addon = PP::addon($id);
		$addon->bind($data);

		if ($addon->apply_on) {
			$addon->plans = '';
		}

		if (!$addon->apply_on && isset($data['plans'])) {
			$addon->plans = json_encode($data['plans']);
		}

		$addon->save();

		$message = 'COM_PP_ADDONS_CREATED_SUCCESS';
		$actionString = 'COM_PP_ACTIONLOGS_ADDONS_CREATED';

		if ($id) {
			$message = 'COM_PP_ADDONS_UPDATED_SUCCESS';
			$actionString = 'COM_PP_ACTIONLOGS_ADDONS_UPDATED';
		}

		$actionlog = PP::actionlog();
		$actionlog->log($actionString, 'addons', [
			'addOnTitle' => $addon->getTitle(),
			'addOnLink' => 'index.php?option=com_payplans&view=addons&layout=form&id=' . $addon->getId()
		]);

		$this->info->set($message, 'success');

		$task = $this->getTask();

		if ($task == 'apply') {
			return $this->redirectToView('addons', 'form', 'id=' . $addon->getId());
		}

		if ($task == 'saveNew') {
			return $this->redirectToView('addons', 'form');
		}

		return $this->redirectToView('addons');
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
		$cid = $this->input->get('cid', array(), 'array');
		$ordering = $this->input->get('order', array(), 'array');

		if (!$cid) {
			$message = JText::_('COM_PP_INVALID_IDS');
			$this->info->set($message);
			return $this->redirectToView('addons');
		}

		$model = PP::model('addons');

		for($i = 0; $i < count($cid); $i++) {

			$id = $cid[$i];
			$order = $ordering[$i];

			$model->updateOrdering($id, $order);
		}

		$this->info->set(JText::_('COM_PP_PLANADDON_ORDERED_SUCCESSFULLY'), 'success');
		return $this->redirectToView('addons');
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
			$this->info->set($message);
			return $this->redirectToView('addons');
		}

		$db = PP::db();

		foreach ($ids as $id) {
			$table = PP::table('addon');
			$table->load($id);

			$table->move($index);
		}

		$this->info->set(JText::_('COM_PP_PLANADDON_ORDERED_SUCCESSFULLY'), 'success');
		return $this->redirectToView('addons');
	}

}

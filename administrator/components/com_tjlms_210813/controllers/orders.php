<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_Tjlms
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Orders list controller class.
 *
 * @since  1.0.0
 */
class TjlmsControllerOrders extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   STRING  $name    model name
	 * @param   STRING  $prefix  model prefix
	 * @param   ARRAY   $config  configuration
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function getModel($name = 'orders', $prefix = 'TjlmsModel', $config = Array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$input = JFactory::getApplication()->input;
		$pks = $input->post->get('cid', array(), 'array');
		$order = $input->post->get('order', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}

	/**
	 * Method to save/change the order status
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function save()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		$mainframe = JFactory::getApplication();
		$model = $this->getModel('orders');
		$input = JFactory::getApplication()->input;
		$post = $input->post;
		$order_id = $post->get('order_id');
		$status = $post->get('payment_status');

		$updated_order = $model->updateOrderStatus($order_id, $status);

		if ($updated_order)
		{
			// Add a message to the message queue
			$mainframe->enqueueMessage(JText::_('COM_TJLMS_ORDER_STATUS_CHANGE'));
		}
		else
		{
			// Add a message to the message queue
			$mainframe->enqueueMessage(JText::_('COM_TJLMS_ORDER_STATUS_CHANGE_FAILED'), 'error');
		}

		$this->setRedirect(JRoute::_('index.php?option=com_tjlms&view=orders', false));
	}

	/**
	 * Removes an item.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function delete()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');

		if (!is_array($cid) || count($cid) < 1)
		{
			JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel('orders');

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Remove the items.
			if ($model->delete($cid))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_ORDER_ITEMS_DELETED', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=com_tjlms&view=orders', false));
	}
}

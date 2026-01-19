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

class PayplansControllerLog extends PayplansController
{
	public function __construct()
	{
		parent::__construct();
		
		$this->checkAccess('logs');
	}
	
	/**
	 * Purge ipn history
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function purgeIpn()
	{
		$model = PP::model('Log');
		$model->purgePaymentNotifications();

		$actionlog = PP::actionlog();
		$actionlog->log('COM_PP_ACTIONLOGS_LOGS_PAYMENT_NOTIFICATION_PURGED', 'log');

		$this->info->set('COM_PP_PURGED_PAYMENT_NOTIFICATIONS_SUCCESSFULLY', 'success');

		return $this->redirectToView('log', 'payments');
	}

	/**
	 * Purge ipn history
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function purge()
	{
		$model = PP::model('Log');
		$model->purgeAll();

		$actionlog = PP::actionlog();
		$actionlog->log('COM_PP_ACTIONLOGS_LOGS_PURGED_ALL', 'log');

		$this->info->set('COM_PP_PURGED_AUDIT_LOGS_SUCCESSFULLY', 'success');

		return $this->redirectToView('log');
	}

	/**
	 * Removes a log from the system
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function remove()
	{
		$ids = $this->input->get('cid', array(), 'array');
		$actionlog = PP::actionlog();

		if ($ids) {
			foreach ($ids as $id) {
				$log = PP::table('Log');
				$log->load((int) $id);

				$title = $log->message;

				$log->delete();

				$actionlog->log('COM_PP_ACTIONLOGS_LOGS_DELETED', 'log', array(
						'logTitle' => $title
				));
			}
		}

		$this->info->set('COM_PP_REMOVED_AUDIT_LOGS_SUCCESSFULLY', 'success');
		return $this->redirectToView('log');
	}
	
}
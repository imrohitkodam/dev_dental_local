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

class PayplansViewNotifications extends PayPlansAdminView
{
	public function __construct()
	{
		parent::__construct();
		
		$this->checkAccess('notifications');
	}

	/**
	 * Preview a notification template
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function preview()
	{
		$id = $this->input->get('id', 0, 'int');

		$url = JURI::root() . 'administrator/index.php?option=com_payplans&view=notifications&layout=preview&tmpl=component&id=' . $id;

		$theme = PP::themes();
		$theme->set('url', $url);

		$output = $theme->output('admin/notifications/dialogs/preview');

		return $this->ajax->resolve($output);
	}
}
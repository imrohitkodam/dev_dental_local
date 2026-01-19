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

PP::import('site:/views/views');

class PayPlansViewAttachment extends PayPlansSiteView
{
	/**
	 * Display the confirmation to delete an attachment
	 *
	 * @since	4.2.8
	 * @access	public
	 */
	public function confirmDelete()
	{
		$themes = PP::themes();
		$output = $themes->output('site/attachment/dialogs/delete');

		return $this->resolve($output);
	}

	/**
	 * Delete the selected attachment
	 *
	 * @since	4.2.8
	 * @access	public
	 */
	public function delete()
	{
		$type = $this->input->get('type', '', 'string');
		$group = $this->input->get('group', '', 'string');
		$container = $this->input->get('container', '', 'string');
		$objId = $this->input->get('objId', 0, 'int');
		$name = $this->input->get('name', '', 'string');

		if ($group == PP_CUSTOM_DETAILS_TYPE_USER && $objId !== (int) $this->my->id && !PP::isSiteAdmin()) {
			die('You do not have the permission to delete the file.');
		}

		if ($group == PP_CUSTOM_DETAILS_TYPE_SUBSCRIPTION) {
			$subscription = PP::subscription($objId);

			if ((int) $subscription->getBuyer()->getId() !== (int) $this->my->id && !PP::isSiteAdmin()) {
				die('You do not have the permission to delete the file.');
			}
		}

		$file = JPATH_ROOT . '/media/com_payplans/attachments/' . $type . '/' . $group . '/' . $objId . '/' . $container . '/' . $name;

		if (!JFile::exists($file)) {
			return $this->reject('There is no such file exists.');
		}

		JFile::delete($file);

		return $this->resolve();
	}
}
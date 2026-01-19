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

PP::import('admin:/includes/controller');

class PayplansControllerAttachment extends PayPlansController
{
	/**
	 * Allows caller to download a file
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function download()
	{
		$type = $this->input->get('type', '', 'string');

		if (!$type) {
			die('Invalid type');
		}

		if ($type === 'customdetails') {
			$group = $this->input->get('group', '', 'string');
			$container = $this->input->get('container', '', 'string');
			$objId = $this->input->get('objId', 0, 'int');
			$name = $this->input->get('name', '', 'string');

			if (!$group || !$objId || !$name) {
				die('Invalid data');
			}

			if ($group === PP_CUSTOM_DETAILS_TYPE_USER && $objId !== (int) $this->my->id && !PP::isSiteAdmin()) {
				die('You do not have the permission to download the file.');
			}

			if ($group === PP_CUSTOM_DETAILS_TYPE_SUBSCRIPTION) {
				$subscription = PP::subscription($objId);

				if ((int) $subscription->getBuyer()->getId() !== (int) $this->my->id && !PP::isSiteAdmin()) {
					die('You do not have the permission to download the file.');
				}
			}

			$file = JPATH_ROOT . '/media/com_payplans/attachments/customdetails/' . $group . '/' . $objId . '/' . $container . '/' . $name;

			if (!JFile::exists($file)) {
				die('There is no such file exists.');
			}
		}

		header('Content-Description: File Transfer');
		header('Content-Type: ' . mime_content_type($file));
		header('Content-Disposition: attachment; filename="' . $name . '"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize($file));
		header("Cache-Control: private, max-age=10800, pre-check=10800");
		header("Pragma: private");
		header("Expires: " . date(DATE_RFC822,strtotime(" 2 day")));

		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) === filemtime($file))) {
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file)).' GMT', true, 304);
		}

		ob_clean();
		flush();
		readfile($file);
		exit;
	}
}
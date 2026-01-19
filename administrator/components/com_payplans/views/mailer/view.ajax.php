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

class PayPlansViewMailer extends PayPlansAdminView
{
	/**
	 * Previews an email template
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function preview()
	{
		$file = $this->input->get('file', 0, 'default');
		$url = rtrim(JURI::root() , '/' ) . '/administrator/index.php?option=com_payplans&view=mailer&layout=preview&file=' . $file . '&tmpl=component';

		$theme = PP::themes();
		$theme->set('url', $url);
		$contents = $theme->output('admin/mailer/default/preview');

		return $this->ajax->resolve($contents);
	}
}
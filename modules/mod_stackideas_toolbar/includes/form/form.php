<?php
/**
* @package      StackIdeas
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* StackIdeas Toolbar is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class ToolbarForm
{
	/**
	 * Responsible to render the logout form data
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function logout()
	{
		$adapter = FDT::getAdapter(FDT::getMainComponent());

		$themes = FDT::themes();
		$output = $themes->output('form/logout', ['return' => $adapter->logoutRedirect()]);

		return $output;
	}
}
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

class plgPayplansFriendSubscriptionAjax extends PayPlans
{
	/**
	 * Renders a browser user dialog
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function browse()
	{
		$callback = $this->input->get('jscallback', '', 'word');
		$listOption = $this->input->get('listOption', '');

		$theme = PP::themes();
		$theme->set('callback' , $callback);
		$theme->set('listOption', $listOption);
		$contents = $theme->output('plugins:/payplans/friendsubscription/dialogs/browse');

		$ajax = PP::ajax();
		return $ajax->resolve($contents);
	}
}

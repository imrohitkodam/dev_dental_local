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

class PayplansViewRewriter extends PayPlansAdminView
{
	/**
	 * Displays rewriter tokens
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function view()
	{
		$args = [];
		$apps = PP::event()->trigger('onPayplansRewriterDisplayTokens', $args);

		// TODO: Let rewriter lib process the mapping.
		$rewriter = PP::rewriter();
		$items = $rewriter->rewriterMapping();

		$theme = PP::themes();
		$theme->set('rewriter', $rewriter);
		$theme->set('items', $items);
		$theme->set('apps', $apps);
		$output = $theme->output('admin/rewriter/dialogs/view');

		return $this->ajax->resolve($output);
	}
}

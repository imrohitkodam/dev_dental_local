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
	public function display($tpl = null)
	{
		// Currently only available for the backend and site admin
		if (!PP::isSiteAdmin()) {
			$this->info->set(JText::_('COM_PP_INVALID_PAGE'), 'error');

			$redirect = PPR::_('index.php?option=com_payplans&view=dashboard', false);
			return $this->app->redirect($redirect);
		}

		$args = [];
		$apps = PP::event()->trigger('onPayplansRewriterDisplayTokens', $args);

		// TODO: Let rewriter lib process the mapping.
		$rewriter = PP::rewriter();
		$items = $rewriter->rewriterMapping();

		$this->set('rewriter', $rewriter);
		$this->set('items', $items);
		$this->set('apps', $apps);

		return parent::display('rewriter/default');

	}
}

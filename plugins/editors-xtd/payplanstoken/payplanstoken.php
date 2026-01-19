<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class PlgButtonPayplanstoken extends JPlugin
{
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Renders the button when editor is rendered
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function onDisplay($name)
	{
		$user = JFactory::getUser();
		$isAdmin = $user->authorise('core.admin');

		// Currently only available for the backend and site admin
		if (!PP::isFromAdmin() || !$isAdmin) {
			return false;
		}

		$link = 'index.php?option=com_payplans&view=rewriter&tmpl=component&jscallback=payplansCallback';

		$button = new JObject;
		$button->modal = true;
		$button->class = 'btn';
		$button->link = $link;
		$button->text = JText::_('Payplans Token');
		$button->name = 'database';
		$button->options = "{handler: 'iframe', size: {x: 800, y: 500}}";

		return $button;
	}
}

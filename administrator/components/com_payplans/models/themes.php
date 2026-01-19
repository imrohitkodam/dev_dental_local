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

PP::import('admin:/includes/model');

class PayplansModelThemes extends PayPlansModel
{
	public function __construct()
	{
		parent::__construct('themes');
	}

	/**
	 * Retrieves the current Joomla template
	 *
	 * @since	4.2.6
	 * @access	public
	 */
	public function getCurrentJoomlaTemplate()
	{
		static $template = null;

		if (is_null($template)) {

			$db = PP::db();

			$query = 'SELECT ' . $db->nameQuote('template') . ' FROM ' . $db->nameQuote('#__template_styles');
			$query .= ' WHERE ' . $db->nameQuote('home') . '!=' . $db->Quote(0);
			$query .= ' AND ' . $db->qn('client_id') . '=' . $db->Quote(0);

			$db->setQuery($query);

			$template = $db->loadResult();
		}

		return $template;
	}
}

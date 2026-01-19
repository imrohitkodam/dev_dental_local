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

use Joomla\CMS\Factory;

// Load toolbar engine
require_once(dirname(dirname(__DIR__)) . '/includes/toolbar.php');

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldToolbarmenu extends JFormField
{
	protected $type = 'toolbarmenu';

	protected function getInput()
	{
		$theme = FDT::themes();

		$output = $theme->output('admin/html/toolbarmenu', [
			'id' => $this->id,
			'name' => $this->name,
			'value' => $this->value,
			'menus' => $this->getMenus(),
			'toolbars' => $this->getExtensionToolbars()
		]);

		return $output;
	}

	protected function getExtensionToolbars()
	{
		$toolbars = [
			(object) [
				'value' => 'toolbardefault-easyblog',
				'title' => 'MOD_SI_TOOLBAR_EB_TOOLBAR'
			],
			(object) [
				'value' => 'toolbardefault-easysocial',
				'title' => 'MOD_SI_TOOLBAR_ES_TOOLBAR'
			],
			(object) [
				'value' => 'toolbardefault-easydiscuss',
				'title' => 'MOD_SI_TOOLBAR_ED_TOOLBAR'
			],
			(object) [
				'value' => 'toolbardefault-payplans',
				'title' => 'MOD_SI_TOOLBAR_PP_TOOLBAR'
			],
			(object) [
				'value' => 'toolbardefault-komento',
				'title' => 'MOD_SI_TOOLBAR_KT_TOOLBAR'
			]
		];

		return $toolbars;
	}

	protected function getMenus()
	{
		$db = JFactory::getDbo();
		$query = 'SELECT `menutype`, `title` FROM `#__menu_types`';
		$db->setQuery($query);

		$menus = $db->loadObjectList();

		return $menus;
	}
}

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

class JFormFieldToolbarsearch extends JFormField
{
	protected $type = 'toolbarsearch';

	protected function getInput()
	{
		$theme = FDT::themes();

		$output = $theme->output('admin/html/toolbarsearch', [
			'id' => $this->id,
			'name' => $this->name,
			'value' => $this->value,
			'search' => $this->getExtensionDefaultSearch()
		]);

		return $output;
	}

	protected function getExtensionDefaultSearch()
	{
		$search = [
			(object) [
				'value' => 'search-easyblog',
				'title' => 'MOD_SI_TOOLBAR_EB_SEARCH_OPT'
			],
			(object) [
				'value' => 'search-easydiscuss',
				'title' => 'MOD_SI_TOOLBAR_ED_SEARCH_OPT'
			],
			(object) [
				'value' => 'search-easysocial',
				'title' => 'MOD_SI_TOOLBAR_ES_SEARCH_OPT'
			]
		];

		// PP do not have search feature, therefore we'll exclude it. #22#note_174746

		return $search;
	}
}

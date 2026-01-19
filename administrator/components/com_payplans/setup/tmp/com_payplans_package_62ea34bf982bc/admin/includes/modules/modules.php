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

class PPModules extends PayPlans
{
	public $name = null;
	public $module = null;
	public $params = null;
	public $baseurl = null;

	public function __construct($module)
	{
		parent::__construct();

		$this->module = $module;
		$this->name = $this->module->module;

		// At times, the $module->params variable could be converted into a registry object already by the Joomla template
		// To ensure compatibility with these sort of templates, we cannot convert it into a JRegistry again.
		if ($this->module->params instanceof JRegistry) {
			$this->params = $this->module->params;
		} else {
			$this->params = new JRegistry($this->module->params);
		}

		$this->baseurl = JURI::root(true);

		PP::initialize();

		// Try to load component's language file just in case the module needs it
		JFactory::getLanguage()->load('com_payplans', JPATH_ROOT);
	}

	/**
	 * Determines if this is a mobile layout
	 *
	 * @since	3.7
	 * @access	public
	 */
	public function isMobile()
	{
		static $responsive = null;

		if (is_null($responsive)) {
			$responsive = PP::responsive()->isMobile();
		}

		return $responsive;
	}
}
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

PP::import('admin:/includes/views');

use Foundry\Libraries\Scripts;

abstract class PayPlansAdminView extends PayPlansView
{
	protected $page = null;
	protected $help = null;
	private $sidebar = true;

	public function __construct($config = array())
	{
		// Initialize page.
		$page = new stdClass();

		// Initialize page values.
		$page->heading = '';
		$page->description = '';

		$this->page = $page;
		$this->my = JFactory::getUser();
		$this->showSidebar = true;
		$this->theme = PP::themes();

		$view = $this->getName();

		parent::__construct($config);
	}

	/**
	 * Inserts an initialization set of css to prevent screen flickering
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function addInitializeCss()
	{
		if (!PP::isJoomla4()) {
			return;
		}

		$css = '
body.com_payplans #pp.pp-backend {margin: 0 -15px;}
body .subhead .btn {font-size: 12px; line-height: 26px; height: 26px;}
body .subhead {padding: 4px 0;}
';

		$this->doc->addStyleDeclaration($css);

	}

	/**
	 * Adds a new help button
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function addHelpButton($url)
	{
		$this->help = $url;
		// JToolbarHelper::help('com_payplans', true, $url);
	}

	/**
	 * Checks for user access
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function authorise($command, $extension = 'com_payplans')
	{
		return $this->my->authorise($command, $extension);
	}

	/**
	 * Checks if the current viewer can really access this section
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function checkAccess($rule)
	{
		$rule = 'payplans.' . $rule;

		if (!$this->my->authorise($rule , 'com_payplans')) {
			PP::info()->set(JText::_('JERROR_ALERTNOAUTHOR'), 'error');

			return $this->app->redirect('index.php?option=com_payplans');
		}
	}

	/**
	 * Central method that is called by child items to display the output.
	 * All views that inherit from this class should use display to output the html codes.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$format = $this->doc->getType();
		$tmpl = $this->input->get('tmpl', '', 'string');

		// Joomla page title should always display PayPlasns
		JToolbarHelper::title(JText::_('COM_PP_TITLE'), 'payplans');

		if ($format === 'html') {

			Scripts::initializeAdmin();

			// Ensure that the behavior.core is loaded
			JHtml::_('behavior.core');

			// Prevent screen flickering on sidebar
			// $this->addInitializeCss();

			// Initialize the necessary css / js
			PPCompat::renderJQueryFramework();
			PP::initialize('admin');

			$config = PP::config();

			$theme = PP::themes();

			$class = '';

			if ($tmpl == 'component') {
				$class = 'pp-window';
			}

			// Main wrapper
			$class = isset($class) ? $class : '';

			// Add the sidebar to the page obj.
			$sidebar = $this->getSideBar();

			// temp fix on styleguide to hide sidebar.
			$isStyleGuide = false;

			if ($this->input->get('view') == 'styleguide') {
				$isStyleGuide = true;
			}

			$result = $this->triggerPlugins('onPayplansViewBeforeExecute');
			// Capture contents.
			ob_start();
			parent::display('admin/' . $tpl);
			$html = ob_get_contents();
			ob_end_clean();

			$version = PP::getLocalVersion();

			$theme->set('help', $this->help);
			$theme->set('showSidebar', $this->showSidebar);
			$theme->set('class', $class);
			$theme->set('version', $version);
			$theme->set('tmpl', $tmpl);
			$theme->set('contents', $html);
			$theme->set('sidebar', $sidebar);
			$theme->set('isStyleguide', $isStyleGuide);
			$theme->set('page', $this->page);
			$theme->set('customAction', $this->getCustomAction());

			$contents = $theme->output('admin/structure/default');

			// Collect all javascripts attached so that we can output them at the bottom of the page
			$scripts = PP::scripts()->getScripts();
			$this->doc->addCustomTag($scripts);

			echo $contents;

			return;
		}

		return parent::display($tpl);
	}

	/**
	 * Checks if the current viewer can really access this section
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function hasAccess($rule)
	{
		$rule = 'payplans.' . $rule;

		if (!$this->my->authorise($rule , 'com_payplans')) {
			return false;
		}
		return true;
	}

	/**
	 * Hides the sidebar
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function hideSidebar()
	{
		$this->sidebar = false;

		if (PP::isJoomla4()) {
			$this->input->set('hidemainmenu', true);
		}
	}

	/**
	 * Get custom action
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getCustomAction()
	{
		if (isset($this->page->customAction) && $this->page->customAction) {
			return $this->page->customAction;
		}
	}

	/**
	 * This is only used for the model on the back end to retrieve available states
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getStates($availableStates = [], $model = null)
	{
		if (is_null($model)) {
			$model = PP::model($this->getName());
		}

		$states = new stdClass();

		foreach ($availableStates as $state) {
			$states->$state = $model->getState($state);
		}

		return $states;
	}

	/**
	 * Allows caller to set the header title in the structure layout.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function heading($title, $description = '', $prefix = true)
	{
		if ($prefix) {
			$title = str_replace(' ', '_', $title);
			$title = 'COM_PP_HEADING_' . strtoupper($title);
		}

		$desc = $title . '_DESC';

		if ($description) {
			$desc = $description;
		}

		$this->page->heading = JText::_($title);
		$this->page->description = JText::_($desc);
	}

	/**
	 * Set custom action to be display on the header
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function setCustomAction($html)
	{
		$this->page->customAction = $html;
	}

	/**
	 * Returns the sidebar html codes.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getSideBar()
	{
		if (!$this->sidebar) {
			return;
		}

		$showSidebar = $this->input->get('sidebar', 1);
		$showSidebar = $showSidebar == 1 ? true : false;

		if (!$showSidebar) {
			return;
		}

		$view = $this->getName();
		$layout = $this->getLayout();

		$model = PP::model('Sidebar');
		$menus = $model->getItems($view);

		$output = PP::fd()->html('admin.sidebar', $menus, $view, $layout);

		return $output;
	}
}

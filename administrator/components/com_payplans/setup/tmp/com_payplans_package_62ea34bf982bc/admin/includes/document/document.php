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

class PPDocument extends PayPlans
{
	static $instance = null;
	private $helper = null;
	
	public $scripts = array();
	public $stylesheets = array();
	public $inlineStylesheets = array();
	public $title = null;

	public function __construct()
	{
		parent::__construct();

		// Determine the current document type.
		$type = $this->doc->getType();

		$known = array('ajax', 'feed', 'html', 'json', 'embed');

		if (!in_array($type, $known)) {
			return;
		}
	}

	/**
	 * There should only be one copy of document running at page load.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public static function getInstance()
	{
		if (self::$instance === null) {
			self::$instance	= new self();
		}

		return self::$instance;
	}

	/**
	 * We need to wrap all javascripts into a single <script> block. This helps us maintain a single object.
	 *
	 * @since	3.7.0
	 * @access	private
	 */
	public function addScript($path)
	{
		$url = $this->toUri($path);

		if (!empty($url)) {
			$this->scripts[] = $url;
		}
	}

	/**
	 * Internal method to build scripts to be embedded on the head or
	 * external script files to be added on the head.
	 *
	 * @since	3.7.0
	 * @access	private
	 */
	public function processScripts()
	{
		// Scripts
		if (!empty($this->scripts)) {
			foreach ($this->scripts as $script) {
				$this->doc->addScript($script);
			}
		}

		if (empty($this->inlineScripts)) {
			return;
		}

		// Inline scripts
		$script = PP::get('Script');
		$script->file = PP_MEDIA . '/head.js';
		$script->scriptTag	= true;
		$script->CDATA = true;
		$script->set('contents', implode($this->inlineScripts));
		$inlineScript = $script->parse();

		if ($this->doc->getType() == 'html') {
			$this->doc->addCustomTag($inlineScript);
		}
	}

	/**
	 * Allows caller to set a canonical link
	 *
	 * @since	3.7
	 * @access	public
	 */
	public function canonical($link)
	{
		$docLinks = $this->doc->_links;

		// if joomla already added a canonial links here, we remove it.
		if ($docLinks) {
			foreach($docLinks as $jLink => $data) {
				if ($data['relation'] == 'canonical' && $data['relType'] == 'rel') {

					//unset this variable
					unset($this->doc->_links[$jLink]);
				}
			}
		}

		$link = PP::string()->escape($link);
		$this->doc->addHeadLink($link, 'canonical');
	}

	/**
	 * Given a string to be added to the title, compute it with the site title
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getSiteTitle($title = '')
	{
		$jConfig = PP::jconfig();
		$addTitle = $jConfig->get('sitename_pagetitles');
		$siteTitle = $jConfig->get('sitename');

		if ($addTitle) {

			$siteTitle 	= $jConfig->get('sitename');

			if ($addTitle == 1) {
				$title 	= $siteTitle . ' - ' . $title;
			}

			if ($addTitle == 2) {
				$title	= $title . ' - ' . $siteTitle;
			}
		}

		return $title;
	}

	/**
	 * Sets the title of the page.
	 *
	 * @since	4.2.8
	 * @access	public
	 */
	public function title($default, $override = true, $view = null)
	{
		// Get the view.
		$view = is_null($view) ? $this->input->get('view', '', 'cmd') : $view;

		// Get the passed in title.
		$title = $default;

		// Get current menu
		$activeMenu = $this->app->getMenu()->getActive();

		if ($activeMenu) {
			$params = $activeMenu->getParams();
			$menuView = isset($activeMenu->query['view']) ? $activeMenu->query['view'] : false;

			if ($menuView && $params->get('page_title') && $override && $view == $menuView) {
				$title = $params->get('page_title');
			}
		}

		// Apply translations on the title
		$title = JText::_($title);

		// Prepare Joomla's site title if necessary.
		$this->title = $this->getSiteTitle($title);

		$this->doc->setTitle($this->title);
	}
}

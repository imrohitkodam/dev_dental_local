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

abstract class PayPlansSiteView extends PayPlansView
{
	protected $page = null;

	public function __construct($config = [])
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

		PP::loadLanguages(JPATH_ADMINISTRATOR);

		parent::__construct($config);

	}
	
	/**
	 * Central method that is called by child items to display the output.
	 * All views that inherit from this class should use display to output the html codes.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$type = $this->doc->getType();
		$show = $this->input->get('show', '', 'string');

		// Get the current view.
		$view = $this->input->get('view', '', 'cmd');
		$view = !empty($view) ? ' view-' . $view : '';

		// Get the current task
		$task = $this->input->get('task', '', 'cmd');
		$task = !empty($task) ? ' task-' . $task : '';

		// Set vary user-agent
		$this->app->setHeader('Vary', 'User-Agent', true);

		PP::initialize('site');

		// Render the custom styles
		if ($type == 'html') {
			$theme = PP::themes();
			$customCss = $theme->output('site/structure/css');

			// Compress custom css
			$customCss = PP::minifyCSS($customCss);

			$this->doc->addCustomTag($customCss);
		}

		// Include main structure here.
		$theme = PP::themes();
		$config = PP::config();

		// Maybe we can change the trigger name to 'onPayplansViewBeforeExecute' instead.
		// Before rendering the output, trigger plugins
		// for 'onPayplansViewBeforeRender', the execution moved to controller
		// see PayPlansController::display()

		$args = [&$this, &$task];
		PPEvent::trigger('onPayplansViewBeforeRender', $args, '', $this);

		// Do not allow zooming on mobile devices
		if ($theme->isMobile()) {
			$this->doc->setMetaData('viewport', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no');
		}
        
        // Only in YOOtheme PRO Tntegration
        if ($this->app->isClient('site') && stripos($this->app->getTemplate(), 'yootheme') === 0) {
            
			$args = [$this, $tpl];
            
            // Trigger the custom YOOtheme Pro event
			$dispatcher = PP::dispatcher();
			$dispatcher->trigger('onLoadTemplate', $args);

			// Retrieve the output if something set from the YooTheme builder page
            $yooThemeOutput = $this->get('_output');

            // Print the output and don't display anything else if the event override the output
            if ($yooThemeOutput) {
                echo $yooThemeOutput;
                return;
            }
        }

		// Capture output.
		ob_start();
		parent::display($tpl);
		$contents = ob_get_contents();
		ob_end_clean();

		// Trigger apps to allow them to attach html output on the page too.
		// $dispatcher = PP::dispatcher();
		// $dispatcher->trigger('user', 'onComponentOutput', array(&$contents));

		// Get the menu's suffix
		$suffix = $this->getMenuSuffix();

		// Get any "id" or "cid" from the request.
		$object = $this->input->get('id', $this->input->get('cid', 0, 'int'), 'int');
		$object = !empty($object) ? ' object-' . $object : '';

		// Get any layout
		$layout = $this->input->get('layout', '', 'cmd');
		$layout = !empty($layout) ? ' layout-' . $layout : '';

		// Determines if the layout is a full page layout
		$fullPage = $this->input->get('tmpl', '', 'word');
		$fullPage = $fullPage == 'component' ? true : false;

		// Get page level scripts to be added on the page
		$scripts = PP::scripts()->getScripts();

		$theme->set('fullPage', $fullPage);
		$theme->set('suffix', $suffix);
		$theme->set('layout', $layout);
		$theme->set('object', $object);
		$theme->set('task', $task);
		$theme->set('view', $view);
		$theme->set('show', $show);
		$theme->set('contents', $contents);
		$theme->set('scripts', $scripts);

		if ($fullPage) {
			$output = $theme->output('site/structure/full');
		} else {
			$output = $theme->output('site/structure/default');
		}

		$args = [&$this, &$task, &$output];
		PPEvent::trigger('onPayplansViewAfterRender', $args, '', $this);

		echo $output;
	}

	/**
	 * Generic 404 error page
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function error()
	{
		return JError::raiseError(404, JText::_('COM_PP_PAGE_NOT_AVAILABLE'));
	}

	/**
	 * Retrieve the menu suffix for a page
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getMenuSuffix()
	{
		$menu 	= $this->app->getMenu()->getActive();
		$suffix	= '';

		if ($menu) {

			if (PP::getJoomlaVersion() < 3.7) {
				$suffix = $menu->params->get('pageclass_sfx', '');

				return $suffix;
			}

			// We can no longer retrieve the params property from JMenuItem directly in J4. 
			// Use the getParams() method instead since it is available since Joomla 3.7
			$suffix = $menu->getParams()->get('pageclass_sfx', '');
		}

		return $suffix;
	}

	/**
	 * Allows overriden objects to redirect the current request only when in html mode.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function redirect($uri, $message = '', $class = '')
	{
		if (PP::isJoomla4()) {
			if ($message) {
				$this->app->enqueueMessage($message, $class);
			}

			return $this->app->redirect($uri);
		}

		return $this->app->redirect($uri, $message, $class);
	}

	/**
	 * Adds the breadcrumbs on the site
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function setPathway($title, $link ='')
	{
		// Get the pathway
		$pathway = $this->app->getPathway();

		// Translate the pathway item
		$title = JText::_($title);
		$state = $pathway->addItem($title, $link);

		return $state;
	}
}

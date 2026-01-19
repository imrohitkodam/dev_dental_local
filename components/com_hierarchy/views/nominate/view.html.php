<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_hierarchy
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Hierarchy.
 *
 * @since  1.6
 */
class HierarchyViewNominate extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	protected $params;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		$app                 = JFactory::getApplication();
		$this->state         = $this->get('State');
		$this->items         = $this->get('Items');

		$path                     = JPATH_ROOT . '/components/com_jticketing/helpers/main.php';

		if (!class_exists('jticketingmainhelper'))
		{
			JLoader::register('jticketingmainhelper', $path);
			JLoader::load('jticketingmainhelper');
		}

		$jticketingmainhelper = new jticketingmainhelper;

		if (!empty($this->items))
		{
			foreach ($this->items AS $items)
			{
				$params = array();
				$params['only_completed_orders'] = 1;
				$nominee = JFactory::getUser($items->id);
				$groups = $nominee_groups = '';
				$groups = $nominee->getAuthorisedViewLevels();
				$nominee_groups = $jticketingmainhelper->getAccessLevels($groups);
				$nmgrp_array = array();
				foreach ($nominee_groups as $nmgrp)
				{
					$nmgrp_array[] = $nmgrp->title;
				}

				$items->nominee_groups = implode(",", $nmgrp_array);
				$already_nominated_events_obj = $jticketingmainhelper->geteventnamesBybuyer($items->id, $params);
				$already_nominated_events = array();
				if (!empty($already_nominated_events_obj))
				{

					foreach ($already_nominated_events_obj AS $already_nominated_event)
					{
						$date_str = $startdate = $enddate ='';
						$startdate = JFactory::getDate($already_nominated_event->startdate)->Format(JText::_('COM_HIERARCHY_DATE_FORMAT_SHOW_SHORT'));
						$enddate = JFactory::getDate($already_nominated_event->enddate)->Format(JText::_('COM_HIERARCHY_DATE_FORMAT_SHOW_SHORT'));
						$date_str = $startdate . JText::_('COM_HIERARCHY_TO') . $enddate;
						$already_nominated_events[] = $already_nominated_event->title . " (" .$date_str . " )";
					}
				}

				if (!empty($already_nominated_events))
				{
					$items->already_nominated_events = implode(", ", $already_nominated_events);
				}
			}
		}

		$this->assignNominee = $this->get('AssignEventToNominee');
		$this->pagination    = $this->get('Pagination');
		$this->params        = $app->getParams('com_hierarchy');
		$this->activeFilters = $this->get('ActiveFilters');

		// View also takes responsibility for checking if the user logged in with remember me.
		$user = JFactory::getUser();
		$userLogin = JFactory::getUser()->id;

		if (empty($userLogin))
		{
			// If so, the user must login to edit the password and other data.
			// What should happen here? Should we force a logout which detroys the cookies?
			$app->enqueueMessage(JText::_('COM_HIERARCHY_NOMINATION_MUST_LOGIN'), 'error');
			$app->redirect(JUri::base() . 'index.php?option=com_users&view=login', '', 302);

			return false;
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document.
	 *
	 * @return  void.
	 */
	protected function _prepareDocument()
	{
		$app = JFactory::getApplication();
		$menus = $app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// We need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_HIERARCHY_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title))
		{
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}

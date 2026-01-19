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
class HierarchyViewNominates extends JViewLegacy
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
		$app = JFactory::getApplication();
		$jinput        = JFactory::getApplication()->input;
		$this->state = $this->get('State');
		$this->items = '';
		$this->items_data = $this->get('Items');
		$nomineeId  = $jinput->get('nomineeId');
		$popupClose  = $jinput->get('popupClose');
		$hiearchy_params     = $app->getParams('com_hierarchy');

		$user = JFactory::getUser();
		$userId = $user->get('id');

		if (empty($userId))
		{
			// .JFactory::getApplication()->input->get('Itemid');
			$self_nominate_link = JURI::root(). "index.php?option=com_hierarchy&view=nominates&nomineeId=". $nomineeId ."&Itemid=";
			$uri    = JRoute::_($self_nominate_link, false);
			$url    = base64_encode($uri);
			$msg = JText::_('COM_HIERARCHY_NOMINATION_LOGIN_VIEW');
			$link = "<a href=" .JRoute::_('index.php?option=com_users&view=login&return=' . $url, false). ">Click Here to login</a>";
			echo $msg = str_replace("[LOGIN_LINK]",$link, $msg);
			return;
		}

		// if nominee is self check self nomination
		if ($nomineeId == $userId)
		{
			$groups = $user->getAuthorisedViewLevels();
			$app  = JFactory::getApplication();
			$hiearchy_params     = $app->getParams('com_hierarchy');
			$groups_for_self_nomination     =  $hiearchy_params->get('groups_for_self_nomination');
			$allow_sef_nomination = count(array_intersect($groups, $groups_for_self_nomination));

			if (empty($allow_sef_nomination))
			{
				echo $msg = JText::_('COM_HIERARCHY_NOMINATION_NOT_AUTHORISED_SELF_NOMINATION_FAILED');
				return;
			}
		}



		$groups = $user->getAuthorisedViewLevels();
		$user_is_valid=0;
		$groups_for_any_nomination     =  $hiearchy_params->get('groups_for_any_nomination');
		$allow_any_nomination = count(array_intersect($groups, $groups_for_any_nomination));

		// Check if nominee user is under curent logged in user
		$nominee_is_allowed = $this->getmodel()->ReportToList($nomineeId, $userId);

		if ($nomineeId != $userId)
		{
			if($allow_any_nomination)
			{
			}
			else if(!$nominee_is_allowed)
			{
				echo $msg = JText::_('COM_HIERARCHY_NOMINATION_NOT_AUTHORISED_NOMINEE_NOT_UNDER');
				return;
			}
		}

		$already_nominated_event = $this->getmodel()->alreadynominated($nomineeId);


		if (!empty($already_nominated_event) and !isset($popupClose))
		{
			//echo $msg = JText::_('COM_HIERARCHY_ALREADY_NOMINATED');
			//return;
		}

		$path                     = JPATH_ROOT . '/components/com_jticketing/controllers/nomim.php';

		if (!class_exists('HierarchyControllerNominate'))
		{
			JLoader::register('HierarchyControllerNominate', $path);
			JLoader::load('HierarchyControllerNominate');
		}
		$i = 0;
		foreach ($this->items_data as $i => $item)
		{
			$jinput->set('catId', $item->catId);
			$jinput->set('nomineeId', $nomineeId);

			$options_arr = $this->get('Events');

			if($options_arr['disabled'] ==1)
			{
				$disabled=1;
				$msg = $options_arr['msg'];
			}
			else
			{
				$disabled=0;
				$this->items[$i] = $item;
				$this->items[$i]->options=$options_arr['options'];
				$i++;
			}


		}

		$this->disabled = $disabled;
		$this->disabled_msg = $msg['date_of_joining_missing'];
		$this->assignNominee = $this->get('AssignEventToNominee');
		$this->pagination = $this->get('Pagination');
		$this->params = $app->getParams('com_hierarchy');

		// $this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

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

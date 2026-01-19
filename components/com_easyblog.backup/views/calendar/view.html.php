<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewCalendar extends EasyBlogView
{
	/**
	 * Displays the calendar layout
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tpl = null)
	{
		// Set the pathway
		$this->setPathway('COM_EASYBLOG_CALENDAR_BREADCRUMB');

		// Get the year and month if it's defined in the menu
		$year = $this->theme->params->get('calendar_year', 0);
		$month = $this->theme->params->get('calendar_month', 0);

		// Perhaps the year and month are being passed as query strings
		$year = $this->input->get('year', $year, 'default');
		$month = $this->input->get('month', $month, 'default');
		$day = $this->input->get('day', '01', 'default');

		// If category is provided, we should filter by specific category
		$category = $this->input->get('category', array(), 'array');
		$category = implode(",",$category);

		// Get the Itemid
		$itemId = $this->input->get('Itemid', 0, 'int');

		// Try to generate timestamp if there's year and month provided
		$timestamp = '';
		$timestampDate = '';

		if ($year && $month) {
			$timestampDate = EB::date($year . '-' . $month . '-' . $day);
			$timestamp = strtotime($timestampDate->toSql());
		}

		// Get the date object based on the timestamp provided
		$date = EB::calendar()->getDateObject($timestamp);

		// Get the related data
		$calendar = EB::calendar()->prepare($date);

		// Default namespace
		$namespace = 'blogs/calendar/list';

		// Do we want to display a list or a calendar
		$calendar = $this->input->get('calendar', false, 'bool');
		$posts = array();

		if ($calendar) {
			$namespace = 'blogs/calendar/default';
		} else {
	        // Get the posts data
	        $model = EB::model('Archive');
	        $posts = $model->getArchivePostListByMonth($date->month, $date->year, false, $category);

	        $posts = EB::formatter('list', $posts);
		}

		// Prepare the date
		if (!$timestampDate) {
			$timestampDate = EB::date($date->unix);
		}

		// Set list view permalink
		$listViewUrl = EBR::_('index.php?option=com_easyblog&view=calendar&month=' . $date->month . '&year=' . $date->year);

		// meta, too late to add new meta id so we 'trick' the system to use the custom description.
		EB::setMeta('0', META_TYPE_VIEW, JText::sprintf('COM_EASYBLOG_CALENDAR_PAGE_TITLE_VIEW', $timestampDate->format('F'), $timestampDate->format('Y')));

		// Set the page title
		$title = EB::getPageTitle(JText::sprintf('COM_EASYBLOG_CALENDAR_PAGE_TITLE_VIEW', $timestampDate->format('F'), $timestampDate->format('Y')));
		parent::setPageTitle($title, false, $this->config->get('main_pagetitle_autoappend'));
		
		$this->set('posts', $posts);
		$this->set('category', $category);
		$this->set('date', $timestampDate);
		$this->set('timestamp', $timestamp);
		$this->set('listViewUrl', $listViewUrl);

		return parent::display($namespace);
	}
}

<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_Tjlms
 * @copyright  Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://www.techjoomla.com
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;

jimport('joomla.application.component.view');   

/**
 * Class for Review View
 *
 * @since  1.0.0
 */
class TjlmsViewOwnassessment extends HtmlView
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @since  1.0.0
	 */
	public function display($tpl = null)
	{
		
		$app = Factory::getApplication();
		$jinput = Factory::getApplication()->input;
		$user = Factory::getUser();
		

		if (empty($user->id))
		{
			$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'warning');

			return;
		}
		$this->items = $this->get('Items');
		$this->state = $this->get('State');
		$this->pagination = $this->get('Pagination');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$this->tjlmsparams = ComponentHelper::getParams('com_tjlms');
		$this->adminKey = $this->tjlmsparams->get('admin_key_review_answersheet', 'abcd1234', 'STRING');
		$this->adminKey = md5($this->adminKey);

		parent::display($tpl);
	}

}



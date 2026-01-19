<?php
/**
 * @version    SVN: <svn_id>
 * @package    Techjoomla.Libraries
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
jimport('techjoomla.view.csv');

/**
 * TjCsv
 *
 * @package     Techjoomla.Libraries
 * @subpackage  TjCsv
 * @since       1.0
 */
class TjlmsViewAttemptreport extends TjExportCsv
{
	/**
	 * Display view
	 *
	 * @param   STRING  $tpl  template name
	 *
	 * @return  Object|Boolean in case of success instance and failure - boolean
	 *
	 * @since	1.6
	 */
	public function display($tpl = null)
	{
		$input = JFactory::getApplication()->input;
		$user  = JFactory::getUser();
		$userAuthorisedExport = $user->authorise('core.create', 'com_tjlms');

		if ($userAuthorisedExport !== true || !$user->id)
		{
			// Redirect to the list screen.
			$redirect = JRoute::_('index.php?option=com_tjlms&view=attemptreports', false);
			JFactory::getApplication()->redirect($redirect, JText::_('JERROR_ALERTNOAUTHOR'));

			return false;
		}
		else
		{
			if ($input->get('task') == 'download')
			{
				$fileName = $input->get('file_name');
				$this->download($fileName);
				JFactory::getApplication()->close();
			}
			else
			{
				parent::display();
			}
		}
	}
}

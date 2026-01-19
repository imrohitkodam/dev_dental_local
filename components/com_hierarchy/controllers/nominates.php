<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_hierarchy
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
jimport( 'joomla.client.http' );
require_once JPATH_COMPONENT . '/controller.php';

/**
 * The Nominates List Controller
 *
 * @since  1.6
 */
class HierarchyControllerNominates extends HierarchyController
{
	/**
	 * Proxy for getModel
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  The array of possible config values. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.6
	 */
	public function &getModel($name = 'Nominates', $prefix = 'HierarchyModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	/**
	 * applyEvent
	 *
	 * @return  avoid
	 *
	 * @since   1.0
	 */
	public function applyEvent()
	{
		$jinput     = JFactory::getApplication()->input;
		$mainframe = JFactory::getApplication();
		$eventlist   = $jinput->post->get('eventlist', '', 'array');
		$catId   = $jinput->post->get('catId', '', 'array');
		$nomineeId   = $jinput->post->get('nomineeId', '', 'int');
		$model      = $this->getModel();
		$DATA       = $model->applyEvent($nomineeId, $eventlist);
		$doc = JFactory::getDocument();
		$msg = JText::_('COM_HIERARCHY_NOMINATION_COMPLETE');
		// $mainframe->enqueueMessage($msg);
		$mainframe->redirect(JRoute::_('index.php?option=com_hierarchy&view=nominates&nomineeId='.$nomineeId.'&Itemid=668&popupClose=1',false));
	}

	/**
	 * searchLocationEvent
	 *
	 * @return  avoid
	 *
	 * @since   1.0
	 */
	public function searchLocationEvent()
	{
		$jinput     = JFactory::getApplication()->input;
		$location   = $jinput->post->get('location', '', 'string');
		$model      = $this->getModel();
		$DATA       = $model->getAssignEventToNominee();
		$this->getEvents($location);
	}

}

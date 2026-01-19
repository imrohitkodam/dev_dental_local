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

require_once JPATH_COMPONENT . '/controller.php';

/**
 * The Nominate List Controller
 *
 * @since  1.6
 */
class HierarchyControllerNominate extends HierarchyController
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
	public function &getModel($name = 'Nominate', $prefix = 'HierarchyModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	/**
	 * search location event
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

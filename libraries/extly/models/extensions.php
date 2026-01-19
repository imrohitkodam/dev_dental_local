<?php

/**
 * @package     Extly.Library
 * @subpackage  lib_extly - Extly Framework
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (C) 2007 - 2017 Extly, CB. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * ExtlyModelExtensions
 *
 * @package     Extly.Library
 * @subpackage  lib_extly
 * @since       1.0
 */
class ExtlyModelExtensions extends F0FModel
{
	/**
	 * buildQuery
	 *
	 * @param   bool  $overrideLimits  Param
	 *
	 * @return	F0FQuery
	 */
	public function buildQuery($overrideLimits = false)
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true)->select('*')->from($db->quoteName('#__extensions'));

		$fltName = $this->getState('name', null, 'string');

		if ($fltName)
		{
			$fltName = "%$fltName%";
			$query->where($db->qn('name') . ' LIKE ' . $db->q($fltName));
		}

		$fltEnabled = $this->getState('enabled', null, 'cmd');

		if ($fltEnabled != '')
		{
			$query->where($db->qn('enabled') . ' = ' . $db->q($fltEnabled));
		}

		$fltType = $this->getState('type', null, 'cmd');

		if ($fltType)
		{
			$query->where($db->qn('type') . ' = ' . $db->q($fltType));
		}

		$fltFolder = $this->getState('folder', null, 'cmd');

		if ($fltFolder)
		{
			$query->where($db->qn('folder') . ' = ' . $db->q($fltFolder));
		}

		$fltElement = $this->getState('element', null, 'cmd');

		if ($fltElement)
		{
			$query->where($db->qn('element') . ' = ' . $db->q($fltElement));
		}

		$order = $this->getState('filter_order', 'extension_id', 'cmd');

		if (!in_array($order, array_keys($this->getTable()->getData())))
		{
			$order = 'extension_id';
		}

		$dir = $this->getState('filter_order_Dir', 'ASC', 'cmd');
		$query->order($order . ' ' . $dir);

		return $query;
	}

	/**
	 * This method runs before the $data is saved to the $table. Return false to
	 * stop saving.
	 *
	 * @param   array   &$data   Param
	 * @param   JTable  &$table  Param
	 *
	 * @return bool
	 */
	protected function onBeforeSave(&$data, &$table)
	{
		jimport('extly.form.eform');
		EForm::onBeforeSaveWithParams($data);

		return parent::onBeforeSave($data, $table);
	}

	/**
	 * This method runs after an item has been gotten from the database in a read
	 * operation. You can modify it before it's returned to the MVC triad for
	 * further processing.
	 *
	 * @param   JTable  &$record  Param
	 *
	 * @return bool
	 */
	protected function onAfterGetItem(&$record)
	{
		jimport('extly.form.eform');
		$record->xtform = EForm::paramsToRegistry($record);

		return parent::onAfterGetItem($record);
	}
}

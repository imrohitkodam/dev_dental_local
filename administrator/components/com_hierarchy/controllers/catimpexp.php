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

jimport('joomla.application.component.controlleradmin');

/**
 * The Categories List Controller
 *
 * @since  1.6
 */
class HierarchyControllerCatimpexp extends JControllerAdmin
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
	public function getModel($name = 'catimpexp', $prefix = 'HierarchyModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	/**
	 * Method to categories csv Import
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function csvImport()
	{
		jimport('joomla.filesystem.file');
		$mainframe = JFactory::getApplication();
		$rs1 = @mkdir(JPATH_COMPONENT_ADMINISTRATOR . '/csv', 0777);

		// Start file heandling functionality *
		$fname = $_FILES['csvfile']['name'];
		$uploads_dir = JPATH_COMPONENT_ADMINISTRATOR . '/csv/' . $fname;
		move_uploaded_file($_FILES['csvfile']['tmp_name'], $uploads_dir);

		$file = fopen($uploads_dir, "r");

		$info = pathinfo($uploads_dir);

		if ($info['extension'] != 'csv')
		{
			$msg = JText::_('NOT_CSV_MSG');
			$mainframe->redirect(JRoute::_('index.php?option=com_hierarchy&view=catimpexp', false), "<b>" . $msg . "</b>");

			return;
		}

		$least_data = 0;

		$rowNum = '';

		while (($data = fgetcsv($file)) !== false)
		{
			if ($rowNum == 0)
			{
				// Parsing the CSV header
				$headers = array();

				foreach ($data as $d)
				{
					$headers[] = $d;
				}
			}
			else
			{
				// Parsing the data rows
				$rowData = array();

				foreach ($data as $d)
				{
					$rowData[] = $d;
				}

				$categoryData[] = array_combine($headers, $rowData);
			}

			$rowNum++;
		}

		fclose($file);

		// End file heandling functionality
		if (!$categoryData)
		{
			// $msg = JText::_('ZERO_CONTACTS_CSV_ERROR');
			$mainframe->redirect(JRoute::_('index.php?option=com_hierarchy&view=catimpexp', false), "<b>" . $msg . "</b>");

			return;
		}
		else
		{
			// $model = $this->getModel('hierarchy');

			$model = $this->getModel();

			 $return = $model->importCategoryCSVdata($categoryData);

			//~ foreach ($categoryData as $eachCategory)
			//~ {
				//~ $return = $model->importCategoryCSVdata($eachCategory);
			//~ }

			//~ $msg = JText::_('COM_HIERARCHY_IMPORT_CATEGORY_SUCCESSFULY');
			$mainframe->redirect(JRoute::_('index.php?option=com_hierarchy&view=catimpexp', false), "<b>" . $msg . "</b>");

			return;
		}
	}

	/**
	 * Method to categories csv export
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function csvexport()
	{
		// #load categories model file as it is
		JLoader::import('categories', JPATH_ADMINISTRATOR . '/components/com_categories/models');
		$table = JTable::getInstance('Category', 'CategoriesTable', $config = array());

		$categoriesModel = new CategoriesModelCategories;
		$extension = $categoriesModel->setState('filter.extension','com_jticketing');
		$DATA       = $categoriesModel->getItems();

		$db         = JFactory::getDBO();

		// Create CSV headers
		$csvData       = null;
		$csvData_arr[] = JText::_('Id');
		$csvData_arr[] = JText::_('Category Name');
		$csvData_arr[] = JText::_('ParentId');

		$filename = "EventCategoryExport" . date("Y-m-d_H-i", time());

		// Set CSV headers
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=" . $filename . ".csv");
		header("Pragma: no-cache");
		header("Expires: 0");

		$csvData .= implode(',', $csvData_arr);
		$csvData .= "\n";
		echo $csvData;

		$csvData      = '';

		foreach ($DATA as $data)
		{
			$csvData_arr1 = array();

			if ($data->id)
			{
				$csvData_arr1[] = $data->id;
				$csvData_arr1[] = $data->title;
				$csvData_arr1[] = $data->parent_id;

				$csvData = implode(',', $csvData_arr1);
				echo $csvData . "\n";
			}
		}

		jexit();
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$input = JFactory::getApplication()->input;
		$pks = $input->post->get('cid', array(), 'array');
		$order = $input->post->get('order', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}
}

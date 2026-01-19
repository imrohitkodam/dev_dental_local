<?php
/**
 * @version        1.11.0
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// No direct access.
defined('_JEXEC') or die;

class EDocmanModelCategory extends OSModelAdmin
{
	/**
	 * Method to get the form for the model.
	 *
	 * @param    array   $data     An optional array of data for the form.
	 * @param    boolean $loadData True if the form is to load its own data (default case), false if not.
	 *
	 * @return    JForm    A JForm object
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = parent::getForm($data, $loadData);
		$form->setFieldAttribute('category_layout', 'directory', JPATH_ROOT . '/components/com_edocman/view/category/tmpl');

		return $form;
	}

	/**
	 * Override loadData method to process path of category
	 */
	public function loadData()
	{
		parent::loadData();
		if ($this->data->path)
		{
			// Strip parent document path
			$pos = strrpos($this->data->path, '/');
			if ($pos !== false)
			{
				$this->data->path = substr($this->data->path, $pos + 1);
			}
		}
	}
	/**
	 * Store Category
	 *
	 * @param OSInput $input
	 *
	 * @return bool|void
	 * @throws Exception
	 */
	public function save($input)
	{
		// Initialise variables;
        $db   = JFactory::getDbo();
		$row  = $this->getTable();
		$data = $input->get('jform', array(), 'array');
		$id   = (int) $data['id'];
		// Delete old image if needed
		if ($id > 0)
		{
			$row->load($id);
			if ($input->has('del_image') && $row->image)
			{
				$categoryImagePath      = JPATH_ROOT . '/media/com_edocman/category/';
				$thumbCategoryImagePath = $categoryImagePath . 'thumbs/';
				if (JFile::exists($categoryImagePath . $row->image))
				{
					JFile::delete($categoryImagePath . $row->image);
				}
				if (JFile::exists($thumbCategoryImagePath . $row->image))
				{
					JFile::delete($thumbCategoryImagePath . $row->image);
				}
				$data['image'] = '';
			}
		}
		//Process image upload
		$files = $input->files->get('jform');
		$image = $files['image'];
		if (is_uploaded_file($image['tmp_name']))
		{
			if (!getimagesize($image['tmp_name']))
			{
				throw new Exception(JText::_('EDOCMAN_IMAGE_ERROR'));
			}
			$categoryImagePath      = JPATH_ROOT . '/media/com_edocman/category/';
			$thumbCategoryImagePath = $categoryImagePath . 'thumbs/';
			$fileName               = JFile::makeSafe($image['name']);
			if (JFile::upload($image['tmp_name'], $categoryImagePath . $fileName))
			{
				$config = EdocmanHelper::getConfig();
				$width  = $config->category_thumb_width > 0 ? $config->category_thumb_width : 100;
				$height = $config->category_thumb_height > 0 ? $config->category_thumb_height : 100;
				//Perform resizing image
				EdocmanHelper::resizeImage($categoryImagePath . $fileName, $thumbCategoryImagePath . $fileName, $width, $height);
				if ($row->image)
				{
					if (JFile::exists($categoryImagePath . $row->image))
					{
						JFile::delete($categoryImagePath . $row->image);
					}
					if (JFile::exists($thumbCategoryImagePath . $row->image))
					{
						JFile::delete($thumbCategoryImagePath . $row->image);
					}
				}
				$data['image'] = $fileName;
			}
		}

		if (JPluginHelper::isEnabled('edocman', 'notification'))
		{
			if (isset($data['notify_group_ids']))
			{
				$data['notify_group_ids'] = implode(',', $data['notify_group_ids']);
			}
			else
			{
				$data['notify_group_ids'] = '';
			}
		}

		//For Save As copy, if no thumbnail image uploaded, we just use thumbnail from original record
		if ($input->get('task') == 'save2copy' && empty($data['image']))
		{
			$originalCategory = clone $row;
			$originalCategory->load($input->getInt('id'));
			$data['image'] = $originalCategory->image;
		}

		//access level
        if($data['accesspicker'] == 1){
		    $data['access'] = 255;
        }
		//Store the modified jform data back to input object for processing in parent class
		$input->set('jform', $data);

		parent::save($input);

		$category_id = $input->getInt('id');
		if($category_id > 0){
            $query = $db->getQuery(true);
            $query->select('count(id)')->from('#__edocman_levels')->where('data_type = 0')->where('item_id = '.$category_id);
            $db->setQuery($query);
            $count = $db->loadResult();

            $groups = $data['groups'];
            $groups = implode(",",$groups);
            if($count == 0){
                $db->setQuery("Insert into #__edocman_levels (id,data_type,item_id,`groups`) values (NULL,'0',$category_id,'".$groups."')");
                $db->execute();
            }else{
                $db->setQuery("Update #__edocman_levels set `groups` = '".$groups."' where data_type = 0 and item_id = $category_id");
                $db->execute();
            }
        }
	}

	/**
	 *
	 * Prepare the category data, before saving record to database
	 *
	 * @param JTable $row
	 * @param string $task
	 * @param array  $data
	 */
	protected function prepareTable($row, $task, $data = array())
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		if ($row->path)
		{
			$config    = EdocmanHelper::getConfig();
			$path      = str_replace("\\", "/", $row->path);
			$path      = JString::strtolower($path);
			$path      = JFolder::makeSafe($path);
			// In case parent category has it own folder, children category should be stored in a child folder of parent category
			if ($row->parent_id > 0)
			{
				$query->select('`path`')
					->from('#__edocman_categories')
					->where('id = '. (int) $row->parent_id);
				$db->setQuery($query);
				$parentCategoryPath = $db->loadResult();
				if ($parentCategoryPath)
				{
					$parentCategoryPath = JFolder::makeSafe($parentCategoryPath);
					$path = $parentCategoryPath . '/' . $path;
				}
			}
			$row->path = $path;
			$path      = $config->documents_path . '/' . $row->path;
			if (!JFolder::exists($path))
			{
				JFolder::create($path);
			}
		}

		if ($row->parent_id > 0)
		{
			// Calculate level
			$query->clear();
			$query->select('`level`')
				->from('#__edocman_categories')
				->where('id = '. (int) $row->parent_id);
			$db->setQuery($query);
			$row->level = (int) $db->loadResult() + 1;
		}
		else
		{
			$row->level = 1;
		}

		parent::prepareTable($row, $task, $data);
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param JTable A record object.
	 *
	 * @return array  An array of conditions to add to add to ordering queries.
	 */
	protected function getReorderConditions($table)
	{
		$condition   = array();
		$condition[] = ' parent_id = ' . (int) $table->parent_id;

		return $condition;
	}

	/**
	 * Method to check if the current user can delete the record
	 *
	 * @param EDocmanTableCategory $row
	 *
	 * @return boolean
	 */

	protected function canDelete($row)
	{
		if (!empty($row->id))
		{
			return JFactory::getUser()->authorise('core.delete', 'com_edocman.category.' . (int) $row->id);
		}
		else
		{
			return parent::canDelete($row);
		}
	}

	/**
	 * Method to check if the current user can change status of the record
	 *
	 * @see OSModelForm::canEditState()
	 */
	protected function canEditState($row)
	{
		if (!empty($row->id))
		{
			return JFactory::getUser()->authorise('core.edit.state', 'com_edocman.category.' . (int) $row->id);
		}
		else
		{
			return parent::canEditState($row);
		}
	}

	/**
	 * Override getData method to process notify group ids
	 *
	 * @return array|object
	 */
	public function getData()
	{
	    $db = JFactory::getDbo();
		if (empty($this->data))
		{
			$data = parent::getData();
			if ($data->id && JPluginHelper::isEnabled('edocman', 'notification'))
			{
				$data->notify_group_ids = explode(',', $data->notify_group_ids);
			}

			if (!$data->id)
			{
				// New category, set layout to default
				$data->category_layout = 'default';
			}
			$data->groups = array();
			if($data->id > 0) {
                $groups = EDocmanHelper::getGroupLevels(0, $data->id);
                if($groups != ''){
                    $data->groups = explode(",",$groups);
                }
            }
			$this->data = $data;
		}

		return $this->data;
	}
}
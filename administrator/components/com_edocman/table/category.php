<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die();

/**
 * Category Table class
 */
class EDocmanTableCategory extends JTable
{

	/**
	 * Constructor
	 *
	 * @param JDatabase $db A database connector object
	 *
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__edocman_categories', 'id', $db);
	}

	/**
	 * Method to return name to use for asset table
	 *
	 * @return string
	 */
	protected function _getAssetName()
	{
		return 'com_edocman.category.' . (int) $this->id;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return string
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}

	/**
	 * Get the parent asset id for the record
	 *
	 * @param JTable  $table A JTable object for the asset parent.
	 *
	 * @param integer $id    The id for the asset
	 *
	 * @return integer The id of the asset's parent
	 */
	public function _getAssetParentId(JTable $table = null, $id = null)
	{
		// Initialise variables.
		$assetId = null;
		$db      = $this->getDbo();
		// This is a category under a category.
		if ($this->parent_id > 0)
		{
			// Build the query to get the asset id for the parent category.
			$query = $db->getQuery(true);
			$query->select('asset_id');
			$query->from('#__edocman_categories');
			$query->where('id = ' . (int) $this->parent_id);
			// Get the asset id from the database.
			$db->setQuery($query);
			if ($result = $db->loadResult())
			{
				$assetId = (int) $result;
			}
		}

		// This is a category that needs to parent with the extension.
		elseif ($assetId === null)
		{
			// Build the query to get the asset id for the parent category.
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from('#__assets');
			$query->where('name = ' . $db->quote('com_edocman'));

			// Get the asset id from the database.
			$db->setQuery($query);
			if ($result = $db->loadResult())
			{
				$assetId = (int) $result;
			}
		}
		// Return the asset id.
		if ($assetId)
		{
			return $assetId;
		}
		else
		{
			return parent::_getAssetParentId($table, $id);
		}
	}

	/**
	 * Bind data from request into table object
	 *
	 * @see JTable::bind()
	 */
	public function bind($array, $ignore = '')
	{
		// Bind the rules.
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}

		return parent::bind($array, $ignore);
	}
	
	/**
	 * Override delete function
	 *
	 * @see JTable::delete()
	 */
	function delete($pk = null)
	{
	    jimport('joomla.filesystem.folder');
		// Delete all documents belong this category
		$db             = $this->getDbo();
		$query          = $db->getQuery(true);
        $config         = EdocmanHelper::getConfig();
        if($config->remove_category_folder)
        {
            $query->select("*")->from("#__edocman_categories")->where("id = '".(int) $pk."'");
            $db->setQuery($query);
            $category   = $db->loadObject();
            $path       = $category->path;
            if($path != "")
            {
                $categoryPath  = $config->documents_path;
                $categoryPath .= "/".$path;
                if(JFolder::exists($categoryPath))
                {
                    if(self::dir_is_empty($categoryPath))
                    {
                        JFolder::delete($categoryPath);
                    }
                }
            }
        }

        $query->clear();
		$query->delete('#__edocman_document_category')
			->where('category_id = ' . (int) $pk);
		$db->setQuery($query);
		$db->execute();
		// Check if the asset id for this record
		$query->clear();
		$query->select('asset_id')
			->from('#__edocman_categories')
			->where('id = ' . (int) $pk);
		$db->setQuery($query);
		$assetId = $db->loadResult();
		if ($assetId)
		{
			return parent::delete($pk);
		}
		else
		{
			$query->clear();
			$query->delete('#__edocman_categories')
				->where('id = ' . (int) $pk);
			$db->setQuery($query);
			$db->execute();

			return true;
		}
	}

    public function dir_is_empty($dir)
    {
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle)))
        {
            if ($entry != "." && $entry != "..")
            {
                return FALSE;
            }
        }
        return TRUE;
    }
}

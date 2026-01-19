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
 * document Table class
 */
class EDocmanTableDocument extends JTable
{

	/**
	 * Document main category
	 *
	 * @var int
	 */
	private $_mainCategoryId = 0;

	/**
	 * Constructor
	 *
	 * @param
	 *            JDatabase A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__edocman_documents', 'id', $db);
	}

	/**
	 * Set main category of the document
	 *
	 * @param $catId
	 */
	public function setMainCategory($catId)
	{
		$this->_mainCategoryId = $catId;
	}

	/**
	 * Get document main category
	 *
	 * @return int
	 */
	public function getMainCategoryId()
	{
		return $this->_mainCategoryId;
	}

	/**
	 * Overloaded bind function to pre-process the params.
	 *
	 * @param array  $array array
	 * @param string $ignore
	 *
	 * @return null string operation was satisfactory, otherwise returns an error
	 *
	 * @see JTable:bind
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
	 * Method to return the name to use for asset table
	 *
	 * @return string
	 *
	 * @since 11.1
	 */
	protected function _getAssetName()
	{
		return 'com_edocman.document.' . (int) $this->id;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return string
	 *
	 * @since 11.1
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
	 *
	 * @since 11.1
	 */
	public function _getAssetParentId(JTable $table = null, $id = null)
	{
		// Initialise variables.
		$assetId    = null;
		$db         = $this->getDbo();
		$query      = $db->getQuery(true);
		$categoryId = (int) $this->_mainCategoryId;

		if (!$categoryId && $this->id)
		{
			// Get main category of the document
			$query->select('category_id')
				->from('#__edocman_document_category AS a')
				->where('document_id=' . (int) $this->id)
				->where('is_main_category=1');
			$db->setQuery($query);
			$categoryId = (int) $db->loadResult();
			$query->clear();
		}


		// This is a category under a category.
		if ($categoryId)
		{
			$query->select('asset_id');
			$query->from('#__edocman_categories AS a');
			$query->where('a.id = ' . (int) $categoryId);
			// Get the asset id from the database.
			$db->setQuery($query);
			$assetId = (int) $db->loadResult();
		}
		// This is a category that needs to parent with the extension.
		elseif ($assetId === null)
		{
			// Build the query to get the asset id for the parent category.
			$query->select('id');
			$query->from('#__assets');
			$query->where('name = ' . $db->quote('com_edocman'));

			// Get the asset id from the database.
			$db->setQuery($query);
			$assetId = (int) $db->loadResult();
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
	 * Override delete function
	 *
	 * @see JTable::delete()
	 */
	function delete($pk = null)
	{
		$db     = $this->getDbo();
		$query  = $db->getQuery(true);
		$config = EdocmanHelper::getConfig();
		if ($config->delete_file_when_document_deleted)
		{
			$query->select('filename')
				->from('#__edocman_documents')
				->where('id = ' . $pk);
			$db->setQuery($query);
			$fileName = $db->loadResult();
			if ($fileName && JFile::exists($config->documents_path . '/' . $fileName))
			{
				JFile::delete($config->documents_path . '/' . $fileName);
			}
			$query->clear();
		}

		// Delete from relative table
		$query->delete('#__edocman_document_category')
			->where('document_id = ' . $pk);
		$db->setQuery($query);
		$db->execute();

		// Check if the asset id for this record
		$query->clear();
		$query->select('asset_id')
			->from('#__edocman_documents')
			->where('id = ' . $pk);
		$db->setQuery($query);
		$assetId = $db->loadResult();
		if ($assetId)
		{
			return parent::delete($pk);
		}
		else
		{
			$query->clear();
			$query->delete('#__edocman_documents')
				->where('id = ' . $pk);
			$db->setQuery($query);
			$db->execute();

			return true;
		}
	}

    /**
     * Sanitize data before storing into database
     *
     * @return bool|void
     */
    public function check()
    {
        $this->hits             = (int)$this->hits;
        $this->downloads        = (int)$this->downloads;
        if (!$this->publish_up) {
            $this->publish_up = $this->getDbo()->getNullDate();
        }

        if (!$this->publish_down) {
            $this->publish_down = $this->getDbo()->getNullDate();
        }

        if (!$this->locked_time ) {
            $this->locked_time  = $this->getDbo()->getNullDate();
        }
        return parent::check();
    }
}

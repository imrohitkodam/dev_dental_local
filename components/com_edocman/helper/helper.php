<?php
/**
 * @version   	   1.9.9
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die();

/**
 * EDocman helper.
 */
class EDocmanHelper
{
	public static function addSideBarmenus($vName = ''){
		if (version_compare(JVERSION, '3.0', 'ge')) {
			JHtmlSidebar::addEntry(
				JText::_('EDOCMAN_DASHBOARD'),
				'index.php?option=com_edocman&view=dashboard',
				$vName == 'dashboard'
			);

			JHtmlSidebar::addEntry(
				JText::_('EDOCMAN_CATEGORIES'),
				'index.php?option=com_edocman&view=categories',
				$vName == 'categories' || $vName == 'category'
			);
			JHtmlSidebar::addEntry(
				JText::_('EDOCMAN_DOCUMENTS'),
				'index.php?option=com_edocman&view=documents',
				$vName == 'documents' || $vName == 'document'
			);

            JHtmlSidebar::addEntry(
                JText::_('EDOCMAN_REMOVE_ORPHAN_DOCUMENTS'),
                'index.php?option=com_edocman&view=documents&layout=remove_orphan'
            );

			JHtmlSidebar::addEntry(
				JText::_('EDOCMAN_LICENSES'),
				'index.php?option=com_edocman&view=licenses',
				$vName == 'licenses' || $vName == 'license'
			);

			JHtmlSidebar::addEntry(
				JText::_('EDOCMAN_TRANSLATION'),
				'index.php?option=com_edocman&view=language',
				$vName == 'language'
			);

			JHtmlSidebar::addEntry(
				JText::_('EDOCMAN_BATCH_UPLOAD'),
				'index.php?option=com_edocman&view=upload',
				$vName == 'upload'
			);

			JHtmlSidebar::addEntry(
				JText::_('EDOCMAN_BULK_IMPORT'),
				'index.php?option=com_edocman&view=import',
				$vName == 'import'
			);

			JHtmlSidebar::addEntry(
				JText::_('EDOCMAN_DOWNLOAD_LOG'),
				'index.php?option=com_edocman&view=downloadlogs',
				$vName == 'downloadlogs'
			);

			JHtmlSidebar::addEntry(
				JText::_('EDOCMAN_CONFIG'),
				'index.php?option=com_edocman&view=configuration',
				$vName == 'configuration'
			);
		}
	}
	
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenus($vName = '')
	{
		if (version_compare(JVERSION, '3.0', 'lt')) {
			JSubMenuHelper::addEntry(
				JText::_('EDOCMAN_DASHBOARD'),
				'index.php?option=com_edocman&view=dashboard',
				$vName == 'dashboard'
			);

			JSubMenuHelper::addEntry(
				JText::_('EDOCMAN_CATEGORIES'),
				'index.php?option=com_edocman&view=categories',
				$vName == 'categories' || $vName == 'category'
			);
			JSubMenuHelper::addEntry(
				JText::_('EDOCMAN_DOCUMENTS'),
				'index.php?option=com_edocman&view=documents',
				$vName == 'documents' || $vName == 'document'
			);
			JSubMenuHelper::addEntry(
				JText::_('EDOCMAN_LICENSES'),
				'index.php?option=com_edocman&view=licenses',
				$vName == 'licenses' || $vName == 'license'
			);

			JSubMenuHelper::addEntry(
				JText::_('EDOCMAN_TRANSLATION'),
				'index.php?option=com_edocman&view=language',
				$vName == 'language'
			);

			JSubMenuHelper::addEntry(
				JText::_('EDOCMAN_BATCH_UPLOAD'),
				'index.php?option=com_edocman&view=upload',
				$vName == 'upload'
			);

			JSubMenuHelper::addEntry(
				JText::_('EDOCMAN_BULK_IMPORT'),
				'index.php?option=com_edocman&view=import',
				$vName == 'import'
			);

			JSubMenuHelper::addEntry(
				JText::_('EDOCMAN_DOWNLOAD_LOG'),
				'index.php?option=com_edocman&view=downloadlogs',
				$vName == 'downloadlogs'
			);

			JSubMenuHelper::addEntry(
				JText::_('EDOCMAN_CONFIG'),
				'index.php?option=com_edocman&view=configuration',
				$vName == 'configuration'
			);
		}
	}


	/**
	 * Get list of categories which the current user can perform a certain action
	 *
	 * @param $action
	 *
	 * @return mixed
	 */
	public static function getAuthorisedCategories($action)
	{
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__edocman_categories')
			->where('published = 1');
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		for ($i =  0, $n = count($rows) ; $i < $n; $i++)
		{
			$row = $rows[$i];
			if (!$user->authorise($action, 'com_edocman.category.'.$row->id))
			{
				unset($rows[$i]);
			}
		}

		return $rows;
	}
	/**
	 * This function is used to find the link to possible views in the component
	 *
	 * @param array $views
	 *
	 * @return string|NULL
	 */
	public static function getViewUrl($views = array())
	{
		$app       = JFactory::getApplication();
		$menus     = $app->getMenu('site');
		$component = JComponentHelper::getComponent('com_edocman');
		$items     = $menus->getItems('component_id', $component->id);
		foreach ($views as $view)
		{
			$viewUrl = 'index.php?option=com_edocman&view=' . $view;
			foreach ($items as $item)
			{
				if (strpos($item->link, $viewUrl) !== false)
				{
					if (strpos($item->link, 'Itemid=') === false)
					{
						return JRoute::_($item->link . '&Itemid=' . $item->id);
					}
					else
					{
						return JRoute::_($item->link);
					}
				}
			}
		}

		return null;
	}

	/**
	 * Get details information of a category based on it's id
	 *
	 * @param int $id
	 *
	 * @return object
	 */
	public static function getCategory($id)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__edocman_categories')
			->where('id=' . (int) $id);
		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Function to load jQuery chosen plugin
	 */
	public static function chosen($selector = 'select')
	{
		static $chosenLoaded;
		if (!$chosenLoaded)
		{
			if (version_compare(JVERSION, '3.0', 'ge'))
			{
				JHtml::_('formbehavior.chosen', $selector);
			}
			else
			{
				$document = JFactory::getDocument();
				$document->addScript(JURI::base() . 'components/com_edocman/assets/chosen/chosen.jquery.js');
				$document->addStyleSheet(JURI::base() . 'components/com_edocman/assets/chosen/chosen.css');
				$document->addScriptDeclaration(
					"jQuery(document).ready(function(){
                            jQuery(\"" . $selector . "\").chosen();
                        });");
			}
			$chosenLoaded = true;
		}
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return JObject
	 */
	public static function getActions($view = 'categories', OSModelState $state = null)
	{
		$user   = JFactory::getUser();
		$result = new JObject();
		switch ($view)
		{
			case 'categories':
				$assetName = 'com_edocman';
				break;
			case 'category':
				if ($state->id)
				{
					$assetName = 'com_edocman.category.' . $state->id;
				}
				else
				{
					$assetName = 'com_edocman';
				}
				break;
			case 'documents':
				if ($state->filter_category_id)
				{
					$assetName = 'com_edocman.category.' . $state->filter_category_id;
				}
				else
				{
					$assetName = 'com_edocman';
				}
				break;
			case 'document':
				if ($state->id)
				{
					$assetName = 'com_edocman.document.' . $state->id;
				}
				else
				{
					$assetName = 'com_edocman';
				}
				break;
			default:
				$assetName = 'com_edocman';
				break;
		}
		$actions = array('core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete');
		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}

	/**
	 * Get list of parent categories
	 *
	 * @param $categoryId int
	 *                    the current category Id
	 *
	 * @return array list of parent categoies
	 */
	public static function getParentCategories($categoryId)
	{
		$db      = JFactory::getDbo();
		$parents = array();
		while (true)
		{
			$sql = "SELECT id, title, parent_id, category_layout, notify_group_ids FROM #__edocman_categories WHERE id = " . $categoryId . " AND published=1";
			$db->setQuery($sql);
			$row = $db->loadObject();
			if ($row)
			{
				$sql = 'SELECT COUNT(*) FROM #__edocman_categories WHERE parent_id=' . $row->id;
				$db->setQuery($sql);
				$total               = $db->loadResult();
				$row->total_children = $total;
				$parents[]           = $row;
				$categoryId          = $row->parent_id;
			}
			else
			{
				break;
			}
		}

		return $parents;
	}

	public static function getCategoriesBreadcrumb($id, $parentId)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, title, parent_id')
			->from('#__edocman_categories')
			->where('published=1');
		$db->setQuery($query);
		$categories = $db->loadObjectList('id');
		$paths      = array();
		while ($id != $parentId)
		{
			if (isset($categories[$id]))
			{
				$paths[] = $categories[$id];
				$id      = $categories[$id]->parent_id;
			}
			else
			{
				break;
			}
		}

		return $paths;
	}

	/**
	 * Count total documents from a given category and all it's sub-categories
	 *
	 * @param int $categoryId
	 *            the category which we want to get total documents
	 *
	 * @return int total documents
	 */
	public static function countDocuments($categoryId)
	{
		$config = EDocmanHelper::getConfig();
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);
		$user   = JFactory::getUser();
		$userId = $user->get('id');

		// Get all children categories of this category
		$categoryIds = EDocmanHelper::getAllChildrenCategories(array($categoryId));

		$query->select('COUNT(tbl.id)')
			->from('#__edocman_documents AS tbl');

		if (!$user->authorise('core.admin', 'com_edocman'))
		{
			$usergroup          = $user->groups;
			$usergroupArr       = array();
			$usergroupSql       = "";
			if(count($usergroup) > 0){
				foreach ($usergroup as $group){
					$usergroupArr[] = " (`groups`='$group' OR `groups` LIKE '$group,%' OR `groups` LIKE '%,$group,%' OR `groups` LIKE '%,$group') AND `data_type` = '1'";
				}
				$usergroupSql = implode(" OR ",$usergroupArr);
				$usergroupSql = " tbl.id in (Select item_id from #__edocman_levels where $usergroupSql) ";
				$usergroupSql = " OR (tbl.user_ids = '' AND tbl.accesspicker = '1' AND $usergroupSql ) ";
			}
			$groupQuery = EdocmanHelper::buildUserGroupQuery('tbl.owner_group_ids');
			$query->where('tbl.published=1')
				->where("((tbl.user_ids = '' AND tbl.access IN (" . implode(',', $user->getAuthorisedViewLevels()) . ")) $usergroupSql $groupQuery OR tbl.user_ids='$userId' OR tbl.user_ids LIKE '$userId,%' OR tbl.user_ids LIKE '%,$userId,%' OR tbl.user_ids LIKE '%,$userId' OR (tbl.created_user_id=$userId AND tbl.created_user_id > 0))");
		}

		$query->where('tbl.id IN (SELECT document_id FROM #__edocman_document_category WHERE category_id IN(' . implode(',', $categoryIds) . '))');

		// Filter by start and end dates.
		if ((!$user->authorise('core.edit.state', 'com_edocman')) && (!$user->authorise('core.edit', 'com_edocman')))
		{
			$nullDate = $db->quote($db->getNullDate());
			$nowDate  = $db->quote(JFactory::getDate()->toSql());
			$query->where('(tbl.publish_up = ' . $nullDate . ' OR tbl.publish_up <= ' . $nowDate . ')')
				->where('(tbl.publish_down = ' . $nullDate . ' OR tbl.publish_down >= ' . $nowDate . ')');
		}

		// Multilingual filter
		if ($config->activate_multilingual_feature && JFactory::getApplication()->getLanguageFilter())
		{
			$query->where('tbl.language IN (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ', "")');
		}

		$db->setQuery($query);

		return (int) $db->loadResult();
	}

	/**
	 *
	 * @param $items
	 * @param $numberDaysForNew
	 */
	public static function setNewIndicator($items, $numberDaysForNew)
	{
		foreach($items as $item)
		{
			if ($item->number_created_days <= $numberDaysForNew)
			{
				$item->new_indicator = 1;
			}
		}
	}

	/**
	 *
	 * @param $items
	 * @param $numberDaysForNew
	 */
	public static function setUpdateIndicator($items, $numberDaysForUpdatew)
	{
		foreach($items as $item)
		{
			if ($item->number_updated_days <= $numberDaysForUpdatew)
			{
				$item->update_indicator = 1;
			}
		}
	}
	/**
	 * Display copyright of the extension
	 */
	public static function displayCopyright()
	{
		echo '<div class="clearfix"></div><div class="copyright" style="text-align:center;margin-top: 5px;"><a href="http://joomdonation.com/components/edocman.html" target="_blank"><strong>EDocman</strong></a> version ' . self::getInstalledVersion() . ', Copyright (C) 2011-' .
			date('Y') . ' <a href="http://joomdonation.com" target="_blank"><strong>Ossolution Team</strong></a></div>';
	}

	/**
	 * Load language
	 */
	public static function loadLanguage()
	{
		static $loaded;
		if (!$loaded)
		{
			$lang = JFactory::getLanguage();
			$tag  = $lang->getTag();
			if (!$tag)
			{
				$tag = 'en-GB';
			}
			$lang->load('com_edocman', JPATH_ROOT, $tag);
			$loaded = true;
		}
	}

	public static function getItemid()
	{
		require_once JPATH_ROOT.'/components/com_edocman/helper/route.php';

		$views = array('categories', 'category', 'document');

		foreach($views as $view)
		{
			if ($item = EDocmanHelperRoute::findView($view, 0))
			{
				return $item;
			}
		}

		return 0;
	}


	/**
	 * Get config value from database
	 *
	 * @param string $configKey
	 * @param string $default
	 */
	public static function getConfigValue($configKey, $default = null)
	{
		static $configValues;
		if (!isset($configValues["$configKey"]))
		{
			$db  = JFactory::getDbo();
			$sql = 'SELECT config_value FROM #__edocman_configs WHERE config_key="' . $configKey . '"';
			$db->setQuery($sql);
			$configValues[$configKey] = $db->loadResult();
		}

		return $configValues[$configKey] ? $configValues[$configKey] : $default;
	}

	/**
	 *
	 *
	 * Resize image to a pre-defined size
	 *
	 * @param string $srcFile
	 * @param string $desFile
	 * @param int    $thumbWidth
	 * @param int    $thumbHeight
	 * @param string $method
	 *            gd1 or gd2
	 * @param int    $quality
	 */
	public static function resizeImage($srcFile, $desFile, $thumbWidth, $thumbHeight)
	{
        /*
		$image = new JImage($srcFile);
		$image->resize($thumbWidth, $thumbHeight, false);
		$image->toFile($desFile);
        */
        require_once(JPATH_ROOT.'/components/com_edocman/helper/images.php');
        OsImageHelper::createImage($srcFile, $desFile, $thumbWidth, $thumbHeight, true, 100);
		return true;
	}

	/**
	 * Get configuration object
	 */
	public static function getConfig()
	{
		static $config;
		if ($config == null)
		{
			$config = new stdClass();
			$db     = JFactory::getDbo();
			$sql    = 'SELECT * FROM #__edocman_configs';
			$db->setQuery($sql);
			$rows = $db->loadObjectList();
			for ($i = 0, $n = count($rows); $i < $n; $i++)
			{
				$row            = $rows[$i];
				$key            = $row->config_key;
				$value          = $row->config_value;
				$config->{$key} = $value;
			}
		}

		return $config;
	}

	/**
	 * Generate User Input Select
	 *
	 * @param int $userId
	 */
	public static function getUserInput($userIds)
	{
		$app = JFactory::getApplication();
		// Initialize variables.
		$html = array();
		$link = 'index.php?option=com_edocman&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field=user_id';
		// Initialize some field attributes.
		$attr = ' class="inputbox"';
		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal_user_id');
		// Build the script.
		$script   = array();
		$script[] = '	function jSelectUser_user_id(id, title) {';
		$script[] = '		var old_ids = document.getElementById("jform_user_ids").value;';
		$script[] = '		if (old_ids) {';
		$script[] = '			document.getElementById("jform_user_ids").value = old_ids + "," + id;';
		$script[] = '		} else {';
		$script[] = '			document.getElementById("jform_user_ids").value = id; ';
		$script[] = '		}';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';
		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
		// Load the current username if available.
		// Create a dummy text field with the user name.
		$html[] = '<div class="fltlft">';
		$html[] = '	<input type="text" name="jform[user_ids]" id="jform_user_ids"' . ' value="' . $userIds . '"' . $attr . ' />';
		$html[] = '</div>';
		// Create the user select button.
		$html[] = '<div class="button2-left">';
		$html[] = '<div class="blank">';
		$html[] = '<a class="modal_user_id" title="' . JText::_('JLIB_FORM_CHANGE_USER') . '"' . ' href="' . $link . '"' .
			' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
		$html[] = '	' . JText::_('JLIB_FORM_CHANGE_USER') . '</a>';
		$html[] = '</div>';
		$html[] = '</div>';

		return implode("\n", $html);
	}

	/**
	 * Process downloading file
	 *
	 * @param string $filePath
	 *            Path to the file
	 * @param string $filename
	 *            Name of the file
	 */
    public static function processDownload($filePath,$filename,$original_filename, $download = false, $fileid = 0)
    {
        JPluginHelper::importPlugin('edocman');
        $app = JFactory::getApplication();

        $results = $app->triggerEvent('onGetDocumentFile', array($filename, $fileid));
        if (count($results))
        {
            foreach ($results as $result)
            {
                if (!empty($result))
                {
                    break;
                }
            }
        }

        if ($download)
        {
            $cont_dis = 'attachment';
        }
        else
        {
            $cont_dis = 'inline';
        }
        if (!empty($result))
        {
            $stream   = $result['stream'];
            $fsize    = $result['Content-Length'];
            $mod_date = $result['modification-date'];
            $mime     = $result['Content-Type'];
        }
        else
        {
            $config   = EDocmanHelper::getConfig();
            $filePath = $config->documents_path . '/' . $filename;
            $fsize    = @filesize($filePath);
            $mod_date = date('r', filemtime($filePath));
            $ext      = JFile::getExt($filename);
            $mime     = EDocmanHelper::getMimeType($ext);

            $stream = fopen($filePath, 'rb');
            if ($stream === false)
            {
                return false;
            }
        }
        // required for IE, otherwise Content-disposition is ignored
        if (ini_get('zlib.output_compression'))
        {
            ini_set('zlib.output_compression', 'Off');
        }
        header("Pragma: public");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Expires: 0");
        header("Content-Transfer-Encoding: binary");
        header(
            'Content-Disposition:' . $cont_dis . ';' . ' filename="' .
            (strlen($original_filename) ? JFile::getName($original_filename) : JFile::getName($filename)) . '";' . ' modification-date="' .
            $mod_date . '";' . ' size=' . $fsize . ';'); // RFC2183
        header("Content-Type: " . $mime); // MIME type
        header("Content-Length: " . $fsize);

        if (!ini_get('safe_mode'))
        { // set_time_limit doesn't work in safe mode
            @set_time_limit(0);
        }
        EDocmanHelper::readfile_chunked($stream);
    }

    /**
     * Get mimetype of a file
     *
     * @return string
     */
    public static function getMimeType($ext)
    {
        require_once JPATH_ROOT . "/components/com_edocman/helper/mime.mapping.php";
        foreach ($mime_extension_map as $key => $value)
        {
            if ($key == $ext)
            {
                return $value;
            }
        }

        return "";
    }

    /**
     * Read file
     *
     * @param string $filename
     * @param        $retbytes
     *
     * @return unknown
     */
    public static function readfile_chunked($handle, $retbytes = true)
    {
        $chunksize = 1 * (1024 * 1024); // how many bytes per chunk
        $buffer    = '';
        $cnt       = 0;
        while (!feof($handle))
        {
            $buffer = fread($handle, $chunksize);
            echo $buffer;
            @ob_flush();
            flush();
            if ($retbytes)
            {
                $cnt += strlen($buffer);
            }
        }

        $status = null;
        if (is_resource($handle))
        {
            $status = fclose($handle);
        }

        if ($retbytes && $status)
        {
            return $cnt; // return num. bytes delivered like readfile() does.
        }

        return $status;
    }

	/**
	 * Check to see whether users can access to the category
	 *
	 * @param int $categoryId
	 *            Id of the category users accessing to
	 *
	 * @return boolean true if users can access, fail if not
	 *
     * Update version 1.9.10
	 */
	public static function canAccessCategory($categoryId)
	{
		$user  = JFactory::getUser();
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__edocman_categories')
			->where('id = ' . $categoryId);
		$db->setQuery($query);
		$row = $db->loadObject();

		// Invalid category, so of course, it could not be accessed
		if (!$row)
		{
			return false;
		}

		// Users with super admin permission should be able to access all categories
		if ($user->authorise('core.admin'))
		{
			return true;
		}

		if ($row->user_ids)
		{
			$userIds = explode(',', $row->user_ids);
			if (!in_array($user->get('id'), $userIds))
			{
				return false;
			}
		}
		else
		{
		    if($row->accesspicker == 0) {
                if (!in_array($row->access, $user->getAuthorisedViewLevels())) {
                    return false;
                }
            }else{
                $usergroup          = $user->groups;
                if(count($usergroup) > 0){
                    foreach ($usergroup as $group){
                        $query->clear();
                        $query->select('count(id)')->from('#__edocman_levels')->where('data_type = 0')->where('(`groups`="'.$group.'" OR `groups` LIKE "'.$group.'%" OR `groups` LIKE "%,'.$group.',%" OR `groups` LIKE "%,'.$group.'")')->where('item_id = "'.$categoryId.'"');
                        $db->setQuery($query);
                        $count = $db->loadResult();
                        if($count > 0){
                            return true;
                        }
                    }
                }
            }
		}

		return true;
	}

	/**
	 * Check to see whether users can access to a document
	 *
	 * @param int $documentId
	 *
	 * @return boolean
	 */
	public static function canAccessDocument($documentId)
	{
		$user  = JFactory::getUser();
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__edocman_documents')
			->where('id = ' . $documentId);
		$db->setQuery($query);
		$row = $db->loadObject();
		if (!$row)
		{
			return false;
		}

		// Users has super admin permission should be able to access to all documents
		if ($user->authorise('core.admin'))
		{
			return true;
		}

		// Owner of the document of course can viwe it
		if ($user->id == $row->created_user_id)
		{
			return true;
		}
		if ($row->user_ids)
		{
			$userIds = explode(',', $row->user_ids);
			if (!in_array($user->get('id'), $userIds))
			{
				return false;
			}
		}elseif($row->owner_group_ids){
		    $usergroups = $user->groups;
		    if($row->owner_group_ids != ""){
		        $owner_group_ids = explode(",", $row->owner_group_ids);
		        if(count($owner_group_ids) > 0 && count($usergroups) > 0){
		            foreach($usergroups as $group){
		                if(in_array($group,$owner_group_ids)){
		                    return true;
                        }
                    }
                }
            }
            return false;
        }
		else
		{
			$assetName = 'com_edocman.document.' . $documentId;
			if ($user->authorise('core.edit', $assetName) || $user->authorise('core.delete', $assetName) || $user->id == $row->created_user_id)
			{
				return true;
			}
			elseif (!in_array($row->access, $user->getAuthorisedViewLevels())  && $row->accesspicker == 0)
			{
				return false;
			}
			elseif($row->accesspicker == 1)
			{
				$usergroup          = $user->groups;
                if(count($usergroup) > 0){
                    foreach ($usergroup as $group){
                        $query->clear();
                        $query->select('count(id)')->from('#__edocman_levels')->where('data_type = 1')->where('(`groups`="'.$group.'" OR `groups` LIKE "'.$group.'%" OR `groups` LIKE "%,'.$group.',%" OR `groups` LIKE "%,'.$group.'")')->where('item_id = "'.$documentId.'"');
                        $db->setQuery($query);
                        $count = $db->loadResult();
                        if($count > 0){
                            return true;
                        }
                    }
                }
				return false;
			}
		}

		return true;
	}

	/**
	 * Send upload notification emails to administrator when someone upload document from front-end
	 *
	 * @param object $row
	 *            Document object
	 * @param object $config
	 *            Configuration data
	 */
	function sendUploadNotificationEmail($row, $config, $categoryId)
	{
		$jconfig = new JConfig();
		$user    = JFactory::getUser();
		$subject = $config->upload_email_subject;
		$body    = nl2br($config->upload_email_body);
		if ($user->id)
		{
			$username = $user->username;
			$name     = $user->name;
		}
		else
		{
			$username = JText::_('EDOCMAN_GUEST');
			$name     = JText::_('EDOCMAN_GUEST');
		}
		$userIp             = @$_SERVER['REMOTE_ADDR'];
		$documentTitle      = $row->title;
		$body               = str_replace('[USERNAME]', $username, $body);
		$body               = str_replace('[NAME]', $name, $body);
		$body               = str_replace('[USER_IP]', $userIp, $body);
		$body               = str_replace('[DOCUMENT_TITLE]', $documentTitle, $body);
		$subject		    = str_replace('[DOCUMENT_TITLE]', $documentTitle, $subject);
		$mailer             = JFactory::getMailer();
		$notificationEmails = '';
		if ($categoryId)
		{
			$db = JFactory::getDbo();
			while ($categoryId > 0)
			{
				$sql = 'SELECT parent_id, notification_emails FROM #__edocman_categories WHERE id=' . $categoryId;
				$db->setQuery($sql);
				$rowCategory        = $db->loadObject();
				$notificationEmails = $rowCategory->notification_emails;
				$categoryId         = $rowCategory->parent_id;
				if ($notificationEmails)
				{
					break;
				}
			}
		}
		if (!$notificationEmails)
		{
			$notificationEmails = trim($config->notification_emails);
		}
		if (strlen($notificationEmails) < 5)
		{
			$notificationEmails = $jconfig->mailfrom;
		}
		$notificationEmails = explode(',', $notificationEmails);
		for ($i = 0, $n = count($notificationEmails); $i < $n; $i++)
		{
			$email = trim($notificationEmails[$i]);
			if ($email)
			{
				$mailer->sendMail($jconfig->mailfrom, $jconfig->fromname, $email, $subject, $body, 1);
				$mailer->ClearAllRecipients();
			}
		}
	}

	/**
	 *
	 * @param Boolean $loadJs
	 */
	public static function loadBootstrap($loadJs = true)
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet(JUri::root(true) . '/components/com_edocman/assets/bootstrap/css/bootstrap.css');
		if (JFactory::getApplication()->isAdmin())
		{
			$document->addStyleSheet(JUri::root(true) . '/components/com_edocman/assets/bootstrap/css/bootstrap-tabs.css');
		}
		if ($loadJs)
		{
			self::loadJQuery();
			$document->addScript(JUri::root(true) . '/components/com_edocman/assets/bootstrap/js/bootstrap.min.js');
		}
	}

	/**
	 * Load jQuery library
	 */
	public static function loadJQuery()
	{
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			JHtml::_('jquery.framework');
		}
		else
		{
			$document = JFactory::getDocument();
			$document->addScript(JUri::root(true) . '/components/com_edocman/assets/bootstrap/js/jquery.min.js');
			$document->addScript(JUri::root(true) . '/components/com_edocman/assets/bootstrap/js/jquery-noconflict.js');
		}
	}

	/**
	 * Load bootstrap js
	 */
	public static function loadBootstrapJs()
	{
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			JHtml::_('script', 'jui/bootstrap.min.js', false, true, false, false, false);
		}
		else
		{
			JFactory::getDocument()->addScript(JUri::root(true) . '/components/com_edocman/assets/bootstrap/js/bootstrap.min.js');
		}
	}
	/**
	 * Send notification emails to users who are assigned the document to
	 *
	 * @param object $row
	 *            Document object
	 */
	public static function sendDocumentAssignedEmails($row, $oldUserIds)
	{
		$mailer  = JFactory::getMailer();
		$db      = JFactory::getDbo();
		$jconfig = new JConfig();
		$config  = self::getConfig();
		$user    = JFactory::getUser();
		$subject = $config->document_assigned_email_subject;
		$body    = nl2br($config->document_assigned_email_body);
		$userIds = $row->user_ids;
		if ($user->id)
		{
			$username = $user->username;
			$name     = $user->name;
		}
		$documentTitle = $row->title;
		// subject Email
		$subject = str_replace('[OWNER_USERNAME]', $username, $subject);
		$subject = str_replace('[OWNER_NAME]', $name, $subject);
		$subject = str_replace('[DOCUMENT_TITLE]', $documentTitle, $subject);

		// body email
		$body = str_replace('[OWNER_USERNAME]', $username, $body);
		$body = str_replace('[OWNER_NAME]', $name, $body);
		$body = str_replace('[DOCUMENT_TITLE]', $documentTitle, $body);

		// get Name Email
		$query = $db->getQuery(true);
		$query->select('id, username, name, email');
		$query->from($db->quoteName('#__users'));
		$query->where($db->quoteName('id') . 'IN (' . $userIds . ')');
		$db->setQuery($query);
		$documentEmails = $db->loadObjectList();
		$oldUserIds     = explode(',', $oldUserIds);
		JArrayHelper::toInteger($oldUserIds);
		for ($i = 0, $n = count($documentEmails); $i < $n; $i++)
		{
			$email = $documentEmails[$i];
			if (in_array($email->id, $oldUserIds))
			{
				continue;
			}
			$emailSubject = $subject;
			$emailBody    = $body;
			$emailSubject = str_replace('[NAME]', $email->name, $emailSubject);
			$emailSubject = str_replace('[USERNAME]', $email->username, $emailSubject);
			$emailBody    = str_replace('[NAME]', $email->name, $emailBody);
			$emailBody    = str_replace('[USERNAME]', $email->username, $emailBody);

			$mailer->sendMail($jconfig->mailfrom, $jconfig->fromname, $email->email, $emailSubject, $emailBody, 1);
			$mailer->ClearAllRecipients();
		}
	}

	public static function getChildrenCategories($categoryId)
	{
		return self::getAllChildrenCategories(array($categoryId));
	}


	public static function getAllChildrenCategories($categoryIds = array())
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, parent_id')
			->from('#__edocman_categories')
			->where('published = 1');
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$children = array();
		// first pass - collect children
		if (count($rows))
		{
			foreach ($rows as $v)
			{
				$pt   = $v->parent_id;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v->id);
				$children[$pt] = $list;
			}
		}

		$queues = $categoryIds;
		$allCategories = $categoryIds;
		while (count($queues))
		{
			$id = array_pop($queues);
			if (isset($children[$id]))
			{
				$allCategories = array_merge($allCategories, $children[$id]);
				$queues = array_merge($queues, $children[$id]);
			}
		}

		return $allCategories;
	}

	/**
	 *
	 * Apply some fixes for request data
	 *
	 * @return void
	 */
	public static function prepareRequestData()
	{
		//Remove cookie vars from request data
		$cookieVars = array_keys($_COOKIE);
		if (count($cookieVars))
		{
			foreach ($cookieVars as $key)
			{
				if (!isset($_POST[$key]) && !isset($_GET[$key]))
				{
					unset($_REQUEST[$key]);
				}
			}
		}
		if (isset($_REQUEST['start']) && !isset($_REQUEST['limitstart']))
		{
			$_REQUEST['limitstart'] = $_REQUEST['start'];
		}
		if (!isset($_REQUEST['limitstart']))
		{
			$_REQUEST['limitstart'] = 0;
		}
	}

	/**
	 * Prepare data before saving to database
	 *
	 * @param $row
	 * @param $categoryId
	 */
	public static function prepareDocument($row, $categoryId)
	{
		$user = JFactory::getUser();
		if (!$row->id)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('MAX(ordering)')
				->from('#__edocman_documents AS a')
				->innerJoin('#__edocman_document_category AS b ON a.id = b.document_id')
				->where('b.category_id = ' . $categoryId);
			$db->setQuery($query);
			$row->ordering = 1 + (int) $db->loadResult();
		}

		$row->title = htmlspecialchars_decode($row->title, ENT_QUOTES);
		if (empty($row->alias))
		{
			$row->alias = $row->title;
		}
		$row->alias = JApplication::stringURLSafe($row->alias);
		if (empty($row->id))
		{
			if (property_exists($row, 'created_time') && !$row->created_time)
			{
				$row->created_time = JFactory::getDate()->toSql();
			}

			if (property_exists($row, 'created_user_id') && !$row->created_user_id)
			{
				$row->created_user_id = $user->get('id');
			}
		}

		if (property_exists($row, 'modified_time') && !$row->modified_time)
		{
			$row->modified_time = JFactory::getDate()->toSql();
		}
		if (property_exists($row, 'modified_user_id') && !$row->modified_user_id)
		{
			$row->modified_user_id = $user->get('id');
		}
	}

	/**
	 * Calculate level for categories, used when upgrade from old version to new version
	 *
	 * @param     $id
	 * @param     $list
	 * @param     $children
	 * @param int $maxlevel
	 * @param int $level
	 *
	 * @return mixed
	 */
	public static function calculateCategoriesLevel($id, $list, &$children, $maxlevel = 9999, $level = 1)
	{
		if (@$children[$id] && $level <= $maxlevel)
		{
			foreach ($children[$id] as $v)
			{
				$id = $v->id;
				$v->level = $level;
				$list[$id] = $v;
				$list = self::calculateCategoriesLevel($id, $list, $children, $maxlevel, $level + 1);
			}
		}

		return $list;
	}

    /**
     * @param $item
     */
    public static function getFileExtension($item){
        $original_filename = $item->original_filename;
        $original_filename = explode(".",$original_filename);
        $extension         = $original_filename[count($original_filename) - 1];
        if($extension == ""){
            return "generic";
        }else {
            return $extension;
        }
    }

	public static function cleanPath($path, $removeLastSlashes = true) {
		$path = (string) $path;
		$path = trim($path);
		if(!empty($path)) {
			/*//add slash at the end of path
			$path .= '/';*/
			//clean slashes
			$path = JPath::clean($path, '/');
			//remove first slashes
			$path = preg_replace("/^\/*/", '', $path);
			//remove last slashes
			if($removeLastSlashes) {
				$path = preg_replace("/\/*$/", '', $path);
			}
			//encode url
			//$path = urlencode($path);
		}
		return $path;
	}

	public static function isDropBoxTurnedOn()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('count(extension_id)')->from('#__extensions')->where('type="plugin" and `element`= "dropbox" and enabled = "1"');
        $db->setQuery($query);
        $count = $db->loadResult();
        if($count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function isAmazonS3TurnedOn()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('count(extension_id)')->from('#__extensions')->where('type="plugin" and `element`= "amazon" and enabled = "1"');
        $db->setQuery($query);
        $count = $db->loadResult();
        if($count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function isGdriveTurnedOn()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('count(extension_id)')->from('#__extensions')->where('type="plugin" and `element`= "googledrive" and enabled = "1"');
        $db->setQuery($query);
        $count = $db->loadResult();
        if($count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function isUploadLimitTurnedOn()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('count(extension_id)')->from('#__extensions')->where('type="plugin" and `element`= "limitupload" and enabled = "1"');
        $db->setQuery($query);
        $count = $db->loadResult();
        if($count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

	public static function getDefaultLicense(){
		$db = JFactory::getDbo();
		$config = self::getConfig();
		if ($config->use_default_license){
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from('#__edocman_licenses');
			$query->where('published=1 and default_license=1');
			$db->setQuery($query);
			return (int)$db->loadResult();
		}else{
			return 0;
		}
	}

	static function generateThumbnail($document){
		jimport('joomla.file.filesystem');
		$config			= self::getConfig();
		$thumbname		= md5(date('Y-m-d H:i:s:u'));
		$thumbname		.= ".jpg";
		$target			= JPATH_ROOT.'/media/com_edocman/document/'.$thumbname;
		/*
		$im				= new Imagick($source."[0]"); // 0-first page, 1-second page
		$im->setImageColorspace(255); // prevent image colors from inverting
		$im->setimageformat("jpg");
		$im->thumbnailimage(160, 120); // width and height
		$im->writeimage($target);
		$im->clear();
		$im->destroy();
		*/
		$pdf = $source."[0]";
		exec("convert $pdf convert-img/".$target); 
		JFile::copy($target,JPATH_ROOT.'/media/com_edocman/document/thumbs/'.$thumbname);
		return $thumbname;
	}

	public static function getDocumentCategory($documentId){
		$db = JFactory::getDbo();
		$db->setQuery("Select a.* from #__edocman_categories as a inner join #__edocman_document_category as b on b.category_id = a.id where b.document_id = '$documentId' and b.is_main_category = '1'");
		$category = $db->loadObject();
		return $category;
	}

	static function buildUserGroupQuery($group_col){
		$user		= JFactory::getUser();
		//get user groups
		$returnSql	= "";
		$db			= JFactory::getDbo();
		$tempQuery	= array();
        $groups = $user->getAuthorisedGroups();
		if(count($groups)){
			foreach($groups as $group){
				$tempQuery[] = " ".$group_col." ='".$group."' OR ".$group_col." LIKE '".$group.",%' OR ".$group_col." LIKE '%,".$group.",%' OR ".$group_col." LIKE '%,".$group."'";
			}
		}
		if(count($tempQuery)){
			$returnSql = " or (".implode(" or ",$tempQuery).")";
		}
		return $returnSql;
	}

	/**
	 * Build the list representing the menu tree
	 *
	 * @param   integer  $id         Id of the menu item
	 * @param   string   $indent     The indentation string
	 * @param   array    $list       The list to process
	 * @param   array    &$children  The children of the current item
	 * @param   integer  $maxlevel   The maximum number of levels in the tree
	 * @param   integer  $level      The starting level
	 * @param   int      $type       Set the type of spacer to use. Use 1 for |_ or 0 for -
	 *
	 * @return  array
	 *
	 * @since   1.5
	 */
	public static function treerecurse($id, $indent, $list, &$children, &$prgarr, $maxlevel = 9999, $level = 0, $type = 1)
	{
		if ($level <= $maxlevel && @$children[$id])
		{
			foreach ($children[$id] as $v)
			{
				$id = $v->id;
				if(!in_array($id, $prgarr)){
					$prgarr[] = $id;
					$level = self::getCategoryLevel($id);
					if ($v->parent_id == 0)
					{
						$txt = $v->title;
					}
					else
					{
						if ($type)
						{
							$pre    = '<sup>|_</sup>&#160;';
							$spacer = '';
							for($j=1;$j<=$level;$j++){
								$spacer .= '.&#160;&#160;&#160;&#160;&#160;&#160;';
							}
						}
						else
						{
							$pre    = '- ';
							$spacer = '';
							for($j=1;$j<=$level;$j++){
								$spacer .= '&#160;&#160;';
							}
						}
						$txt = $pre . $v->title;
					}
					$list[$id]           = $v;
					$list[$id]->treename = $indent . $spacer . $txt;
					$list[$id]->children = count(@$children[$id]);
					$list                = self::treerecurse($id, $indent . $spacer, $list, $children, $prgarr, $maxlevel, $level + 1, $type);
				}
			}
		}

		return $list;
	}

	public static function getCategoryLevel($id){
		$db = JFactory::getDbo();
		$level = 0;
		while($id > 0){
			$db->setQuery("Select parent_id from #__edocman_categories where id = '$id'");
			$id = $db->loadResult();
			if($id > 0){
				$level++;
			}
		}
		return $level;
	}

    /**
     * This function is used to check if the document is viewable
     * @param $item
     * @return int
     */
	static function canView($item){
	    $config                 = self::getConfig();
        $extsForView            = explode(',', $config->exts_for_view);
        for ($i = 0, $n = count($extsForView); $i < $n; $i++)
        {
            $extsForView[$i]    = strtolower(trim($extsForView[$i]));
        }
        $fileName               = $item->filename;
        $fileExt                = strtolower(JFile::getExt($fileName));
        if (in_array($fileExt, $extsForView) || ($item->view_url != ''))
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    /**
     * This function is used to check if Google Drive plugin is enabled or not
     * @return bool
     */
    static function isGoogleDrivePluginEnabled(){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('count(extension_id)')->from('#__extensions')->where('`type`= "plugin"')->where('`element` = "googledrive"')->where('`folder` = "edocman"')->where('enabled = "1"');
        $db->setQuery($query);
        $count = $db->loadResult();
        if($count > 0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Render showon string
     *
     * @param array $fields
     *
     * @return string
     */
    public static function renderShowon($fields)
    {
        $output = array();

        $i = 0;

        foreach ($fields as $name => $values)
        {
            $i++;

            $values = (array) $values;

            $data = array(
                'field'  => $name,
                'values' => $values
            );

            if (version_compare(JVERSION, '3.6.99', 'ge'))
            {
                $data['sign'] = '=';
            }

            $data['op'] = $i > 1 ? 'AND' : '';

            $output[] = json_encode($data);
        }

        return '[' . implode(',', $output) . ']';
    }

	static function getGroupLevels($data_type,$item_id){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('`groups`')->from('#__edocman_levels')->where('data_type='.$data_type)->where('item_id='.$item_id);
        $db->setQuery($query);
        return $db->loadResult();
    }

    /**
     * Return Access groups of item
     * @param $data_type
     * @param $item_id
     * @return string
     */
    static function getAccessGroup($data_type,$item_id){
        $db             = JFactory::getDbo();
        $query          = $db->getQuery(true);
        $groups         = self::getGroupLevels($data_type,$item_id);
        $returnArr      = array();
        if($groups != ''){
            $groupArr   = explode(",",$groups);
            foreach ($groupArr as $group){
                $query->clear();
                $query->select('title')->from('#__usergroups')->where('id = '.(int) $group);
                $db->setQuery($query);
                $title  = $db->loadResult();
                $returnArr[] = $title;
            }
        }
        return implode(",", $returnArr);
    }

    /**
     * Return the configuration field checkboxes
     * @param $fieldname
     * @param $fieldvalue
     */
    public static function showCheckboxfield($fieldname,$fieldvalue,$option1='',$option2='')
    {
        if($option1 == ""){
            $option1 = JText::_('OS_YES');
        }
        if($option2 == ""){
            $option2 = JText::_('OS_NO');
        }
        if (version_compare(JVERSION, '3.0', 'lt')) {
            $optionArr = array();
            $optionArr[] = JHTML::_('select.option',1,$option1);
            $optionArr[] = JHTML::_('select.option',0,$option2);
            echo JHTML::_('select.genericlist',$optionArr,$fieldname,'class="input-mini"','value','text',$fieldvalue);
        }else{
            $name = $fieldname;
            if(intval($fieldvalue) == 1){
                $checked2 = 'checked="checked"';
                $checked1 = "";
            }else{
                $checked1 = 'checked="checked"';
                $checked2 = "";
            }
            ?>
            <fieldset id="jform_params_<?php echo $name;?>" class="radio btn-group">
                <input type="radio" id="jform_params_<?php echo $name;?>0" name="jform[<?php echo $name; ?>]" value="0" <?php echO $checked1;?> onclick="javascript:updateRadioButton(0);"/>
                <label for="jform_params_<?php echo $name;?>0"><?php echo $option1;?></label>
                <input type="radio" id="jform_params_<?php echo $name;?>1" name="jform[<?php echo $name; ?>]" value="1" <?php echO $checked2;?> onclick="javascript:updateRadioButton(1);"/>
                <label for="jform_params_<?php echo $name;?>1"><?php echo $option2;?></label>
            </fieldset>
            <?php
        }
    }

    /**
     * This function is used to calculate total files uploaded by user
     * @return int
     */
    static function getTotalUploadFile()
    {
        $user  = JFactory::getUser();
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('count(id)')->from('#__edocman_documents')->where('created_user_id = "'.$user->id.'"');
        $db->setQuery($query);
        return (int)$db->loadResult();
    }

    /**
     * This function is used to calculate total size of files have been uploaded by current logged user
     */
    static function getCurrentUserUploadedSize()
    {
        $user  = JFactory::getUser();
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('`file_size`')->from('#__edocman_documents')->where('created_user_id = "'.$user->id.'"');
        $db->setQuery($query);
        $rows  = $db->loadObjectList();
        $total = 0;
        if(count($rows))
        {
            foreach($rows as $row)
            {
                $filesize = $row->file_size;
                $filesize = explode(" ",$filesize);
                $size     = $filesize[0];
                $filetype = $filesize[1];
                switch ($filetype)
                {
                    case "Byte":
                        $total += (float) $size;
                        break;
                    case "KB":
                        $total += (float) $size * 1024;
                        break;
                    case "MB":
                        $total += (float) $size * 1024 * 1024;
                        break;
                    case "GB":
                        $total += (float) $size * 1024 * 1024 * 1024;
                        break;
                    case "TB":
                        $total += (float) $size * 1024 * 1024 * 1024 * 1024;
                        break;
                }
            }
        }
        return $total;
    }

    static function calculateExpiredDates($unpublished_date)
    {
        $day = 3600*24;
        $hour = 3600;
        $current_time = time();
        $unpublished_date = strtotime($unpublished_date);
        if($unpublished_date > $current_time)
        {
            $period = $unpublished_date - $current_time;
            $nday = $period / $day;
            if($nday > 1 && $nday < 7)
            {
                return round($nday). " ". JText::_('EDOCMAN_DAYS');
            }
            else
            {
                $nhour = $period / $hour;
                return round($nhour). " ". JText::_('EDOCMAN_HOURS');
            }
        }
    }
	/**
	 * Get the version which is currently installed on the site
	 *
	 * @return string
	 */
	public static function getInstalledVersion()
	{
		return '1.11.6';
	}
}

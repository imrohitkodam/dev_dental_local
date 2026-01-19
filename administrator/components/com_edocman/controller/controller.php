<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Dang Thuc Dam
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die();

class EDocmanController extends OSControllerAdmin
{
	/**
	 * Method to display a view.
	 *
	 * @param    boolean $cachable  If true, the view output will be cached
	 * @param    array   $urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return    OSControllerAdmin        This object to support chaining.
	 */
	public function display($cachable = false, array $urlparams = array())
	{
		JFactory::getDocument()->addStyleSheet(JUri::base(true) . '/components/com_edocman/assets/css/style.css');
        JFactory::getDocument()->addStyleSheet(JUri::root(true) . '/components/com_edocman/assets/css/font.css');
		if (version_compare(JVERSION, '3.0', 'lt'))
		{
			EdocmanHelper::loadBootstrap();
		}
		parent::display();
		EdocmanHelper::displayCopyright();

		return $this;
	}

	/**
	 * Run update script to update database schema to latest version
	 */
	function upgrade()
	{
		require_once JPATH_COMPONENT . '/install.edocman.php';
		com_edocmanInstallerScript::com_install();
	}

	/**
	 * Check to see the installed version is up to date or not
	 *
	 * @return int 0 : error, 1 : Up to date, 2 : outof date
	 */
	function check_update()
	{
		$installedVersion = EDocmanHelper::getInstalledVersion();
		$result           = array();
		$result['status'] = 0;
		/*
		if (function_exists('curl_init'))
		{
			$url = 'http://joomdonationdemo.com/versions/edocman.txt';
			$ch  = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$latestVersion = curl_exec($ch);
			curl_close($ch);
			if ($latestVersion)
			{
				if (version_compare($latestVersion, $installedVersion, 'gt'))
				{
					$result['status']  = 2;
					$result['message'] = JText::sprintf('EDOCMAN_UPDATE_CHECKING_UPDATEFOUND', $latestVersion);
				}
				else
				{
					$result['status']  = 1;
					$result['message'] = JText::_('EDOCMAN_UPDATE_CHECKING_UPTODATE');
				}
			}
		}
		echo json_encode($result);
		JFactory::getApplication()->close();
		*/
		$component     = JComponentHelper::getComponent('com_installer');
		$params        = $component->params;
		$cache_timeout = $params->get('cachetimeout', 6, 'int');
		$cache_timeout = 3600 * $cache_timeout;
		// Get the minimum stability.
		$minimum_stability = $params->get('minimum_stability', JUpdater::STABILITY_STABLE, 'int');
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_installer/models');
		/** @var InstallerModelUpdate $model */
		$model = JModelLegacy::getInstance('Update', 'InstallerModel');
		$model->purge();
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('extension_id')
			->from('#__extensions')
			->where('`type` = "package"')
			->where('`element` = "pkg_edocman"');
		$db->setQuery($query);
		$eid = (int) $db->loadResult();
		$result['status'] = 0;
		if ($eid)
		{
			$ret = JUpdater::getInstance()->findUpdates($eid, $cache_timeout, $minimum_stability);
			if ($ret)
			{
				$model->setState('list.start', 0);
				$model->setState('list.limit', 0);
				$model->setState('filter.extension_id', $eid);
				$updates          = $model->getItems();
				$result['status'] = 2;
				if (count($updates))
				{
					$result['status']  = 2;
					$result['message'] = JText::sprintf('EDOCMAN_UPDATE_CHECKING_UPDATEFOUND', $updates[0]->version);
				}
				else
				{
					$result['status']  = 2;
					$result['message'] = JText::sprintf('EDOCMAN_UPDATE_CHECKING_UPDATEFOUND', null);
				}
			}
			else
			{
				$result['status']  = 1;
				$result['message'] = JText::_('EDOCMAN_UPDATE_CHECKING_UPTODATE');
			}
		}
		echo json_encode($result);
		JFactory::getApplication()->close();
	}
	
	public function rebuild_categories()
	{
		require_once JPATH_ADMINISTRATOR.'/components/com_edocman/table/category.php';
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$arrCats    = array(0);
		while (count($arrCats))
		{
			$catId = array_pop($arrCats);
			$query->clear();
			$query->select('id')
				->from('#__edocman_categories')
				->where('parent_id = '. $catId);
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			for ($i = 0, $n = count($rows); $i < $n; $i++)
			{
				$row       = $rows[$i];
				$arrCats[] = $row->id;
				$rowCategory = JTable::getInstance('Category', 'EDocmanTable');
				$rowCategory->load($row->id);
				if ($rowCategory->id)
				{
					$rowCategory->store();
				}
			}
		}
	}


	public function rebuild_documents()
	{
		require_once JPATH_ADMINISTRATOR.'/components/com_edocman/table/document.php';
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.id, b.category_id')
			->from('#__edocman_documents AS a')
			->innerJoin('#__edocman_document_category AS b ON (a.id = b.document_id AND b.is_main_category = 1)');
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		for ($i = 0, $n = count($rows); $i < $n; $i++)
		{
			$row       = $rows[$i];
			$arrCats[] = $row->id;
			$rowDocument = JTable::getInstance('Document', 'EDocmanTable');
			$rowDocument->load($row->id);
			if ($rowDocument->id)
			{
				$rowDocument->setMainCategory($row->category_id);
				$rowDocument->store();
			}
		}
	}


    /**
     * Method to run batch operations.
     *
     * @param   JModelLegacy  $model  The model of the component being processed.
     *
     * @return	boolean	 True if successful, false otherwise and internal error is set.
     *
     * @since	12.2
     */
    public function batch($model)
    {
        $vars = $this->input->post->get('batch', array(), 'array');
        $cid  = $this->input->post->get('cid', array(), 'array');
        // Attempt to run the batch operation.
        if ($model->batch($vars, $cid))
        {
            $this->setMessage(JText::_('JLIB_APPLICATION_SUCCESS_BATCH'));
            return true;
        }
        else
        {
            $this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_FAILED', $model->getError()), 'warning');
            return false;
        }
    }

	public function updatedatabase(){
        //Get document path config option
        $db = JFactory::getDbo();
        $sql = 'SELECT config_value FROM #__edocman_configs WHERE config_key="documents_path"';
        $db->setQuery($sql);
        $path = $db->loadResult();
        if (!$path)
        {
            $path = JPATH_ROOT . '/edocman';
            if (!JFolder::exists($path))
            {
                //Create the folder
                JFolder::create($path);
                //Copy htaccess file
                JFile::copy(JPATH_ADMINISTRATOR . '/components/com_edocman/htaccess.txt', $path . '/.htaccess');
            }
            $path = str_replace("\\", "/", $path);
            $sql  = 'UPDATE #__edocman_configs SET config_value="' . $path . '" WHERE config_key="documents_path"';
            $db->setQuery($sql);
            $db->execute();
        }

        $sql = 'SELECT config_value FROM #__edocman_configs WHERE config_key="default_sort_option"';
        $db->setQuery($sql);
        $defaultSortOption = $db->loadResult();
        if ($defaultSortOption)
        {
            $defaultSortOption = str_replace('a.', 'tbl.', $defaultSortOption);
            $sql               = "UPDATE #__edocman_configs SET config_value='$defaultSortOption' WHERE config_key='default_sort_option'";
            $db->setQuery($sql);
            $db->execute();
        }

        $fields = array_keys($db->getTableColumns('#__edocman_licenses'));
        if (!in_array('default_license', $fields))
        {
            $sql = "ALTER TABLE  `#__edocman_licenses` ADD `default_license` tinyint(1) NOT NULL DEFAULT '0' AFTER `published`;";
            $db->setQuery($sql);
            $db->execute();
        }


        $fields = array_keys($db->getTableColumns('#__edocman_categories'));
        if (in_array('directory', $fields))
        {
            //We need to update data here
            $sql = 'SELECT a.id, a.filename, c.path FROM #__edocman_documents AS a '
                . 'INNER JOIN #__edocman_document_category AS b '
                . 'ON a.id = b.document_id '
                . 'INNER JOIN #__edocman_categories AS c '
                . 'ON b.category_id = c.id ';
            $db->setQuery($sql);
            $rows = $db->loadObjectList();
            for ($i = 0, $n = count($rows); $i < $n; $i++)
            {
                $row = $rows[$i];
                if ($row->path)
                {
                    $newFileName = $row->path . '/' . $row->filename;
                    $sql         = 'UPDATE #__edocman_documents SET filename="' . $newFileName . '" WHERE id=' . $row->id;
                    $db->setQuery($sql);
                    $db->execute();
                }
            }
            //Now, remove the unnecessary columns
            $sql = 'ALTER TABLE #__edocman_categories DROP COLUMN `directory`';
            $db->setQuery($sql);
            $db->execute();
        }

        if (!in_array('path', $fields))
        {
            $sql = "ALTER TABLE  `#__edocman_categories` ADD  `path` VARCHAR( 255 ) NULL DEFAULT  '';";
            $db->setQuery($sql);
            $db->execute();
        }

        if (!in_array('level', $fields))
        {
            $sql = "ALTER TABLE  `#__edocman_categories` ADD  `level` TINYINT( 4 ) NOT NULL DEFAULT '1';";
            $db->setQuery($sql);
            $db->execute();

            // Update level for categories
            $query = $db->getQuery(true);
            $query->select('id, title, parent_id');
            $query->from('#__edocman_categories');
            $query->where('published=1');
            $db->setQuery($query);
            $rows = $db->loadObjectList();
            // first pass - collect children
            if (count($rows))
            {
                $children = array();
                foreach ($rows as $v)
                {
                    $pt   = $v->parent_id;
                    $list = @$children[$pt] ? $children[$pt] : array();
                    array_push($list, $v);
                    $children[$pt] = $list;
                }

                require_once JPATH_ROOT . '/components/com_edocman/helper/helper.php';
                $list = EDocmanHelper::calculateCategoriesLevel(0, array(), $children, 4);
                foreach ($list as $id => $category)
                {
                    $sql = "UPDATE #__edocman_categories SET `level`=$category->level WHERE id=$id";
                    $db->setQuery($sql);
                    $db->execute();
                }
            }

        }

        if (!in_array('user_ids', $fields))
        {
            $sql = "ALTER TABLE  `#__edocman_categories` ADD  `user_ids` VARCHAR( 255 ) NULL DEFAULT  '';";
            $db->setQuery($sql);
            $db->execute();
        }


        if (!in_array('notify_group_ids', $fields))
        {
            $sql = "ALTER TABLE  `#__edocman_categories` ADD  `notify_group_ids` VARCHAR( 255 ) NULL DEFAULT  '';";
            $db->setQuery($sql);
            $db->execute();
        }

        if (!in_array('language', $fields))
        {
            $sql = "ALTER TABLE  `#__edocman_categories` ADD  `language` VARCHAR( 50 ) NULL DEFAULT  '*';";
            $db->setQuery($sql);
            $db->execute();

            $sql = 'UPDATE #__edocman_categories SET `language`="*" ';
            $db->setQuery($sql);
            $db->execute();
        }
        if (!in_array('notification_emails', $fields))
        {
            $sql = "ALTER TABLE  `#__edocman_categories` ADD  `notification_emails` VARCHAR( 255 ) NULL DEFAULT  '';";
            $db->setQuery($sql);
            $db->execute();
        }
        $fields = array_keys($db->getTableColumns('#__edocman_documents'));
        if (!in_array('user_ids', $fields))
        {
            $sql = "ALTER TABLE  `#__edocman_documents` ADD  `user_ids` VARCHAR( 255 ) NULL DEFAULT  '';";
            $db->setQuery($sql);
            $db->execute();
        }
        if (!in_array('language', $fields))
        {
            $sql = "ALTER TABLE  `#__edocman_documents` ADD  `language` VARCHAR( 50 ) NULL DEFAULT  '*';";
            $db->setQuery($sql);
            $db->execute();

            $sql = 'UPDATE #__edocman_documents SET `language`="*" ';
            $db->setQuery($sql);
            $db->execute();
        }

        if (!in_array('indexed_content', $fields))
        {
            $sql = "ALTER TABLE  `#__edocman_documents` ADD  `indexed_content` text NULL DEFAULT  '';";
            $db->setQuery($sql);
            $db->execute();
        }

        if (!in_array('tags', $fields))
        {
            $sql = "ALTER TABLE  `#__edocman_documents` ADD  `tags` text NULL DEFAULT NULL;";
            $db->setQuery($sql);
            $db->execute();
        }

        if (!in_array('indicators', $fields))
        {
            $sql = "ALTER TABLE  `#__edocman_documents` ADD  `indicators` VARCHAR( 50 ) NULL;";
            $db->setQuery($sql);
            $db->execute();
        }
        if (!in_array('publish_up', $fields))
        {
            $sql = "ALTER TABLE  `#__edocman_documents` ADD  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';";
            $db->setQuery($sql);
            $db->execute();
        }

        if (!in_array('publish_down', $fields))
        {
            $sql = "ALTER TABLE  `#__edocman_documents` ADD  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';";
            $db->setQuery($sql);
            $db->execute();
        }

        if (!in_array('params', $fields))
        {
            $sql = "ALTER TABLE  `#__edocman_documents` ADD  `params` text NULL DEFAULT  '';";
            $db->setQuery($sql);
            $db->execute();
        }

        if (!in_array('view_url', $fields))
        {
            $sql = "ALTER TABLE  `#__edocman_documents` ADD  `view_url` varchar(255) DEFAULT '';";
            $db->setQuery($sql);
            $db->execute();
        }

        if (!in_array('file_size', $fields))
        {
            $sql = "ALTER TABLE  `#__edocman_documents` ADD  `file_size` varchar(100) NOT NULL;";
            $db->setQuery($sql);
            $db->execute();
        }

        $fields = array_keys($db->getTableColumns('#__edocman_document_category'));
        if (!in_array('is_main_category', $fields))
        {
            $sql = "ALTER TABLE  `#__edocman_document_category` ADD  `is_main_category` TINYINT NOT NULL DEFAULT  '0' ;";
            $db->setQuery($sql);
            $db->execute();

            $sql = 'UPDATE #__edocman_document_category SET is_main_category=1';
            $db->setQuery($sql);
            $db->execute();
        }
        #Add index to improve the speed
        $sql = 'SHOW INDEX FROM #__edocman_document_category';
        $db->setQuery($sql);
        $rows   = $db->loadObjectList();
        $fields = array();
        for ($i = 0, $n = count($rows); $i < $n; $i++)
        {
            $row      = $rows[$i];
            $fields[] = $row->Column_name;
        }
        if (!in_array('document_id', $fields))
        {
            $sql = 'ALTER TABLE `#__edocman_document_category` ADD INDEX ( `document_id` )';
            $db->setQuery($sql);
            $db->execute();
        }
        if (!in_array('category_id', $fields))
        {
            $sql = 'ALTER TABLE `#__edocman_document_category` ADD INDEX ( `category_id` )';
            $db->setQuery($sql);
            $db->execute();
        }

        $fields = array_keys($db->getTableColumns('#__edocman_statistics'));
        if (!in_array('name', $fields))
        {
            $sql = "ALTER TABLE  `#__edocman_statistics` ADD  `name` VARCHAR( 255 ) NULL DEFAULT  '';";
            $db->setQuery($sql);
            $db->execute();
        }

        if (!in_array('email', $fields))
        {
            $sql = "ALTER TABLE  `#__edocman_statistics` ADD  `email` VARCHAR( 255 ) NULL DEFAULT  '';";
            $db->setQuery($sql);
            $db->execute();
        }

        if (!in_array('download_code', $fields))
        {
            $sql = "ALTER TABLE  `#__edocman_statistics` ADD  `download_code` VARCHAR( 50 ) NULL DEFAULT  '';";
            $db->setQuery($sql);
            $db->execute();
        }

        $sql = "CREATE TABLE IF NOT EXISTS `#__edocman_tags` (
	    `id` int(11) NOT NULL AUTO_INCREMENT,
	    `tag` varchar(100) DEFAULT NULL,
	    `published` tinyint(3) unsigned DEFAULT NULL,
	    PRIMARY KEY (`id`)
		) DEFAULT CHARSET=utf8;";

        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__edocman_document_tags` (
	    `id` int(11) NOT NULL AUTO_INCREMENT,
	    `document_id` int(11) DEFAULT NULL,
	    `tag_id` int(11) DEFAULT NULL,
	    PRIMARY KEY (`id`)
		) DEFAULT CHARSET=utf8;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `#__edocman_urls` (
	      `id` int(11) NOT NULL AUTO_INCREMENT,
	      `md5_key` text,
	      `query` text,
	      `object_name` varchar(50) DEFAULT NULL,
	      `object_id` int(11) NOT NULL DEFAULT '0',
	      PRIMARY KEY (`id`)
	    )DEFAULT CHARSET=utf8;";
        $db->setQuery($sql);
        $db->execute();

        $sql = "TRUNCATE TABLE `#__edocman_urls`";
        $db->setQuery($sql);
        $db->execute();

        // Fix documents date from old version
        $query = $db->getQuery(true);
        $query->select('id, filename, created_time, modified_time')
            ->from('#__edocman_documents')
            ->where('(created_time LIKE "0000-00-00 00:00:00" OR created_time IS NULL OR modified_time LIKE "0000-00-00 00:00:00" OR modified_time IS NULL)')
            ->where('document_url LIKE "" OR document_url IS NULL');
        $db->setQuery($query);
        $documents = $db->loadObjectList();
        if (count($documents))
        {
            $sql = 'SELECT config_value FROM #__edocman_configs WHERE config_key="documents_path"';
            $db->setQuery($sql);
            $documentPath = $db->loadResult();
            foreach ($documents as $document)
            {
                $filePath = $documentPath . '/' . $document->filename;
                if (file_exists($filePath))
                {
                    $createDate   = (empty($document->created_time) || $document->created_time = '0000-00-00 00:00:00' || $document->created_time = 'NULL') ? JFactory::getDate(strftime('%Y-%m-%d %H:%M:%S', filectime($filePath))) : $document->created_time;
                    $modifiedDate = (empty($document->modified_time) || $document->modified_time = '0000-00-00 00:00:00' || $document->modified_time = 'NULL') ? JFactory::getDate(strftime('%Y-%m-%d %H:%M:%S', filemtime($filePath))) : $document->created_time;
                    $query->clear();
                    $query->update('#__edocman_documents')
                        ->set('created_time=' . $db->quote($createDate))
                        ->set('modified_time=' . $db->quote($modifiedDate))
                        ->where('id=' . (int) $document->id);
                    $db->setQuery($query);
                    $db->execute();
                }
            }
        }
		$msg = "DATABASE SCHEMA HAS BEEN UPDATED SUCCESSFULLY !";
		JFactory::getApplication()->enqueueMessage($msg);
		JFactory::getApplication()->redirect("index.php?option=com_edocman");
    }

	public function sefoptimize(){
		$db = JFactory::getDbo();
    	$db->setQuery("Delete from #__edocman_urls");
    	$db->execute();
		JFactory::getApplication()->enqueueMessage(JText::_('EDOCMAN_SEF_URLS_OPTIMIZATION_HAS_BEEN_COMPLETED'));
    	JFactory::getApplication()->redirect("index.php?option=com_edocman");
	}

	public function googledriveauthenticate(){
        $db = JFactory::getDbo();
        JLoader::register('EdocmanGoogle', JPATH_PLUGINS.'/edocman/googledrive/Google.php');
        $google = new EdocmanGoogle();
        $credentials = $google->authenticate();
       // echo $credentials;
        $google->storeCredentials($credentials);
        //Check if dropfiles folder exists and create if not
        $plugin = JPluginHelper::getPlugin('edocman','googledrive');
        if($plugin){
            $addrootpath        = 0;
            $pluginParams       = new JRegistry($plugin->params);
            $root_path          = $pluginParams->get('root_path','edocman');
            $db->setQuery("Select count(id) from #__edocman_googledrive where `type` = 0 and element_name like '$root_path' and element_id = '0'");
            $count              = $db->loadResult();
            if($count == 0){
                $addrootpath    = 1;
            }else{
                $db->setQuery("Select cloud_id from #__edocman_googledrive where `type` = 0 and element_name like '$root_path' and element_id = '0'");
                $cloud_id       = $db->loadResult();
                if($cloud_id    != ''){
                    if(! $google->folderExists($cloud_id)){
                        $addrootpath = 1;
                    }
                }
            }
            if ($addrootpath == 1) {
                $folderId       = $google->createFolder($root_path);
                $folderId       = $folderId->id;
                $db->setQuery("Select count(id) from #__edocman_googledrive where `type` = 0 and element_id = '0' and element_name like '$root_path' ");
                $count          = $db->loadResult();
                if($count == 0){
                    $db->setQuery("Insert into #__edocman_googledrive (id,`type`,element_id,element_name,cloud_id) values (NULL,'0','0','$root_path','$folderId')");
                    $db->execute();
                }else{
                    $db->setQuery("Update #__edocman_googledrive set cloud_id = '$folderId' where `type`= '0' and element_name like '$root_path' and element_id = '0'");
                    $db->execute();
                }
            }
        }

        $this->setRedirect('index.php?option=com_edocman&view=configuration&layout=redirect');
        $this->redirect();
    }

    public function googledrivelogout(){
        JLoader::register('EdocmanGoogle', JPATH_PLUGINS.'/edocman/googledrive/Google.php');
        $google = new EdocmanGoogle();
        $google->logout();
        $this->setRedirect($_SERVER['HTTP_REFERER']);
        $this->redirect();
    }

    public function copydocument()
    {

    }
}
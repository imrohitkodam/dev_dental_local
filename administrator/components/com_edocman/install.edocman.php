<?php
/**
 * @version        1.11.3
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2019 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
/**
 * Change the db structure of the previous version
 *
 */
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class com_edocmanInstallerScript
{

	function preflight($type, $parent)
	{
		//Deleting files/folders which are not using from version 1.6.0
		if (JFolder::exists(JPATH_ADMINISTRATOR . '/components/com_edocman/controllers'))
		{
			JFolder::delete(JPATH_ADMINISTRATOR . '/components/com_edocman/controllers');
		}
		if (JFolder::exists(JPATH_ADMINISTRATOR . '/components/com_edocman/models'))
		{
			JFolder::delete(JPATH_ADMINISTRATOR . '/components/com_edocman/models');
		}
		if (JFolder::exists(JPATH_ADMINISTRATOR . '/components/com_edocman/views'))
		{
			JFolder::delete(JPATH_ADMINISTRATOR . '/components/com_edocman/views');
		}
		if (JFolder::exists(JPATH_ADMINISTRATOR . '/components/com_edocman/tables'))
		{
			JFolder::delete(JPATH_ADMINISTRATOR . '/components/com_edocman/tables');
		}
		if (JFolder::exists(JPATH_ADMINISTRATOR . '/components/com_edocman/helpers'))
		{
			JFolder::delete(JPATH_ADMINISTRATOR . '/components/com_edocman/helpers');
		}
		if (JFile::exists(JPATH_ADMINISTRATOR . '/components/com_edocman/controller.php'))
		{
			JFile::delete(JPATH_ADMINISTRATOR . '/components/com_edocman/controller.php');
		}
		if (JFolder::exists(JPATH_ROOT . '/components/com_edocman/controllers'))
		{
			JFolder::delete(JPATH_ROOT . '/components/com_edocman/controllers');
		}
		if (JFolder::exists(JPATH_ROOT . '/components/com_edocman/models'))
		{
			JFolder::delete(JPATH_ROOT . '/components/com_edocman/models');
		}
		if (JFolder::exists(JPATH_ROOT . '/components/com_edocman/views'))
		{
			JFolder::delete(JPATH_ROOT . '/components/com_edocman/views');
		}
		if (JFile::exists(JPATH_ROOT . '/components/com_edocman/controller.php'))
		{
			JFile::delete(JPATH_ROOT . '/components/com_edocman/controller.php');
		}

		if (JFolder::exists(JPATH_ROOT . '/edocman'))
		{
			// Change folder permission back to 0755
			@chmod(JPATH_ROOT . '/edocman', 0755);
		}

		$customCss = JPATH_ROOT . '/components/com_edocman/assets/css/custom.css';
		if (!file_exists($customCss))
		{
			$fp = fopen($customCss, 'w');
			fclose($fp);
			@chmod($customCss, 0777);
		}
	}

	function install($parent)
	{
		self::com_install();
	}

	function update($parent)
	{
		self::com_install();
	}

	function postflight($type, $parent)
	{
		if ($type == 'install')
		{
			//Setup default permissions
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id')
				->from('#__assets')
				->where('name="com_edocman"');
			$db->setQuery($query);
			$assetId = (int) $db->loadResult();
			if ($assetId)
			{
				$query->clear();
				$query->update('#__assets')
					->set('rules=' . $db->quote('{"core.admin":[],"core.manage":[],"core.create":[],"core.delete":[],"core.edit":[],"core.edit.state":[],"core.edit.own":[],"edocman.download":{"1":1},"edocman.assign_documents_to_users":{"6":1,"7":1}}'))
					->where('id=' . $assetId);
				$db->setQuery($query);
				$db->execute();
			}
		}
	}


	public static function com_install()
	{
		$db  = JFactory::getDbo();
		$sql = 'SELECT COUNT(*) FROM #__edocman_configs';
		$db->setQuery($sql);
		$total = $db->loadResult();
		if (!$total)
		{
			$configSql = JPATH_ADMINISTRATOR . '/components/com_edocman/sql/config.edocman.sql';
			$sql       = JFile::read($configSql);
			$queries   = $db->splitSql($sql);
			if (count($queries))
			{
				foreach ($queries as $query)
				{
					$query = trim($query);
					if ($query != '' && $query{0} != '#')
					{
						$db->setQuery($query);
						$db->execute();
					}
				}
			}
		}
		//Get document path config option
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

		$sql = "Select count(id) from #__edocman_configs where config_key like 'user_group_ids'";
		$db->setQuery($sql);
		$count = $db->loadResult();
		if((int)$count == 0){
			$db->setQuery("Insert into #__edocman_configs(id, config_key,config_value) values (NULL,'user_group_ids','1')");
			$db->execute();
		}

		$sql = "Select count(id) from #__edocman_configs where config_key like 'show_alias_form'";
		$db->setQuery($sql);
		$count = $db->loadResult();
		if((int)$count == 0){
			$db->setQuery("Insert into #__edocman_configs(id, config_key,config_value) values (NULL,'show_alias_form','1')");
			$db->execute();
		}

		$sql = "Select count(id) from #__edocman_configs where config_key like 'show_thumb_form'";
		$db->setQuery($sql);
		$count = $db->loadResult();
		if((int)$count == 0){
			$db->setQuery("Insert into #__edocman_configs(id, config_key,config_value) values (NULL,'show_thumb_form','1')");
			$db->execute();
		}

		$sql = "Select count(id) from #__edocman_configs where config_key like 'show_meta_form'";
		$db->setQuery($sql);
		$count = $db->loadResult();
		if((int)$count == 0){
			$db->setQuery("Insert into #__edocman_configs(id, config_key,config_value) values (NULL,'show_meta_form','1')");
			$db->execute();
		}

		$sql = "Select count(id) from #__edocman_configs where config_key like 'show_tag_form'";
		$db->setQuery($sql);
		$count = $db->loadResult();
		if((int)$count == 0){
			$db->setQuery("Insert into #__edocman_configs(id, config_key,config_value) values (NULL,'show_tag_form','1')");
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

		if (!in_array('auto_approval', $fields))
		{
			$sql = "ALTER TABLE  `#__edocman_categories` ADD  `auto_approval` TINYINT(1) NULL DEFAULT  '0';";
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

		if (!in_array('hide_download', $fields))
		{
			$sql = "ALTER TABLE  `#__edocman_categories` ADD  `hide_download` TINYINT(1) NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('show_view', $fields))
		{
			$sql = "ALTER TABLE  `#__edocman_categories` ADD  `show_view` TINYINT(1) NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('accesspicker', $fields))
		{
			$sql = "ALTER TABLE  `#__edocman_categories` ADD  `accesspicker` TINYINT(1) NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('sort_option', $fields))
		{
			$sql = "ALTER TABLE  `#__edocman_categories` ADD  `sort_option` varchar(255) NULL DEFAULT  '';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('sort_direction', $fields))
		{
			$sql = "ALTER TABLE  `#__edocman_categories` ADD  `sort_direction` varchar(255) NULL DEFAULT  '';";
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
		if (!in_array('owner_group_ids', $fields))
		{
			$sql = "ALTER TABLE  `#__edocman_documents` ADD  `owner_group_ids` VARCHAR( 255 ) NULL DEFAULT  '';";
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

		if (!in_array('document_history', $fields))
		{
			$sql = "ALTER TABLE  `#__edocman_documents` ADD  `document_history` text NOT NULL ;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('document_version', $fields))
		{
			$sql = "ALTER TABLE  `#__edocman_documents` ADD  `document_version` varchar(10) NOT NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('is_locked', $fields))
		{
			$sql = "ALTER TABLE  `#__edocman_documents` ADD  `is_locked` tinyint(1) NOT NULL DEFAULT '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('locked_by', $fields))
		{
			$sql = "ALTER TABLE  `#__edocman_documents` ADD  `locked_by` int(11) NOT NULL DEFAULT '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('locked_time', $fields))
		{
			$sql = "ALTER TABLE  `#__edocman_documents` ADD  `locked_time` datetime NOT NULL;";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('accesspicker', $fields))
		{
			$sql = "ALTER TABLE  `#__edocman_documents` ADD  `accesspicker` TINYINT(1) NULL DEFAULT  '0';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('download_limit_per_user', $fields))
		{
			$sql = "ALTER TABLE  `#__edocman_documents` ADD  `download_limit_per_user` INT(5) NULL DEFAULT  '-1';";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!in_array('send_reminder', $fields))
		{
			$sql = "ALTER TABLE  `#__edocman_documents` ADD  `send_reminder` TINYINT(1) NULL DEFAULT  '0';";
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

		$db->setQuery("CREATE TABLE IF NOT EXISTS `#__edocman_unsubscribe_emails` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `email` varchar(255) NOT NULL,
						  PRIMARY KEY (`id`)
						) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");
		$db->execute();

		$db->setQuery("CREATE TABLE IF NOT EXISTS `#__edocman_menus` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `menu_name` varchar(255) DEFAULT NULL,
						  `menu_parent_id` int(11) DEFAULT NULL,
						  `menu_link` varchar(255) DEFAULT NULL,
						  `published` tinyint(1) UNSIGNED DEFAULT NULL,
						  `ordering` int(11) DEFAULT NULL,
						  `menu_class` varchar(255) DEFAULT NULL,
						  PRIMARY KEY (`id`)
						) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;");
		$db->execute();
		
		$db->setQuery("Select count(id) from `#__edocman_menus` group by id");
		$count = $db->loadResult();
		if($count == 0){
			$db->setQuery("INSERT INTO `#__edocman_menus` (`id`, `menu_name`, `menu_parent_id`, `menu_link`, `published`, `ordering`, `menu_class`) VALUES
						(11, 'EDOCMAN_DASHBOARD', 0, 'index.php?option=com_edocman&view=dashboard', 1, 1, 'home'),
						(12, 'EDOCMAN_CATEGORIES', 0, 'index.php?option=com_edocman&view=categories', 1, 2, 'list-view'),
						(13, 'EDOCMAN_DOCUMENTS', 0, 'index.php?option=com_edocman&view=documents', 1, 3, 'book'),
						(14, 'EDOCMAN_LICENSE', 0, 'index.php?option=com_edocman&view=licenses', 1, 4, 'shuffle'),
						(15, 'EDOCMAN_OTHERS', 0, '', 1, 5, 'folder-open'),
						(16, 'EDOCMAN_BATCH_UPLOAD', 15, 'index.php?option=com_edocman&view=upload', 1, 1, 'upload'),
						(17, 'EDOCMAN_BULK_IMPORT', 15, 'index.php?option=com_edocman&view=import', 1, 2, 'upload'),
						(18, 'EDOCMAN_DOWNLOADLOGS', 15, 'index.php?option=com_edocman&view=downloadlogs', 1, 3, 'download'),
						(19, 'EDOCMAN_TRANSLATION', 15, 'index.php?option=com_edocman&view=language', 1, 4, 'flag'),
						(20, 'EDOCMAN_REMOVE_ORPHAN_DOCUMENTS', 15, 'index.php?option=com_edocman&view=documents&layout=remove_orphan', 1, 5, 'delete'),
						(21, 'EDOCMAN_SEF_OPTIMIZE', 15, 'index.php?option=com_edocman&view=sefoptimize', 1, 6, 'link'),
						(22, 'EDOCMAN_CONFIGURATION', 0, 'index.php?option=com_edocman&view=configuration', 1, 6, 'cog');");
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

		$sql = "CREATE TABLE IF NOT EXISTS `#__edocman_googledrive` (
				  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				  `type` tinyint(1) NOT NULL DEFAULT '0',
				  `element_id` int(11) NOT NULL DEFAULT '0',
				  `element_name` varchar(255) NOT NULL,
				  `cloud_id` varchar(255) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->execute();

		$sql = "CREATE TABLE IF NOT EXISTS `#__edocman_googledrive_credentials` (
				  `credentials` text NOT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
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


		$sql = "CREATE TABLE IF NOT EXISTS `#__edocman_galleries` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `document_id` int(10) UNSIGNED DEFAULT '0',
			  `title` varchar(255) DEFAULT NULL,
			  `image` varchar(255) DEFAULT NULL,
			  `ordering` int(11) UNSIGNED DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_document_id` (`document_id`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->execute();

		$sql = "CREATE TABLE IF NOT EXISTS `#__edocman_levels` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `data_type` tinyint(1) NOT NULL DEFAULT '0',
				  `item_id` int(11) NOT NULL DEFAULT '0',
				  `groups` varchar(255) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;";
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


		if (!JFolder::exists(JPATH_ROOT . '/images/com_edocman'))
		{
			JFolder::create(JPATH_ROOT . '/images/com_edocman');
		}

		if (!JFolder::exists(JPATH_ROOT . '/images/com_edocman/galleries'))
		{
			JFolder::create(JPATH_ROOT . '/images/com_edocman/galleries');
		}
	}
}

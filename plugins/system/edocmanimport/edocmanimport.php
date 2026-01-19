<?php
/**
 * @version               1.6.8
 * @package               Joomla
 * @subpackage            Event Booking
 * @author                Tuan Pham Ngoc
 * @copyright             Copyright (C) 2010 - 2015 Ossolution Team
 * @license               GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class plgSystemEDocmanImport extends JPlugin
{
	public function onAfterRender()
	{
		if (file_exists(JPATH_ROOT . '/components/com_edocman/edocman.php'))
		{
			jimport('joomla.filesystem.folder');
			jimport('joomla.filesystem.file');
			$lastRun   = (int) $this->params->get('last_run', 0);
			$now       = time();
			$cacheTime = 3600; // 60 minutes
			if (($now - $lastRun) < $cacheTime)
			{
				return;
			}

			//Store last run time
			$db          = JFactory::getDbo();
			$query       = $db->getQuery(true);
			$insertQuery = $db->getQuery(true);
			$this->params->set('last_run', $now);
			$params = $this->params->toString();
			$query->clear();
			$query->update('#__extensions')
				->set('params=' . $db->quote($params))
				->where('`element`="edocmanimport"')
				->where('`folder`="system"');

			try
			{
				// Lock the tables to prevent multiple plugin executions causing a race condition
				$db->lockTable('#__extensions');
			}
			catch (Exception $e)
			{
				// If we can't lock the tables it's too risk continuing execution
				return;
			}

			try
			{
				// Update the plugin parameters
				$result = $db->setQuery($query)->execute();
				$this->clearCacheGroups(array('com_plugins'), array(0, 1));
			}
			catch (Exception $exc)
			{
				// If we failed to execite
				$db->unlockTables();
				$result = false;
			}
			try
			{
				// Unlock the tables after writing
				$db->unlockTables();
			}
			catch (Exception $e)
			{
				// If we can't lock the tables assume we have somehow failed
				$result = false;
			}
			// Abort on failure
			if (!$result)
			{
				return;
			}

			require_once JPATH_ROOT . '/components/com_edocman/helper/helper.php';
			require_once JPATH_ADMINISTRATOR . '/components/com_edocman/table/document.php';


			$config         = EDocmanHelper::getConfig();
			$path           = $config->documents_path;
			$path           = str_replace("\\", "/", $path);
			$path           = rtrim($path, '/');
			$fileExtensions = explode(',', $config->allowed_file_types);
			for ($i = 0, $n = count($fileExtensions); $i < $n; $i++)
			{
				$fileExtensions[$i] = trim($fileExtensions[$i]);
			}
			$filter     = implode('|', $fileExtensions);
			$files      = JFolder::files($path, $filter, true, true);
			$pathLength = strlen($path);
			if (count($files))
			{
				// Get list of categories by path
				$query->clear();
				$query->select('id, `path`, `access`')
					->from('#__edocman_categories');
				$db->setQuery($query);
				$categories = $db->loadObjectList('path');

				$query->clear()
					->select('COUNT(*)')
					->from('#__edocman_documents AS a')
					->innerJoin('#__edocman_document_category AS b ON a.id = b.document_id');

				foreach ($files as $file)
				{
					$file         = substr($file, $pathLength + 1);
					$file         = str_replace("\\", "/", $file);
					$fullFilePath = $file;
					$categoryId   = $this->params->get('default_category_id', 0);
					$documentData = array();
					$slashPos     = strrpos($file, '/');
					$access       = 1;
					if ($slashPos !== false)
					{
						$path = substr($file, 0, $slashPos);
						if ($path && isset($categories[$path]))
						{
							$categoryId = $categories[$path]->id;
							$access     = $categories[$path]->access;
						}
						$file = substr($file, $slashPos + 1);
					}

					// Could not detect the category, so won't import this file

					if (!$categoryId)
					{
						continue;
					}

					$ext = JFile::getExt($file);
					if ($ext)
					{
						$basename = substr($file, 0, strlen($file) - (strlen($ext) + 1));
					}
					else
					{
						$basename = $file;
					}

					// TODO : Get access level from the category document belong to

					$documentData['access']            = $access;
					$documentData['title']             = $basename;
					$documentData['original_filename'] = $file;
					$documentData['alias']             = '';
					$documentData['published']         = 1;
					$documentData['filename']          = $fullFilePath;

					// Check if the document is existed or not
					$query->where('a.filename = ' . $db->quote($documentData['filename']));
					$db->setQuery($query);
					$total = (int) $db->loadResult();

					// Clear where clause, make it ready to use for next iterator
					$query->clear('where');

					// If document was imported before, ignore it
					if ($total > 0)
					{
						continue;
					}
					$row = JTable::getInstance('Document', 'EdocmanTable');
					$row->setMainCategory($categoryId);
					$row->bind($documentData);
					EDocmanHelper::prepareDocument($row, $categoryId);
					$row->check();
					$row->store();

					//Insert category mapping into document category table
					$insertQuery->insert('#__edocman_document_category')
						->columns('category_id, document_id, is_main_category')
						->values("$categoryId, $row->id, 1");
					$db->setQuery($insertQuery);
					$db->execute();
					$insertQuery->clear();
				}
			}
		}

		return true;
	}

	/**
	 * Clears cache groups. We use it to clear the plugins cache after we update the last run timestamp.
	 *
	 * @param   array $clearGroups  The cache groups to clean
	 * @param   array $cacheClients The cache clients (site, admin) to clean
	 *
	 * @return  void
	 *
	 * @since   1.6.8
	 */
	private function clearCacheGroups(array $clearGroups, array $cacheClients = array(0, 1))
	{
		$conf = JFactory::getConfig();
		foreach ($clearGroups as $group)
		{
			foreach ($cacheClients as $client_id)
			{
				try
				{
					$options = array(
						'defaultgroup' => $group,
						'cachebase'    => ($client_id) ? JPATH_ADMINISTRATOR . '/cache' :
							$conf->get('cache_path', JPATH_SITE . '/cache')
					);
					$cache   = JCache::getInstance('callback', $options);
					$cache->clean();
				}
				catch (Exception $e)
				{
					// Ignore it
				}
			}
		}
	}
}

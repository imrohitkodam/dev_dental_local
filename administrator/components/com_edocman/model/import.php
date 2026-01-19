<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class EDocmanModelImport extends OSModel
{
	/**
	 * Import documents from the selected folder
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	function store($data)
	{
		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$insertQuery = $db->getQuery(true);
		$config      = EdocmanHelper::getConfig();
		$categoryId  = (int) $data['category_id'];
        if($config->access_level_inheritance){
            $query->clear();
            $query->select('accesspicker, access')->from('#__edocman_categories')->where('id = '.$categoryId);
            $db->setQuery($query);
            $categoryAccess = $db->loadObject();
            $categoryGroups = EDocmanHelper::getGroupLevels(0, $categoryId);
			$query->clear();
        }

		$data['folder'] = trim($data['folder']);
		$path           = strlen($data['folder']) ? $config->documents_path . '/' . $data['folder'] : $config->documents_path;
		if ($data['exts'])
		{
			$fileExts = explode(',', $data['exts']);
			for ($i = 0, $n = count($fileExts); $i < $n; $i++)
			{
				$fileExts[$i] = trim($fileExts[$i]);
			}
			$filter = implode('|', $fileExts);
		}
		else
		{
			$filter = '.*';
		}
		$files = JFolder::files($path, $filter);

		if (count($files))
		{
			$query->select('COUNT(id)')
				->from('#__edocman_documents');

			foreach ($files as $file)
			{
				$documentData = array();
				$ext = JFile::getExt($file);
				if ($ext)
				{
					$basename = substr($file, 0, strlen($file) - (strlen($ext) + 1));
				}
				else
				{
					$basename = $file;
				}

				//$documentData['access']            = $data['access'];
                if($config->access_level_inheritance){
                    $documentData['accesspicker']  = $categoryAccess->accesspicker;
                    $documentData['access']        = $categoryAccess->access;
                    $documentData['groups']        = $categoryGroups;
                }else {
                    $documentData['accesspicker']  = $data['accesspicker'];
                    $documentData['access']        = $data['access'];
                    $documentData['groups']        = implode(",",array_filter($data['groups']));
                }
                if($documentData['accesspicker'] == 1){
                    $documentData['access']        = 255;
                }

				$documentData['title']             = $basename;
				$documentData['original_filename'] = $file;
				$documentData['alias']             = '';
				$documentData['published']         = 1;
				if ($data['folder'])
				{
					$documentData['filename'] = $data['folder'] . '/' . $file;
				}
				else
				{
					$documentData['filename'] = $file;
				}

				// Check if the document is existed or not
				$query->where('filename = ' . $db->quote($documentData['filename']));
				$db->setQuery($query);
				$total = (int) $db->loadResult();
				
				// Clear where clause, make it ready to use for next iterator
				$query->clear('where');
				// If document was imported before, ignore it
				if ($total > 0)
				{
					continue;
				}

				$row         = JTable::getInstance('Document', 'EdocmanTable');
				$row->setMainCategory($categoryId);
				$row->bind($documentData);
				EDocmanHelper::prepareDocument($row, $categoryId);
				$row->check();
				$row->store();

                if($documentData['accesspicker'] == 1) {
                    $query->clear();
                    $query->select('count(id)')->from('#__edocman_levels')->where('data_type = 1')->where('item_id = ' . $row->id);
                    $db->setQuery($query);
                    $count      = $db->loadResult();
                    $groups     = $documentData['groups'];
                    if ($count == 0) {
                        $db->setQuery("Insert into #__edocman_levels (id,data_type,item_id,groups) values (NULL,'1',$row->id,'" . $groups . "')");
                        $db->execute();
                    } else {
                        $db->setQuery("Update #__edocman_levels set groups = '" . $groups . "' where data_type = 1 and item_id = $row->id");
                        $db->execute();
                    }
                }

				//Insert category mapping into document category table
				$insertQuery->insert('#__edocman_document_category')
					->columns('category_id, document_id, is_main_category')
					->values("$categoryId, $row->id, 1");
				$db->setQuery($insertQuery);
				$db->execute();
				$insertQuery->clear();				
			}
		}
		return true;
	}
}
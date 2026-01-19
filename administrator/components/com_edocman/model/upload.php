<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// No direct access.
defined('_JEXEC') or die;

class EdocmanModelUpload extends OSModel
{
	public function __construct(array $config = array())
	{
		parent::__construct();
		$this->state->insert('category_id', 'int', 0)
			->insert('published', 'int', 0)
            ->insert('accesspicker','int',0)
            ->insert('groups', 'array', array())
			->insert('access', 'int', 0);
	}

	/**
	 * Store list of uploaded documents
	 *
	 * @param array $data
	 *
	 * @return boolean
	 */
	public function store($data)
	{
		$db             = $this->getDbo();
		$config         = EDocmanHelper::getConfig();
		$query          = $db->getQuery(true);
		$categoryId     = (int) $data['category_id'];
		if($config->access_level_inheritance){
		    $query->clear();
		    $query->select('accesspicker, access')->from('#__edocman_categories')->where('id = '.$categoryId);
		    $db->setQuery($query);
		    $categoryAccess = $db->loadObject();
		    $categoryGroups = EDocmanHelper::getGroupLevels(0, $categoryId);
        }
		$files          = $data['file'];
		$originalFiles  = $data['original_file'];
		$titles         = $data['title'];
		$descriptions   = $data['description'];
        $filesize       = $data['filesize'];
        $fileid         = $data['fileid'];
        //echo $data['accesspicker'];die();
		if (count($files))
		{
			for ($i = 0, $n = count($files); $i < $n; $i++)
			{
				$documentData                      = array();
                if($config->access_level_inheritance){
                    $documentData['accesspicker']  = $categoryAccess->accesspicker;
                    $documentData['access']        = $categoryAccess->access;
                    $documentData['groups']        = $categoryGroups;
                }else {
                    $documentData['accesspicker']  = $data['accesspicker'];
                    $documentData['access']        = $data['access'];
                    $documentData['groups']        = $data['groups'];
                }
                if($documentData['accesspicker'] == 1){
                    $documentData['access']        = 255;
                }
				$documentData['published']         = $data['published'];
				$documentData['title']             = $titles[$i];
				$documentData['original_filename'] = $originalFiles[$i];
				$documentData['filename']          = $files[$i];
				$documentData['alias']             = '';
				$documentData['short_description'] = $documentData['description'] = $descriptions[$i];
                $documentData['file_size']         = $filesize[$i];
				// Check if the document is existed or not
				$sql = 'SELECT COUNT(id) FROM #__edocman_documents WHERE filename="' . $documentData['filename'] . '"';
				$db->setQuery($sql);
				$total = (int) $db->loadResult();
				if ($total > 0)
				{
					continue;
				}
				$row        = JTable::getInstance('Document', 'EdocmanTable');
				$row->bind($documentData);
				$row->setMainCategory($categoryId);
				EDocmanHelper::prepareDocument($row, $categoryId);
				$row->check();
				$row->store();
				$query->insert('#__edocman_document_category')
					->columns('category_id, document_id, is_main_category')
					->values("$categoryId, $row->id, 1");
				$db->setQuery($query);
				$db->execute();
				$query->clear();

                if($documentData['accesspicker'] == 1) {
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

                //for google drive
                if(EDocmanHelper::isGoogleDrivePluginEnabled()) {
                    if ($fileid[$i] != '') {
                        $gdrive = JTable::getInstance('Googledrive', 'EdocmanTable');
                        $gdrive->id = 0;
                        $gdrive->type = 1;
                        $gdrive->element_id = $row->id;
                        $gdrive->cloud_id = $fileid[$i];
                        $gdrive->store();
                    }
                }
			}
		}

		return true;
	}
}
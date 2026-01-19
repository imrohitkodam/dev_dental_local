<?php
/**
 * @version        1.11.3
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2019 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die();

class EDocmanControllerUpload extends EDocmanController
{
	/**
	 * Upload files and store it to the correct folder
	 */
	public function upload()
	{
        $session    = JFactory::getSession();
        $files      = $session->get('files', array());
        $app        = JFactory::getApplication();
		$config     = EDocmanHelper::getConfig();
		$rootDir    = $config->documents_path;
		$categoryId = $this->input->getInt('category_id', 0);
		$file       = $this->input->files->get('file',null,'raw');
		$fileName   = $file['name'];
		$uploadPath = $rootDir;

        //die('{"jsonrpc" : "2.0", "error" : {"code": 500, "message": "THIS IS AN ERROR."}, "id" : "id"}');
        JPluginHelper::importPlugin('edocman');
        $results        = array();
        $results        = $app->triggerEvent('onBeforeDocumentBatchUpload', array($file,count($files)));

		if (($categoryId > 0) && ($config->activate_herachical_folder_structure))
		{
			$db     = JFactory::getDbo();
			$query  = $db->getQuery(true);
			$query->select('`path`')
				  ->from('#__edocman_categories')
				  ->where('id=' . (int) $categoryId);
			$db->setQuery($query);
			$path = $db->loadResult();
			if ($path)
			{
				$uploadPath .= '/' . $path;
			}
		}
		else
		{
            $path = "";
        }

		if (JFile::exists($uploadPath . '/' . $fileName) && !$config->overwrite_existing_file)
		{
			$savedFilename = uniqid('file_') . '_' . $fileName;
		}
		else
		{
			$savedFilename = $fileName;
		}

        // Give plugin a change to upload the file

        JPluginHelper::importPlugin('edocman');
        $results        = array();
        $results        = $app->triggerEvent('onDocumentBatchUpload', array($savedFilename, $file, $path));
        $return         = $results[0][0];

        if ($return != true)
        {
            if (version_compare(JVERSION, '3.4.4', 'ge'))
            {
                JFile::upload($file['tmp_name'], $uploadPath . '/' . $savedFilename, false, true);
                $results[0][1] = @filesize($uploadPath . '/' . $savedFilename);
            }
            else
            {
                JFile::upload($file['tmp_name'], $uploadPath . '/' . $savedFilename);
                $results[0][1] = @filesize($uploadPath . '/' . $savedFilename);
            }
        }

        $filesize        = $session->get('filesize', array());
		$originalFiles   = $session->get('originalFiles', array());
		$files           = (array) $files;
		$originalFiles   = (array) $originalFiles;
		$files[]         = empty($path) ? $savedFilename : $path . '/' . $savedFilename;
		$originalFiles[] = $savedFilename;
        $filesize[]      = $results[0][1];
        //for google drive
        $fileid[]        = $results[0][2];

		$session->set('files', $files);
        $session->set('filesize', $filesize);
		$session->set('originalFiles', $originalFiles);
        $session->set('fileid', $fileid);
	}

	/**
	 * Display form which allows batch editing the uploaded documents
	 */
	public function edit_documents()
	{
		$session        = JFactory::getSession();
		$files          = $session->get('files', array());
		$category_id    = JFactory::getApplication()->input->getInt('category_id',0);
		$itemid         = JFactory::getApplication()->input->getInt('Itemid',0);
		if (!count($files))
		{
			//Redirect back to upload page, display error message
			$this->setRedirect('index.php?option=com_edocman&view=document&layout=edit&catid='.$category_id.'&Itemid='.$itemid, JText::_('EDOCMAN_NO_FILES_UPLOADED'), 'warning');
			return;
		}
		$this->input->set('view', 'document');
		$this->input->set('layout', 'edit_documents');
		$this->input->set('category_id',$category_id);
		$this->input->set('itemid',$itemid);
		$this->input->set('files',$files);
		$this->display();
	}

	/**
	 * Store title, description of the documents which were entered in batch-edit screen
	 */
	public function store_documents()
	{
		$model      = $this->getModel();
		$data       = $this->input->getData();
		$model->store($data);
		$session    = JFactory::getSession();
		$session->clear('files');
		$session->clear('originalFiles');
        $session->clear('filesize');
        $session->clear('fileid');
		$this->setRedirect('index.php?option=com_edocman&view=userdocuments', JText::_('EDOCMAN_DOCUMENT_SUCCESSFULLY_UPLOADED'));
	}

	/**
	 * Cancel Bulk upload action, redirect user to documents management page
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_edocman&view=documents');
	}
}
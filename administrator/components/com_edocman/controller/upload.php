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

class EDocmanControllerUpload extends EDocmanController
{
	/**
	 * Upload files and store it to the correct folder
	 */
	public function upload()
	{
		$config     = EDocmanHelper::getConfig();
		$rootDir    = $config->documents_path;
		$categoryId = $this->input->getInt('category_id', 0);
		$file       = $this->input->files->get('file',null,'raw');
		$fileName   = $file['name'];
		$uploadPath = $rootDir;

		if (($categoryId > 0) and ($config->activate_herachical_folder_structure))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('`path`')
				->from('#__edocman_categories')
				->where('id=' . (int) $categoryId);
			$db->setQuery($query);
			$path = $db->loadResult();
			if ($path)
			{
				$uploadPath .= '/' . $path;
			}
		}else{
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
        $app = JFactory::getApplication();

        JPluginHelper::importPlugin('edocman');
        $results = array();
        $results = $app->triggerEvent('onDocumentBatchUpload', array($savedFilename, $file, $path));
        $return  = $results[0][0];

        if ($return != true) {
            if (version_compare(JVERSION, '3.4.4', 'ge')) {
                JFile::upload($file['tmp_name'], $uploadPath . '/' . $savedFilename, false, true);
                $results[0][1] = @filesize($uploadPath . '/' . $savedFilename);
            } else {
                JFile::upload($file['tmp_name'], $uploadPath . '/' . $savedFilename);
                $results[0][1] = @filesize($uploadPath . '/' . $savedFilename);
            }
        }

		$session         = JFactory::getSession();
		$files           = $session->get('files', array());
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
		$session = JFactory::getSession();
		$files   = $session->get('files', array());
		if (!count($files))
		{
			//Redirect back to upload page, display error message
			$this->setRedirect('index.php?option=com_edocman&view=upload', JText::_('EDOCMAN_NO_FILES_UPLOADED'), 'warning');

			return;
		}
		$this->input->set('view', 'upload');
		$this->input->set('layout', 'edit');
		$this->display();
	}

	/**
	 * Store title, description of the documents which were entered in batch-edit screen
	 */
	public function store_documents()
	{
		$model = $this->getModel();
		$data  = $this->input->getData();
		$model->store($data);
		$session = JFactory::getSession();
		$session->clear('files');
		$session->clear('originalFiles');
        $session->clear('filesize');
        $session->clear('fileid');
		$this->setRedirect('index.php?option=com_edocman&view=documents', JText::_('Documents successfully uploaded'));
	}

	/**
	 * Cancel Bulk upload action, redirect user to documents management page
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_edocman&view=documents');
	}
}
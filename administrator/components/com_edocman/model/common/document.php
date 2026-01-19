<?php
/**
 * @version        1.11.3
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2019 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// No direct access.
defined('_JEXEC') or die();

/**
 * Edocman model.
 */
class EDocmanModelCommonDocument extends OSModelAdmin
{
	/**
	 * Override getdata method
	 *
	 * @see OSModelItem::getData()
	 */
	public function getData()
	{
		$config = EDocmanHelper::getConfig();
		if (empty($this->data))
		{
			$data = parent::getData();
			if ($data->id)
			{
				$db    = $this->getDbo();
				$query = $db->getQuery(true);
				$query->select('category_id, is_main_category')
					->from('#__edocman_document_category')
					->where('document_id=' . (int) $data->id);
				$db->setQuery($query);
				$categories               = $db->loadObjectList();
				$data->extra_category_ids = array();
				$data->category_id        = 0;
				if (count($categories))
				{
					foreach ($categories as $category)
					{
						if ($category->is_main_category)
						{
							$data->category_id = $category->category_id;
						}
						else
						{
							$data->extra_category_ids[] = $category->category_id;
						}
					}
				}
				$data->select_filename = $data->filename;
				if ($data->indicators)
				{
					$data->indicators      = explode(',', $data->indicators);
				}
				else
				{
					$data->indicators = array();
				}
				//Get license title
				if ($data->license_id > 0)
				{
					$query->clear();
					$query->select('title')
						->from('#__edocman_licenses')
						->where('id = '. $data->license_id);
					$db->setQuery($query);
					$data->license_title = $db->loadResult();
				}
			}
			elseif ($this->state->catid)
			{
				$data->category_id = $this->state->catid;
			}
			$this->data = $data;
		}

		if($config->user_group_ids)
		{
			$group_ids = $this->data->owner_group_ids;
			if($group_ids != "")
			{
				$this->data->group_ids = array();
				$group_ids = explode(",",$group_ids);
				foreach($group_ids as $gid){
					$this->data->group_ids[]->value = $gid;
				}
			}
		}

		return $this->data;
	}

	/**
	 * Method to save Document
	 *
	 * @param OSInput $input
	 *
	 * @return bool
	 */
	public function save($input)
	{
		// Initialise variables;
		$app    = JFactory::getApplication();
		$data   = $input->get('jform', array(), 'array');
		$task   = $input->get('task');
		$db     = $this->getDbo();
		$query  = $db->getQuery(true);
		$row    = $this->getTable();
		$id     = (int) $data['id'];
		$isNew  = true;
		$config = EDocmanHelper::getConfig();
		$user   = JFactory::getUser();
		$newupload = false;

		// Validate to make sure a category is selected for docuument
		$categoryId = (int) $data['category_id'];
		if ($categoryId == 0)
		{
			throw new Exception (JText::_('EDOCMAN_CHOOSE_CATEGORY'));
		}
		// Load the row if saving an existing document
		if ($id > 0)
		{
			$row->load($id);
			if ($input->has('del_image') && $row->image) //Delete the original thumb image if user choose to do so
			{
				$documentImagePath      = JPATH_ROOT . '/media/com_edocman/document/';
				$thumbDocumentImagePath = $documentImagePath . 'thumbs/';
				if (JFile::exists($documentImagePath . $row->image))
				{
					JFile::delete($documentImagePath . $row->image);
				}
				if (JFile::exists($thumbDocumentImagePath . $row->image))
				{
					JFile::delete($thumbDocumentImagePath . $row->image);
				}
				$data['image'] = '';
			}
			$oldUserIds = $row->user_ids;
			$isNew      = false;

			//check publish down to update reminder_email
            $publish_down = $row->publish_down;
            if($publish_down != "" && $publish_down != "0000-00-00 00:00:00" && $data['publish_down'] != "")
            {
                if($publish_down != $data['publish_down'])
                {
                    $data['send_reminder'] = 0;
                }
            }
		}
		else
		{
            $newupload  = true;
			$db->setQuery("Select * from #__edocman_categories where id = '$categoryId'");
			$category = $db->loadObject();
			$auto_approval = $category->auto_approval;
			if($auto_approval == 0)
			{
				if ($app->isSite() && $config->require_admin_approve == 0)
				{
					$data['published'] = 1;
					//do nothing
				}
				if ($app->isSite() && $config->require_admin_approve == 1)
				{
					$data['published'] = 0;
				}
			}
			elseif($auto_approval == 1)
            {
				$data['published'] = 1;
			}
			elseif($auto_approval == 2)
            {
				$data['published'] = 0;
			}
			$oldUserIds = '';
		}
		//File upload processing
		$files = $input->files->get('jform', null, 'raw');
		$file  = $files['filename'];
		if ($task != 'save2copy')
		{
			// Processing file upload
			if ($isNew)
			{
				// We need to check to see whether users uploaded / choose a file for this document yet
				if ((int) $config->file_upload_method == 0 || $app->isSite())
				{
					if (!$file['name'] && empty($data['document_url']))
					{
						throw new Exception(JText::_('EDOCMAN_NO_FILE_UPLOADED'));
					}
				}
				else
				{
					if (($data['select_filename'] == '' || $data['select_filename'] == '-1') && empty($data['document_url']))
					{
						throw new Exception(JText::_('EDOCMAN_NO_FILE_UPLOADED'));
					}
				}
			}
		}
		
		if (is_uploaded_file($file['tmp_name']))
		{
		    $newupload        = true;
			$uploadPath       = $config->documents_path;
			$fileName         = $file['name'];
			$fileSize         = $file['size'];
			$maxFileSize      = $config->max_file_size ? $config->max_file_size : 2;
			$maxFileSizeType  = $config->max_filesize_type ? $config->max_filesize_type : 3;
			$maxFileSizeInBye = 2 * 1024 * 1024;
			switch ($maxFileSizeType)
			{
				case 1:
					$maxFileSizeInBye = $maxFileSize;
					break;
				case 2:
					$maxFileSizeInBye = $maxFileSize * 1024;
					break;
				case 3:
					$maxFileSizeInBye = $maxFileSize * 1024 * 1024;
					break;
			}
			if ($maxFileSizeInBye < $fileSize)
			{
				throw new Exception(JText::sprintf('EDOCMAN_FILE_TOO_BIG', $maxFileSizeInBye));
			}
			// Check file extension
			$fileExt          = strtolower(JFile::getExt($fileName));
			$allowedFileTypes = explode(',', $config->allowed_file_types);
			for ($i = 0, $n = count($allowedFileTypes); $i < $n; $i++)
			{
				$allowedFileTypes[$i] = strtolower(trim($allowedFileTypes[$i]));
			}
			if (!in_array($fileExt, $allowedFileTypes))
			{
				throw new Exception(JText::sprintf('EDOCMAN_FILETYPE_NOT_ALLOWED', $config->allowed_file_types));
			}
			// Make filename safe
			$fileName = JFile::makeSafe($fileName);
			$query->clear();
			$query->select('`path`')
				->from('#__edocman_categories')
				->where('id=' . (int) $categoryId);
			$db->setQuery($query);
			$path = $db->loadResult();
			if (($path) and ($config->activate_herachical_folder_structure))
			{
				$uploadPath .= '/' . $path;
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
			$results = array();
            $results = $app->triggerEvent('onBeforeDocumentUploadProgress', array($isNew, $file));
            if (count($results) && (!in_array(true, $results, true)) && ($app->isSite()))
            {
                throw new Exception(JText::_('EDOCMAN_UPLOAD_LIMIT_ERROR'));
            }

            $results = $app->triggerEvent('onDocumentUpload', array($row, $isNew, $file, $path, $fileName, $categoryId));

            if (!in_array(true, $results, true))
            {
                if (version_compare(JVERSION, '3.4.4', 'ge'))
                {
                    JFile::upload($file['tmp_name'], $uploadPath . '/' . $savedFilename, false, true);
                }
                else
                {
                    JFile::upload($file['tmp_name'], $uploadPath . '/' . $savedFilename);
                }
				$data['file_size'] = filesize($uploadPath . '/' . $savedFilename);
            }
            else
            {
				//get file size
				if($isNew)
				{
					$filesize = $app->triggerEvent('onFilesizeUpload', array($row, $isNew , $path, $fileName));
					if($filesize[0] > 0)
					{
						$data['file_size'] = $filesize[0];
					}
				}
			}
			
			if($config->activate_herachical_folder_structure)
			{
				$data['filename']           = empty($path) ? $savedFilename : $path . '/' . $savedFilename;
			}
			else
			{
				$data['filename']           = $savedFilename;
			}
			$data['original_filename']      = $file['name'];
		}
		elseif (isset($data['select_filename']) && $data['select_filename'])
		{
			$data['original_filename']      = $data['filename'] = $data['select_filename'];
		}
		elseif(! isset($data['select_filename']) && ! $data['select_filename'] && ! is_uploaded_file($file['tmp_name']) && ! $isNew && $config->activate_herachical_folder_structure && $id > 0)
        {
            $uploadPath             = $config->documents_path;
            $originalUploadPath     = $uploadPath;
            $old_category           = EDocmanHelper::getDocumentCategory($id);
            $old_category_id        = $old_category->id;
            //update 1.9.10 with new Configure option - move_document_when_changing_category
            if($old_category_id != $categoryId && $config->move_document_when_changing_category == 1)
            {
                $query->clear();
                $query->select('`path`')
                    ->from('#__edocman_categories')
                    ->where('id=' . (int) $categoryId);
                $db->setQuery($query);
                $path = $db->loadResult();
                if (($path) and ($config->activate_herachical_folder_structure))
                {
                    $uploadPath .= '/' . $path;
                }
                $query->clear();
                $query->select('filename,original_filename')->from('#__edocman_documents')->where('id="'.$id.'"');
                $db->setQuery($query);
                $document = $db->loadObject();
                $document_file = $originalUploadPath.'/'.$document->filename;
                //echo $document_file;die();
                if(JFile::exists($document_file))
                {
                    JFile::copy($document_file,$uploadPath.'/'.$document->original_filename);
                    $data['filename'] = $path.'/'.$document->original_filename;
                    JFile::delete($document_file);
                }
            }
        }
		$image = $files['image'];
		if (is_uploaded_file($image['tmp_name']))
		{
			if (!getimagesize($image['tmp_name']))
			{
				throw new Exception (JText::_('COM_EDOCMAN_IMAGE_ERROR'));
			}
			$documentImagePath      = JPATH_ROOT . '/media/com_edocman/document/';
			$thumbDocumentImagePath = $documentImagePath . 'thumbs/';
			if (JFile::exists($documentImagePath . $image['name']))
			{
				$fileName = uniqid('category_') . $image['name'];
			}
			else
			{
				$fileName = $image['name'];
			}
			if (JFile::upload($image['tmp_name'], $documentImagePath . $fileName))
			{
				$width  = $config->document_thumb_width ? $config->document_thumb_width : 100;
				$height = $config->document_thumb_height ? $config->document_thumb_height : 100;
				EDocmanHelper::resizeImage($documentImagePath . $fileName, $thumbDocumentImagePath . $fileName, $width, $height);
				if ($row->image)
				{
					if (JFile::exists($documentImagePath . $row->image))
					{
						JFile::delete($documentImagePath . $row->image);
					}
					if (JFile::exists($thumbDocumentImagePath . $row->image))
					{
						JFile::delete($thumbDocumentImagePath . $row->image);
					}
				}
				$data['image'] = $fileName;
			}
		}
		//Prepare other data
		if (isset($data['indicators']))
		{
			$data['indicators'] = implode(',', $data['indicators']);
		}
		else
		{
			$data['indicators'] = '';
		}
		//inherit from main category
		if($config->access_level_inheritance){
            $query->clear();
            $query->select('`accesspicker`,`access`')
                ->from('#__edocman_categories')
                ->where('id=' . (int) $categoryId);
            $db->setQuery($query);
            $accessObj      = $db->loadObject();
            $accesspicker   = $accessObj->accesspicker;
            $access         = $accessObj->access;
            if($accesspicker == 0){
                $data['access'] = $access;
            }else{
                $data['groups'] = explode(",", EDocmanHelper::getGroupLevels(0,$categoryId));
            }
            $data['accesspicker'] = $accesspicker;
        }

		if(count($data['group_ids']) > 0){
			$group_ids = $data['group_ids'];
			$group_ids = implode(",",$group_ids);
			$data['owner_group_ids'] = $group_ids;
		}else{
		    $data['owner_group_ids'] = "";
        }

		if ($task == 'save2copy')
		{
			//If no file input uploaded, we just use file from original record
			$originalDocument = clone $row;
			$originalDocument->load($input->getInt('id'));

			if (empty($data['filename']))
			{
				$data['filename']          = $originalDocument->filename;
				$data['original_filename'] = $originalDocument->original_filename;
			}

			if (empty($data['image']))
			{
				$data['image'] = $originalDocument->image;
			}
		}
		if($app->isSite() && $isNew && $config->owner_assigned){ //auto assign owner user IDs
			$data['user_ids'] = $user->id;
		}

        //access level
        if($data['accesspicker'] == 1){
            $data['access'] = 255;
        }
        if($newupload && $config->increase_document_version){
		    $document_version = (float)$data['document_version'];
            $document_version = $document_version + 1;
            $data['document_version'] = $document_version;
        }
		$input->set('jform', $data);
		parent::save($input);

        // Give plugin a change to upload the file
        JPluginHelper::importPlugin('edocman');
        $results = $app->triggerEvent('onAfterSaveDocument', array($row, $data, $isNew));

		$id = $input->getInt('id', 0);

        if($id > 0){
            $query = $db->getQuery(true);
            $query->select('count(id)')->from('#__edocman_levels')->where('data_type = 1')->where('item_id = '.$id);
            $db->setQuery($query);
            $count = $db->loadResult();

            $groups = $data['groups'];
            $groups = implode(",",$groups);
            if($count == 0){
                $db->setQuery("Insert into #__edocman_levels (id,data_type,item_id,`groups`) values (NULL,'1',$id,'".$groups."')");
                $db->execute();
            }else{
                $db->setQuery("Update #__edocman_levels set `groups` = '".$groups."' where data_type = 1 and item_id = $id");
                $db->execute();
            }
        }
		//google drive
        if($isNew){
            $session = JFactory::getSession();
            $fileid = $session->get('fileid','');
            $gdrive = JTable::getInstance('Googledrive', 'EdocmanTable');
            $gdrive->id = 0;
            $gdrive->type = 1;
            $gdrive->element_id = $id;
            $gdrive->cloud_id = $fileid;
            $gdrive->store();
            $session->clear('fileid');
        }
		//Store category relationship
		if (!$isNew)
		{
			$query->clear();
			$query->delete('#__edocman_document_category')
				->where('document_id = ' . $id);
			$db->setQuery($query);
			$db->execute();
		}
		$categoryId = $data['category_id'];
		$query->clear();
		$query->insert('#__edocman_document_category')
			->columns('category_id, document_id, is_main_category')
			->values("$categoryId, $id, 1");
		$db->setQuery($query);
		$db->execute();
		if (isset($data['extra_category_ids']))
		{
			$categoryIds = $data['extra_category_ids'];
			$categoryIds = array_diff($categoryIds, array($categoryId));
			if (count($categoryIds))
			{
				foreach ($categoryIds as $categoryId)
				{
					$query->clear();
					$query->insert('#__edocman_document_category')
						->columns('category_id, document_id, is_main_category')
						->values("$categoryId, $id, 0");
					$db->setQuery($query);
					$db->execute();
				}
			}
		}

		//locked status
		if($config->lock_function){
			$query->clear();
			if(($data['is_locked'] == 1) && ($data['old_locked_status'] == 0)){
				$query->clear();
				$fields =  array(
					'`locked_by` = '.$user->id,
					'`locked_time` = "'.date("Y-m-d H:i:s").'"'
					);
				$query->update($db->quoteName('#__edocman_documents'))->set($fields)->where('id = "'.$id.'"');
				$db->setQuery($query);
				$db->execute();
			}
		}

		$query->clear();
		$query->delete('#__edocman_document_tags')->where('document_id=' . $id);
		$db->setQuery($query);
		$db->execute();
		if ($data['tags'])
		{
			$rowTag = JTable::getInstance('Tag', 'EDocmanTable');
			$tags   = explode(',', $data['tags']);
			foreach ($tags as $tag)
			{
				$tag = trim($tag);
				if ($tag)
				{
					$query->clear();
					$query->select('id')
						->from('#__edocman_tags')
						->where("tag=" . $db->quote($tag));
					$db->setQuery($query);
					$tagId = $db->loadResult();
					if (!$tagId)
					{
						$rowTag->id  = 0;
						$rowTag->tag = $tag;
						$rowTag->store();
						$tagId = $rowTag->id;
					}
					// save new data docuent tags
					$query->clear();
					$query->insert('#__edocman_document_tags')
						->columns('document_id, tag_id')
						->values("$id, $tagId");
					$db->setQuery($query);
					$db->execute();
				}
			}
		}

        //reset download log
        //echo $config->reset_downloadlog;die();
        if($config->reset_downloadlog){
            $query->clear();
            $query->delete('#__edocman_statistics')->where('document_id="'.$row->id.'"');
            $db->setQuery($query);
            $db->execute();

            $query->clear();
            $query->update('#__edocman_documents')->set('downloads="0"')->where('id="'.$row->id.'"');
            $db->setQuery($query);
            $db->execute();
        }

		//Reload the document object
		$row->load($id);
		if ($isNew && $config->upload_notification && $app->isSite())
		{
			EDocmanHelper::sendUploadNotificationEmail($row, $config, (int) $data['category_id']);
		}
		if (strlen(trim($row->user_ids)) && $config->document_assigned_notification)
		{
			if ($isNew || ($row->user_ids != $oldUserIds))
			{
				EDocmanHelper::sendDocumentAssignedEmails($row, $oldUserIds);
			}
		}
	}


	/**
	 *
	 * Prepare the table data, before saving record to database
	 *
	 * @param JTable $row
	 * @param string $task
	 * @param array  $data
	 */
	protected function prepareTable($row, $task, $data = array())
	{
		$row->setMainCategory((int) $data['category_id']);
		if (!$row->id)
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('MAX(ordering)')
				->from('#__edocman_documents AS a')
				->innerJoin('#__edocman_document_category AS b ON a.id = b.document_id')
				->where('b.category_id = ' . (int) $data['category_id']);
			$db->setQuery($query);
			$row->ordering = 1 + (int) $db->loadResult();
		}

		parent::prepareTable($row, $task, $data);
	}

	/**
	 * Generate new title and alias when copy a document
	 *
	 * @see OSModelAdmin::generateNewTitle()
	 */
	protected function generateNewTitle($row, $alias, $title)
	{
		$db         = $this->getDbo();
		$query      = $db->getQuery(true);
		$categoryId = $row->getMainCategoryId();
		while (true)
		{
			// Check to see whether there are any documetns in same category has this alias
			$query->select('COUNT(*)')
				->from('#__edocman_documents AS a')
				->innerJoin('#__edocman_document_category AS b ON a.id = b.document_id')
				->where('b.is_main_category = 1')
				->where('a.alias=' . $db->quote($alias))
				->where('b.category_id=' . $categoryId);
			$db->setQuery($query);
			$total = $db->loadResult();
			if ($total)
			{
				$title = JString::increment($title);
				$alias = JString::increment($alias, 'dash');
				$query->clear();
			}
			else
			{
				break;
			}
		}

		return array($title, $alias);
	}

	/**
	 * Get document detail
	 *
	 * @return object
	 */
	function canDownload($recordId)
	{
		$db     = $this->getDbo();
		$user   = JFactory::getUser();
		$userId = $user->get('id');
		$query  = $db->getQuery(true);
		if(JFactory::getApplication()->isAdmin()){
			$query->select('*')
				->from('#__edocman_documents')
				->where('id=' . (int) $recordId);
		}else{
			$query->select('*')
				->from('#__edocman_documents')
				->where('id=' . (int) $recordId)
				->where('published=1');
		}
		$db->setQuery($query);
		$document = $db->loadObject();

		if (!$document)
		{
			return false;
		}
        if ($user->authorise('core.admin', 'com_edocman'))
        {
            return true;
        }

		$jinput = JFactory::getApplication()->input;
		$p = $jinput->getInt('p',0);
		if($p == 1){
			return true;
		}
		
        //if (!in_array($document->access, $user->getAuthorisedViewLevels()))
		//{
			//return false;
		//}

		if (!in_array($document->access, $user->getAuthorisedViewLevels())  && $document->accesspicker == 0)
		{
			return false;
		}
		elseif($document->accesspicker == 1)
		{
			$exists = 0;
			$usergroup          = $user->groups;
			if(count($usergroup) > 0){
				foreach ($usergroup as $group){
					$query->clear();
					$query->select('count(id)')->from('#__edocman_levels')->where('data_type = 1')->where('(`groups`="'.$group.'" OR `groups` LIKE "'.$group.'%" OR `groups` LIKE "%,'.$group.',%" OR `groups` LIKE "%,'.$group.'")')->where('item_id = "'.$document->id.'"');
					$db->setQuery($query);
					$count = $db->loadResult();
					if($count > 0){
						$exists = 1;
					}
				}
			}

			if($exists == 0){
				return false;
			}
		}

		$query->clear();
		$query->select('*,a.id as catid')
			->from('#__edocman_categories AS a')
			->innerJoin('#__edocman_document_category AS b ON (a.id = b.category_id AND b.is_main_category = 1)')
			->where('b.document_id = ' . $document->id);
		$db->setQuery($query);
		$category = $db->loadObject();
		if (!$category)
		{
			return false;
		}

		if($category->accesspicker == 0) {
			if (!in_array($category->access, $user->getAuthorisedViewLevels())) {
				return false;
			}
		}else{
			$exists = 0;
			$usergroup          = $user->groups;
			if(count($usergroup) > 0){
				foreach ($usergroup as $group){
					$query->clear();
					$query->select('count(id)')->from('#__edocman_levels')->where('data_type = 0')->where('(`groups`="'.$group.'" OR `groups` LIKE "'.$group.'%" OR `groups` LIKE "%,'.$group.',%" OR `groups` LIKE "%,'.$group.'")')->where('item_id = "'.$category->catid.'"');
					$db->setQuery($query);
					$count = $db->loadResult();
					if($count > 0){
						$exists = 1;
					}
				}
			}
			if($exists == 0){
				return false;
			}
		}

		// Prevents bots from downloading files
		if (!empty($_SERVER['HTTP_USER_AGENT']) and preg_match('~(bot|crawl)~i', $_SERVER['HTTP_USER_AGENT']))
		{
			return false;
		}
		// Check if the user has download permission
		$assetName = 'com_edocman.document.' . $recordId;
		if (($document->user_ids == "" &&
				($user->authorise('edocman.download', $assetName) || $user->authorise('core.edit', $assetName) || ($userId == $document->created_user_id))) ||
			($document->user_ids && in_array($userId, explode(',', $document->user_ids))) || ($document->created_user_id == $userId)
		)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Download for viewing
	 *
	 * @ $documentID int ID of Document
	 *
	**/
	static function forcedownload($documentId){
		$config		= EDocmanHelper::getConfig();
		$app		= JFactory::getApplication();
		$db			= JFactory::getDbo();
		$sql		= 'SELECT * FROM #__edocman_documents WHERE id=' . $documentId;
		$db->setQuery($sql);
		$table		= $db->loadObject();
		$rootDir	= $config->documents_path;
		$filePath	= $rootDir . '/' . $table->filename;
		while (@ob_end_clean()) ;
		EDocmanHelper::processDownload($filePath, $table->filename, $table->original_filename,  true, $documentId);
		$app->close(0);
	}

	/**
	 * Process downloading document
	 *
	 * @param int  $documentId ID of the document which will be downloaded
	 *
	 * @param bool $download   true if the document will be downloaded, false if the document is viewed
	 *
	 * @param bool $logDownload  if true, will log the data and send noification....
	 *
	 */
	function download($documentId, $download = true, $logDownload = true)
	{
        $logId              = 0;
        $user               = JFactory::getUser();
        $db                 = JFactory::getDbo();
		$session            = JFactory::getSession();
		$send_notify        = $session->get('send_notify',1);
		$app                = JFactory::getApplication();
        $jinput             = $app->input;
        $pass_check         = $jinput->getInt('p',0);
		$sql                = 'SELECT * FROM #__edocman_documents WHERE id=' . $documentId;
		$db->setQuery($sql);
		$table              = $db->loadObject();
		$config             = EDocmanHelper::getConfig();
		
		if($config->onetime_collect){
			$name           = $session->get('name');
			$email          = $session->get('email');
			if($name == ""){
				$name       = $user->name;
			}
			if($email == ""){
				$email      = $user->email;
			}
		}else{
			$session->set('name','');
			$session->set('email','');
		}

		$received_email     = $email;
        if($pass_check == 0) {
            if ($app->isSite() && $config->download_log) {
                // Logs
                if ($config->download_log) {
                    jimport('joomla.environment.browser');
					$config             = EDocmanHelper::getConfig();
                    $browser            = JBrowser::getInstance();
                    $row                = $this->getTable('statistic', 'EDocmanTable');
                    $row->document_id   = $documentId;
                    $row->user_id       = $user->get('id');
					$row->name          = $name;
					$row->email         = $email;
                    $row->user_ip       = @$_SERVER['REMOTE_ADDR'];
                    $row->download_time = gmdate('Y-m-d H:i:s');
                    $row->browser       = $browser->getBrowser();
                    $row->os            = $browser->getPlatform();
					$download_code      = JUserHelper::genRandomPassword(20);
                    $row->download_code = $download_code;
                    $now                = JFactory::getDate()->toSql();
                    //solve the duplicate case
                    $db->setQuery("Select count(id) from #__edocman_statistics where `document_id` = '$documentId' and `user_id` = '".$user->get('id')."' and `name` like '$name' and email like '$email' and `browser` like '".$browser->getBrowser()."' and `os` like '".$browser->getPlatform()."' and user_ip like '".$row->user_ip."' and TIMESTAMPDIFF(SECOND,download_time,'$now') < 3");
                    $alreadyLog = $db->loadResult();
                    if($alreadyLog == 0)
                    {
                        $row->store();
                        $logId = $db->insertid();
                    }
                }else{
					$download_code      = JUserHelper::genRandomPassword(20);
				}
                // Send notification email
                if ($config->download_notification) {
                    $jconfig            = new JConfig();
                    $subject            = $config->download_email_subject;
                    $body               = nl2br($config->download_email_body);

                    if ($user->id) {
                        $username       = $user->username;
                        $name           = $user->name;
                        $email          = $user->email;
					} elseif($name != '' && $email != ''){
						$username       = $name;
                    } else {
                        $username       = JText::_('EDOCMAN_GUEST');
                        $name           = JText::_('EDOCMAN_GUEST');
                        $email          = JText::_('EDOCMAN_GUEST');
                    }
                    $userIp = @$_SERVER['REMOTE_ADDR'];
                    $documentTitle = $table->title;
                    $body               = str_replace('[USERNAME]', $username, $body);
                    $body               = str_replace('[NAME]', $name, $body);
                    $body               = str_replace('[USER_IP]', $userIp, $body);
                    $body               = str_replace('[DOCUMENT_TITLE]', $documentTitle, $body);
					$subject            = str_replace('[DOCUMENT_TITLE]', $documentTitle, $subject);
                    $body               = str_replace('[EMAIL]', $email, $body);
                    $notificationEmails = trim($config->notification_emails);
                    if (strlen($notificationEmails) < 5) {
                        $notificationEmails = $jconfig->mailfrom;
                    }
                    $notificationEmails = explode(',', $notificationEmails);
                    $mailer             = JFactory::getMailer();
                    for ($i = 0, $n = count($notificationEmails); $i < $n; $i++) {
                        $email          = trim($notificationEmails[$i]);
                        if ($email && $send_notify == 1) {
                            $mailer->sendMail($jconfig->mailfrom, $jconfig->fromname, $email, $subject, $body, 1);
                            $mailer->ClearAllRecipients();
                        }
                    }
                }
            }
        }

        if($pass_check == 0){
            if ($table->document_url)
            {
                $app->redirect($table->document_url);
            }
            elseif($config->download_type == 1 && $jinput->getString('download_code','') == "")
            {
                $user               = JFactory::getUser();
                $fromName           = JFactory::getConfig()->get('fromname');
                $fromEmail          = JFactory::getConfig()->get('mailfrom');
                $username           = $user->username;
                $name               = $user->name;
				if($received_email  == "" && $user->id > 0){
					$received_email = $user->email;
				}
                $userIp             = @$_SERVER['REMOTE_ADDR'];
                $downloadUrl        = JUri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')) . JRoute::_('index.php?option=com_edocman&task=document.download&id=' . $documentId . '&download_code=' . $download_code, false);
                $downloadLink       = '<a href="' . $downloadUrl . '">' . JText::_('EDOCMAN_CLICK_TO_DOWNLOAD') . '</a>';
                $subject            = $config->download_link_email_subject;
                $body               = nl2br($config->download_link_email_body);
                $subject            = str_replace('[DOCUMENT_TITLE]', $documentTitle, $subject);
                $body               = str_replace('[USERNAME]', $username, $body);
                $body               = str_replace('[NAME]', $name, $body);
                $body               = str_replace('[USER_IP]', $userIp, $body);
                $body               = str_replace('[DOCUMENT_TITLE]', $documentTitle, $body);
				$subject		    = str_replace('[DOCUMENT_TITLE]', $documentTitle, $subject);
                $body               = str_replace('[EMAIL]', $received_email, $body);
                $body               = str_replace('[DOWNLOAD_LINK]', $downloadLink, $body);
                $mailer             = JFactory::getMailer();
                $mailer->ClearAllRecipients();
                $mailer->sendMail($fromEmail, $fromName, $received_email, $subject, $body, 1);
				$app->enqueueMessage(JText::_('EDOCMAN_DOWNLOAD_LINK_HAS_BEEN_SENT'));
                $app->redirect($_SERVER['HTTP_REFERER']);
            }
            else
            {
                // Give plugin a change to download the file
                JPluginHelper::importPlugin('edocman');
                $results = $app->triggerEvent('onDocumentDownload', array($table));
                if (in_array(true, $results, true) || count($results) == 0)
                {
                    $sql = 'UPDATE #__edocman_documents SET downloads=downloads + 1 WHERE id=' . $documentId;
                    $db->setQuery($sql);
                    $db->execute();
                    $rootDir = $config->documents_path;
                    $filePath = $rootDir . '/' . $table->filename;
                    if(!EDocmanHelper::isAmazonS3TurnedOn() && !EDocmanHelper::isDropBoxTurnedOn() && !EDocmanHelper::isGdriveTurnedOn())
                    {
                        if (!file_exists($filePath))
                        {
                            throw new \Exception(JText::_('EDOCMAN_PHYSICAL_FILE_NOT_FOUND'), 404);
                        }
                    }
                    while (@ob_end_clean()) ;
                    EDocmanHelper::processDownload($filePath, $table->filename, $table->original_filename, $download, $documentId);
                    $app->close(0);
                }
                else
                {
                    if ($app->isSite() && $config->download_log && $logId > 0)
                    {
                        $db->setQuery("Delete from #__edocman_statistics where id = '$logId'");
                        $db->execute();
                    }
                }
            }
        }
        else
        {   //pass_check == 1
            if ($table->document_url)
            {
                $app->redirect($table->document_url);
            }
            else
            {
                // Give plugin a change to download the file
                JPluginHelper::importPlugin('edocman');
                $results = $app->triggerEvent('onDocumentDownload', array($table));
                if (in_array(true, $results, true) || count($results) == 0)
                {
                    $sql = 'UPDATE #__edocman_documents SET downloads=downloads + 1 WHERE id=' . $documentId;
                    $db->setQuery($sql);
                    $db->execute();
                    $rootDir = $config->documents_path;
                    $filePath = $rootDir . '/' . $table->filename;
                    while (@ob_end_clean()) ;
                    EDocmanHelper::processDownload($filePath, $table->filename, $table->original_filename, $download, $documentId);
                    $app->close(0);
                }
                else
                {
                    if ($app->isSite() && $config->download_log && $logId > 0)
                    {
                        $db->setQuery("Delete from #__edocman_statistics where id = '$logId'");
                        $db->execute();
                    }
                }
            }
        }
	}

	/**
	 * Can delete the document or not
	 *
	 * @param int $record
	 *
	 * @return boolean
	 */
	protected function canDelete($record)
	{
		if ($record->asset_id == 0)
		{
			return true;
		}
		if ($record->id)
		{
			return JFactory::getUser()->authorise('core.delete', 'com_edocman.document.' . (int) $record->id);
		}
	}

	/**
	 * Can edit the status of the document or not
	 *
	 * @see OSModelForm::canEditState()
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();
		if (!empty($record->category_id))
		{
			$categoryId = $record->category_id;
		}
		else
		{
			$categoryId = $this->state->catid;
		}

		if (!empty($record->id))
		{
			return $user->authorise('core.edit.state', 'com_edocman.document.' . $record->id);
		}
		elseif ($categoryId)
		{
			return $user->authorise('core.edit.state', 'com_edocman.category.' . $categoryId);
		}
		else
		{
			return parent::canEditState($record);
		}
	}

	protected function getReorderConditions($table)
	{
		$conditions = array();
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('category_id')
			->from('#__edocman_document_category')
			->where('document_id = '. $table->id)
			->where('is_main_category = 1');
		$db->setQuery($query);
		$categoryId = $db->loadResult();
		if ($categoryId > 0)
		{
			$conditions[] = " id IN (SELECT document_id FROM #__edocman_document_category WHERE category_id=$categoryId AND is_main_category=1)";
		}

		return $conditions;
	}

	/**
	 * Save document, using for editor xtd
	 *
	 * @param $input
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function saveDocument($input)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$config = EDocmanHelper::getConfig();
		$data = $input->getData();
		$file = $input->files->get('filename');
		$categoryId = $input->getInt('category_id', 0);
		if ($categoryId == 0)
		{
			throw new Exception (JText::_('EDOCMAN_CHOOSE_CATEGORY'));
		}
		// Upload file first
		if (is_uploaded_file($file['tmp_name']))
		{
			$rootDir          = $config->documents_path;
			$fileName         = $file['name'];
			$fileSize         = $file['size'];
			$maxFileSize      = $config->max_file_size ? $config->max_file_size : 2;
			$maxFileSizeType  = $config->max_filesize_type ? $config->max_filesize_type : 3;
			$maxFileSizeInBye = 2 * 1024 * 1024;
			switch ($maxFileSizeType)
			{
				case 1:
					$maxFileSizeInBye = $maxFileSize;
					break;
				case 2:
					$maxFileSizeInBye = $maxFileSize * 1024;
					break;
				case 3:
					$maxFileSizeInBye = $maxFileSize * 1024 * 1024;
					break;
			}
			if ($maxFileSizeInBye < $fileSize)
			{
				throw new Exception(JText::sprintf('EDOCMAN_FILE_TOO_BIG', $maxFileSizeInBye));
			}
			// Check file extension
			$fileExt          = strtolower(JFile::getExt($fileName));
			$allowedFileTypes = explode(',', $config->allowed_file_types);
			for ($i = 0, $n = count($allowedFileTypes); $i < $n; $i++)
			{
				$allowedFileTypes[$i] = strtolower(trim($allowedFileTypes[$i]));
			}
			if (!in_array($fileExt, $allowedFileTypes))
			{
				throw new Exception(JText::sprintf('EDOCMAN_FILETYPE_NOT_ALLOWED', $config->allowed_file_types));
			}
			// Make filename safe
			$fileName = JFile::makeSafe($fileName);
			$query->clear();
			$query->select('`path`')
				->from('#__edocman_categories')
				->where('id=' . (int) $categoryId);
			$db->setQuery($query);
			$path = $db->loadResult();
			if (($path) and ($config->activate_herachical_folder_structure))
			{
				$savedFilePath = $rootDir . '/' . $path . '/' . $fileName;
			}
			else
			{
				$savedFilePath = $rootDir . '/' . $fileName;
			}
			if (JFile::exists($savedFilePath) && !$config->overwrite_existing_file)
			{
				$savedFilename = uniqid('file_') . '_' . $fileName;
			}
			else
			{
				$savedFilename = $fileName;
			}
			if (($path) and ($config->activate_herachical_folder_structure))
			{
				JFile::upload($file['tmp_name'], $rootDir . '/' . $path . '/' . $savedFilename);
			}
			else
			{
				JFile::upload($file['tmp_name'], $rootDir . '/' . $savedFilename);
			}
			if (($path) and ($config->activate_herachical_folder_structure))
			{
				$data['filename'] = $path . '/' . $savedFilename;
			}
			else
			{
				$data['filename'] = $savedFilename;
			}
			$data['original_filename'] = $file['name'];
		}
		else
		{
			throw new Exception(JText::_('EDOCMAN_NO_FILE_UPLOADED'));
		}

		$row    = $this->getTable();
		$row->bind($data);
		EDocmanHelper::prepareDocument($row, $categoryId);
		$row->store();
		$input->set('id', $row->id);

		$query->clear();
		//Insert category mapping into document category table
		$query->insert('#__edocman_document_category')
			->columns('category_id, document_id, is_main_category')
			->values("$categoryId, $row->id, 1");
		$db->setQuery($query);
		$db->execute();

        //reset download log
        //echo $config->reset_downloadlog;die();
        if($config->reset_downloadlog){
            $query->clear();
            $query->delete('#__edocman_statistics')->where('document_id="'.$row->id.'"');
            $db->setQuery($query);
            $db->execute();

            $query->clear();
            $query->update('#__edocman_documents')->set('downloads="0"')->where('id="'.$row->id.'"');
            $db->setQuery($query);
            $db->execute();
        }

		return true;
	}
}
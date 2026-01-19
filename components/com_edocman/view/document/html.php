<?php
/**
 * @version         1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011-2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class EDocmanViewDocumentHtml extends OSViewHtml
{
	function display()
	{
		jimport('joomla.filesystem.file');
		// Load dependencies class
		require_once JPATH_ROOT . '/components/com_edocman/helper/file.class.php';

		//Handle upload/delete document in a separate method
		if ($this->getLayout() == 'edit')
		{
			$this->_displayForm();

			return;
		}
        elseif($this->getLayout() == "edit_documents")
        {
            $this->_displayEditDocumentsForm();

            return;
        }
		$app      = JFactory::getApplication();
		$document = JFactory::getDocument();
		$user     = JFactory::getUser();
		$db       = JFactory::getDbo();
		$query    = $db->getQuery(true);
		$config   = EDocmanHelper::getConfig();
		$userId   = $user->get('id', 0);
		$model    = $this->getModel();
		$state    = $model->getState();
		$id       = $state->id;
		// Check document access
		if (!EDocmanHelper::canAccessDocument($id))
		{
			if (!$userId)
			{
				$returnUrl = base64_encode(JRoute::_(EDocmanHelperRoute::getDocumentRoute($id, $state->catid, $this->Itemid)));
				$app->enqueueMessage(JText::_('EDOCMAN_LOGIN_TO_ACCESS'));
				$app->redirect('index.php?option=com_users&view=login&return=' . $returnUrl);
			}
			else
			{
				$url = EDocmanHelper::getViewUrl(array('categories', 'category', 'document'));
				if (!$url)
				{
					$url = JRoute::_('index.php?option=com_edocman&Itemid=' . $this->Itemid);
				}
				$app->enqueueMessage(JText::_('EDOCMAN_INVALID_DOCUMENT'), 'error');
				$app->redirect($url);
			}
		}

		// Make sure a valid document ID is passed in URL
		$item = $model->getData();
		if (!$item->id || !$item->published)
		{
			$url = EDocmanHelper::getViewUrl(array('categories', 'category', 'document'));
			if (!$url)
			{
				$url = JRoute::_('index.php?option=com_edocman&Itemid=' . $this->Itemid);
			}
			$app->enqueueMessage(JText::_('EDOCMAN_INVALID_DOCUMENT'), 'error');
			$app->redirect($url);
		}

		// Handle hits
		$model->hitCounter();

		// Handle breadcrumb
		$categoryId = $state->catid;
		if (!$categoryId)
		{
			$query->clear();
			$query->select('category_id')
				->from('#__edocman_document_category AS a')
				->where('document_id=' . (int) $id)
				->where('is_main_category=1');
			$db->setQuery($query);
			$categoryId = (int) $db->loadResult();
		}
		$menuItem = $app->getMenu()->getActive();
		if ($menuItem)
		{
			if (isset($menuItem->query['view']) && ($menuItem->query['view'] == 'categories' || $menuItem->query['view'] == 'category'))
			{
				$parentId = (int) $menuItem->query['id'];
				if ($categoryId)
				{
					$pathway = $app->getPathway();
					$paths   = EDocmanHelper::getCategoriesBreadcrumb($categoryId, $parentId);
					for ($i = count($paths) - 1; $i >= 0; $i--)
					{
						$category = $paths[$i];
						$pathUrl  = EDocmanHelperRoute::getCategoryRoute($category->id, $this->Itemid);
						$pathway->addItem($category->title, $pathUrl);
					}
					$pathway->addItem($item->title);
				}
			}
		}
		if (strlen(trim(strip_tags($item->description))) == 0)
		{
			$item->description = $item->short_description;
		}
		if (empty($item->document_url))
		{
			$item->data		= new EDocman_File($item->id, $item->filename, $config->documents_path);
		}
		$metaKey			= $item->metakey;
		$metaDescription	= $item->metadesc;
		if ($metaKey)
		{
			$document->setMetaData('keywords', $metaKey);
		}
		if ($metaDescription)
		{
			$document->setMetaData('description', $metaDescription);
			$document->addCustomTag( '<meta property="og:description" content="'.$metaDescription.'" />' );
		}
		if ($item->image && JFile::exists(JPATH_ROOT.'/media/com_edocman/document/thumbs/'.$item->image))
		{
			$imgSrc = JUri::base().'media/com_edocman/document/'.$item->image ;
			$document->addCustomTag('<link rel="image_src" href="'.$imgSrc.'" />');
			$document->addCustomTag('<meta property="og:image" content="'.$imgSrc.'" />');
		}

		$document->setTitle($item->title . ' - ' . JFactory::getApplication()->getCfg('sitename'));
		if ($config->process_plugin)
		{
			$item->description = JHtml::_('content.prepare', $item->description);
		}		
		if ($state->tmpl == 'component')
		{
			$showTaskBar	= false;
		}
		else
		{
			$showTaskBar	= true;
		}

		//check link
		$active_item_id		= $this->input->getInt('Itemid');
		$document_itemid	= EDocmanHelperRoute::getDocumentMenuId($item->id);
		if(($document_itemid > 0) && ($document_itemid != $active_item_id)){
			$canonicallink = JUri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')).JRoute::_('index.php?option=com_edocman&view=document&id='.$item->id.'&Itemid='.$document_itemid);
			$document->addCustomTag('<link rel="canonical" content="'.$canonicallink.'" />');
		}

        $item->canView = EDocmanHelper::canView($item);

		if (count($item->params))
		{
			$this->fields = $this->model->getForm()->getGroup('params');
		}
		// Set new indicator
		if ($config->day_for_new > 0)
		{
			EDocmanHelper::setNewIndicator(array($item), (int) $config->day_for_new);
		}
		// Set new indicator
		if ($config->day_for_update > 0)
		{
			EDocmanHelper::setUpdateIndicator(array($item), (int) $config->day_for_update);
		}
		if ($config->use_default_license){
            $query->clear();
			$query->select('id');
			$query->from('#__edocman_licenses');
			$query->where('published=1 and default_license=1');
			$db->setQuery($query);
			$this->default_license = (int)$db->loadResult();
		}

		if($item->license_id > 0){
			$this->default_license = $item->license_id;
		}

		if($this->default_license > 0){
			$query->clear();
			$query->select('*')->from('#__edocman_licenses')->where('id = "'.$this->default_license.'"');
			$db->setQuery($query);
			$this->license = $db->loadObject();
		}

        if($config->show_related_documents){
            JLoader::register('EDocmanModelList', JPATH_ROOT . '/components/com_edocman/model/list.php');
		    $documentsModel    = OSModel::getInstance('List','EDocmanModel');
		    if($config->related_documents_in_same_cat){
		        $documentsModel->set('filter_category_id',$categoryId);
            }
            if($config->related_documents_in_author){
		        $documentsModel->set('filter_created_user',$item->created_user_id);
            }
            $documentsModel->set('filter_related_documents',1);
            $documentsModel->set('document_id',$item->id);
            $documentsModel->set('limit',($config->number_related_documents > 0) ? $config->number_related_documents:6);
		    $documentsModel->set('filter_order','rand()');
		    $documentsModel->set('filter_order_Dir','');
		    $related_items      = $documentsModel->getData();
        }

        JPluginHelper::importPlugin('edocman');
        $plugins = JFactory::getApplication()->triggerEvent('onDocumentDisplay', array($item));

		$this->item        = $item;
		$this->config      = $config;
		$this->showTaskBar = $showTaskBar;
		$this->userId      = $userId;
		$this->viewLevels  = $user->getAuthorisedViewLevels();
		$this->categoryId  = $categoryId;
		$this->category	   = EdocmanHelper::getCategory($categoryId);
		$this->related_items = $related_items;
		$this->plugins     = $plugins;
		$this->bootstrapHelper = new EDocmanHelperBootstrap($config->twitter_bootstrap_version);

		parent::display();
	}

	/**
	 * Display form allow submitting/editing document
	 */
	function _displayForm()
	{
		$app = JFactory::getApplication();
        $db    = JFactory::getDbo();
		// Permission checking
		$user = JFactory::getUser();
		if (!$user->id)
		{
			// Redirect user to login page
			$returnUrl = JRoute::_('index.php?option=com_edocman&view=document&layout=edit&Itemid=' . $this->Itemid, false);
			$app->enqueueMessage(JText::_('EDOCMAN_LOGIN_TO_UPLOAD'));
			$app->redirect(JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($returnUrl)));
		}
		$id = $this->model->getState()->id;
		if (!$id)
		{
			$catId = $this->model->getState()->catid;
			if ($catId)
			{
				$ret   = $user->authorise('core.create', 'com_edocman.category.' . $catId);
				$query = $db->getQuery(true);
				$query->select('title')
					->from('#__edocman_categories')
					->where('id=' . $catId);
				$db->setQuery($query);
				$this->categoryTitle = $db->loadResult();
			}
			else
			{
				$ret = $user->authorise('core.create', 'com_edocman');
			}
			if (!$ret)
			{
				$app->enqueueMessage(JText::_('EDOCMAN_DO_NOT_HAVE_UPLOAD_PERMISSION'), 'error');
				$url = EDocmanHelper::getViewUrl(array('categories', 'category', 'document'));
				if (!$url)
				{
					$url = JRoute::_('index.php?option=com_edocman&Itemid=' . $this->input->getInt('Itemid'));
				}
				$app->redirect($url);
			}

			//limit upload
            JPluginHelper::importPlugin('edocman');
			$results = array();
			$results[] = true;
            $results = $app->triggerEvent('onBeforeUploadDocument', array());
            if (in_array(false, $results, true))
            {
                $url = EDocmanHelper::getViewUrl(array('categories', 'category', 'document'));
                if (!$url)
                {
                    $url = JRoute::_('index.php?option=com_edocman&Itemid=' . $this->input->getInt('Itemid'));
                }
                $app->redirect($url);
            }
		}

		JFactory::getDocument()->addStyleSheet(JUri::base(true) . '/components/com_edocman/assets/css/form.css');
		$this->params = JFactory::getApplication()->getParams();
		$this->state  = $this->model->getState();
		$this->item   = $this->model->getData();
		$this->form   = $this->model->getForm();
		$this->canDo  = EDocmanHelper::getActions('document', $this->state);
		$this->config = EDocmanHelper::getConfig();
		$this->catId  = $catId;

        $header_text  = $this->config->header_text;
        $header_text  = str_replace("[CATEGORY]",$this->categoryTitle,$header_text);
        $this->header_text = $header_text;

		if ($this->config->use_default_license){
			$this->default_license = EdocmanHelper::getDefaultLicense();
		}

		if($this->item->license_id > 0){
			$this->default_license = $this->item->license_id;
		}

        switch ($this->config->use_simple_upload_form)
        {
            case "2":
                if($id > 0)
                {
                    $this->setLayout('edit');
                }
                else
                {
                    EDocmanHelperJquery::upload();
                    $maxFilesize = $this->config->max_file_size ? (int)$this->config->max_file_size : 2;
                    $maxFilesizeType = $this->config->max_filesize_type ? (int)$this->config->max_filesize_type : 3;
                    if ($maxFilesizeType == 1) {
                        $maxFilesize = ceil($maxFilesize / (1024 * 1024));
                    } elseif ($maxFilesizeType == 2) {
                        $maxFilesize = ceil($maxFilesize / 1024);
                    }
                    if (!$maxFilesize) {
                        $maxFilesize = 2;
                    }
                    $allowedFiletypes = $this->config->allowed_file_types;
                    if (!$allowedFiletypes) {
                        $allowedFiletypes = 'doc, docx, ppt, pptx, pdf, zip, rar, png, zipx';
                    }
                    $allowedFiletypes = explode(',', $allowedFiletypes);
                    $allowedFiletypes = array_map('trim', $allowedFiletypes);
                    $this->maxFilesize = $maxFilesize;
                    $this->allowedFiletypes = implode(',', $allowedFiletypes);
                    $this->setLayout('ajax');
                }
                break;
            case "1":
                $this->setLayout('simple');
                break;
        }

		$this->bootstrapHelper = new EDocmanHelperBootstrap($this->config->twitter_bootstrap_version);

		parent::display();
	}

    /**
     * This function is used to edit documents uploaded through Ajax mode
     */
	function _displayEditDocumentsForm()
    {
        $session                = JFactory::getSession();
        $config                 = EDocmanHelper::getConfig();
        $files                  = $session->get('files', array());
        $filesize               = $session->get('filesize', array());
        $originalFiles          = $session->get('originalFiles', array());
        $fileid                 = $session->get('fileid', array());
        $form                   = JFactory::getApplication()->input->get('jform',array(),'array');
        $category_id            = $form['category_id'];
        $this->category_id      = $category_id;
        $this->files            = $files;
        $this->filesize         = $filesize;
        $this->originalFiles    = $originalFiles;
        $this->fileid           = $fileid;
        $this->bootstrapHelper = new EDocmanHelperBootstrap($config->twitter_bootstrap_version);
        parent::display();
    }
}
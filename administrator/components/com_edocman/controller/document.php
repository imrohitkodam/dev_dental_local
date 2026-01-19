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

class EdocmanControllerDocument extends EDocmanController
{

	protected function allowAdd($data = array())
	{
		$user = JFactory::getUser();

		if (isset($data['category_id']))
		{
			$categoryId = (int) $data['category_id'];
		}
		else
		{
			$categoryId = 0;
		}

		if ($categoryId)
		{
			return $user->authorise('core.create', 'com_edocman.category.' . $categoryId);
		}
		else
		{
			return $user->authorise('core.create', 'com_edocman') || count(EDocmanHelper::getAuthorisedCategories('core.create'));
		}
	}
	/**
	 * Method to check whether edit action is allowed for the given document
	 *
	 * @see OSControllerAdmin::allowEdit()
	 */
	protected function allowEdit($data = array())
	{
		// Initialise variables.
		$id   = (int) $data['id'];
		$user = JFactory::getUser();
		// Check general edit permission first.
		if ($user->authorise('core.edit', 'com_edocman.document.' . $id))
		{
			return true;
		}
		// Fallback on edit.own.
		if ($user->authorise('core.edit.own', 'com_edocman.document.' . $id))
		{
			$item = $this->getModel()->getData();

			return $item->created_user_id == $user->id;
		}

		// Since there is no asset tracking, fallback to the component permissions.
		return parent::allowEdit($data);
	}

	/**
	 * Method to download file associated with selected document
	 */
	public function download()
	{
		$id    = $this->input->getInt('id', 0);
		$model = $this->getModel('Document');
		if ($model->canDownload($id))
		{
			$model->download($id);
		}
		else
		{
			$this->app->enqueueMessage(JText::_('EDOCMAN_NOT_ALLOWED_ACTION'));
			$this->app->redirect('index.php?option=com_edocman&view=' . $this->defaultView);
		}
	}

	/**
	 * Save document from Ajax request
	 */
	public function saveDocument()
	{
		$response = array();
		try
		{
			$model = $this->getModel('Document');
			$model->saveDocument($this->input);
			$response['success'] = 1;
			$response['id'] = $this->input->getInt('id', 0);
			$response['title'] = $this->input->getString('title');
			?>
			<script type="text/javascript">
				if (window.parent)
				{
					window.parent.jSelectEdocman(<?php echo $response['id'];?>, "<?php echo $response['title']; ?>");
				}
			</script>
		<?php
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage());
			$this->input->set('view', 'documents');
			$this->input->set('layout', 'modal');
			$this->input->set('tmpl', 'component');
			$this->input->set('choose_document_option', 1);
			$this->display();
		}
	}

	function indexcontent(){
		$id    = $this->input->getInt('id', 0);
		$db    = JFactory::getDbo();
		$row   = &JTable::getInstance('Document', 'EDocmanTable');
		if($id > 0){
			$row->load($id);
			$config = EDocmanHelper::getConfig() ;	
			if (file_exists($config->documents_path.'/'.$row->filename)) {
				jimport('joomla.filesystem.file');
				$ext = strtolower(JFile::getExt($row->filename)) ;
				if ($ext == 'pdf' || $ext == 'doc') 
				{
					require_once JPATH_ROOT.'/plugins/edocman/indexer/adapter.php' ;
					$content = IndexerAdapter::getText($config->documents_path.'/'.$row->filename);		
					$row->indexed_content = $content ;
					$row->store();
				}
			}	
		}
		$this->app->enqueueMessage(JText::_('EDOCMAN_DOCUMENT_HAS_BEEN_INDEXED'));
		$this->app->redirect('index.php?option=com_edocman&view=documents' );
	}

    /**
     * This function is used to move main category of documents
     */
    function movingcategory(){
        $config                     = EDocmanHelper::getConfig();
        $access_level_inheritance   = $config->access_level_inheritance;
        $moving_category_id         = $this->input->getInt('moving_category_id',0);
        $cid                        = $this->input->get('cid',array(),'array');
        $db                         = JFactory::getDbo();
        if(count($cid)){
            foreach($cid as $id){
                $query      = $db->getQuery(true);
                $query->select("count(id)")->from('#__edocman_document_category')->where('document_id="'.$id.'" and is_main_category = "1"');
                $db->setQuery($query);
                $count = $db->loadResult();

                if($count) {
                    $query = $db->getQuery(true);
                    $query->update('#__edocman_document_category')->set('category_id = "' . $moving_category_id . '"')->where('document_id="' . $id . '" and is_main_category = "1"');
                    $db->setQuery($query);
                    $db->execute();
                }else{
                    $query = $db->getQuery(true);
                    $columns = array('id', 'document_id', 'category_id', 'is_main_category');
                    $values  = array('NULL',$id,$moving_category_id,'1');
                    $query->insert("#__edocman_document_category")->columns($columns)->values(implode(',',$values));
                    $db->setQuery($query);
                    $db->execute();
                }

                $query      = $db->getQuery(true);
                $query->delete('#__edocman_document_category')->where('category_id = "'.$moving_category_id.'" and document_id="'.$id.'" and is_main_category = "0"');
                $db->setQuery($query);
                $db->execute();

                $query->clear();
                $query->select('asset_id')->from('#__edocman_categories')->where("id = '$moving_category_id'");
                $db->setQuery($query);
                $asset_id = $db->loadResult();

                if ($access_level_inheritance == 1) {
                    $query->clear();
                    // Fields to update.
                    $fields = array(
                        $db->quoteName('asset_id') . ' = ' . $asset_id
                    );

                    // Conditions for which records should be updated.
                    $conditions = array(
                        $db->quoteName('id') . ' = '.$id
                    );
                    $query->update('#__edocman_documents')->set($fields)->where($conditions);
                    $db->setQuery($query);
                    $db->execute();
                }
            }
        }
		$this->app->enqueueMessage(JText::_('EDOCMAN_MOVING_CATEGORY_COMPLETED'));
        $this->app->redirect('index.php?option=com_edocman&view=documents');
    }

	function movingcategory1(){
        $config                     = EDocmanHelper::getConfig();
        $access_level_inheritance   = $config->access_level_inheritance;
        $moving_category_id         = $this->input->getInt('moving_category_id1',0);
        $cid                        = $this->input->get('cid', array(),'array');
        $db                         = JFactory::getDbo();
        if((count($cid) && ($moving_category_id > 0))){
            foreach($cid as $id){
                $query      = $db->getQuery(true);

				$query->select("count(id)")->from('#__edocman_document_category')->where('document_id="'.$id.'" and category_id = "' . $moving_category_id . '" and is_main_category = "1"');
                $db->setQuery($query);
                $count1 = $db->loadResult();


				$query->clear();
                $query->select("count(id)")->from('#__edocman_document_category')->where('document_id="'.$id.'" and is_main_category <> "1"');
                $db->setQuery($query);
                $count = $db->loadResult();

				if($count1 == 0){//only process if this category isn't main category of document
					if($count) {
						$query = $db->getQuery(true);
						$query->update('#__edocman_document_category')->set('category_id = "' . $moving_category_id . '"')->where('document_id="' . $id . '" and is_main_category = "0"');
						$db->setQuery($query);
						$db->execute();
					}else{
						$query = $db->getQuery(true);
						$columns = array('id', 'document_id', 'category_id', 'is_main_category');
						$values  = array('NULL',$id,$moving_category_id,'0');
						$query->insert("#__edocman_document_category")->columns($columns)->values(implode(',',$values));
						$db->setQuery($query);
						$db->execute();
					}
				}
            }
        }
		$this->app->enqueueMessage(JText::_('EDOCMAN_MOVING_CATEGORY_COMPLETED'));
        $this->app->redirect('index.php?option=com_edocman&view=documents');
    }

    /**
     * Remove Orphan Documents
     */
    function removeorphan(){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id')->from('#__edocman_documents')->where('id not in (Select document_id from #__edocman_document_category)');
        $db->setQuery($query);
        $rows = $db->loadColumn(0);
        $document = JTable::getInstance('Document','EDocmanTable');
        if(count($rows) > 0){
            foreach($rows as $row){
                $document->delete($row);
            }
        }
        $query->clear();
        $query->select('id')->from('#__edocman_documents')->where('id in (Select document_id from #__edocman_document_category where category_id not in (Select id from #__edocman_categories))');
        $db->setQuery($query);
        $rows = $db->loadColumn(0);
        $document = JTable::getInstance('Document','EDocmanTable');
        if(count($rows) > 0){
            foreach($rows as $row){
                $document->delete($row);
            }
        }
		$this->app->enqueueMessage(JText::_('EDOCMAN_ORPHAN_DOCUMENTS_HAVE_BEEN_REMOVED_SUCCESSFULLY'));
        $this->app->redirect('index.php?option=com_edocman');
    }

    /**
     * Batch function
     */
    public function batch($model = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Set the model
        $model = $this->getModel('Document');
        // Preset the redirect
        $this->setRedirect('index.php?option=com_edocman&view=documents');

        return parent::batch($model);
    }

    public function copydocument()
    {

    }
}
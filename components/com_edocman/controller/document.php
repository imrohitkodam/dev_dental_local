<?php
/**
 * @version		   1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011-2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// No direct access.
defined('_JEXEC') or die();

class EDocmanControllerDocument extends EDocmanController
{

	/**
	 * Contructor
	 *
	 * @param OSInput $input
	 * @param array   $config
	 */
	public function __construct(OSInput $input = null, array $config = array())
	{
		parent::__construct($input, $config);
		$this->registerTask('viewdoc', 'viewDocument');
	}

	/**
	 * Publish/unpublish the selected document
	 *
	 * @see OSControllerAdmin::publish()
	 */
	public function publish()
	{
		parent::publish();
		$Itemid     = $this->input->getInt('Itemid');
		$categoryId = $this->input->getInt('category_id', 0);
		if ($categoryId)
		{
			$url = JRoute::_(EDocmanHelperRoute::getCategoryRoute($categoryId, $Itemid), false);
		}
		else
		{
			$url = EDocmanHelper::getViewUrl(array('categories', 'category', 'document'));
			if (!$url)
			{
				$url = JRoute::_('index.php?option=com_edocman&Itemid=' . $Itemid);
			}
		}
		$this->setRedirect($url);
	}

	/**
	 * Delete the selected document
	 *
	 * @see OSModelAdmin::delete()
	 */
	function delete()
	{
		parent::delete();
		$Itemid     = $this->input->getInt('Itemid');
		$categoryId = $this->input->getInt('category_id', 0);
		$manageItemId = EDocmanHelperRoute::findView('userdocuments', 0);
		if ($manageItemId)
		{
			$url = JRoute::_('index.php?option=com_edocman&view=userdocuments&Itemid='.$manageItemId);
		}
		else
		{
			if ($categoryId)
			{
				$url = JRoute::_(EDocmanHelperRoute::getCategoryRoute($categoryId, $Itemid), false);
			}
			else
			{
				$url = EDocmanHelper::getViewUrl(array('categories', 'category', 'document'));
				if (!$url)
				{
					$url = JRoute::_('index.php?option=com_edocman&Itemid=' . $Itemid);
				}
			}
		}

		$this->setRedirect($url);
	}

	/**
	 * Cancel add/editing document
	 *
	 * @see OSControllerAdmin::cancel()
	 */
	function cancel()
	{
		$Itemid = $this->input->getInt('Itemid');
		$data   = $this->input->post->get('jform', array(), 'array');
		$id     = $this->input->getInt('id');
		if ($id)
		{
			$model = $this->getModel();
			$model->checkin($id);
		}
		$manageItemId = EDocmanHelperRoute::findView('userdocuments', 0);
		if ($manageItemId)
		{
			$url = JRoute::_('index.php?option=com_edocman&view=userdocuments&Itemid='.$manageItemId);
		}
		else
		{
			$categoryId = isset($data['category_id']) ? (int) $data['category_id'] : 0;

			if ($categoryId)
			{
				$url = JRoute::_(EDocmanHelperRoute::getCategoryRoute($categoryId, $Itemid));
			}
			else
			{
				$url = EDocmanHelper::getViewUrl(array('categories', 'category', 'document'));
				if (!$url)
				{
					$url = JRoute::_('index.php?option=com_edocman&Itemid=' . $Itemid);
				}
			}
		}
		$this->setRedirect($url);
		return true;
	}

	/**
	 *Process download/view the selected document
	 *
	 */
	public function download()
	{
		$session	   = JFactory::getSession();
		$session->set('send_notify',1);
		$name		   = $session->get('name','');
		$email		   = $session->get('email','');
		$model         = $this->getModel();
		$task          = $this->getTask();
		$userId        = JFactory::getUser()->get('id');
		$config        = EDocmanHelper::getConfig();
		$logDownload   = true;
		$forceDownload = $task == 'download' ? true : false;
		$id            = $this->input->getInt('id');
		$downloadCode  = $this->input->getString('download_code');
		$document      = JFactory::getDocument();
		$document->setMetaData( 'robots', 'noindex, nofollow' );
		if (($downloadCode) && (!$userId))
		{
			$db          = JFactory::getDbo();
			$query       = $db->getQuery(true);
			$currentDate = JHtml::_('date', 'Now', 'Y-m-d H:i:s');
			$query->select('document_id')
				->select("DATEDIFF(download_time, '$currentDate') AS number_days")
				->from('#__edocman_statistics')
				->where('download_code = ' . $db->quote($downloadCode));
			$db->setQuery($query);
			$document = $db->loadObject();
			if (!$document)
			{
				$this->app->enqueueMessage(JText::_('EDOCMAN_INVALID_DOWNLOAD_CODE'), 'error');
				$this->setRedirect('index.php');
			}

			if ($document->number_days > 3)
			{
				$this->app->enqueueMessage(JText::_('EDOCMAN_DOWNLOAD_LINK_EXPIRED'), 'error');
				$this->setRedirect('index.php');
			}

			$id            = $document->document_id;
			$logDownload   = false;
			$forceDownload = true;
		}
		elseif ($config->collect_downloader_information && !$userId && ($name == '' || $email == ''))
		{
			$this->app->enqueueMessage(JText::_('EDOCMAN_DIRECT_ACCESS_DOWNLOAD_IS_NOT_ALLOWED'), 'error');
			$this->setRedirect('index.php');
		}

		// Everything is OK, process download
		if ($model->canDownload($id))
		{
			$model->download($id, $forceDownload, $logDownload);
		}
		else
		{
			$user = JFactory::getUser();
			if ($user->id)
			{
				$url = EDocmanHelper::getViewUrl(array('categories', 'category', 'document'));
				if (!$url)
				{
					$Itemid = $this->input->getInt('Itemid', 0);
					$url    = JRoute::_('index.php?option=com_edocman&Itemid=' . $Itemid);
				}
				$this->app->enqueueMessage(JText::_('EDOCMAN_NOT_ALLOWED_ACTION'), 'error');
				$this->setRedirect($url);
			}
			else
				// Give not logged in user a chance to login to download
			{
				$return = base64_encode(JUri::getInstance()->toString());
				$url    = 'index.php?option=com_users&view=login&return=' . $return;
				if ($task == 'download')
				{
					$msg = JText::_('EDOCMAN_LOGIN_TO_VIEW_DOWNLOAD');
				}
				else
				{
					$msg = JText::_('EDOCMAN_LOGIN_TO_VIEW_DOCUMENT');
				}
				$this->setRedirect($url, $msg);
			}
		}
	}
	
	
	public function forcedownload(){
		$id			   = $this->input->getInt('id');
		$model         = $this->getModel();
		$model->forcedownload($id);
	}

	/**
	 *Process download/view the selected document
	 *
	 */
	public function viewDocument()
	{
		$session	   = JFactory::getSession();
		$session->set('send_notify',0);
		$model         = $this->getModel();
		$task          = $this->getTask();
		$userId        = JFactory::getUser()->get('id');
		$config        = EDocmanHelper::getConfig();
		$id            = $this->input->getInt('id');
		$db			   = JFactory::getDbo();
		$db->setQuery("Select view_url,published from #__edocman_documents where id = '$id'");
		$document = $db->loadObject();
		$view_url = $document->view_url;
		$published = $document->published;
		if($published == 0){
			return false;
		}

		if(($config->view_url) and ($view_url != "")){
			$this->setRedirect($view_url);
		}else{

			// Everything is OK, process download
			if($config->use_googleviewer && $config->showing_document_googleviewer){
				$link = JUri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')).JRoute::_('index.php?option=com_edocman&task=document.forcedownload&id='.$id.'&Itemid=9999');
				Jfactory::getApplication()->redirect('http://docs.google.com/viewer?url='.$link);
			}
			elseif ($model->canDownload($id))
			{
				if($config->use_googleviewer){
					$link = JUri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')).JRoute::_('index.php?option=com_edocman&task=document.download&p=1&id='.$id);
					Jfactory::getApplication()->redirect('http://docs.google.com/viewer?url='.$link);
				}else{
					$model->download($id, false, false);
				}
			}
			else
			{
				$user = JFactory::getUser();
				if ($user->id)
				{
					$url = EDocmanHelper::getViewUrl(array('categories', 'category', 'document'));
					if (!$url)
					{
						$Itemid = $this->input->getInt('Itemid', 0);
						$url    = JRoute::_('index.php?option=com_edocman&Itemid=' . $Itemid);
					}
					$this->app->enqueueMessage(JText::_('EDOCMAN_NOT_ALLOWED_ACTION'), 'error');
					$this->setRedirect($url);
				}
				else
					// Give not logged in user a chance to login to download
				{
					$return = base64_encode(JUri::getInstance()->toString());
					$url    = 'index.php?option=com_users&view=login&return=' . $return;
					if ($task == 'download')
					{
						$msg = JText::_('EDOCMAN_LOGIN_TO_VIEW_DONNLOAD');
					}
					else
					{
						$msg = JText::_('EDOCMAN_LOGIN_TO_VIEW_DOCUMENT');
					}
					$this->setRedirect($url, $msg);
				}
			}
		}
	}

	/**
	 * Edit a document
	 * @see OSControllerAdmin::edit()
	 */
	public function edit()
	{
		$this->input->set('layout', 'edit');
		parent::edit();
	}

	/**
	 * Override save function to handle redirect
	 * @see OSModelAdmin::save()
	 */
	public function save()
	{
		$ret        = parent::save();
		$Itemid     = $this->input->getInt('Itemid');
		$data       = $this->input->get('jform', array(), 'array');
		$categoryId = (int) $data['category_id'];
		if ($ret)
		{
			$manageItemId = EDocmanHelperRoute::findView('userdocuments', 0);
			if ($manageItemId)
			{
				$url = JRoute::_('index.php?option=com_edocman&view=userdocuments&Itemid='.$manageItemId);
			}
			else
			{
				$url = JRoute::_(EDocmanHelperRoute::getCategoryRoute($categoryId, $Itemid));
			}
		}
		else
		{
			$recordId = (int) $this->getModel()->getState()->id;
			$url      = JRoute::_('index.php?option=com_edocman&view=document&layout=edit&id=' . $recordId . ($categoryId ? '&catid=' . $categoryId : '') . 'Itemid=' . $Itemid);
		}
		$this->setRedirect($url);

		return $ret;
	}

	/**
	 * Method to check to see whether this user can add new document
	 *
	 * @see OSControllerAdmin::allowAdd()
	 */
	protected function allowAdd($data = array())
	{
		$user = JFactory::getUser();
		if (isset($data['category_id']))
		{
			$categoryId = (int) $data['category_id'];
		}
		else
		{
			$categoryId = (int) $this->input->getInt('catid', 0);
		}
		if ($categoryId)
		{
			return $user->authorise('core.create', 'com_edocman.category.' . $categoryId);
		}
		else
		{
			return parent::allowAdd();
		}
	}

	/**
	 * Method to check whether the current user can perform edit action on the document
	 *
	 * @see OSControllerAdmin::allowEdit()
	 */
	protected function allowEdit($data = array())
	{
		// Initialise variables.
		$id     = (int) isset($data['id']) ? $data['id'] : 0;
		$user   = JFactory::getUser();
		$userId = $user->get('id');

		// Check general edit permission first.
		if ($user->authorise('core.edit', 'com_edocman.document.' . $id))
		{
			return true;
		}

		// Fallback on edit.own.
		if ($user->authorise('core.edit.own', 'com_edocman.document.' . $id))
		{
			// Now test the owner is the user.
			$ownerId = (int) isset($data['created_user_id']) ? $data['created_user_id'] : 0;
			if (empty($ownerId) && $id)
			{
				// Need to do a lookup from the model.
				$record = $this->getModel()->getData();
				if (empty($record))
				{
					return false;
				}
				$ownerId = $record->created_user_id;
			}

			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId)
			{
				return true;
			}
		}

		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data);
	}

	/**
	 * Method to check whether the current user is allowed to delete a record
	 *
	 * @param int id Record ID
	 *
	 * @return boolean True if allowed to delete the record. Defaults to the permission for the component.
	 *
	 */
	protected function allowDelete($id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('created_user_id')->from('#__edocman_documents')->where('id="'.$id.'"');
		$db->setQuery($query);
		$created_user_id = (int) $db->loadResult();
		$canDeleteOwn	= JFactory::getUser()->authorise('core.edit.own',		'com_edocman.document.'.$id) && $created_user_id == JFactory::getUser()->id;
		$canedit = JFactory::getUser()->authorise('core.delete', 'com_edocman.document.' . $id);
		return $canDeleteOwn || $canedit;
	}

	/**
	 * Method to check whether the current user can change status (publish, unpublish of a record)
	 *
	 * @param int $id Id of the record
	 *
	 * @return boolean True if allowed to change the state of the record. Defaults to the permission for the component.
	 *
	 */
	protected function allowEditState($id)
	{
		return JFactory::getUser()->authorise('core.edit.state', 'com_edocman.document.' . $id);
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

	/**
		Share document
	**/
	public function share_document(){
		$db				= JFactory::getDbo();
		$config			= EDocmanHelper::getConfig();
		$id				= $this->input->getInt('document_id');
		$name			= $this->input->getString('name','');
		$friend_name	= $this->input->getString('friend_name','');
		$friend_email	= $this->input->getString('friend_email','');
		$message		= $this->input->getString('message','');

		$query = $db->getQuery(true);
		$query->select("title")->from("#__edocman_documents")->where("id = '".$id."'");
		$db->setQuery($query);
		$document_title = $db->loadResult();
		$document_link	= JUri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')).Jroute::_('index.php?option=com_edocman&view=document&id='.$id);
		$document_link  = "<a href='".$document_link."' target='_blank'>".$document_link."</a>";

		$fromName       = JFactory::getConfig()->get('fromname');
		$fromEmail      = JFactory::getConfig()->get('mailfrom');

		$document_share_email_subject = $config->document_share_email_subject;
		$document_share_email_content = nl2br($config->document_share_email_content);
		
		$document_share_email_content = str_replace('[FRIEND_NAME]', $friend_name, $document_share_email_content);
		$document_share_email_content = str_replace('[NAME]', $name, $document_share_email_content);
		$document_share_email_content = str_replace('[DOCUMENT_TITLE]', $document_title, $document_share_email_content);
		$document_share_email_content = str_replace('[LINK]', $document_link, $document_share_email_content);
		$document_share_email_content = str_replace('[MESSAGE]', $message, $document_share_email_content);

		$mailer         = JFactory::getMailer();
		$mailer->ClearAllRecipients();
		if($mailer->sendMail($fromEmail, $fromName, $friend_email, $document_share_email_subject, $document_share_email_content, 1)){
			echo JText::_('EDOCMAN_DOCUMENT_SHARE_SUCCESSFULLY');
		}
		$this->app->close();
	}

	/**
	 * Store downloader information and send
	 */
	public function store_download()
	{
		$id     = $this->input->getInt('document_id');
		$data   = $this->input->getData();
		$model  = $this->getModel();
		if ($model->canDownload($id))
		{
			$message = $model->storeDownload($id, $data);
			echo $message;
			$this->app->close();
		}
		else
		{
			$url = EDocmanHelper::getViewUrl(array('categories', 'category', 'document'));
			if (!$url)
			{
				$Itemid = $this->input->getInt('Itemid', 0);
				$url    = JRoute::_('index.php?option=com_edocman&Itemid=' . $Itemid);
			}
			$this->app->enqueueMessage(JText::_('EDOCMAN_NOT_ALLOWED_ACTION'), 'error');
			$this->setRedirect($url);
		}
	}

    /**
     * This function is used to add document into current user's bookmark list
     */
	public function bookmark()
    {
        $id         = $this->input->getInt('id');
        $return     = array();
        if(!isset($_COOKIE['bookmark']))
        {
            $bookmarkArr = array();
            $bookmarkArr[0] = $id;
            setcookie('bookmark', implode(",",$bookmarkArr), time() + (86400 * 15), "/"); //15 days
            $return['result'] = JText::_('EDOCMAN_DOCUMENT_HAS_BEEN_ADDED_INTO_BOOKMARK_LIST');
        }
        else
        {
            $bookmarkArr = $_COOKIE['bookmark'];
            $bookmarkArr = explode(",",$bookmarkArr);
            if(!in_array($id, $bookmarkArr))
            {
                $bookmarkArr[count($bookmarkArr)] = $id;
                setcookie('bookmark', implode(",",$bookmarkArr), time() + (86400 * 15), "/");
                $return['result'] = JText::_('EDOCMAN_DOCUMENT_HAS_BEEN_ADDED_INTO_BOOKMARK_LIST');
            }
            else
            {
                $return['result'] = JText::_('EDOCMAN_DOCUMENT_IS_ALREADY_IN_BOOKMARK_LIST');
            }
        }
        //returns data as JSON format
        echo json_encode($return);
        exit(0);
    }

    /**
     * This function is used to remove item in documents bookmark list
     */
    public function removebookmark()
    {
        $id          = $this->input->get('cid',array(),'array');
        $id          = $id[0];
        $itemId      = $this->input->getInt('Itemid');
        $bookmarkArr = $_COOKIE['bookmark'];
        $bookmarkArr = explode(",", $bookmarkArr);
        if(in_array($id, $bookmarkArr))
        {
            $key = array_search($id, $bookmarkArr);
            unset($bookmarkArr[$key]);
            setcookie('bookmark', implode(",",$bookmarkArr), time() + (86400 * 15), "/");
        }
        $app         = JFactory::getApplication();
        $app->enqueueMessage(JText::_('EDOCMAN_DOCUMENT_HAS_BEEN_REMOVED_OUT_OF_BOOKMARK_LIST'));
        $app->redirect(JRoute::_('index.php?option=com_edocman&view=bookmark&Itemid='.$itemId));
    }
}
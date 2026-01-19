<?php
/**
 * @version         1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die();

class EDocmanController extends OSControllerAdmin
{

	/**
	 * Method to display a view.
	 *
	 * @param    boolean $cachable  If true, the view output will be cached
	 * @param    array   $urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return    OSControllerAdmin        This object to support chaining.
	 */
	public function display($cachable = false, array $urlparams = array())
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet(JUri::base(true) . '/components/com_edocman/assets/css/style.css');
        $document->addStyleSheet(JUri::base(true) . '/components/com_edocman/assets/css/font.css');
		$customCss = JPATH_ROOT . '/components/com_edocman/assets/css/custom.css';
		if (file_exists($customCss) && filesize($customCss))
		{
			$document->addStyleSheet(JUri::base(true) . '/components/com_edocman/assets/css/custom.css');
		}
		$config = EDocmanHelper::getConfig();
		if ($config->load_twitter_bootstrap !== '0')
		{
			EDocmanHelper::loadBootstrap(false);
		}

		//Load jquery here
		if ($config->load_jquery !== '0')
		{
			EDocmanHelper::loadJQuery();
		}
		parent::display($cachable, $urlparams);

		return $this;
	}

	/***
	 * Get search parameters from search module and performing redirect
	 */
	public function search()
	{
		$categoryId		= $this->input->getInt('filter_category_id', 0);
		$search			= $this->input->getString('filter_search', '');
		$layout			= $this->input->getCmd('layout', '');
		$Itemid			= $this->input->getInt('Itemid', 0);
		$show_category	= $this->input->getInt('show_category',0);
		$fileType		= $this->input->get('fileType',null,'array');

		$url = 'index.php?option=com_edocman&view=search';
		if ($categoryId)
		{
			$url .= '&filter_category_id=' . $categoryId;
		}


		if ($search)
		{
			$url .= '&filter_search=' . $search;
		}

		if ($layout && ($layout != 'default'))
		{
			$url .= '&layout=' . $layout;
		}

		$url .= '&show_category='. $show_category;

		if(count($fileType) > 0){
			$url .= '&filter_filetype='.implode("-",$fileType);
		}

		$url .= '&Itemid=' . $Itemid;


		$this->app->redirect(JRoute::_($url, false, 0));
	}

    public function login()
    {
        $jinput = $this->app->input;
        $mainframe = & JFactory::getApplication('site');
        if ($return = $jinput->get('return', '' , 'base64'))
        {
            $return = base64_decode($return);
            if (!JURI::isInternal($return))
            {
                $return = '';
            }
        }
        $options = array();
        $options['return'] = $return;
        $credentials = array();
        $credentials['username'] = $jinput->get('username','');
        $credentials['password'] = $jinput->get('password','','RAW');
        //preform the login action
        $error =  $mainframe->login($credentials, $options);
        if(!$error)
        {
            echo JText::_('EDOCMAN_LOGIN_FAIL');
        }
        $this->app->close();
    }

    public function unsubscribe()
    {
	    $jinput = $this->app->input;
	    $email = $jinput->getString('email','');
	    if($email != '')
	    {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('count(id)')->from('#__edocman_unsubscribe_emails')->where('email like "'.$email.'"');
            $db->setQuery($query);
            $count = $db->loadResult();
            if($count > 0)
            {
                JFactory::getApplication()->enqueueMessage(JText::_('EDOCMAN_YOU_ALREADY_UNSUBSCRIBE_OF_MAILING_LIST'));
            }
            else
            {
                $query->clear();
                $query->clear();
                $values = array('NULL', $db->quote($email));
                $query->insert($db->quoteName('#__edocman_unsubscribe_emails'))->columns(array('id','email'))->values(implode(',',$values));
                $db->setQuery($query);
                $db->execute();
                JFactory::getApplication()->enqueueMessage(JText::_('EDOCMAN_YOUR_EMAIL_HAS_JUST_BEEN_UNSUBSCRIBED'));
            }
        }
        return;
    }
}
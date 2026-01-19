<?php
/**
 * @version        1.11.3
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2019 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');

class plgEDocmanLimitUpload extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

    /**
     * On Document Download
     * @param $row
     */
	public function onBeforeUploadDocument()
    {
        //only apply for Front-end side
        if(!JFactory::getApplication()->isSite()){
            return true;
        }
        include_once JPATH_ROOT.'/components/com_edocman/helper/helper.php';
        $user       = JFactory::getUser();

        $max_files  = $this->params->get('max_files',0);
        $max_size   = $this->params->get('max_size',0);
        $user_groups= $this->params->get('user_groups',array());

        //check if user is logged
        if($user->id > 0)
        {
            $usergroups = $user->groups;
            if(count($user_groups) > 0)
            {
                $in_group = false;
                foreach ($usergroups as $group)
                {
                    if(in_array($group, $user_groups))
                    {
                        $in_group = true;
                    }
                }
            }
            else
            {
                $in_group = true;
            }
            if($in_group)
            {
                if($max_files > 0)
                {
                    $total_files = EDocmanHelper::getTotalUploadFile();
                    if ($total_files >= $max_files)
                    {
                        JFactory::getApplication()->enqueueMessage(JText::_('EDOCMAN_UPLOAD_LIMIT_ERROR'), 'error');
                        return false;
                    }
                }

                if($max_size > 0)
                {
                    $max_size = $max_size*1024*1024;
                    $total_size = EDocmanHelper::getCurrentUserUploadedSize();
                    if ($total_size >= $max_size)
                    {
                        JFactory::getApplication()->enqueueMessage(JText::_('EDOCMAN_UPLOAD_LIMIT_ERROR'), 'error');
                        return false;
                    }
                }

            }
        }
        else
        {
            return false;
        }
        return true;
    }

    /**
     * This function is used to check limit size uploaded
     * @param $row
     * @param $isNew
     * @param $file
     * @param $path
     * @param $fileName
     * @param $categoryId
     */
    public function onBeforeDocumentUploadProgress($isNew, $file)
    {
        //only apply for Front-end side
        if(!JFactory::getApplication()->isSite()){
            return true;
        }
        if(!$isNew)
        {
            return true;
        }
        include_once JPATH_ROOT.'/components/com_edocman/helper/helper.php';
        $user       = JFactory::getUser();
        $max_size   = $this->params->get('max_size',0);
        $user_groups= $this->params->get('user_groups',array());

        //check if user is logged
        if($user->id > 0)
        {
            $usergroups = $user->groups;
            if(count($user_groups) > 0)
            {
                $in_group = false;
                foreach ($usergroups as $group)
                {
                    if(in_array($group, $user_groups))
                    {
                        $in_group = true;
                    }
                }
            }
            else
            {
                $in_group = true;
            }
            if($in_group)
            {
                if($max_size > 0)
                {
                    $max_size = $max_size*1024*1024;
                    $total_size = EDocmanHelper::getCurrentUserUploadedSize();
                    $total_size += $file['size'];
                    if ($total_size >= $max_size)
                    {
                        //JFactory::getApplication()->enqueueMessage(JText::_('EDOCMAN_UPLOAD_LIMIT_ERROR'), 'error');
                        return false;
                    }
                }
            }
        }
        else
        {
            return false;
        }
        return true;
    }

    public function onBeforeDocumentBatchUpload($file, $nfiles)
    {
        if(!JFactory::getApplication()->isSite()){
            return true;
        }
        include_once JPATH_ROOT.'/components/com_edocman/helper/helper.php';
        $user       = JFactory::getUser();
        $max_files  = $this->params->get('max_files',0);
        $max_size   = $this->params->get('max_size',0);
        $user_groups= $this->params->get('user_groups',array());

        //check if user is logged
        if($user->id > 0)
        {
            $usergroups = $user->groups;
            if(count($user_groups) > 0)
            {
                $in_group = false;
                foreach ($usergroups as $group)
                {
                    if(in_array($group, $user_groups))
                    {
                        $in_group = true;
                    }
                }
            }
            else
            {
                $in_group = true;
            }
            if($in_group)
            {
                if($max_files > 0)
                {
                    $total_files = EDocmanHelper::getTotalUploadFile();
                    if ($total_files + $nfiles >= $max_files)
                    {
                        die('{"jsonrpc" : "2.0", "error" : {"code": 500, "message": "LIMIT UPLOAD"}, "id" : "id"}');
                    }
                }

                if($max_size > 0)
                {
                    $max_size = $max_size*1024*1024;
                    $total_size = EDocmanHelper::getCurrentUserUploadedSize();
                    $total_size += $file['size'];
                    if ($total_size >= $max_size)
                    {
                        die('{"jsonrpc" : "2.0", "error" : {"code": 500, "message": "LIMIT UPLOAD"}, "id" : "id"}');
                    }
                }
            }
        }
        else
        {
            die('{"jsonrpc" : "2.0", "error" : {"code": 500, "message": "LIMIT UPLOAD"}, "id" : "id"}');
        }
    }

	/**
	 * Method to return a list of users from given groups
	 * @param   array $groupIds IDs of the groups
	 * @return  array
	 *
	 */
	public static function getUsersByGroup($groupIds)
	{
		// Get a database object.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, name, username, email')
			->from('#__users')
			->where('id IN (SELECT DISTINCT user_id FROM #__user_usergroup_map WHERE group_id IN (' . implode(',', $groupIds) . ')) AND `block` = "0" ');
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Get main category of the given document
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public static function getMainCategory($id)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.id, a.title, a.notify_group_ids')
			->from('#__edocman_categories AS a')
			->where('a.id=' . (int) $id);
		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Get URL of the site, using for Ajax request
	 */
	public static function getSiteUrl()
	{
		$uri  = JUri::getInstance();
		$base = $uri->toString(array('scheme', 'host', 'port'));
		if (strpos(php_sapi_name(), 'cgi') !== false && !ini_get('cgi.fix_pathinfo') && !empty($_SERVER['REQUEST_URI']))
		{
			$script_name = $_SERVER['PHP_SELF'];
		}
		else
		{
			$script_name = $_SERVER['SCRIPT_NAME'];
		}
		$path = rtrim(dirname($script_name), '/\\');
		if ($path)
		{
			return $base . $path . '/';
		}
		else
		{
			return $base . '/';
		}
	}
}
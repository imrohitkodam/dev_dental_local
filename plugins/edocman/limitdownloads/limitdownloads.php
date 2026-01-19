<?php
/**
 * @version        1.10.0
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');

class plgEDocmanLimitDownloads extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

    /**
     * On Document Download
     * @param $row
     */
	public function onDocumentDownload($row)
    {
        //only apply for Front-end side
        if(!JFactory::getApplication()->isSite()){
            return true;
        }
        $db         = JFactory::getDbo();
        $query      = $db->getQuery(true);
        $user       = JFactory::getUser();

        $download_limit_per_user    = $this->params->get('download_limit_per_user',20);
        $limit_per_document         = $this->params->get('limit_per_document',0);
        $user_groups                = $this->params->get('user_groups',array());

        if($limit_per_document == 1){
            $download_limit_per_user = $row->download_limit_per_user;
            if($download_limit_per_user == -1){
                $download_limit_per_user = $this->params->get('download_limit_per_user',20);
            }
        }
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
            }else{
                $in_group = true;
            }
            if($in_group)
            {
                $query->select('count(id)')->from('#__edocman_statistics')
                    ->where('document_id = "' . (int)$row->id . '"')
                    ->where('user_id = "' . $user->id . '"');
                $db->setQuery($query);
                $downloadCount = (int)$db->loadResult();
                $downloadCount--;
                if ($downloadCount >= $download_limit_per_user) {
                    JFactory::getApplication()->enqueueMessage(JText::_('EDOCMAN_DOWNLOAD_LIMIT_ERROR'), 'error');
                    return false;
                }
            }
        }
        else
        {
            //IP address
            $user_ip                = $_SERVER['REMOTE_ADDR'];
            $limit_by_ip_address    = $this->params->get('limit_by_ip_address',0);
            if($limit_by_ip_address == 1)
            {
                $query->select('count(id)')->from('#__edocman_statistics')
                    ->where('document_id = "' . (int)$row->id . '"')
                    ->where('user_ip LIKE "' . $user_ip . '"');
                $db->setQuery($query);
                $downloadCount = (int)$db->loadResult();
                $downloadCount--;
                if ($downloadCount >= $download_limit_per_user) {
                    JFactory::getApplication()->enqueueMessage(JText::_('EDOCMAN_DOWNLOAD_LIMIT_ERROR'), 'error');
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Render setting form
     *
     * @param Edocman Document $row
     *
     * @return array
     */
    public function onEditDocument($row)
    {
        if (JFactory::getApplication()->isSite())
        {
            return;
        }
        $limit_per_document = $this->params->get('limit_per_document',0);
        if($limit_per_document == 1) {
            ob_start();
            $this->drawSettingForm($row);

            return array('title' => JText::_('EDOCMAN_LIMIT_DOWNLOAD'),
                'form' => ob_get_clean(),
            );
        }
    }

    /**
     * Display form allows users to change settings on subscription plan add/edit screen
     *
     * @param EventbookingTableEvent $row
     */
    private function drawSettingForm($row)
    {
        $limit_per_document = $this->params->get('limit_per_document',0);
        if($limit_per_document == 1)
        {
            if($row->download_limit_per_user == -1 || (int)$row->id == 0)
            {
                $checked = " checked";
                $disabled = "disabled ";
                $value = "1";
            }
            else
            {
                $value = "0";
                $checked = "";
                $disabled = "";
            }
            ?>
            <table class="admintable adminform" style="width: 100%;">
                <tr>
                    <td width="100%" colspan="3" style="height:40px;">
                        <strong>
                            <?php
                            echo JText::_('EDOCMAN_SET_LIMIT_DOWNLOADS_EXPLAIN');
                            ?>
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td class="key" width="25%">
                        <?php
                        echo JText::_('EDOCMAN_INHERIT_PLUGIN_CONFIGURATION');
                        ?>
                    </td>
                    <td width="20%">
                        <input type="checkbox" name="inherit_limitdownloads" id="inherit_limitdownloads" value="<?php echo $value; ?>" <?php echo $checked; ?>/>
                    </td>
                    <td>
                        <?php echo JText::_('EDOCMAN_INHERIT_PLUGIN_CONFIGURATION_EXPLAIN'); ?>
                    </td>
                </tr>
                <tr>
                    <td class="key" width="25%">
                        <?php
                        echo JText::_('EDOCMAN_DOCUMENT_LIMIT_DOWNLOADS');
                        ?>
                    </td>
                    <td width="20%">
                        <input type="text" name="download_limit_per_user" id="download_limit_per_user" value="<?php echo $row->download_limit_per_user;?>" class="input-small" <?php echo $disabled; ?>/>
                    </td>
                    <td>
                        <?php echo JText::_('EDOCMAN_DOCUMENT_LIMIT_DOWNLOADS_EXPLAIN'); ?>
                    </td>
                </tr>
            </table>
            <script type="text/javascript">
            jQuery( "#inherit_limitdownloads" ).click(function() {
                if (jQuery("#inherit_limitdownloads").val() == 1)
                {
                    jQuery("#inherit_limitdownloads").val(0);
                    jQuery("#download_limit_per_user").val('');
                    jQuery("#download_limit_per_user").prop("disabled",false);
                }
                else
                {
                    jQuery("#inherit_limitdownloads").val(1);
                    jQuery("#download_limit_per_user").val(-1);
                    jQuery("#download_limit_per_user").prop("disabled",true);
                }
            });
            </script>
            <?php
        }
    }

    /**
     * Store Download limits
     *
     * @param                        $row
     * @param bool                   $isNew true if create new event, false if edit
     */
    public function onAfterSaveDocument($row, $data, $isNew)
    {
        if (JFactory::getApplication()->isSite()) {
            return;
        }
        require_once JPATH_ADMINISTRATOR.'/components/com_edocman/table/document.php';
        $jinput                  = JFactory::getApplication()->input;
        $inherit_limitdownloads  = $jinput->getInt('inherit_limitdownloads',0);
        $download_limit_per_user = $jinput->getInt('download_limit_per_user',-1);
        $document = JTable::getInstance('Document','EDocmanTable');
        $document->load((int)$row->id);
        if($inherit_limitdownloads == 1)
        {
            $document->download_limit_per_user = -1;
            $document->store();
        }
        else
        {
            $document->download_limit_per_user = $download_limit_per_user;
            $document->store();
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
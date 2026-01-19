<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view');
jimport('fsj_core.lib.utils.xml');
require_once( JPATH_SITE.DS.'components'.DS.'com_fss'.DS.'helper'.DS.'helper.php' );

class fsj_FSSAddViewCanned_Config extends JViewLegacy
{	
	function display($tpl = NULL)
	{	
		$option = str_replace("com_","",JRequest::getVar('option'));
		if (JRequest::getVar('tmpl') != 'component') fsj_ToolbarsHelper::addSubmenu(JRequest::getCmd('view', $option), 'JHtmlSidebar');	
		$this->sidebar = JHtmlSidebar::render();
		$this->addToolbar();

		$this->filenames = array(
			'user_source' => JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_fsj_fssadd'.DS.'plugins'.DS.'form_canned_user',
			'user_dest' => JPATH_ROOT.DS.'components'.DS.'com_fss'.DS.'plugins'.DS.'gui'.DS.'form_canned_user',
			'admin_source' => JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_fsj_fssadd'.DS.'plugins'.DS.'form_canned_admin',
			'admin_dest' => JPATH_ROOT.DS.'components'.DS.'com_fss'.DS.'plugins'.DS.'gui'.DS.'form_canned_admin'
			);

		$task = JRequest::getCmd('action');

		if ($task == "user.install") $this->installPlugin($this->filenames['user_source'], $this->filenames['user_dest']);
		if ($task == "user.enable") $this->enablePlugin("gui", "form_canned_user", 1);
		if ($task == "user.disable") $this->enablePlugin("gui", "form_canned_user", 0);

		if ($task == "admin.install") $this->installPlugin($this->filenames['admin_source'], $this->filenames['admin_dest']);
		if ($task == "admin.enable") $this->enablePlugin("gui", "form_canned_admin", 1);
		if ($task == "admin.disable") $this->enablePlugin("gui", "form_canned_admin", 0);

		$this->user_state = $this->getFileState($this->filenames['user_source'], $this->filenames['user_dest']);
		$this->admin_state = $this->getFileState($this->filenames['admin_source'], $this->filenames['admin_dest']);

		if ($this->user_state > 0)
			$this->user_plugin = FSS_Helper::IsPluignEnabled("gui", "form_canned_user");

		if ($this->admin_state > 0)
			$this->admin_plugin = FSS_Helper::IsPluignEnabled("gui", "form_canned_admin");

		if (JRequest::getVar('type') == "inline") $tpl = "inline";
	
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		JToolBarHelper::title("Templated Canned Replies Config");
		JToolBarHelper::cancel();
	}

	function getFileState($source, $target)
	{
		if (!file_exists($target.".php")) return 0;

		$source_md5 = md5(file_get_contents($source.".php"));
		$dest_md5 = md5(file_get_contents($target.".php"));
		
		if ($source_md5 != $dest_md5) return 1;

		return 2;
	}

	function installPlugin($source, $target)
	{
		JFile::copy($source . ".php", $target . ".php");
		JFile::copy($source . ".settings.xml", $target . ".settings.xml");

		JFactory::getApplication()->redirect("index.php?option=com_fsj_fssadd&view=canned_config");
	}

	function enablePlugin($type, $file, $enabled)
	{
		// need to update the list of installed plugins using the db updater from Freestyle Support

		require_once (JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fss'.DS.'updatedb.php');

		$u = new FSSUpdater();
		$log = $u->UpdatePlugins();
		
		$db = JFactory::getDBO();
		$sql = "UPDATE #__fss_plugins SET enabled = $enabled WHERE `type` = '" . $db->escape($type) . "' AND name = '" . $db->escape($file) . "'";
		$db->SetQuery($sql);
		$db->Query();

		JFactory::getApplication()->redirect("index.php?option=com_fsj_fssadd&view=canned_config");
	}
}

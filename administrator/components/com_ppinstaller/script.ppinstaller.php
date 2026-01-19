<?php
/**
* @copyright	Copyright (C) 2009 - 2011 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		PayPlans
* @subpackage	Frontend
* @contact 		shyam@readybytes.in
* website		http://www.jpayplans.com
* Technical Support : Forum -	http://www.jpayplans.com/support/support-forum.html
*/
if(defined('_JEXEC')===false) die();

//load helper


class Com_ppinstallerInstallerScript
{
	public function install($parent)
	{	
		require_once dirname(__FILE__).'/admin/defines.php';
		// change status if PPinstaller disable
		PpinstallerHelperInstall::set_extension(1);
		
		// Add java script for auto redirect
		$this->_addScript();
	}

	function _addScript()
	{
		?>
			<script type="text/javascript">
				window.onload = function(){	
				  setTimeout("location.href = 'index.php?option=com_ppinstaller';", 100);
				}
			</script>
		<?php
	}

	//when upgrading ppinstaller
	function update($parent)
	{

		$db = JFactory::getDbo();	
		$sql = 'CREATE TABLE IF NOT EXISTS `#__ppinstaller_config` (
				   `config_id` int(11) NOT NULL AUTO_INCREMENT,
				   `key` varchar(255) NOT NULL,
				   `value` text,
					PRIMARY KEY (`config_id`),
					UNIQUE KEY `idx_key` (`key`)
				)';

		$db->setQuery($sql);
		$db->execute();
		
		self::install($parent);
	}

	public function preflight($type, $parent)
	{
		if($type == 'update'){
			self::_deleteAdminMenu();
		}	
	}

	/**
	 * Joomla! 1.6+ bugfix for "Can not build admin menus"
	 */
	function _deleteAdminMenu()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		// Remove existing admin menu from #__menu record
		$query->delete('#__menu')
			  ->where($db->quoteName('type').' = '.$db->quote('component'))
			  ->where($db->quoteName('menutype').' = '.$db->quote('main'))
			  ->where($db->quoteName('link').' LIKE '.$db->quote('index.php?option=com_ppinstaller'));
		
		return $db->setQuery($query)->query();
		
	}

	public function postflight($type, $parent)
	{
		if($type == 'update'){
			//In finalized step disable menu of ppinstaller
			$db = JFactory::getDbo();
			$query = "DELETE FROM `#__menu` WHERE `alias` = 'payplans-installer'";
			$db->setQuery($query);
			$db->execute();
		}
	}

}

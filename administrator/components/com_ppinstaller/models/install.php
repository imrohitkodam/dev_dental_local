<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_installer'.DS.'models'.DS.'install.php';

/**
 * Extension Manager Install Model
 *
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @since		1.5
 */
class PpinstallerModelInstall extends InstallerModelInstall
{

	/**
	 * Install an extension from either folder, url or upload.
	 *
	 * @return	boolean result of install
	 * @since	1.5
	 */
	function install($url=null, $archivename = null, $extractdir = null)
	{
		$this->setState('action', 'install');

		// XiTODO:: clean below code
		$app = JFactory::getApplication();
		if(empty($url)){
			if(empty($archivename) || empty($extractdir)){
				$extractdir  = PpinstallerHelperMigrate::getFolderPath();
			}else{
				PpinstallerHelperInstall::extract($archivename, $extractdir);
			}
			JRequest::setVar('install_directory', $extractdir);
		}
		
		$installType = JRequest::getWord('installtype','folder');
		
		switch($installType) {
			case 'folder':
				// Remember the 'Install from Directory' path.
				if(0 <= version_compare(JVERSION, '1.6.0'))
					$app->getUserStateFromRequest($this->_context.'.install_directory', 'install_directory');
				
				$package = $this->_getPackageFromFolder();
				break;
/**
			case 'upload':
				$package = $this->_getPackageFromUpload();
				break;
**/
			case 'url':
				$package = $this->_getPackageFromUrl();
				break;

			default:
				$app->setUserState('com_installer.message', JText::_('COM_INSTALLER_NO_INSTALL_TYPE_FOUND'));
				return false;
				break;
		}

		// Was the package unpacked?
		if (!$package) {
			$app->setUserState('com_installer.message', JText::_('COM_PPINSTALLER_UNABLE_TO_FIND_INSTALL_PACKAGE'));
			return false;
		}

		// Get an installer instance
		$installer = JInstaller::getInstance();

        // There was an error installing the package
		//$msg = JText::sprintf('COM_PAYPLANS:COM_INSTALLER_INSTALL_ERROR', JText::_('COM_INSTALLER_TYPE_TYPE_'.strtoupper($package['type'])));
		$result = false;
		
                // Install the package
		if ($installer->install($package['dir'])) {
              // Package installed sucessfully
              //$msg = JText::sprintf('COM_PAYPLANS:COM_INSTALLER_INSTALL_SUCCESS', JText::_('COM_INSTALLER_TYPE_TYPE_'.strtoupper($package['type'])));
              $result = true;
		}

		return $result;
	}
}

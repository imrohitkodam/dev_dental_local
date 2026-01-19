<?php
/**
 * @copyright	Copyright (C) 2009 - 2014 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * @package		Payplans Installer
 * @contact 	support+payplans@readybytes.in
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Extension Manager Default View
 *
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @since		1.5
 */

class PpInstallerViewDefault extends PpinstallerViewAdapt
{

	/**
	 * @since 3.0
	 */
	public function display($tpl = null)
	{	
		//check for component is upgradable or not
		$isUpgradable = PpinstallerHelperUtils::is_component_upgradable();
		
		$exitUrl = (JPluginHelper::isEnabled('system','payplans')) ? JRoute::_('index.php?option=com_payplans') : JRoute::_('index.php');

		$this->assign('isUpgradable',$isUpgradable);
		$this->assign('exitUrl',$exitUrl);
		$this->assign('instructionUrl',JRoute::_('index.php?option=com_ppinstaller&task=instruction'));

		$this->render($tpl);
	}
	
	/**
	 * @since 3.0
	 */
	public function render($tpl)
	{
		//these variable are for javascript
		$vars  = new stdClass();
		
		$vars->url = new stdClass();
		$vars->url->base = JURI::base();
		$vars->url->root = JURI::root();
		$vars->url->base_without_scheme = JURI::base(true);
		
		$script = "var ppInstaller_vars = ".json_encode($vars).";";
		
		PpinstallerHelperInstall::addScript($script,true);
		//JFactory::getDocument()->addScriptDeclaration($script);
					
		PpinstallerHelperInstall::addStyle();
		PpinstallerHelperInstall::addStyle('bootstrap.min.css');
		//PpinstallerHelperInstall::addStyle('flat-ui.css');
		//adding jQuery
		PpinstallerHelperInstall::addScript('jquery.js');
		PpinstallerHelperInstall::addScript('bootstrap.min.js');
		PpinstallerHelperInstall::addScript();
		
		$this->assign('internalTpl','');//$this->loadTemplate($tpl));
		echo $this->loadTemplate('header');
		return true;
	}
	
	/**
	 * this is for showing instruction if documentation is ready 
	 * and have another solution then remove it
	 * @since 3.0
	 */
	public function instruction()
	{
		//these variable are for javascript
		$vars  = new stdClass();
		
		$vars->url = new stdClass();
		$vars->url->base = JURI::base();
		$vars->url->root = JURI::root();
		$vars->url->base_without_scheme = JURI::base(true);
		
		$script = "var ppInstaller_vars = ".json_encode($vars).";";
		
		PpinstallerHelperInstall::addScript($script,true);
		//JFactory::getDocument()->addScriptDeclaration($script);
					
		PpinstallerHelperInstall::addStyle();
		PpinstallerHelperInstall::addStyle('bootstrap.min.css');
		//PpinstallerHelperInstall::addStyle('flat-ui.css');
		//adding jQuery
		PpinstallerHelperInstall::addScript('jquery.js');
		PpinstallerHelperInstall::addScript('bootstrap.min.js');
		//PpinstallerHelperInstall::addScript();
		
		$exitUrl = (JPluginHelper::isEnabled('system','payplans')) ? JRoute::_('index.php?option=com_payplans') : JRoute::_('index.php');
		$this->assign('exitUrl',$exitUrl);
		$startUrl = JRoute::_('index.php?option=com_ppinstaller&task=display');
		$this->assign('startUrl',$startUrl);
		
		$instructions = PpinstallerHelperUtils::getFileContents(PPINSTALLER_INSTRUCTIONS_FILE_URL);
		$this->assign('instructions',$instructions);
		
		echo $this->loadTemplate('instruction');
		return true;
	}
	
	/**
	 * @since 3.0
	 */
	public function upgrade()
	{
		echo $this->loadTemplate('upgrade');
		return true;
	}
}

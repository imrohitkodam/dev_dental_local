<?php
/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		PayPlans
* @subpackage	PayPlans-Installer
* @contact 		payplans@readybytes.in
*/

// No direct access.
defined('_JEXEC') or die;

class PpinstallerControllerComplete extends PpinstallerController
{ 
	public function display($cachable = false, $urlparams = false)
	{	
		$nextView 	= 'complete';
		$nextTask 	= 'patch';
		
		try{
			require_once  JPATH_ROOT.DS.'components'.DS.'com_payplans'.DS.'includes'.DS.'includes.php';	
			
			if(!class_exists('PayplansHelperPatch')) {
				throw new Exception('file not exist');
			}
				
			$patches 	= call_user_func(array('PayplansHelperPatch','getMapper'));			
			
			if(empty($patches)) {
				throw new Exception('No available patch');
			}
			
			PpinstallerHelperPatch::clear_session();
			JFactory::getSession()->set(PPINSTALLER_REQUIRED_PATCHES, $patches);
			
			$patches 	= array_flip($patches);
			
			foreach ($patches as $patch => $flag) {
				$patches[$patch] = (!$patches[$patch])? 0 : -1;	
			}
	
			$this->_display(null,'display',array($nextView,$nextTask,$patches));
		}
		catch (Exception $e) {
			$this->finalize();
		}
	}
	
	public function patch()
	{
		//firstly need to check whether patches should be applied or not	
		
		$currentAction = JFactory::getApplication()->input->get('action','noActionAvailable');
		$offset 	= JFactory::getApplication()->input->get('start',0);
		$preOffset  = $offset;
		$nextView 	= 'complete';
		$nextTask 	= 'patch';
		$success 	= false;
				
		require_once  JPATH_ROOT.DS.'components'.DS.'com_payplans'.DS.'includes'.DS.'includes.php';
			
		$patchClass 	= 'PayplansHelperPatch';
		
		//get all the pathces to be applied
		$this->patches 	= JFactory::getSession()->get(PPINSTALLER_REQUIRED_PATCHES, array());
		
		//if not available then get them
		if(empty($this->patches)) {
			
			$this->patches = call_user_func(array($patchClass,'getMapper'));
						
			PpinstallerHelperPatch::clear_session();
			//set it to session for not fetching again and again
			JFactory::getSession()->set(PPINSTALLER_REQUIRED_PATCHES, $this->patches);
		}
		
		//if first time no action is avalable then start from begining
		if($currentAction == 'noActionAvailable') {
			$currentAction = $this->patches[0];
		}
		
		$nextKey 	= array_search($currentAction, $this->patches);
		
		++$nextKey;

		$nextAction = (isset($this->patches[$nextKey])) ? $this->patches[$nextKey] : null;
		
		//pathces should not empty, action should be available and applicable
		if(!empty($this->patches) && in_array($currentAction,$this->patches)) {
			
			if(PpinstallerHelperPatch::is_applicable($currentAction)) {

				PpinstallerHelperLogger::log(sprintf(JText::_('COM_PPINSTALLER_COMPLETE_PATCH_APPLYING'),$currentAction));
				
				$success = call_user_func_array(array($patchClass,$currentAction),array(PPINSTALLER_PATCH_LIMIT,$offset));
	
				$offset = JFactory::getApplication()->input->get('offset',0);
				
				if(!$offset){
				
					//update current patch to database i.e. it executed
					PpinstallerHelperPatch::insert(array($currentAction => $success));
	
					$msg = ($success) ? 'COM_PPINSTALLER_COMPLETE_PATCH_APPLYING_SUCCESS' : 'COM_PPINSTALLER_COMPLETE_PATCH_APPLYING_FAIL';
					PpinstallerHelperLogger::log(sprintf(JText::_($msg),$currentAction));
				}
				else {
					$nextAction = $currentAction;
					PpinstallerHelperLogger::log(JText::sprintf('COM_PPINSTALLER_COMPLETE_PATCH_UPDATING',$currentAction,$offset));
				}

			}else {
				$success = true;
			}
		}

		//if all patches are finished then goto next task
		if(empty($nextAction)) {
			
			$nextView 	= 'complete';
			$nextTask 	= 'finalize';
		}
		
		$updateMsg = null;
		if( $preOffset != 0 && $offset != 0 ) {
			$updateMsg = JText::sprintf('COM_PPINSTALLER_PATCH_LIMIT',$preOffset,$offset);
		}
		
		$this->_display('default','changeTask',array($currentAction,$nextView,$nextTask,$nextAction,$offset,$updateMsg,$success));
	}

	public function finalize()
	{	
		//update the payplansVersion
		PpinstallerHelperUtils::updatePayplansVersion();
				
		//now fetch the any broadcast according to installed version
		$config = PpinstallerHelperUtils::getConfig();
		$content = PpinstallerHelperUtils::getFileContents("http://pub.readybytes.net/ppinstaller/broadcast/$config->goingToInstall.html");
		$view = $this->getView('complete','ajax');
		$view->set('finalizeContent', $content);

		//it need to log the payplans installation in Payplan itself
		//so PREVIOUS CODE FROM PAYPLANS3.0 
		require_once JPATH_ROOT.DS.'components'.DS.'com_payplans'.DS.'includes'.DS.'includes.php';
		// log the uninstallation of payplans
		if(class_exists('PayplansHelperLogger')){
			$message = "PayPlans Installed Successfully";
			PayplansHelperLogger::log(XiLogger::LEVEL_INFO, $message, null, $message);
		}
		
		//enable old payplans menus
		if(class_exists('PayplansSetupMenus')){
			$object = new PayplansSetupMenus();
			$object->_migrateOldMenus();
		}

		//In finalized step disable menu of ppinstaller
		$db = JFactory::getDbo();
		$query = "DELETE FROM `#__menu` WHERE `alias` = 'payplans-installer'";
		$db->setQuery($query);
		$db->execute();

		//finally send the response
		$this->_display(null,'finalize');
	}

	public function revertComplete()
	{
		//after revert complete from migration view then send the response to run again
		$this->_display(null,'revertComplete');
	}
}

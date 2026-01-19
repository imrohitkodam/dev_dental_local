<?php

/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		Payplans
* @subpackage	EasySocial
* @contact	    support+payplans@readybytes.in
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Payplans EasySocial Profiletype Plugin
 *
 */
class plgPayplansEasysocialProfiletype extends XiPlugin
{
	public function onPayplansSystemStart()
	{
		if(!JFolder::exists(JPATH_SITE .DS.'components'.DS.'com_easysocial'))
		{
			return false;
		}
		//add app path to app loader
		$appPath = dirname(__FILE__).DS.'easysocialprofiletype'.DS.'app';
		PayplansHelperApp::addAppsPath($appPath);

		return true;
	}
	
	function onPayplansPlanAfterSelection($plansId, $planContoller)
	{	
		if(!JFolder::exists(JPATH_SITE .DS.'components'.DS.'com_easysocial')){
			return true;
		}		

		$plan 			= PayplansPlan::getInstance($plansId);
		$easySocialApp 	= PayplansHelperApp::getApplicableApps('easysocialprofiletype', $plan);
		if($easySocialApp){
			foreach ($easySocialApp as $app){
				$profile_id = $app->getAppParam('esprofiletypeOnactive', 0);
			}
			
		}else {
			$profile_id = $this->_getDefaultEasysocialProfiltypes();
		}
	
		if($profile_id){
			$session 	= JFactory::getSession();
			$session->set( 'profile_id' , $profile_id , SOCIAL_SESSION_NAMESPACE );
			$session->set('PP_EASYSOCIAL_PROFLIE', 1, 'payplans');
		}
		
		return true;
	}
	
	public static function _getDefaultEasysocialProfiltypes()
	{
		$db 	= PayplansFactory::getDBO();

		$query = 'SELECT `id`'
			 	. ' FROM #__social_profiles'
			 	. ' WHERE `default` = 1'
			 	;
	 	$db->setQuery( $query );
	 	return $db->loadResult();
	}

	function onUserAfterSave($user, $isnew)
	{
		if(!JFolder::exists(JPATH_SITE .DS.'components'.DS.'com_easysocial')){
			return true;
		}


		if ($isnew==false ) {

			$app 		= JFactory::getApplication();
			$option 	=  $app->input->get('option', 'BLANK');
			$controller = $app->input->get('controller', 'BLANK');
			$task 		=  $app->input->get('task', 'BLANK');

			// on easy social registration page
			if($option == 'com_easysocial' && $controller == 'registration' && $task == 'saveStep') {

				require_once JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';
				$session = JFactory::getSession();

				// Get necessary info about the current registration process.
				$registration = FD::table( 'Registration' );
				$state = $registration->load($session->getId());

				// Load the profile object.
				$profile = FD::table('Profile');
				$profile->load($registration->get('profile_id'));

				// Get the sequence
				$sequence = $profile->getSequenceFromIndex($registration->get('step'), SOCIAL_PROFILES_VIEW_REGISTRATION);

				// Load the current step.
				$step 		= FD::table( 'FieldStep' );
				$step->loadBySequence( $profile->id , SOCIAL_TYPE_PROFILES , $sequence );

				$completed = $step->isFinalStep( SOCIAL_PROFILES_VIEW_REGISTRATION );
				if($completed){
					$userId = $user[id];
					$profile = $this->_getDefaultEasysocialProfiltypes();
					return $this->_setEasysocialprofile($userId, $profile);
				}
			}
		}
	}


	public function _setEasysocialprofile($userId, $easysocialprofileId)
	{
		// If profiletype not set
		if(empty($easysocialprofileId)){
			return true;
		}
		require_once JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';

		$profileModel	= Foundry::model( 'Profiles' );
		$user 			= Foundry::user( $userId );
		$profileModel->updateUserProfile( $userId , $easysocialprofileId );

	}
      
}

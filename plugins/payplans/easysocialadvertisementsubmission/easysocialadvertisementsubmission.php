<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

$file = JPATH_ADMINISTRATOR . '/components/com_payplans/includes/payplans.php';

if (!JFile::exists($file)) {
	return;
}

require_once($file);

class plgPayplansEasysocialadvertisementsubmission extends PPPlugins
{
	/**
	 * Triggered by Joomla system events
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onAfterRoute()
	{
		if (PP::isFromAdmin()) {
			return true;
		}

		$helper = $this->getAppHelper();

		// App is applicable only for ES 4
		$applicable = $helper->exists();
		if(!$applicable) {
			return true;
		}

		$controller = $this->input->get('controller', '', 'default');
		$task = $this->input->get('task', '', 'cmd');
		$layout = $this->input->get('layout', '', 'cmd');
		$view = $this->input->get('view', '', 'cmd');
		$adId = $this->input->get('id', '', 'cmd');

		// Do nothing is user is trying to edit the advertisement
		if ($adId && ($layout === 'form' || ($controller === 'ads' && $task === 'save'))) {
			return true;
		}

		if ($controller !== 'ads' && $view !== 'ads') {
			return true;
		}

		if (($controller === 'ads' && $task !== 'save') || ($view === 'ads' && $layout !== 'form')) {
			return true;
		}

		$user = PP::user();
		$userId = $user->id;

		if (!$helper->isAllowed($userId)) {
			return $this->redirectDisallowed();
		}

		return true;;
	}

	/**
	 * Standard redirection method
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function redirectDisallowed()
	{
		$helper = $this->getAppHelper();
		$redirect = $helper->getRedirectPlanLink();
		$message = JText::_('COM_PP_APP_EASYSOCIALADSSUBMISSION_NOT_ALLOWED_MORE_ADS');

		$doc = JFactory::getDocument();

		if ($doc->getType() != 'html') {
			return ES::ajax()->reject(ES::exception($message))->send();
		}

		PP::info()->set($message, 'error');
		return PP::redirect($redirect);
	}

}

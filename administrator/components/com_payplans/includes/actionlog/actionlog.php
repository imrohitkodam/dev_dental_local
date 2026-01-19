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

use Joomla\CMS\Component\ComponentHelper;

class PPActionLog extends PayPlans
{
	private $defaultData = array(
		'action' => '',
		'title' => 'com_payplans',
		'extension_name' => 'com_payplans'
	);

	/**
     * Determines if actionlog feature is enabled or not from the 'Events To Log' option
     *
     * @since    4.1.2
     * @access    public
     */
    public function isEnabled()
    {
        $params = ComponentHelper::getComponent('com_actionlogs')->getParams();

        $extensions = $params->get('loggable_extensions', array());

        if (in_array('com_payplans', $extensions)) {
            return true;
        }

        return false;
    }


	/**
	 * Determines if actionlog feature already exist in current Joomla version.
	 * Because this actionlog feature only available in Joomla 3.9
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function exists()
	{
		static $loaded = null;

		$file = JPATH_ADMINISTRATOR . '/components/com_actionlogs/models/actionlog.php';

		if (PP::isJoomla4()) {
			$file = JPATH_ADMINISTRATOR . '/components/com_actionlogs/src/Model/ActionlogModel.php';
		}

		if (is_null($loaded)) {
			jimport('joomla.filesystem.file');

			$exists = JFile::exists($file);
			$loaded = $exists;
		}

		return $loaded;
	}

	public function log($actionString, $context, $data = array())
	{
		// Skip this if the actionlog feature not exist in current Joomla version
		if (!$this->exists()) {
			return;
		}

		if (!$this->isEnabled()) {
			return;
		}

		$user = isset($data['user']) && is_object($user) ? $user : $this->my;
		
		$data = array_merge($data, $this->defaultData);
		
		$data['userid'] = $user->id;
		$data['username'] = $user->username;
		$data['accountlink'] = "index.php?option=com_users&task=user.edit&id=" . $user->id;
		
		$context = $data['extension_name'] . '.' . $context;

		$model = $this->getModel();

		// Could be disabled
		if ($model === false) {
			return false;
		}
		
		$model->addLog(array($data), JText::_($actionString), $context, $user->id);
	}

	/**
	 * Retrieve joomla's ActionLog model
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function getModel()
	{
		$config = array('ignore_request' => true);

		if (PP::isJoomla4()) {
			$model = new Joomla\Component\Actionlogs\Administrator\Model\ActionlogModel($config);

			return $model;
		}

		\Joomla\CMS\MVC\Model\ItemModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_actionlogs/models', 'ActionlogsModelActionlog');
		$model = \Joomla\CMS\MVC\Model\ItemModel::getInstance('Actionlog', 'ActionLogsModel', $config);

		return $model;
	}
}

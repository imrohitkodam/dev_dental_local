<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

$file = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/plugins.php';

if (!JFile::exists($file)) {
	return;
}

require_once($file);
require_once(__DIR__ . '/helper.php');

class plgKunenaEasySocial extends EasySocialPlugins
{
	public $params;

	public function __construct(&$subject, $config)
	{
		$isEnabled = $this->kunenaHelper()->isEnabled();

		if (!$isEnabled) {
			return true;
		}

		parent::__construct($subject, $config);
	}

	public function kunenaHelper()
	{
		$helper = new KunenaHelperPlugin();
		return $helper;
	}

	/**
	 * Loads a helper file
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	public function loadHelper($filename)
	{
		// determine which kunena helper to load 5.x or 6.x
		$isKunena6 = $this->kunenaHelper()->isKunena6();

		$folder = $isKunena6 ? '/6/' : '/';
		$filePath = __DIR__ . '/helpers' . $folder . $filename . '.php';

		$exists = JFile::exists($filePath);

		if (!$exists) {
			return false;
		}

		require_once($filePath);

		$default = $isKunena6 ? 'Kunena6' : 'Kunena';
		$className = $default . ucfirst($filename) . 'EasySocial';

		$adapter = new $className($this->params);
		return $adapter;
	}

	/**
	 * Get Kunena login integration object.
	 *
	 * @return boolean|KunenaLogin|KunenaLoginEasySocial
	 * @since Kunena
	 */
	public function onKunenaGetLogin()
	{
		if (!isset($this->params)) {
			return;
		}

		if (!$this->params->get('login', 1)) {
			return;
		}

		$helper = $this->loadHelper('login', $this->params);
		return $helper;
	}

	/**
	 * Get Kunena avatar integration object.
	 *
	 * @return boolean|KunenaAvatar
	 * @since Kunena
	 */
	public function onKunenaGetAvatar()
	{
		if (!isset($this->params)) {
			return;
		}

		if (!$this->params->get('avatar', 1)) {
			return;
		}

		$helper = $this->loadHelper('avatar', $this->params);
		return $helper;
	}

	/**
	 * Get Kunena profile integration object.
	 *
	 * @return boolean|KunenaProfile
	 * @since Kunena
	 */
	public function onKunenaGetProfile()
	{
		if (!isset($this->params)) {
			return;
		}

		if (!$this->params->get('profile', 1)) {
			return;
		}

		$helper = $this->loadHelper('profile', $this->params);
		return $helper;
	}

	/**
	 * Get Kunena private message integration object.
	 *
	 * @return boolean|KunenaPrivate
	 * @since Kunena
	 */
	public function onKunenaGetPrivate()
	{
		if (!isset($this->params)) {
			return;
		}

		if (!$this->params->get('private', 1)) {
			return;
		}

		$helper = $this->loadHelper('private', $this->params);
		return $helper;
	}

	/**
	 * Get Kunena activity stream integration object.
	 *
	 * @return boolean|KunenaActivity
	 * @since Kunena
	 */
	public function onKunenaGetActivity()
	{
		if (!isset($this->params)) {
			return;
		}

		if (!$this->params->get('activity', 1)) {
			return;
		}

		$helper = $this->loadHelper('activity', $this->params);
		return $helper;
	}
}

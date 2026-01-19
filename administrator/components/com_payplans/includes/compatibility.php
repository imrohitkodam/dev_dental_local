<?php
/**
* @package  PayPlans
* @copyright Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license  GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Form\Form;

if (!PP::isJoomla4()) {
	class PPStringBase extends JString
	{
	}
} 

if (PP::isJoomla4()) {
	class PPStringBase extends Joomla\String\StringHelper
	{
	}
}

class PPJString extends PPStringBase
{
}

class PPCompat
{
	/**
	 * Render Joomla editor since J4 and J3 does it differently
	 *
	 * @since   4.2.0
	 * @access  public
	 */
	public static function getEditor($editorType = null)
	{
		if (!$editorType) {
			$jconfig = PP::jconfig();
			$editorType = $jconfig->get('editor');
		}

		if (PP::isJoomla4()) {
			$editor = Joomla\CMS\Editor\Editor::getInstance($editorType);
			return $editor;
		}

		$editor = JFactory::getEditor($editorType);

		if ($editorType == 'none') {
			JHtml::_('behavior.core');
		}

		return $editor;
	}

	/**
	 * Determines if this is from the Joomla backend
	 *
	 * @since	4.2
	 * @access	public
	 */
	public static function isFromAdmin()
	{
		if (PP::isJoomla4()) {
			$app = JFactory::getApplication();
			$admin = $app->isClient('administrator');

			return $admin;
		}

		$app = JFactory::getApplication();
		$admin = $app->isAdmin();

		return $admin;
	}

	/**
	 * Load JQuery from Joomla
	 *
	 * @since	4.2
	 * @access	public
	 */
	public static function renderJQueryFramework()
	{
		if (PP::isJoomla4()) {
			HTMLHelper::_('jquery.framework');

			return;
		}

		JHTML::_('jquery.framework');
	}

	/**
	 * Renders color picker library from Joomla
	 *
	 * @since	4.2
	 * @access	public
	 */
	public static function renderColorPicker()
	{
		if (PP::isJoomla4()) {
			HTMLHelper::_('jquery.framework');
			HTMLHelper::_('script', 'vendor/minicolors/jquery.minicolors.min.js', array('version' => 'auto', 'relative' => true));
			HTMLHelper::_('stylesheet', 'vendor/minicolors/jquery.minicolors.css', array('version' => 'auto', 'relative' => true));
			HTMLHelper::_('script', 'system/fields/color-field-adv-init.min.js', array('version' => 'auto', 'relative' => true));
			return;
		}

		JHTML::_('behavior.colorpicker');
	}
}

class PPUserModel
{
	/**
	 * Load joomla's user forms
	 *
	 * @since   4.0
	 * @access  public
	 */
	public static function loadUserModel()
	{
		if (PP::isJoomla4()) {
			$model = new Joomla\Component\Users\Administrator\Model\UserModel();

			return $model;
		}
		
		require_once(JPATH_ADMINISTRATOR . '/components/com_users/models/user.php');   
		$model = new UsersModelUser();

		return $model;
	}
}

class PPUsersModelRegistration
{
	/**
	 * Load Joomla's user registration model
	 *
	 * @since   4.2
	 * @access  public
	 */
	public static function load()
	{
		if (PP::isJoomla4()) {

			// load com_user model form from frontend.
			Form::addFormPath(JPATH_ROOT . '/components/com_users/forms');

			$model = new Joomla\Component\Users\Site\Model\RegistrationModel();
			return $model;
		} 

		require_once(JPATH_ROOT . '/components/com_users/models/registration.php');
		$model = new UsersModelRegistration();

		return $model;
	}
}

class PPArchive
{
	/**
	 * Load Joomla's Archive
	 *
	 * @since   4.2
	 * @access  public
	 */
	public static function load()
	{
		if (PP::isJoomla4()) {
			$archive = new Joomla\Archive\Archive();

			return $archive;
		} 

		$archive = new JArchive();

		return $archive;
	}

	/**
	 * Perform extract method from Joomla Archive
	 *
	 * @since	4.2
	 * @access	public
	 */
	public static function extract($destination, $extracted)
	{
		$archive = self::load();

		if (!PP::isJoomla4()) {
			$state = $archive::extract($destination, $extracted);

			return $state;
		} 

		$state = $archive->extract($destination, $extracted);
		
		return $state;
	}

	/**
	 * Get a file compression adapter from Joomla Archive
	 *
	 * @since	4.2
	 * @access	public
	 */
	public static function getAdapter($type)
	{
		$archive = self::load();

		if (!PP::isJoomla4()) {
			$adapter = $archive::getAdapter($type);

			return $adapter;
		} 

		$adapter = $archive->getAdapter($type);
		
		return $adapter;
	}
}

class PPArrayHelper
{
	/**
	 * Utility function to map an object to an array
	 *
	 * @since   4.2
	 * @access  public
	 */
	public static function fromObject($data)
	 {
		if (PP::isJoomla4()) {
			$data = Joomla\Utilities\ArrayHelper::fromObject($data);
			return $data;
		}


		$data = JArrayHelper::fromObject($data);
		return $data;
	 }

	/**
	 * Utility function to return a value from a named array or a specified default
	 *
	 * @since   4.2
	 * @access  public
	 */
	public static function getValue($array, $name, $default = null, $type = '')
	{
		if (PP::isJoomla4()) {
			$data = Joomla\Utilities\ArrayHelper::getValue($array, $name, $default, $type);
			return $data;
		}

		$data = JArrayHelper::getValue($array, $name, $default, $type);
		return $data;
	}
}

class PPRouter
{
	/**
	 * Determine whether the site enable SEF.
	 *
	 * @since   4.0
	 * @access  public
	 */
	public static function getMode()
	{
		$jConfig = PP::jConfig();

		if (PP::isFromAdmin()) {
			$isSef = false;
		}

		if (!PP::isFromAdmin()) {
			$isSef = $jConfig->get('sef');
		}

		return $isSef;
	}
}
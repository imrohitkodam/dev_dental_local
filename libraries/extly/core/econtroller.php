<?php

/**
 * @package     Extly.Library
 * @subpackage  lib_extly - Extly Framework
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (C) 2007 - 2017 Extly, CB. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */

defined('_JEXEC') or die;

/**
 * ExtlyController
 *
 * @package     Extly.Library
 * @subpackage  lib_extly
 * @since       1.0
 */
class ExtlyController extends F0FController
{
	/**
	 * Redirects the browser or returns false if no redirect is set.
	 *
	 * @return  boolean  False if no redirect exists.
	 */
	public function redirect()
	{
		if ($this->redirect)
		{
			$app = JFactory::getApplication();
			$app->enqueueMessage($this->message, $this->messageType);

			// Fix for Joomla 3.7
			F0FPlatform::getInstance()->setHeader('Status', '303 See other', true);
			$app->redirect($this->redirect);

			return true;
		}

		return false;
	}
}

<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @author		Jason Rey <jasonrey@stackideas.com>
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

class EasySocialControllerFoundry extends EasySocialController
{
	/**
	 * Performs language translations.
	 *
	 * @since	1.0
	 * @param 	string	The language string to translate.
	 * @return	string	The translated language string.
	 */
	public function getLanguage($languageString)
	{
		return JText::_(strtoupper($languageString));
	}

	/**
	 * Determines if the controller should be visible on lockdown mode
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function isLockDown()
	{
		return false;
	}
}

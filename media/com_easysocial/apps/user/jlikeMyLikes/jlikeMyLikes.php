<?php
/**
 * @version    SVN: <svn_id>
 * @package    JLike
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die();

Foundry::import('admin:/includes/apps/apps');

/**
 * JLike users product application for EasySocial.
 *
 * @version  Release: <1.0>
 * @since    1.0
 */
class SocialUserAppjlikeMyLikes extends SocialAppItem
{
	/**
	 * Constructor
	 *
	 * @param   array  $options  Options array
	 *
	 * return all html code of layout.
	 */
	public function __construct ($options = array())
	{
		// Load language file for plugin
		$lang = JFactory::getLanguage();
		$lang->load('plg_app_user_jlikeMyLikes', JPATH_ADMINISTRATOR);

		parent::__construct($options);
	}
}

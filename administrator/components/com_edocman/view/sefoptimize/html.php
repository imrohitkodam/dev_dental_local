<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die;

/**
 * View to edit
 */
class EdocmanViewSefoptimizeHtml extends OSViewHtml
{
	public function display()
	{
		EDocmanHelperHtml::renderSubmenu('sefoptimize');
		parent::display();
	}	
}

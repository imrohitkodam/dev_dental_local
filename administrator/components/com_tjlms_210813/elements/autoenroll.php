<?php
/**
 * @package     Shika
 * @subpackage  com_tjlms
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Component\ComponentHelper;

/**
 * Auto Enroll field class.
 *
 * @since  1.3.31
 */
class JFormFieldAutoenroll extends FormField
{
	/**
	 * Method to get the field input.
	 *
	 * @return	string	The field input.
	 *
	 * @since	1.3.31
	 */
	public function getInput()
	{
		$params     = ComponentHelper::getParams('com_tjlms');
		$autoEnroll = $params->get('auto_enroll');
		$document   = Factory::getDocument();
		$checkedNo  = $checkedYes = '';

		if ($autoEnroll)
		{
			$checkedYes = 'checked="checked"';
		}
		else
		{
			$checkedNo = 'checked="checked"';
		}

		$html = '<div>
			<fieldset id="jform_autoenroll" class="btn-group radio">
				<input type="radio" id="jform_autoenroll0" name="jform[auto_enroll]" value="1" ' . $checkedYes . '>
				<label for="jform_autoenroll0" class="btn">
					' . Text::_("JYES") . '
				</label>

				<input type="radio" id="jform_autoenroll1" name="jform[auto_enroll]" value="0"' . $checkedNo . ' >
				<label for="jform_autoenroll1" class="btn">
				' . Text::_("JNO") . '
				</label>
			</fieldset>
		</div>';

		$document->addScriptDeclaration("
			jQuery(document).ready(function()
			{
				jQuery(document).on('click', '[name$=\'[admin_approval]\']', function () {
						jQuery('#jform_autoenroll1').click();
			 	});
			});
		");

		echo $html;
	}
}

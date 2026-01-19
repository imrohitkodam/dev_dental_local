<?php
/**
 * @package     LMS_Shika
 * @subpackage  mod_lms_course_progress
 * @copyright   Copyright (C) 2009-2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link        http://www.techjoomla.com
 */
// No direct access.
defined('_JEXEC') or die;
JHTML::_('behavior.modal');
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root(true) . '/modules/mod_lms_course_blocks/assets/style.css');
?>
<div class="<?php echo COM_TJLMS_WRAPPER_DIV; ?> ">

<?php
if ($params->get('progress', 1)) :
	require JModuleHelper::getLayoutPath('mod_lms_course_blocks', 'default_' . 'progress');
endif;

if ($params->get('info', 1)) :
	require JModuleHelper::getLayoutPath('mod_lms_course_blocks', 'default_' . 'info');
endif;

// Durgesh added for dental
jimport( 'joomla.application.module.helper' );
$module = JModuleHelper::getModule('mod_tjfield_presenter');
$attribs['style'] = 'xhtml';
echo JModuleHelper::renderModule($module, $attribs);
// Durgesh added for dental

if ($params->get('assign_user', 1) && $showassign == 1) :
	require JModuleHelper::getLayoutPath('mod_lms_course_blocks', 'default_' . 'assign_user');
endif;

if ($params->get('taught_by', 1)) :
	require JModuleHelper::getLayoutPath('mod_lms_course_blocks', 'default_' . 'taught_by');
endif;

if ($params->get('recommend', 1) && $showrecommend == 1) :
	require JModuleHelper::getLayoutPath('mod_lms_course_blocks', 'default_' . 'recommend');
endif;

if ($params->get('group_info', 1)) :
	require JModuleHelper::getLayoutPath('mod_lms_course_blocks', 'default_' . 'group_info');
endif;

if ($params->get('enrolled', 1)) :
	require JModuleHelper::getLayoutPath('mod_lms_course_blocks', 'default_' . 'enrolled');
endif;
?>
</div>


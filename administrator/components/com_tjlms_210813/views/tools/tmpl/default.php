<?php
/**
 * @package     TJLms
 * @subpackage  com_shika
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;

HTMLHelper::addIncludePath(JPATH_COMPONENT.'/helpers/html');
HTMLHelper::stylesheet('media/techjoomla_strapper/bs3/css/bootstrap.css');

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('behavior.modal');
HTMLHelper::_('jquery.token');

$options['relative'] = true;
$attribs['defer'] = 'defer';
HTMLHelper::script('com_tjlms/js/tjlmsAdmin.min.js', $options, $attribs);
$app = Factory::getApplication();
$courseId = $app->input->get('course_id', 0, 'INT');

?>
<div class="<?php echo COM_TJLMS_WRAPPER_DIV; ?>">
	<form action="<?php echo JRoute::_('index.php?option=com_tjlms&view=tools'); ?>" method="post" name="adminForm" id="adminForm">
	<?php
		ob_start();
		include JPATH_BASE . '/components/com_tjlms/layouts/header.sidebar.php';
		$layoutOutput = ob_get_contents();
		ob_end_clean();
		echo $layoutOutput;
	?> <!--// JHtmlsidebar for menu ends-->
	<div class="progressbar"></div>
		<div class="panel panel-primary panel-heading span6">
			<div class="">
				<h4><?php echo Text::_('COM_TJLMS_TITLE_RECALCULATE_PROGRESS_SELECTED_COURSE'); ?></h4>
			</div>
			<div class="row-fluid">
				<div class="control-group">
					<div class="controls">
						<label>
							<?php echo Text::_('COM_TJLMS_SELECT_COURSE'); ?>
						</label>
						<?php echo HTMLHelper::_('select.genericlist', $this->courses, 'filter[course_names]', 'class="course_names " onchange="tjlmsAdmin.tools.showEnrolledUsers(this.value)"', 'value', 'text', $courseId ? $courseId : $this->state->get('filter.course_names'), 'filter_course_names'); ?>
					</div>
				</div>
			</div>
			<div>
			<div class="alert alert-info hide" id="enrolled_user_notice">
			</div>
			<button type="button" class="btn btn btn-success inactiveLink disabled" id="recalculate" onclick="tjlmsAdmin.tools.calculateCourseProgress();"><?php echo Text::_('COM_TJLMS_PROCEED'); ?></button>
			</div>
		</div>
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div> <!--techjoomla-bootstrap-->
<script>
	tjlmsAdmin.tools.init();
</script>
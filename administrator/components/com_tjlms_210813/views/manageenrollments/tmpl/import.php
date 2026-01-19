<?php
/**
 * @version     1.0.0
 * @package     com_tjlms
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      TechJoomla <extensions@techjoomla.com> - http://www.techjoomla.com
 */

// no direct access
defined('_JEXEC') or die;
include_once JPATH_COMPONENT.'/js_defines.php';
JHtml::script(JUri::root().'administrator/components/com_tmt/assets/js/ajax_file_upload.js');

$filepath = JUri::root() . 'administrator/components/com_tjlms/csv/userData.csv';
$timezoneFilepath = JUri::root() . 'administrator/components/com_tjlms/csv/timeZone.csv';
$courseList = JUri::root() . 'administrator/index.php?option=com_tjlms&view=courses';
$groupList = JUri::root() . 'administrator/index.php?option=com_users&view=groups';

?>
<div id="tjlms_import-csv" class="tjlms-wrapper row-fluid">
	<div id="import" style="width:80%" class="modal hide fade form-horizontal"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3 id="myModalLabel"><?php echo JText::_("COM_TJLMS_ENROLLMENT_CSV_UPLOAD_FILE");?></h3>
		</div>
		<div class="control-group csv-import-user-select" >
		<div class="control-label  tjlmscenter">
			<input id="notify_user_import" type="checkbox" name="notify_user_import" checked="checked">
						<?php echo JText::_('COM_TJLMS_NOTIFY_ASSIGN_USER'); ?>
		</div>
				<div class="control-label tjlmscenter"><?php echo JText::_("COM_TJLMS_ENROLLMENT_CSV_SELECT_FILE");?></div>
				<div class="controls">
					<div class="fileupload fileupload-new pull-left" data-provides="fileupload">
						<div class="input-append">
							<div class="uneditable-input span4">
								<span class="fileupload-preview">
									<?php echo JText::_("COM_TJLMS_ENROLLMENT_CSV_IMPORT_FILE");?>
								</span>
							</div>
							<span class="btn btn-file">
								<span class="fileupload-new"><?php echo JText::_("COM_TJLMS_CHOOSE_FILE");?></span>
								<input type="file" id="user-csv-upload" name="question-csv-upload"
								onchange="jQuery('.fileupload-preview').html(jQuery(this)[0].files[0].name);">
							</span>
							<button class="btn btn-primary" id="upload-submit"
								onclick="validate_import(document.getElementById('upload-submit').form['question-csv-upload'],'1','.csv-import-user-select'); return false;">
								<span class="icon-upload icon-white"></span> <?php echo JText::_("COM_TJLMS_START_UPLOAD");?>
							</button>
						</div>
					</div>
					<div style="clear:both"></div>
				</div>
			</div>
			<hr class="hr hr-condensed">
			<div class="help-block">
				<?php
					echo JText::sprintf('COM_TJLMS_ENROLLMENT_CSVHELP');
				?>
				<br>
				<div class="row-fluid">
					<div class="span4"></div>
					<div class="span4">
						<?php
							$link = '<a href="' . $filepath . '">' . JText::_("COM_TJLMS_ENROLLMENT_CSV_SAMPLE") . '</a>';
							echo JText::sprintf('COM_TJLMS_ENROLLMENT_CSVHELP1', $link);
						?>
					<br>
					<?php
						$link = '<a href="' . $timezoneFilepath . '">' . JText::_("COM_TJLMS_ENROLLMENT_CSV_SAMPLE") . '</a>';
						echo JText::sprintf('COM_TJLMS_TIMEZONE_CSVHELP', $link);
					?>
					<br>
					<?php
						$link = '<a target="_blank" href="' . $courseList . '">' . JText::_("COM_TJLMS_ENROLLMENT_CSV_VIEW_LIST") . '</a>';
						echo JText::sprintf('COM_TJLMS_ENROLLMENT_CSVHELP2', $link);
					?>
					<br>
					<?php
						$link = '<a target="_blank" href="' . $groupList . '">' . JText::_("COM_TJLMS_ENROLLMENT_CSV_VIEW_LIST") . '</a>';
						echo JText::sprintf('COM_TJLMS_ENROLLMENT_CSVHELP3', $link);
					?>
					</div>
					<div class="span4"></div>
				</div>
			</div>
			<hr class="hr hr-condensed">
	</div>
</div>

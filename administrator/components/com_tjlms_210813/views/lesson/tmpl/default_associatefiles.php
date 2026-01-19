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

JHTML::_('behavior.modal', 'a.modal');
?>

<?php	$k=0;
$cMax    = (int) $this->params->get('lesson_upload_size', '0', 'INT');
if(method_exists('JUtility', 'getMaxUploadSize'))
{
	$cMax = JUtility::getMaxUploadSize($cMax . 'MB');
}

$allowedFileSize = JHtml::_('number.bytes', $cMax , 'MB');

?>
<div class="container-fluid">
<div class="<?php echo COM_TJLMS_WRAPPER_DIV; ?>">
	<form action="<?php echo JRoute::_('index.php?option=com_tjlms&view=lesson&course_id='. $this->courseId); ?>" method="post" enctype="multipart/form-data" name="adminForm" data-js-unique="<?php echo $this->formId;?>" id="lesson-associatefile-form_<?php echo $this->formId;?>" class="form-validate lesson-associatefile-form" >

		<div class="row-fluid">
			<div class="span12 oldassocfiles">

		<?php
			$tableclass = '';
			if (empty($this->item->oldAssociateFiles))
			{
				$tableclass = "tjlms_display_none";
			}
			?>

			<table id="list_selected_files" class="table table-bordered table-striped table-responsive list_selected_files <?php echo $tableclass;?>">
			 <caption class="text-left"><strong><?php echo JText::_('COM_TJLMS_YOUR_ASSOC_FILES'); ?></strong></caption>
			<thead>
				<tr class="tableheading">
					<th class="tjlmscenter"><?php echo JText::_('COM_TJLMS_FILENAME'); ?></th>
					<th class="tjlmscenter"><?php echo JText::_('COM_TJLMS_REMOVE_FILE'); ?></th>
				</tr>
			</thead>
				<tbody>
				<?php if (!empty($this->item->oldAssociateFiles)){ ?>

					<?php foreach($this->item->oldAssociateFiles as $assocfiles): ?>

						<tr id="assocfiletr_<?php echo $assocfiles->media_id; ?>" data-js-id="associated-file">
							<td class="tjlmscenter"><span><?php echo $assocfiles->filename; ?></span></td>
							<td class="tjlmscenter"><i  data-js-id="associated-file-remove" data-js-val="<?php echo $assocfiles->media_id;?>" title="<?php echo JText::_('COM_TJLMS_REMOVE_FILE_TITLE'); ?>" class="remove btn">×</i></td>
						</tr>

					<?php endforeach; ?>

				<?php } ?>
				</tbody>
			</table>

			<?php
			$alertclass = '';
			if (!empty($this->item->oldAssociateFiles)){
				$alertclass = "tjlms_display_none";
			 }?>

			<div class="alert alert-info no_selected_files  <?php echo $alertclass;?>">
				<?php echo JText::_('COM_TJLMS_NO_ASSOC_FILES'); ?>
			</div>

			</div>
		</div>

		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span3 help-block">
					<?php echo JText::_('COM_TJLMS_SELECT_ASSOC_FILES'); ?>
					</div>

					<div class="span3 selectfilebtn">
						<a id="selectFileLink" class="btn btn-primary btn-block" data-js-role='tjmodal' data-js-link='index.php?option=com_tjlms&view=lesson&layout=selectassociatefiles&id=<?php echo ($this->item->id) ? $this->item->id : 0;?>&tmpl=component&form_id=<?php echo $this->formId; ?>'><?php echo JText::_('COM_TJLMS_SELECT'); ?></a>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
				<div class="span4 help-block"><?php echo JText::_('COM_TJLMS_NEW_ASSOC_FILES'); ?></div>

				<div id="file_container<?php echo $k; ?>" class="file_container form-inline span8">
					<div class="controls file_browse_area">
						<div id="associate" class="fileupload fileupload-new" data-provides="fileupload">
							<div class="input-append">
								<div class="uneditable-input span3">
									<span class="fileupload-preview">
									<?php echo JText::sprintf('COM_TJLMS_UPLOAD_FILE_WITH_EXTENSION', 'any', $allowedFileSize);?>

									</span>
								</div>
								<span class="btn btn-primary btn-file" data-js-type="associate_file_upload">
									<span class="fileupload-new"><?php echo JText::_("COM_TJLMS_BROWSE");?></span>
									<input type="file" data-allowedsize="<?php echo trim(str_replace('MB','',$allowedFileSize));?>" id="lesson_files<?php echo $k; ?>" name="lesson_files[<?php echo  $k; ?>][file]">

									<input type="hidden" id="assocFileMedia<?php echo $k; ?>" class="assocFileMedia" name="lesson_files[][media_id]" value=""/>
<!--
									<input type="file" id="lesson_files<?php echo $k; ?>" name="lesson_files[<?php echo  $k; ?>][file]" onchange="validate_file(this,'<?php echo $mod_id;?>','')";>
									<input type="hidden" id="assocFileMedia<?php echo $k; ?>" class="assocFileMedia" name="lesson_files[][media_id]" value=""/>
-->
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			</div>
		</div>
		<!--div class="span1">
			<button class="btn btn-small btn-success" type="button" id='add' onclick="addClone('file_container','file_container','<?php echo $form_id; ?>');" 	title='<?php echo JText::_('COM_TJLMS_ADD_BUTTON');?>'>
			<i class="icon-plus icon-white"></i>
			</button>
		</div-->

		<div style="clear:both"></div>

		<!--END-->
		<input type="hidden" name="option" value="com_tjlms" />
		<input type="hidden" name="task" value="lesson.updateassocfiles" />
		<input type="hidden" name="lesson_format[format]" id="jform_format" value="associate">
		<input type="hidden" name="lesson_format[format_id]" id="lesson_format_id" value="0">
		<input type="hidden" name="lesson_format[id]" id="lesson_id" data-js-id="id" value="<?php echo ($this->item) ? $this->item->id : 0;?>">
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
</div>
<style>
.lesson-associatefile-form .controls{margin-left:0px;}
.remove {
	font-size: 20px;
}
</style>

<?php
/**
 * @version        1.9.4
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Dang Thuc Dam
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;
// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>

<script type="text/javascript">
	function submitDocument() {
		if (document.formvalidator.isValid(document.getElementById('item-form'))) {
			<?php echo $this->form->getField('description')->save(); ?>
			<?php echo $this->form->getField('short_description')->save(); ?>
			var answer = confirm('<?php echo JText::_('EDOCMAN_SAVE_DOCUMENT_CONFIRM');?>');
			if(answer == 1) {
				Joomla.submitform('document.save', document.getElementById('item-form'));
			}
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
	function cancelSubmit() {
		var form = document.getElementById('item-form') ;
		form.task.value = 'document.cancel' ;
		form.submit() ;
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_edocman&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data">
	<?php
	if ($this->params->get('page_heading'))
	{
		$heading = $this->params->get('page_heading');
	}
	else
	{
		$heading = JText::_('EDOCMAN_UPLOAD_DOCUMENT');
	}
	?>
	<h1 class="edocman-page-heading"><?php echo $heading; ?></h1>
	<?php
	if($this->header_text != ""){
		?>
		<strong><?php echo $this->header_text;?></strong>
		<?php
	}
	?>
	<table class="adminform" width="100%" style="border:0px !important;">
		<tr>
			<td valign="top" width="80%">
				<table width="100%">
					<tr>
						<td class="edocman_title_col">
							<?php echo $this->form->getLabel('title'); ?>
						</td>
						<td class="edocman_field_cell">
							<?php echo $this->form->getInput('title'); ?>
						</td>
					</tr>
					<?php
						if ($this->catId)
						{
						?>
							<tr>
								<td class="edocman_title_col">
									<?php echo $this->form->getLabel('category_id'); ?>
								</td>
								<td class="edocman_field_cell">
									<input type="hidden" name="jform[category_id]" value="<?php echo $this->catId; ?>" />
									<?php echo $this->categoryTitle; ?>
								</td>
							</tr>
						<?php
						}
						else
						{
						?>
							<tr>
								<td class="edocman_title_col">
									<?php echo $this->form->getLabel('category_id'); ?>
								</td>
								<td class="edocman_field_cell">
									<?php echo $this->form->getInput('category_id'); ?>
								</td>
							</tr>
						<?php
						}
					?>
					<tr>
						<td class="edocman_title_col">
							<?php echo $this->form->getLabel('filename'); ?>
						</td>
						<td class="edocman_field_cell">
							<?php echo $this->form->getInput('filename'); ?>
							<?php
							if ($this->item->id) {
								?>
									<span style="padding-top: 3px; display: block;">
								<?php
								if ($this->item->original_filename) {
									echo JText::_('EDOCMAN_FILE').": ";                                    
								?>
									<a href="<?php echo 'index.php?option=com_edocman&task=document.download&id='.$this->item->id; ?>&Itemid=<?php echo EdocmanHelper::getItemid(); ?>"><?php echo $this->item->original_filename ; ?></a>
								<?php        
								}      
								?>
									</span>
								<?php
							}
							?>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<?php echo $this->form->getLabel('short_description'); ?>
							<div class="clr"></div>
							<?php echo $this->form->getInput('short_description'); ?>
							<div class="clr"></div>
							<?php echo $this->form->getLabel('description'); ?>
							<div class="clr"></div>
							<?php echo $this->form->getInput('description'); ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td  style="float: left;">
				<input type="button" class="btn btn-warning" onclick="cancelSubmit();" value="<?php echo JText::_('EDOCMAN_CANCEL'); ?>" />
				<input type="button" class="btn btn-success" onclick="submitDocument();" value="<?php echo JText::_('EDOCMAN_SUBMIT'); ?>"  />
			</td>
		</tr>
	</table>
	<input type="hidden" name="task" value="" />
	<?php
	if((int)$this->item->id > 0){
		?>
		<input type="hidden" name="jform[modified_time]" id="modified_time" value="<?php echo date("Y-m-d H:i:s");?>" />
		<?php
	}
	?>
	<?php echo JHtml::_('form.token'); ?>
</form>
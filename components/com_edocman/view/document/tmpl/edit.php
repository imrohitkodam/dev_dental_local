<?php
/**
 * @version        1.9.4
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
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
<form action="<?php echo JRoute::_('index.php?option=com_edocman&view=document&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data">
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
				if($this->config->show_alias_form){
				?>
				<tr>
					<td class="edocman_title_col">
						<?php echo $this->form->getLabel('alias'); ?>
					</td>
					<td class="edocman_field_cell">
						<?php echo $this->form->getInput('alias'); ?>
					</td>
				</tr>
				<?php } ?>
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
						<?php echo $this->form->getLabel('url'); ?>
					</td>
					<td class="edocman_field_cell">
						<?php echo $this->form->getInput('url'); ?>
					</td>
				</tr>
				<?php
				if($this->config->show_thumb_form){
				?>
				<tr>
					<td class="edocman_title_col">
						<?php echo $this->form->getLabel('image'); ?>
					</td>
					<td class="edocman_field_cell">
						<?php echo $this->form->getInput('image'); ?>
					</td>
				</tr>
				<?php } ?>
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
					<td class="edocman_title_col">
						<?php echo $this->form->getLabel('document_url'); ?>
					</td>
					<td class="edocman_field_cell">
						<?php echo $this->form->getInput('document_url'); ?>
					</td>
				</tr>
				<?php
				if($this->config->view_url){
				?>
				<tr>
					<td>
						<?php echo $this->form->getLabel('view_url'); ?>
					</td>
					<td>
						<?php echo $this->form->getInput('view_url'); ?>
					</td>
				</tr>
				<?php } ?>
			  <?php
			  if (!isset($this->config->access_level_inheritance) || $this->config->access_level_inheritance !== '1')
			  {
			  ?>
				<tr>
				    <td>
					  <?php echo $this->form->getLabel('access'); ?>
				    </td>
				    <td>
					  <?php echo $this->form->getInput('access'); ?>
				    </td>
				</tr>
			  <?php
			  }
				$user = JFactory::getUser() ;
				if ($user->authorise('edocman.assign_documents_to_users', 'com_edocman'))
				{
				?>
				<tr>
					<td class="edocman_title_col">
						<label id="jform_user_ids-lbl" for="jform_user_ids" class="hasTip" title="<?php echo JText::_('EDOCMAN_USER_IDS_DESC'); ?>" aria-invalid="false">
						<?php echo JText::_('EDOCMAN_USER_IDS');?>
					</label>
				</td>
				<td class="edocman_field_cell">
					<?php
						echo EDocmanHelper::getUserInput($this->item->user_ids);
					?>
					</td>
				</tr>
				<?php
				}
				?>
				<?php
				if($this->config->show_license_form){
				?>
				<tr>
					<td class="edocman_title_col">
						<?php echo $this->form->getLabel('license'); ?>
					</td>
					<td class="edocman_field_cell">
						<?php echo $this->form->getInput('license'); ?>
					</td>
				</tr>
				<?php } ?>
				<?php
				if($this->config->show_published_form){
				?>
				<tr>
					<td class="edocman_title_col">
						<?php echo $this->form->getLabel('published'); ?>
					</td>
					<td class="edocman_field_cell">
						<?php echo $this->form->getInput('published'); ?>
					</td>
				</tr>
				<?php } ?>
				<?php
				if(($this->config->lock_function) && ($this->config->show_lock_form)){
				?>
				<tr>
					<td class="edocman_title_col">
						<?php echo $this->form->getLabel('is_locked'); ?>
					</td>
					<td class="edocman_field_cell">
						<?php echo $this->form->getInput('is_locked'); ?>
					</td>
				</tr>
				<input type="hidden" name="old_locked_status" value="<?php echo $this->item->is_locked; ?>" />
				<?php } ?>
				<?php
				if($this->config->show_meta_form){
				?>
					<tr>
						<td class="edocman_title_col">
							<?php echo $this->form->getLabel('metadesc'); ?>
						</td>
						<td class="edocman_field_cell">
							<?php echo $this->form->getInput('metadesc'); ?>
						</td>
					</tr>

					<tr>
						<td class="edocman_title_col">
							<?php echo $this->form->getLabel('metakey'); ?>
						</td>
						<td class="edocman_field_cell">
							<?php echo $this->form->getInput('metakey'); ?>
						</td>
					</tr>
				<?php } ?>
				<?php
				if($this->config->show_tag_form){
				?>
				<tr>
					<td class="edocman_title_col">
						<?php echo $this->form->getLabel('tags'); ?>
					</td>
					<td class="edocman_field_cell">
						<?php echo $this->form->getInput('tags'); ?>
					</td>
				</tr>
				<?php } ?>
				<?php
				/*
					if ($this->config->activate_multilingual_feature) {
					?>
					<tr>	
						<td><?php echo $this->form->getLabel('language'); ?></td>
						<td><?php echo $this->form->getInput('language'); ?></td>
					</tr>      	
					<?php    
					} 
				*/
				?>	
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
			<td style="float: left;">
				<input type="button" class="btn btn-warning" onclick="cancelSubmit();" value="<?php echo JText::_('EDOCMAN_CANCEL'); ?>" />
				<input type="button" class="btn btn-success" onclick="submitDocument();" value="<?php echo JText::_('EDOCMAN_SUBMIT'); ?>"  />
			</td>
		</tr>
	</table>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="license_id" id="license_id" value="<?php echo $this->default_license; ?>" />
	<?php
	if((int)$this->item->id > 0){
		?>
		<input type="hidden" name="jform[modified_time]" id="modified_time" value="<?php echo date("Y-m-d H:i:s");?>" />
		<?php
	}
	?>
	<?php echo JHtml::_('form.token'); ?>
</form>
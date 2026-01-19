<?php
/**
 * @version        1.9.10
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
EDocmanHelper::chosen('select.chosen');
$fields = $this->form->getGroup('params');
$bootstrapHelper = $this->bootstrapHelper;
$rowFluidClass = $bootstrapHelper->getClassMapping('row-fluid');
$span8Class = $bootstrapHelper->getClassMapping('span8');
$span4Class = $bootstrapHelper->getClassMapping('span4');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'cancel' || document.formvalidator.isValid(document.getElementById('item-form'))) {
			<?php echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task, document.getElementById('item-form'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_edocman&view=document&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data">
<div id="j-main-container">
	<div class="row-fluid">
		<?php
		echo JHtml::_('bootstrap.startTabSet', 'document', array('active' => 'general-page'));
		?>
		<div class="tab-content">
			<?php
			echo JHtml::_('bootstrap.addTab', 'document', 'general-page', JText::_('EDOCMAN_GENERAL', true));
			?>
			<div class="tab-pane active <?php echo $rowFluidClass;?>" id="general-page">
				<div class="<?php echo $span8Class;?>">
					<fieldset class="adminform">
						<legend><?php echo JText::_('EDOCMAN_FIELDSET_DETAILS');?></legend>
						<table width="100%">
							<tr>
								<td class="key">
									<?php echo $this->form->getLabel('title'); ?>	
								</td>
								<td>
									<?php echo $this->form->getInput('title'); ?>
								</td>
							</tr>				
							<tr>
								<td class="key">
									<?php echo $this->form->getLabel('alias'); ?>	
								</td>
								<td>
									<?php echo $this->form->getInput('alias'); ?>
								</td>					
							</tr>	
							<?php
			                    if ((int)$this->config->file_upload_method == 0) {
			                    ?>
			                    	<tr>
			        					<td>
			        						<?php echo $this->form->getLabel('filename'); ?>
			        					</td>
			        					<td>
			        						<?php echo $this->form->getInput('filename'); ?>        						
			        						<?php
			                                    if ($this->item->id) {
			                                        ?>
			                                        	<span style="padding-top: 3px; display: block;">
			                                        <?php
			                                        if ($this->item->original_filename) {
			                                            echo JText::_('Current File : ');                                    
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
			                    <?php        
			                    } else {
			                    ?>
			                    	<tr>
			        					<td>
			        						<?php echo $this->form->getLabel('select_filename'); ?>
			        					</td>
			        					<td>
			        						<?php echo $this->form->getInput('select_filename'); ?>        																											
			        					</td>
			        				</tr>
			                    <?php    
			                    }
							?>											
							<tr>
								<td>
									<?php echo $this->form->getLabel('document_url'); ?>
								</td>
								<td>
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
							<tr>
								<td class="key">
									<?php echo $this->form->getLabel('tags'); ?>	
								</td>
								<td>
									<?php echo $this->form->getInput('tags'); ?>		
								</td>
							</tr>
						</table>							
						<div class="clr"></div>
						<?php echo $this->form->getLabel('short_description'); ?>
						<div class="clr"></div>
						<?php echo $this->form->getInput('short_description'); ?>
						<div class="clr"></div>
						<?php echo $this->form->getLabel('description'); ?>
						<div class="clr"></div>
						<?php echo $this->form->getInput('description'); ?>
						<div class="clr"></div>
						<?php echo $this->form->getLabel('document_history'); ?>
						<div class="clr"></div>
						<?php echo $this->form->getInput('document_history'); ?>
					</fieldset>
				</div>
				<div class="<?php echo $span4Class;?>">
					<table width="100%" class="adminform rightsidetable">
						<tr>
							<th colspan="2">
								<strong><?php echo JText::_('EDOCMAN_CATEGORY');?></strong>
							</th>
						</tr>
						<tr>
							<td class="key">
								<?php echo $this->form->getLabel('category_id'); ?>	
							</td>
							<td  style="padding-bottom: 10px;">
								<?php echo $this->form->getInput('category_id'); ?>		
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo $this->form->getLabel('extra_category_ids'); ?>	
							</td>
							<td  style="padding-bottom: 5px;">
								<?php echo $this->form->getInput('extra_category_ids'); ?>		
							</td>
						</tr>
					</table>
					<BR />
					<table width="100%" class="adminform rightsidetable">
						<tr>
							<th colspan="2">
								<strong><?php echo JText::_('EDOCMAN_ACCESS');?></strong>
							</th>
						</tr>
						<?php
							if (!isset($this->config->access_level_inheritance) || $this->config->access_level_inheritance !== '1')
							{
							?>
								<tr>
									<td valign="top" width="35%">
                                        <?php echo $this->form->getLabel('accesspicker'); ?>
									</td>
									<td width="65%">
                                        <?php
                                        EDocmanHelper::showCheckboxfield('accesspicker', $this->item->accesspicker, JText::_('EDOCMAN_PRESETS'), JText::_('EDOCMAN_GROUPS'));
                                        ?>
                                        <div class="clr"></div>
                                        <?php
                                        if($this->item->accesspicker == 0){
                                            $style1 = "display:block";
                                            $style2 = "display:none";
                                        }else{
                                            $style2 = "display:block";
                                            $style1 = "display:none";
                                        }
                                        ?>
                                        <div class="control-group" id="presetsdiv" style="<?php echo $style1;?>" data-showon='<?php echo EDocmanHelper::renderShowon(array('jform[accesspicker]' => '0')); ?>'>
                                            <?php echo $this->form->getInput('access'); ?>
                                        </div>
                                        <div class="control-group" id="groupsdiv" style="<?php echo $style2;?>" data-showon='<?php echo EDocmanHelper::renderShowon(array('jform[accesspicker]' => '1')); ?>'>
                                            <?php echo $this->form->getLabel('groups'); ?>
                                            <?php echo $this->form->getInput('groups'); ?>
                                        </div>
									</td>
								</tr>
							<?php
							}
						?>
						<tr>
							<td>
								<?php echo $this->form->getLabel('user_ids'); ?>
							</td>
							<td>						
								<?php echo EdocmanHelper::getUserInput($this->item->user_ids) ; ?>
							</td>
						</tr>
						<?php
						if($this->config->user_group_ids){
						?>
						<tr>
							<td>
								<?php echo $this->form->getLabel('group_ids'); ?>
							</td>
							<td>						
								<?php 
								echo $this->form->getInput('group_ids'); 
								?>
							</td>
						</tr>
						<?php } ?>
						<tr>
							<td>
								<?php echo $this->form->getLabel('published'); ?>
							</td>
							<td>
								<?php echo $this->form->getInput('published'); ?>
							</td>
						</tr>
						<?php
						if($this->config->lock_function){
						?>
						<tr>
							<td>
								<?php echo $this->form->getLabel('is_locked'); ?>
							</td>
							<td>
								<?php echo $this->form->getInput('is_locked'); ?>
							</td>
						</tr>
						<input type="hidden" name="old_locked_status" value="<?php echo $this->item->is_locked; ?>" />
						<?php
						}
						?>
					</table>
					<BR />
					<table width="100%" class="adminform rightsidetable">
						<tr>
							<th colspan="2">
								<strong><?php echo JText::_('EDOCMAN_OTHER_INFORMATION');?></strong>
							</th>
						</tr>
                        <?php
                        if(!$this->config->increase_document_version){
                        ?>
						<tr>
							<td class="key">
								<?php echo $this->form->getLabel('document_version'); ?>	
							</td>
							<td>
								<?php echo $this->form->getInput('document_version'); ?>
							</td>					
						</tr>
                        <?php } ?>
						<tr>
							<td>
								<?php echo $this->form->getLabel('license_id'); ?>
							</td>
							<td>
								<?php echo $this->form->getInput('license_id'); ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo $this->form->getLabel('indicators'); ?>	
							</td>
							<td>
								<?php echo $this->form->getInput('indicators'); ?>		
							</td>
						</tr>
						<?php
							if ($this->config->activate_multilingual_feature) {
							?>
							<tr>	
								<td><?php echo $this->form->getLabel('language'); ?></td>
								<td><?php echo $this->form->getInput('language'); ?></td>
							</tr>      	
							<?php    
							}        
						?>
					</table>
					<BR />
					<table width="100%" class="adminform rightsidetable">
						<tr>
							<th colspan="2">
								<strong><?php echo JText::_('EDOCMAN_IMAGE');?></strong>
							</th>
						</tr>
						<tr>
							<td>
								<?php echo $this->form->getLabel('image'); ?>
							</td>
							<td>
								<?php 
									if ($this->item->image && file_exists(JPATH_ROOT.'/media/com_edocman/document/thumbs/'.$this->item->image)) {								
									?>	
										<img src="<?php echo JUri::root()?>/media/com_edocman/document/thumbs/<?php echo $this->item->image;?>"  class="img-polaroid" />
										<div class="clearfix"></div>
										<input type="checkbox" name="del_image" value="1" title="<?php echo JText::_('EDOCMAN_DEL_IMAGE_DESC'); ?>" />
										<?php echo JText::_('EDOCMAN_DELETE_THUMBNAIL');?>?
										<div class="clearfix"></div>
									<?php	
									}
								?>
								<?php echo $this->form->getInput('image'); ?>
							</td>
						</tr>
					</table>
					<?php
					if(count($fields))
					{
					?>
						<BR />
						<table width="100%" class="adminform rightsidetable">
							<tr>
								<th colspan="2">
									<strong><?php echo JText::_('EDOCMAN_CUSTOM_FIELDS');?></strong>
								</th>
							</tr>
							<tr>
								<TD COLSPAN="2">
									<?php foreach($fields as $field): ?>
										<?php if ($field->hidden): ?>
											<tr>
												<td colspan="2">
													<?php echo $field->input; ?>
												</td>
											</tr>
										<?php else: ?>
											<tr>
												<td class="key">
													<?php echo $field->label; ?>
												</td>
												<td>
													<?php echo $field->input; ?>
												</td>
											</tr>
										<?php endif; ?>
									<?php endforeach; ?>
								</TD>
							</tr>
						</table>
					<?php
					}
					?>
				</div>
			</div>
			<?php
			echo JHtml::_('bootstrap.endTab');
			echo JHtml::_('bootstrap.addTab', 'document', 'permission-page', JText::_('Permissions', true));
			?>
			<div class="tab-pane" id="permission-page">		
				<?php
					if ($this->canDo->get('core.admin'))
					{
						echo $this->form->getInput('rules');
					}						
				?>
			</div>
			<?php
			echo JHtml::_('bootstrap.endTab');
			echo JHtml::_('bootstrap.addTab', 'document', 'publishing-details', JText::_('JGLOBAL_FIELDSET_PUBLISHING', true));
			?>
			<div class="tab-pane" id="publishing-details">			
				<?php echo $this->loadTemplate('options'); ?>			
			</div>
			<?php
			echo JHtml::_('bootstrap.endTab');
			echo JHtml::_('bootstrap.addTab', 'document', 'meta-options', JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS', true));
			?>
			<div class="tab-pane" id="meta-options">
				<?php echo $this->loadTemplate('metadata'); ?>	
			</div>
			<?php
			echo JHtml::_('bootstrap.endTab');
			echo JHtml::_('bootstrap.addTab', 'document', 'indexed-content', JText::_('EDOCMAN_INDEXED_CONTENT', true));
			?>
			<div class="tab-pane" id="indexed-content">
				<?php 
				if($this->item->indexed_content != ""){
					?>
					<div class="row-fluid">
						<div class="span12" style="border:1px solid #CCC;padding:10px;">
							<?php
							echo $this->item->indexed_content;
							?>
						</div>
					</div>
					<?php
				}else{
					echo '<div class="alert alert-no-items">'.JText::_('EDOCMAN_HASNOT_INDEX_CONTENT_OF_THIS_DOCUMENT_WILL_DO_IT_AFTER_SAVING').'</div>';
				}
				?>	
			</div>
            <?php
            echo JHtml::_('bootstrap.endTab');
            if (count($this->plugins))
            {
                $count = 0;

                foreach ($this->plugins as $plugin)
                {
                    $count++;
                    echo JHtml::_('bootstrap.addTab', 'document', 'tab_' . $count, JText::_($plugin['title'], true));
                    echo $plugin['form'];
                    echo JHtml::_('bootstrap.endTab');
                }
            }
            echo JHtml::_('bootstrap.endTabSet');
            ?>
            <input type="hidden" name="task" value="" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
	</div>
</div>
</form>
<script type="text/javascript">
    /**
    jQuery("[name='accesspicker']").click(function(){
        if(jQuery("[name='accesspicker']").val() == '0'){
            jQuery('#presetsdiv').slideDown();
            jQuery('#groupsdiv').slideUp();
        }else{
            jQuery('#presetsdiv').slideUp();
            jQuery('#groupsdiv').slideDown();
        }
    })
     **/

    function updateRadioButton(select_item){
        if(select_item == '0'){
            jQuery('#presetsdiv').slideDown();
            jQuery('#groupsdiv').slideUp();
        }else{
            jQuery('#presetsdiv').slideUp();
            jQuery('#groupsdiv').slideDown();
        }
    }
</script>
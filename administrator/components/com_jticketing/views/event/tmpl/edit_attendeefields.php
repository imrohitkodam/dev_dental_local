<?php
   defined('_JEXEC') or die('Restricted access');

   $document=JFactory::getDocument();
   $com_params	=JComponentHelper::getParams('com_jticketing');
   $this->integration = $com_params->get('integration','','INT');
   if(!empty($this->custom_fields['attendee_fields']))
   	$countoption=count($this->custom_fields['attendee_fields']);
   else
   	$countoption=0;
   ?>
<script type="text/javascript">
   var field_lenght="<?php echo $countoption;?>"

   removetext="<?php echo  JText::_('COM_JTICKETING_REMOVE_TOOLTIP');?>";
   function jt_showoptions(obj,idcount)
   {
   	var idstr=obj.id;
   	var idarr= idstr.replace("ticket_field_type_","");
   	if(obj.value=='single_select' || obj.value=='multi_select' || obj.value=='radio')
   	{
   		techjoomla.jQuery("#ticket_field_default_selected_option_parent_"+idarr).show();
   	}
   	else
   	{
   		var element=techjoomla.jQuery("#ticket_field_default_selected_option_parent_"+idarr);

   		techjoomla.jQuery("#ticket_field_default_selected_option_parent_"+idarr).hide();
   		element.find('textarea[id=\"ticket_field_default_selected_option_'+idarr+'\"]').attr({'value':''});

   	}


   }
   /*add clone script*/
   function addClone_attendee(rId,rClass)
   {
   	var pre=field_lenght;
   	field_lenght++;

   	var removeButton="<div id='remove_btn_div"+field_lenght+"' class='span3 com_jticketing_field_remove_button'>";
   	removeButton+="<button class='btn btn-small btn-danger' type='button' id='remove"+field_lenght+"'";
   	removeButton+="onclick=\"removeClone_attendee('com_jticketing_ticketfields_repeating_block"+field_lenght+"','remove_btn_div"+field_lenght+"');\" title=\"<?php echo JText::_('COM_JTICKETING_REMOVE_TOOLTIP');?>\" >";
   	removeButton+="<i class=\"icon-minus\"></i></button>";
   	removeButton+="</div>";

   	var newElem=techjoomla.jQuery('#'+rId+pre).clone().attr('id',rId+field_lenght);
   	newElem.find('.com_jticketing_field_remove_button').remove();
   	newElem.find('input[name=\"ticket_field[' + pre + '][id]\"]').attr({'name': 'ticket_field[' + field_lenght + '][id]','value':''});

   	newElem.find('input[name=\"ticket_field[' + pre + '][label]\"]').attr({'name': 'ticket_field[' + field_lenght + '][label]','value':''});
   	newElem.find('textarea[name=\"ticket_field[' + pre + '][default_selected_option]\"]').attr({'name': 'ticket_field[' + field_lenght + '][default_selected_option]','value':''});
   	newElem.find('select[name=\"ticket_field[' + pre + '][type]\"]').attr({'name': 'ticket_field[' + field_lenght + '][type]','value':''});
   	newElem.find('select[name=\"ticket_field[' + pre + '][required]\"]').attr({'name': 'ticket_field[' + field_lenght + '][required]'}); //newElem.find('img[src="localhost/jt315/administrator/components/com_jticketing_field/images/default.png"]').attr({'src':'localhost/jt315/administrator/components/com_jticketing_field/images/nodefault.png'});

   	/*incremnt id*/
   	newElem.find('input[id=\"ticket_field_label_'+pre+'\"]').attr({'id': 'ticket_field_label_'+field_lenght,'value':''});
   	newElem.find('textarea[id=\"ticket_field_default_selected_option_'+pre+'\"]').attr({'id': 'ticket_field_default_selected_option_'+field_lenght,'value':''});
   	newElem.find('select[id=\"ticket_field_type_'+pre+'\"]').attr({'id': 'ticket_field_type_'+field_lenght,'value':''});
   	newElem.find('select[id=\"ticket_field_required_'+pre+'\"]').attr({'id': 'ticket_field_required_'+field_lenght,'value':''});
   	newElem.find('[id=\"ticket_field_default_selected_option_parent_'+pre+'\"]').attr({'id': 'ticket_field_default_selected_option_parent_'+field_lenght});

  techjoomla.jQuery(newElem).children('.com_jticketing_repeating_block').children('.jticketing-form-group').children('.jticketing-controls').children('').each(function(){
   		var kid=jQuery(this);

   		if(kid.attr('id')!=undefined)
   		{

   			//var idN=kid.attr('id');
   			//kid.attr('id',idN+num).attr('id',idN+num);

   			if(kid.is('input:text') )
   			{

   				/*if input type is text then empty value*/
   				kid.val("");
   			}
   		}

   	});

   	techjoomla.jQuery('#'+rId+pre).after(newElem);

   	newElem.find('.com_jticketing_repeating_block').after(removeButton);
   	techjoomla.jQuery("#ticket_field_default_selected_option_parent_"+field_lenght).hide();


   }

   function removeClone_attendee(rId,r_btndivId)
   {
   	field_lenght--;
   	techjoomla.jQuery('#'+rId).remove();
   	techjoomla.jQuery('#'+r_btndivId).remove();
   }
</script>
<?php
   if(JVERSION < '3.0' && $this->integration != 2)
   {
   ?>
<div class="techjoomla-bootstrap">
   <?php
      } ?>
   <div class="row-fluid">
      <div class="alert alert-info alert-help-inline"><?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_CORE');?></div>
      <table class="table table-hover table-striped table-bordered" width="60%">
         <tr>
            <th>
               <?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_TITLE');?>
            </th>
            <th>
               <?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_TYPE');?>
            </th>
         </tr>
         <?php
            //$other_fields=array_merge($this->custom_fields['core_fields'],$this->custom_fields['universal_attendee_fields']);
            if(!empty($this->custom_fields['universal_attendee_fields']) and !empty($this->custom_fields['attendee_fields']))
            {
            	$other_fields=array_merge((array)$this->custom_fields['core_fields'],(array)$this->custom_fields['universal_attendee_fields'],(array)$this->custom_fields['attendee_fields']);
            }

            else if(!empty($this->custom_fields['attendee_fields']))
            {
            	$other_fields=array_merge((array)$this->custom_fields['core_fields'],(array)$this->custom_fields['attendee_fields']);
            }

            else if(!empty($this->custom_fields['universal_attendee_fields']))
            {
            	$other_fields=array_merge((array)$this->custom_fields['core_fields'],(array)$this->custom_fields['universal_attendee_fields']);
            }
            else
            {
            	$other_fields=$this->custom_fields['core_fields'];
            }

            if(!empty($other_fields))
            {

            	foreach($other_fields AS $fields)
            	{


            ?>
         <tr>
            <td>
               <?php echo $fields->label; ?>
            </td>
            <td>
               <?php echo $fields->type; ?>
            </td>
         </tr>
         <!--<div class="well span10" style="margin-left:0px;!important">
            <div></div>
            <div class="control-group jticketing-form-group">
            	<label  class="control-label" title="<?php echo JText::_('COM_JTICKETING_TICKET_FIELD_TITLE');?>" ><?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_TITLE');?></label>
            	<div class="controls jticketing-controls">
            		<?php echo $fields->label; ?>
            	</div>
            </div>
            <div class="control-group jticketing-form-group">
            	<label  class="control-label" title="<?php echo JText::_('COM_JTICKETING_TICKET_FIELD_TYPE');?>" ><?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_TYPE');?></label>
            	<div class="controls jticketing-controls">
            		<?php echo $fields->type; ?>
            	</div>
            </div>
            </div>-->
         <?php
            }
            }
            ?>
      </table>
   </div>
   <div class="row-fluid">
      <div class="alert alert-info alert-help-inline"><?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_CUSTOM_FIELDS_ATTENDEE');?></div>
      <?php
         $i=0;
         if(!empty($this->custom_fields['attendee_fields']))
         {
         	foreach($this->custom_fields['attendee_fields'] AS $fields)
         	{
         		$fieldtype=$fields->type;
         		if(($fieldtype=='single_select') or ($fieldtype=='multi_select') or ($fieldtype=='radio'))
         		{
         			$field_type_style="style='display:block'";
         		}
         		else
         		{
         			$field_type_style="style='display:none'";
         		}


         	?>
      <div class="com_jticketing_ticketfields_repeating_block " id="com_jticketing_ticketfields_repeating_block<?php echo $i?>">
         <div class="com_jticketing_repeating_block form-horizontal span8">
            <input type="hidden" value="<?php echo $fields->id?>" placeholder=""  	name="ticket_field[<?php echo $i; ?>][id]" id="ticket_field_id_<?php echo $i; ?>" >
            <div class="control-group jticketing-form-group">
               <label  class="control-label" title="<?php echo JText::_('COM_JTICKETING_TICKET_FIELD_TITLE');?>" ><?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_TITLE');?></label>
               <div class="controls jticketing-controls">
                  <input type="text" value="<?php echo $fields->label?>" placeholder="<?php echo JText::_('COM_JTICKETING_TICKET_FIELD_TITLE');?>" 				  	name="ticket_field[<?php echo $i; ?>][label]" id="ticket_field_label_<?php echo $i; ?>" >
               </div>
            </div>
            <div class="control-group jticketing-form-group">
               <label  class="control-label" title="<?php echo JText::_('COM_JTICKETING_TICKET_FIELD_TYPE');?>" ><?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_TYPE');?></label>
               <div class="controls jticketing-controls">
                  <select class="chzn-done" data-chosen="com_jticketing" name="ticket_field[<?php echo $i;?>][type]" id="ticket_field_type_<?php echo $i;?>" onchange="jt_showoptions(this,'<?php echo $i;?>')">
                     <option <?php if(isset($fieldtype) and ($fieldtype=="text")) {echo "selected=selected";}?> value="text"><?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_TYPE_TEXT');?></option>
                     <option <?php if(isset($fieldtype) and ($fieldtype=="textarea")){  echo "selected=selected";}?> value="textarea"><?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_TYPE_TEXTAREA');?></option>
                     <option <?php if(isset($fieldtype) and ($fieldtype=="single_select")){ echo "selected=selected";}?>  value="single_select"><?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_TYPE_SINGLE_SELECT');?></option>
                     <option <?php if(isset($fieldtype) and ($fieldtype=="multi_select")){ echo "selected=selected";}?>  value="multi_select"><?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_TYPE_MULTIPLE_SELECT');?></option>
                     <option <?php if(isset($fieldtype) and ($fieldtype=="radio")) {echo "selected=selected";}?>  value="radio"><?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_TYPE_RADIO');?></option>
                     <option <?php if(isset($fieldtype) and ($fieldtype=="calendar")) {echo "selected=selected";}?>  value="calendar"><?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_TYPE_DATE');?></option>
                  </select>
               </div>
            </div>
            <div class="control-group jticketing-form-group" id="ticket_field_default_selected_option_parent_<?php echo $i;?>" <?php echo $field_type_style;?> >
               <label  class="control-label" title="<?php echo JText::_('COM_JTICKETING_TICKET_FIELD_DEFAULT_OPTION_LABEL');?>" ><?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_DEFAULT_OPTION_LABEL');?></label>
               <div class="controls jticketing-controls">
                  <textarea  	name="ticket_field[<?php echo $i; ?>][default_selected_option]" id="ticket_field_default_selected_option_<?php echo $i; ?>" ><?php echo $fields->default_selected_option;?></textarea>
               </div>
            </div>
            <div class="control-group jticketing-form-group">
               <label  class="control-label" title="<?php echo JText::_('COM_JTICKETING_TICKET_FIELD_REQUIRED');?>" ><?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_REQUIRED');?></label>
               <div class="controls jticketing-controls">
                  <select class="chzn-done" data-chosen="com_jticketing" id="ticket_field[<?php echo $i;?>][required]" name="ticket_field[<?php echo $i;?>][required]">
                     <option <?php if($fields->required==1) echo "selected=selected";?> value="1"><?php echo JText::_('COM_JTICKETING_YES');?></option>
                     <option <?php if($fields->required==0) echo "selected=selected";?> value="0"><?php echo JText::_('COM_JTICKETING_NO');?></option>
                  </select>
               </div>
            </div>
         </div>
         <div class="com_jticketing_field_remove_button   span3" id="remove_btn_div0">
            <button title="COM_JTICKETING_REMOVE_TOOLTIP" onclick="removeClone_attendee('com_jticketing_ticketfields_repeating_block<?php echo $i?>','com_jticketing_ticketfields_repeating_block<?php echo $i?>');" id="remove0" type="button" class="btn btn-small btn-danger"><i class="icon-minus"></i>
            </button>
         </div>
         <div style="clear:both"></div>
         <hr class="hr hr-condensed">
      </div>
      <div style="clear:both"></div>
      <?php
         $i++;
         }
         }
         ?>
      <div class="com_jticketing_ticketfields_repeating_block" id="com_jticketing_ticketfields_repeating_block<?php echo $i?>">
         <div class="com_jticketing_repeating_block form-horizontal span8">
            <input type="hidden" value="" placeholder=""  	name="ticket_field[<?php echo $i; ?>][id]" id="ticket_field_id_<?php echo $i; ?>" >
            <div class="control-group jticketing-form-group">
               <label  class="control-label" title="<?php echo JText::_('COM_JTICKETING_TICKET_FIELD_TITLE');?>" ><?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_TITLE');?></label>
               <div class="controls jticketing-controls">
                  <input type="text" value="" placeholder="<?php echo JText::_('COM_JTICKETING_TICKET_FIELD_TITLE');?>" 				  	name="ticket_field[<?php echo $i; ?>][label]" id="ticket_field_label_<?php echo $i; ?>" >
               </div>
            </div>
            <div class="control-group jticketing-form-group">
               <label  class="control-label" title="<?php echo JText::_('COM_JTICKETING_TICKET_FIELD_TYPE');?>" ><?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_TYPE');?></label>
               <div class="controls jticketing-controls">
                  <select class="chzn-done" data-chosen="com_jticketing" name="ticket_field[<?php echo $i;?>][type]" id="ticket_field_type_<?php echo $i;?>" onchange="jt_showoptions(this,'<?php echo $i;?>')">
                     <option value="text"><?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_TYPE_TEXT');?></option>
                     <option value="textarea"><?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_TYPE_TEXTAREA');?></option>
                     <option value="single_select"><?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_TYPE_SINGLE_SELECT');?></option>
                     <option value="multi_select"><?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_TYPE_MULTIPLE_SELECT');?></option>
                     <option value="calendar"><?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_TYPE_DATE');?></option>
                     <option value="radio"><?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_TYPE_RADIO');?></option>
                  </select>
               </div>
            </div>
            <div class="control-group " id="ticket_field_default_selected_option_parent_<?php echo $i;?>" style="display:none;">
               <label  class="control-label" title="<?php echo JText::_('COM_JTICKETING_TICKET_FIELD_DEFAULT_OPTION_LABEL');?>" ><?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_DEFAULT_OPTION_LABEL');?></label>
               <div class="controls jticketing-controls">
                  <textarea  	name="ticket_field[<?php echo $i; ?>][default_selected_option]" id="ticket_field_default_selected_option_<?php echo $i; ?>" ></textarea>
               </div>
            </div>
                <div class="control-group">
               <label  class="control-label" title="<?php echo JText::_('COM_JTICKETING_TICKET_FIELD_REQUIRED');?>" ><?php  echo JText::_('COM_JTICKETING_TICKET_FIELD_REQUIRED');?></label>
               <div class="controls jticketing-controls">
                  <select class="chzn-done" data-chosen="com_jticketing" id="ticket_field[<?php echo $i;?>][required]" name="ticket_field[<?php echo $i;?>][required]">
                     <option  value="0"><?php echo JText::_('COM_JTICKETING_NO');?></option>
                     <option  value="1"><?php echo JText::_('COM_JTICKETING_YES');?></option>
                  </select>
               </div>
            </div>
         </div>
         <div class="com_jticketing_ticketfields_add_button span3">
            <button title="" onclick="addClone_attendee('com_jticketing_ticketfields_repeating_block','ticketfields_container');" id="add_field<?php echo $i?>" type="button" class="btn btn-small btn-success" aria-invalid="false">
            <i class="icon-plus"></i>
            </button>
         </div>
         <div style="clear:both"></div>
         <hr class="hr hr-condensed">
         <div style="clear:both"></div>
      </div>
   </div>
   <?php
      if(JVERSION < '3.0' && $this->integration != 2)
      {
      ?>
</div>
<!--BOOTSTRAP ENDS-->
<?php
   } ?>

<?php
/**
* @version    SVN: <svn_id>
* @package    JTicketing
* @author     Techjoomla <extensions@techjoomla.com>
* @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
* @license    GNU General Public License version 2 or later.
*/

defined('_JEXEC') or die;

// Load tjstrapper in admin
$mainframe = JFactory::getApplication();
$path = JPATH_SITE . '/components/com_jticketing/helpers/main.php';

if (!class_exists('Jticketingmainhelper'))
{
	JLoader::register('Jticketingmainhelper', $path);
	JLoader::load('Jticketingmainhelper');
}

$jticketingmainhelper  = new jticketingmainhelper;
$language_const = $jticketingmainhelper->getLanguageConstant();
?>
<script type="text/javascript">
   techjoomla.jQuery(document).ready(function()
   {
   	/* Skip country and state dropdowns from chosen js and css*/
   	techjoomla.jQuery('.jticket_access').attr('data-chosen', 'com_jticketing');

   });
   function checkforalpha(el)
   {

   	var i =0;
   	for(i=0;i<el.value.length;i++)
   	{
   		if((el.value.charCodeAt(i) > 64 && el.value.charCodeAt(i) < 92) || (el.value.charCodeAt(i) > 96 && el.value.charCodeAt(i) < 123)) { alert("<?php echo JText::_('COM_JTICKETING_ENTER_NUMERICS'); ?>"); el.value = el.value.substring(0,i); return;}
   	}

   	if(el.value<0)
   	{
   		alert("<?php echo JText::_('COM_JTICKETING_ENTER_AMOUNT_GR_ZERO'); ?>");
   		el.value = 0;

   	}
   }

   function togglefield(field,classnm)
   {
	   var divf = techjoomla.jQuery('#'+field.id);
		jQuery(divf).parents('.com_jticketing_repeating_block').children('.'+classnm).each(function()
		{

			var kid1=jQuery(this);
			kid1.toggle();
		})

   }

</script>
<?php
   if (file_exists(JPATH_ROOT . '/media/techjoomla_strapper/tjstrapper.php'))
   {
   	require_once JPATH_ROOT . '/media/techjoomla_strapper/tjstrapper.php';
   	TjStrapper::loadTjAssets('com_jticketing');
   }

   $document            = JFactory::getDocument();
   $db                  = JFactory::getDBO();
   $com_params          = JComponentHelper::getParams('com_jticketing');
   $this->integration   = $com_params->get('integration', '', 'INT');
   $siteadmin_comm_per  = $com_params->get('siteadmin_comm_per');
   $siteadmin_comm_flat = $com_params->get('siteadmin_comm_flat');
   $deposite_config     = $com_params->get('deposite_pay');
   $gateways            = $com_params->get('gateways');
   $handle_transactions = $com_params->get('handle_transactions');
   $currency            = $com_params->get('currency');
   $default_accesslevels 	= $com_params->get('default_accesslevels');
   $show_access_level 		= $com_params->get('show_access_level');
   $style="";
   $unlimited_seats_options[] = JHtml::_('select.option', 0, JText::_('COM_JTICKETING_UNLIMITED_SEATS_NO'));
   $unlimited_seats_options[] = JHtml::_('select.option', 1, JText::_('COM_JTICKETING_UNLIMITED_SEATS_YES'));

   if (!$show_access_level)
   {
   	$style="display:none;";
   }
   // Add JS for adding clone
   $addfields_js = JUri::root() . 'components/com_jticketing/assets/js/addfields.js';
   $document->addScript($addfields_js);
   $user                 = JFactory::getUser();
   $jticketingmainhelper = new jticketingmainhelper;
   $input                = JFactory::getApplication()->input;
   $Itemid               = $input->get('Itemid', '');

   if (empty($eventid))
   {
   	$eventid = $input->get('id', '', 'GET');
   }

   $ticketprice = "";
   $display     = "display:none;";
   $typecnt     = 1;

   if (!empty($eventid))
   {
   	$xrefid = $jticketingmainhelper->getEventrefid($eventid);

   	if ($xrefid)
   	{
   		$db    = JFactory::getDBO();
   		$query = "SELECT id,paypal_email
   		 FROM #__jticketing_integration_xref	WHERE id = {$xrefid}";
   		$db->setQuery($query);
   		$eventpresent = $db->loadObject();

   		if (!empty($xrefid))
   		{
   			$query = "SELECT * FROM #__jticketing_types	WHERE eventid = " . $xrefid;
   			$db->setQuery($query);
   			$tickettypes  = $db->loadObjectlist();
   			$typecnt      = count($tickettypes);
   			$paypal_email = $eventpresent->paypal_email;

   			if (!empty($tickettypes))
   			{
   				$display = "display:block;";
   			}
   		}
   		else
   		{
   			$display = "display:block;";
   		}
   	}
   }
   ?>
<!--START HTML PART-->
<?php
   if (JVERSION < '3.0' && $this->integration != 2)
   {
   ?>
<div class="techjoomla-bootstrap">
   <?php
      }

      $j = 0;


      ?>
   <div  id="container_add_fields_ticket" >
      <div class="row-fluid">
         <!--EMAIL PART START-->
         <div class="span8">
            <div class="control-group jticketing-form-group ">
				<div class="control-label">
					<label label-default for="paypal_email">
						<?php echo JHtml::tooltip(JText::_('JT_TICKET_TYPE_PAYPAL_MAIL_TOOLTIP'), JText::_('JT_TICKET_TYPE_PAYPAL_MAIL_LABEL'), '', JText::_('JT_TICKET_TYPE_PAYPAL_MAIL_LABEL')); ?>
					</label>
				</div>
               <div class="controls jticketing-controls">
                  <input type="text" id="paypal_email" class="  " name="paypal_email"
                     placeholder="<?php	echo JText::_('JT_PAYPAL_EMAIL_DESC');?>"
                     value="<?php   if (!empty($paypal_email)){   	echo $paypal_email;}?>">
               </div>
            </div>
         </div>
         <!--FORM-HORIZONTAL DIV ENDS-->
      </div>
      <!--ROW-FLUID DIV ENDS-->
      <hr class="hr hr-condensed">
      <!--EMAIL PART OVER-->
      <?php
         // If ALREADY PRESENT TICKET TYPES...PREFILL THEM
         if (isset($tickettypes) && !empty($tickettypes))
         {
         ?>
      <div class="row-fluid">
         <?php
            foreach ($tickettypes as $tickettype)
            {
            ?>
         <div id="jticketing_container<?php		echo $j;?>" class="jticketing_container  row-fluid " style="padding-top: 10px;" >
            <div class="com_jticketing_repeating_block span8" >
               <div class="control-group jticketing-form-group" >
				<div class="control-label">
					<label label-default for="ticket_type_title<?php echo $j; ?>">
								<?php echo JHtml::tooltip(JText::_('JT_TICKET_TYPE_TITLE_TOOLTIP_LABEL'), JText::_('JT_TICKET_TYPE_TITLE_LABEL'), '', JText::_('JT_TICKET_TYPE_TITLE_LABEL') . ' *'); ?>
					</label>
				</div>
                  <div class="controls jticketing-controls">
                     <div class="">
                        <input type="text" id="ticket_type_title<?php	echo $j;?>" class="  required " name="ticket_type_title[]" placeholder="<?php
                           echo JText::_('JT_TICKET_TYPE_TITLE_TOOLTIP');?>" value="<?php		echo $tickettype->title;?>">
                        <input type="hidden" value="1" name="jt_editflag" id="jt_editflag">
                        <input type="hidden" id="ticket_type_id<?php		echo $j;?>" class="  " name="ticket_type_id[]" placeholder="Ticket type id" value="<?php
                           echo $tickettype->id;?>">
                     </div>
                  </div>
               </div>
               <!--ticket_type_title DIV ENDS-->
               <div class="control-group jticketing-form-group">
				<div class="control-label">
					<label label-default for="ticket_type_desc<?php echo $j;?>">
								<?php echo JHtml::tooltip(JText::_('JT_TICKET_TYPE_DESC_TOOLTIP'), JText::_('JT_TICKET_TYPE_DESC_LABEL'), '', JText::_('JT_TICKET_TYPE_DESC_LABEL')); ?>
					</label>
				</div>
                  <div class="controls jticketing-controls">
                     <div class="">
                        <input type="text" id="ticket_type_desc<?php		echo $j;?>" class="" name="ticket_type_desc[]"       placeholder="<?php		echo JText::_('JT_TICKET_TYPE_DESC_TOOLTIP');?>"
                           value="<?php		echo $tickettype->desc;?>">
                     </div>
                  </div>
               </div>
               <!--ticket_type_desc DIV ENDS-->
               <div class="control-group jticketing-form-group">
				<div class="control-label">
					<label label-default for="ticket_type_state">
								<?php echo JHtml::tooltip(JText::_('JT_TICKET_TYPE_STATE_TOOLTIP'), JText::_('JT_TICKET_TYPE_STATE_LABEL'), '', JText::_('JT_TICKET_TYPE_STATE_LABEL')); ?>
					</label>
				 </div>
                  <div class="controls ">
                     <div class="">
                        <?php
                           $state_options = array();
                           $state_options[] = JHtml::_('select.option', '1', JText::_('COM_JTICKETING_PUBLISH'));
                           $state_options[] = JHtml::_('select.option', '0', JText::_('COM_JTICKETING_UNPUBLISH'));

                           if ($tickettype->state==1)
                           {
                           	echo JHtml::_('select.genericlist', $state_options, "ticket_type_state[]", 'class="chzn-done jticket_state" data-chosen="com_jticketing"', "value", "text", $tickettype->state);
                           }
                           else
                           {
                           	echo JHtml::_('select.genericlist', $state_options, "ticket_type_state[]", 'class="chzn-done jticket_state" data-chosen="com_jticketing"', "value", "text",0);
                           }

                           ?>
                     </div>
                  </div>
               </div>
               <div class="control-group jticketing-form-group">
				<div class="control-label">
					<label label-default for="ticket_type_price<?php echo $j; ?>">
								<?php echo JHtml::tooltip(JText::_('JT_TICKET_TYPE_PRICE_TOOLTIP'), JText::_('JT_TICKET_TYPE_PRICE_LABEL'), '', JText::_('JT_TICKET_TYPE_PRICE_LABEL')); ?>
					</label>
				</div>
                  <div class="controls jticketing-controls">
                     <div class="input-append">
                        <input type="text"  id="ticket_type_price<?php		echo $j;?> " onkeyup="checkforalpha(this)" class=" required validate-numeric input-small"
                       name="ticket_type_price[]" placeholder="<?php
                           echo JText::_('JT_TICKET_TYPE_PRICE_TOOLTIP');?>"
                           value="<?php	echo $tickettype->price;?>"><?php		echo $currency;?></span>
                     </div>
                     <span class="help-inline"><?php		echo JText::_('COM_JTICKETING_FREE_TICKET_MSG');?></span>
                  </div>
               </div>
               <!--ticket_type_price DIV ENDS-->
               <!--ticket_type_unlimited Seats-->
               <div class="control-group jticketing-form-group" >
				<div class="control-label">
					<label label-default for="ticket_type_unlimited_seats_<?php echo $j ?>">
								<?php echo JHtml::tooltip(JText::_('JT_TICKET_TYPE_SEATS_AVAILABLE_TOOLTIP'), JText::_('JT_TICKET_TYPE_SEATS_AVAILABLE_LABEL'), '', JText::_('JT_TICKET_TYPE_SEATS_AVAILABLE_LABEL')); ?>
					</label>
				</div>
                  <div class="controls jticketing-controls">
                     <div class="">
                        <?php
                           if ($tickettype->unlimited_seats)
                           {
							   $avalaible_class="ticket_type_available_field_hide";
                           	echo JHtml::_('select.genericlist', $unlimited_seats_options, "ticket_type_unlimited_seats[]", 'class="chzn-done jticket_access"onchange="togglefield(this,\'ticket_type_available_field\')" data-chosen="com_jticketing"', "value", "text", $tickettype->unlimited_seats,"ticket_type_unlimited_seats_".$j);
                           }
                           else
                           {
							   $avalaible_class="";

                           		echo JHtml::_('select.genericlist', $unlimited_seats_options, "ticket_type_unlimited_seats[]", 'class="chzn-done jticket_access" onchange="togglefield(this,\'ticket_type_available_field\')" data-chosen="com_jticketing"', "value", "text",0,"ticket_type_unlimited_seats_".$j);
                           }

                           ?>
                     </div>
                  </div>
               </div>
               <!--ticket_type_unlimited Seats DIV ENDS-->
               <div class="control-group jticketing-form-group ticket_type_available_field <?php echo $avalaible_class;?>">
               	<div class="control-label">
					<label label-default for="ticket_type_available<?php	echo $j;?>">
								<?php echo JHtml::tooltip(JText::_('JT_TICKET_TYPE_LIMITED_SEATS_TOOLTIP'), JText::_('JT_TICKET_TYPE_LIMITED_SEATS_LABEL'), '', JText::_('JT_TICKET_TYPE_LIMITED_SEATS_LABEL')); ?>
					</label>
				</div>
                  <div class="controls ">
                     <div>
                        <input type="text"  id="ticket_type_available<?php	echo $j;?>" class="availablecnt" name="ticket_type_available[]"  placeholder="<?php
                           echo JText::_('JT_TICKET_TYPE_AVAILABLE_TOOLTIP');?>" value="<?php	if ($tickettype->count<0) echo "0";
                           else	echo $tickettype->count;?>">
                     </div>
                  </div>
               </div>
               <!--ticket_type_available DIV ENDS-->
               <!--ticket_type_access DIV starts-->
               <div class="control-group jticketing-form-group" style="<?php echo $style;?>">
					<div class="control-label">
						<label label-default for="ticket_type_access<?php	echo $j;?>">
									<?php echo JHtml::tooltip(JText::_('JFIELD_ACCESS_DESC'), JText::_('JFIELD_ACCESS_DESC'), '', JText::_('JFIELD_ACCESS_LABEL')); ?>
						</label>
					</div>
                  <div class="controls jticketing-controls">
                     <div class="">
                        <?php
                           $accesslevels         = $jticketingmainhelper->getAccessLevels();
                           $accesslevels_options = array();

                           if ($accesslevels)
                           {
                           	foreach ($accesslevels AS $accesslevel)
                           	{
                           		$accesslevels_options[] = JHtml::_('select.option', $accesslevel->id, $accesslevel->title);
                           	}
                           }

                           if ($tickettype->access)
                           {
                           	echo JHtml::_('select.genericlist', $accesslevels_options, "ticket_type_access[]", 'class="chzn-done jticket_access" id="ticket_type_access'.$j.'" data-chosen="com_jticketing"', "value", "text", $tickettype->access, "ticket_type_access".$j );
                           }
                           else
                           {
                           	if ($default_accesslevels)
                           	{
                           		echo JHtml::_('select.genericlist', $accesslevels_options, "ticket_type_access[]", 'class="chzn-done jticket_access" id="ticket_type_access'.$j.'" data-chosen="com_jticketing"', "value", "text",$default_accesslevels, "ticket_type_access".$j);
                           	}
                           	else
                           	{
                           		echo JHtml::_('select.genericlist', $accesslevels_options, "ticket_type_access[]", 'class="chzn-done jticket_access" id="ticket_type_access'.$j.'" data-chosen="com_jticketing"', "value", "text", "ticket_type_access".$j);
                           	}

                           }
                           ?>
                        <span class="help-inline"><?php
                           echo JText::_('COM_JTICKETING_ACCESS_MSG');
                           ?></span>
                     </div>
                  </div>
               </div>
               <!--ticket_type_access DIV ENDS-->
            </div>
            <!--com_jticketing_repeating_block ENDS-->
            <!--REMOVE BUTTON CODE FOR EDIT CONDITION-->
            <div class="" >
               <button class="btn btn-small btn-danger" type="button" id="remove<?php	echo $j;?>"
                  onclick="removeClone('jticketing_container<?php		echo $j;?>','jticketing_container<?php		echo $j;?>');" title="<?php		echo JText::_('COM_JTICKETING_REMOVE_TOOLTIP');
                     ?>" >
               <i class="icon-minus"></i>
               </button>
            </div>
            <!--REMOVE BUTTON CODE FOR EDIT CONDITION ENDS-->
            <div style="clear:both"></div>
            <hr class="hr hr-condensed">
         </div>
         <!--jticketing_container DIV ENDS-->
         <div style="clear:both"></div>
         <?php
            $j++;
            }
            ?>
      </div>
      <!--ROW-FLUID ENDS-->
      <?php
         }


         ?>
      <!--IF NOT EDIT PRINT A PLAIN DIV-->
      <div class="row-fluid">
         <div id="jticketing_container"  class="jticketing_container">
            <div class="com_jticketing_repeating_block span8" >
               <div class="control-group jticketing-form-group" >
				<div class="control-label">
					<label label-default for="ticket_type_title<?php echo $j; ?>">
								<?php echo JHtml::tooltip(JText::_('JT_TICKET_TYPE_TITLE_TOOLTIP_LABEL'), JText::_('JT_TICKET_TYPE_TITLE_LABEL'), '', JText::_('JT_TICKET_TYPE_TITLE_LABEL') . ' *'); ?>
					</label>
				</div>
                  <div class="controls jticketing-controls">
                     <div class="" id="edit_ticket_title">
                        <input type="hidden" value="1" name="jt_editflag" id="jt_editflag">
                        <input type="text" class="edit_ticket_title" id="ticket_type_title<?php
                           echo $j;
                           ?>" name="ticket_type_title[]" placeholder="<?php
                           echo JText::_('JT_TICKET_TYPE_TITLE_TOOLTIP');
                           ?>" value="" required="required">
                     </div>
                     <input type="hidden" id="ticket_type_id<?php
                        echo $j;
                        ?>"  name="ticket_type_id[]" value="">
                  </div>
               </div>
               <!--ticket_type_title DIV ENDS-->
               <div class="control-group jticketing-form-group">
				<div class="control-label">
					<label label-default for="ticket_type_desc<?php echo $j;?>">
								<?php echo JHtml::tooltip(JText::_('JT_TICKET_TYPE_DESC_TOOLTIP'), JText::_('JT_TICKET_TYPE_DESC_LABEL'), '', JText::_('JT_TICKET_TYPE_DESC_LABEL')); ?>
					</label>
				</div>
                  <div class="controls jticketing-controls">
                     <div class="">
                        <input type="text" id="ticket_type_desc<?php
                           echo $j;
                           ?>"  name="ticket_type_desc[]" placeholder="<?php
                           echo JText::_('JT_TICKET_TYPE_DESC_TOOLTIP');
                           ?>" value="">
                     </div>
                  </div>
               </div>
               <!--ticket_type_desc DIV ENDS-->
               <div class="control-group jticketing-form-group">
				<div class="control-label">
					<label label-default for="ticket_type_state">
								<?php echo JHtml::tooltip(JText::_('JT_TICKET_TYPE_STATE_TOOLTIP'), JText::_('JT_TICKET_TYPE_STATE_LABEL'), '', JText::_('JT_TICKET_TYPE_STATE_LABEL')); ?>
					</label>
				 </div>
                  <div class="controls ">
                     <div class="">
                        <?php
                           $state_options = array();
                           $state_options[] = JHtml::_('select.option', '1', JText::_('COM_JTICKETING_PUBLISH'));
                           $state_options[] = JHtml::_('select.option', '0', JText::_('COM_JTICKETING_UNPUBLISH'));

                           if (isset($tickettype->state) and $tickettype->state==1)
                           {
                           	echo JHtml::_('select.genericlist', $state_options, "ticket_type_state[]", 'class="chzn-done jticket_state" data-chosen="com_jticketing"', "value", "text", $tickettype->state);
                           }
                           else
                           {
                           	echo JHtml::_('select.genericlist', $state_options, "ticket_type_state[]", 'class="chzn-done jticket_state" data-chosen="com_jticketing"', "value", "text", 1);
                           }

                           ?>
                     </div>
                  </div>
               </div>
               <div class="control-group jticketing-form-group">
				<div class="control-label">
					<label label-default for="ticket_type_price<?php echo $j; ?>">
								<?php echo JHtml::tooltip(JText::_('JT_TICKET_TYPE_PRICE_TOOLTIP'), JText::_('JT_TICKET_TYPE_PRICE_LABEL'), '', JText::_('JT_TICKET_TYPE_PRICE_LABEL')); ?>
					</label>
				</div>
                  <div class="controls jticketing-controls">
                     <div class="input-append">
                        <input type="text"  id="ticket_type_price<?php
                           echo $j;
                           ?>" class="validate-numeric input-small" onkeyup="checkforalpha(this)" name="ticket_type_price[]" placeholder="<?php
                           echo JText::_('JT_TICKET_TYPE_PRICE_TOOLTIP');
                           ?>" value="0"><span class="jt_add_on add-on"><?php
                           echo $currency;
                           ?></span>
                     </div>
                     <span class="help-inline"><?php
                        echo JText::_('COM_JTICKETING_FREE_TICKET_MSG');
                        ?></span>
                  </div>
               </div>
               <!--ticket_type_price DIV ENDS-->
               <?php
                  if ($deposite_config == 1)
                  {
                  ?>
               <div class="control-group jticketing-form-group">
                  <div class="controls jticketing-controls">
                     <div class="">
                        <input type="text"  id="ticket_type_deposit_price<?php
                           echo $j;
                           ?>" class=" validate-numeric  "  name="ticket_type_deposit_price[]" onkeyup="checkforalpha(this)" placeholder="<?php
                           echo $ticket_type_deposit_price;
                           ?>" value="">
                     </div>
                  </div>
               </div>
               <!--ticket_type_deposit_price DIV ENDS-->
               <?php
                  }
                  ?>
               <!--ticket_type_unlimited Seats-->
               <div class="control-group jticketing-form-group " >
				<div class="control-label">
					<label label-default for="ticket_type_unlimited_seats_<?php echo $j ?>">
								<?php echo JHtml::tooltip(JText::_('JT_TICKET_TYPE_SEATS_AVAILABLE_TOOLTIP'), JText::_('JT_TICKET_TYPE_SEATS_AVAILABLE_LABEL'), '', JText::_('JT_TICKET_TYPE_SEATS_AVAILABLE_LABEL')); ?>
					</label>
				</div>
                  <div class="controls jticketing-controls">
                     <div class="">
                        <?php
                           		echo JHtml::_('select.genericlist', $unlimited_seats_options, "ticket_type_unlimited_seats[]", 'class="chzn-done jticket_access"  id="ticket_type_unlimited_seats_'.$j.'" onchange="togglefield(this,\'ticket_type_available_field\')" data-chosen="com_jticketing"', "value", "text",0,"ticket_type_unlimited_seats_".$j);
                           ?>
                     </div>
                  </div>
               </div>
               <!--ticket_type_unlimited Seats DIV ENDS-->
               <div class="control-group jticketing-form-group ticket_type_available_field ">
				<div class="control-label">
					<label label-default for="ticket_type_available<?php	echo $j;?>">
								<?php echo JHtml::tooltip(JText::_('JT_TICKET_TYPE_LIMITED_SEATS_TOOLTIP'), JText::_('JT_TICKET_TYPE_LIMITED_SEATS_LABEL'), '', JText::_('JT_TICKET_TYPE_LIMITED_SEATS_LABEL')); ?>
					</label>
				</div>
                  <div class="controls ">
                     <div>
                        <input type="text"  id="ticket_type_available<?php	echo $j;?>" class="availablecnt" name="ticket_type_available[]"  placeholder="<?php
                           echo JText::_('JT_TICKET_TYPE_AVAILABLE_TOOLTIP');?>" value="0">
                     </div>
                  </div>
               </div>
               <!--ticket_type_available DIV ENDS-->
               <!--ticket_type_access DIV starts-->
               <div class="control-group jticketing-form-group"  style="<?php echo $style;?>">
               	<div class="control-label">
					<label label-default for="ticket_type_access<?php	echo $j;?>">
								<?php echo JHtml::tooltip(JText::_('JFIELD_ACCESS_DESC'), JText::_('JFIELD_ACCESS_DESC'), '', JText::_('JFIELD_ACCESS_LABEL')); ?>
					</label>
				</div>
                  <div class="controls jticketing-controls">
                     <div class="">
                        <?php
                           $accesslevels         = $jticketingmainhelper->getAccessLevels();
                           $accesslevels_options = array();

                           if ($accesslevels)
                           {
                           	foreach ($accesslevels AS $accesslevel)
                           	{
                           		$accesslevels_options[] = JHtml::_('select.option', $accesslevel->id, $accesslevel->title);
                           	}
                           }


                           	if ($default_accesslevels)
                           	{
                           		echo JHtml::_('select.genericlist', $accesslevels_options, "ticket_type_access[]", 'class="chzn-done jticket_access" id="ticket_type_access'.$j.'" data-chosen="com_jticketing"', "value", "text",$default_accesslevels,"ticket_type_access".$j);
                           	}
                           	else
                           	{
                           		echo JHtml::_('select.genericlist', $accesslevels_options, "ticket_type_access[]", 'class="chzn-done jticket_access" id="ticket_type_access'.$j.'" data-chosen="com_jticketing"', "value", "text","ticket_type_access".$j);
                           	}

                           ?>
                        <span class="help-inline"><?php
                           echo JText::_('COM_JTICKETING_ACCESS_MSG');
                           ?></span>
                     </div>
                  </div>
               </div>
               <!--ticket_type_access DIV ENDS-->
               <!--<div class="control-group jticketing-form-group">
                  <div class="controls ">
                  <div class=" ">
                  <input type="text"  id="max_limit_ticket<?php
                     echo $j;
                     ?>" class="validate-numeric required" name="max_limit_ticket[]" placeholder="<?php
                     echo $max_limit_ticket;
                     ?>" value="<?php
                     if (isset($tickettype->max_limit_ticket))
                     {
                     	echo $tickettype->max_limit_ticket;
                     }
                     else
                     {
                     	echo 0;
                     }
                     ?>">
                  <span class="help-inline"><?php
                     echo JText::_('COM_JTICKETING_MAX_LIMIT_UNLIMIT');
                     ?></span>

                  </div>
                  </div>
                  </div>
                  -->
               <!--max_limit_ticket DIV ENDS-->
            </div>
            <!--com_jticketing_repeating_block DIV ENDS-->
            <div class="jt_add_remove_button">
               <button class="btn btn-small btn-success" type="button" id="addbtn"
                  onclick="addClone('jticketing_container','jticketing_container');"
                  title="<?php
                     echo JText::_('COM_JTICKETING_ADD_MORE_TOOLTIP');
                     ?>">
               <i class="icon-plus"></i>
               </button>
			<?php if(!empty($this->item->id))
				{ ?>
				   <button class="btn btn-small btn-danger" type="button" id="removebtn"
					  onclick="removeClone('jticketing_container','jticketing_container');"
					  title="<?php echo JText::_('COM_JTICKETING_REMOVE_TOOLTIP');
						 ?>" >
					<i class="icon-minus"></i>
					</button>
				<?php
				}
			?>
            </div>
            <div style="clear:both"></div>
            <hr class="hr hr-condensed">
         </div>
         <!--jticketing_container DIV ENDS-->
         <div style="clear:both"></div>
         <!--IF NOT EDIT PRINT A PLAIN DIV ENDS-->
      </div>
      <!--ROW_FLUID DIV ENDS-->
   </div>
   <!--CONTAINNER DIV ENDS-->
   <!--COMMISSION INFO DIV-->
		<div id="commission " class="row-fluid">
		<div class="" >
			<?php
			if ($siteadmin_comm_per > 0)
			{ ?>
			 <span class="help-inline"><strong><?php
				echo JText::sprintf('COMMISSION_DEDUCTED_NOT_PERCENT', $siteadmin_comm_per, '%');
				?></strong></span>
			<?php
			}
			if ($siteadmin_comm_flat > 0 & $siteadmin_comm_per > 0)
			{?>
				 <span class="help-inline "><strong><?php
				echo JText::_('COMMISSION_DEDUCTED_ALSO');
				?></strong></span>
			<?php
			}
			if ($siteadmin_comm_flat > 0)
			{
			?>
			 <span class="help-inline"><strong>
			 <?php		echo JText::sprintf('COMMISSION_DEDUCTED_NOT_FLAT', $siteadmin_comm_flat, $currency);?>
			 </strong></span>
			 <?php
			}
			?>
		</div>
	</div>
   <!--COMMISSION INFO DIV ENDS-->
   <?php
      if (JVERSION < '3.0' && $this->integration != 2)
      {
      ?>
</div>
<!--BOOTSTRAP ENDS-->
<?php
   }
   ?>
<!--END HTML PART-->
<script type="text/javascript">
   /*
   This function is used for validation of paypal email.

   */
   function validateJTdata()
   {
       if (document.getElementById("paypal_email").value=="") {
           alert("Please Enter ' .$emailtext . '");
           return false;
       } else {
           return true;
       }
   }

   //MAY BE THIS WILL NOT BE USED.
   function add_my_clone()
   {
       techjoomla.jQuery("#jticketing_container").attr("style", "display: block;");
       $("input[type=text]").addClass("required");
   }
</script>

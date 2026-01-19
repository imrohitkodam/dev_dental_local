<?php
/**
 * @version     1.0.0
 * @package     com_hierarchy
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Parth Lawate <contact@techjoomla.com> - http://techjoomla.com
 */

// no direct access
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

JHtml::stylesheet(JURI::root().'components/com_hierarchy/assets/css/reschedule.css');
JHtml::script(JURI::root().'media/jui/js/jquery.min.js');
JHtml::script(JURI::root().'components/com_hierarchy/assets/js/formview.js');
$training = HierarchyFrontendHelper::getTrainingAdminExit();
?>

<div class="tjlms-inner">
<div class="row-fluid layout">

	<!--show courses-->

	<div class="cat-layout course-ld">
		<div class="main-cat hidden-phone">
			<div class="tjlms-st01"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_WORKFLOW_HEADING');?></div>
			<div class="tjlms-st11"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_WORKFLOW_HEADING');?></div>
			<div class="tjlms-st21"><div class="hidden-phone">
</div></div>
		</div>
		<div class="main-cat visible-phone">
			<div class="tjlms-st21"><div class="hidden-phone">
<div class="cat-img">
<img src="images/BL_Business_Loan.png" alt="Business Loan">
</div>
<div class="cat-desc"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_WORKFLOW_FOOTER_HEADING');?></div>
</div>
<div class="hidden-desktop hidden-tablet">
<div class="row-fluid" style="margin-top: 20px; margin-left: 0px; margin-bottom: 10px;">
<div class="pull-left">&nbsp;</div>
<div style="color: #e14b54; font-size: 18px; font-weight: bold; margin-top: 18px;">&nbsp;</div>
</div>
</div></div>
		</div>
	</div>
<?php
if (empty($training))
{
	echo '<p><span class="alert alert-warning middle">'.JText::_("COM_HIERARCHY_RESCHEDULES_AUTHORIZE_GROUP").'</span></p>';
}
else
{

?>

	<div class="course-rd tjlms-courses-cat-1">
			<div class="tjlms-toggle-box row-fluid ">
				<div class="tjlms-toggle-title">
					<i class="fa fa-minus fa-bg"></i>
					<strong><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_WORKFLOW');?></strong>
					
					<div class="pull-right"><a href="<?php echo JRoute::_('index.php?option=com_hierarchy&view=reschedules');?>"><h4><?php echo JText::_("COM_HIERARCHY_RESCHEDULES_ALL_RESCHEDULES");?> </h4></a></div>
				</div>
				
				<div class="form-inline" id="reschedule_div" >
	<?php echo JText::_('COM_HIERARCHY_RESCHEDULES_TICKET_ID').'*';?>
	  <input type="text" class="form-control" id="ticket_id">
	  <button type="" class="btn btn-primary btn-submit" onclick="getValue();"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_WORKFLOW_SUBMIT');?></button>
	</div>
	<div id="not_found" style="display:none;">

		<p class="alert alert-warning form-inline middle"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_NOT_FOUND');?></p>
	</div>
	<div id="status" style="display:none;">

		<p class="alert alert-warning form-inline middle"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_IN_PROGREASS');?></p>
	</div>
	<div id="newTicket" style="display:none;">

		<p class="alert alert-warning form-inline middle"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_NEW_TICKET');?></p>
	</div>
	<div id="doneTicket" style="display:none;">
		<p class="alert alert-info form-inline middle"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_ALREADY_RESCHEDULE_DONE_TICKET');?></p>
	</div>
	<div id="ajaxdiv" style="display:none;">
		<div class="alert-info form-inline middle" id="remain_div"> 
		<span id="days"></span>
		<span id="positive"><?php echo JText::sprintf('COM_HIERARCHY_RESCHEDULES_REMAIN_DAYS','');?></span>
		<span id="negative"><?php echo JText::sprintf('COM_HIERARCHY_RESCHEDULES_ALREADY','');?></span>
	</div>
	<form action="<?php echo JRoute::_('index.php?option=com_hierarchy&view=reschedules'); ?>" method="post" name="adminForm" id="adminForm">
		<div class="row-fluid" style="margin-top:20px;">
			<div class="span4">
				<div class="control-group">
					<div class="control-label">
						<label id="jform_domain-lbl" for="jform_domain" class="hasTooltip"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_TICKET_ID');?></label> : 
						<label id="ticket_id_label"class="hidden-lbl"></label></div>
						<div class="controls"><input type="hidden" name="ticket_id" value="">
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label id="jform_domain-lbl" for="jform_domain" class="hasTooltip"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_TICKET_NAME');?></label> : 
						<label id="name_label"class="hidden-lbl"></label></div>
						<div class="controls"><input type="hidden" name="name" value="">
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label id="jform_domain-lbl" for="jform_domain" class="hasTooltip"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_TICKET_EMAIL');?></label> : 
						<label id="email_label"class="hidden-lbl"></label></div>
						<div class="controls"><input type="hidden" name="email" value="">
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label id="jform_domain-lbl" for="jform_domain" class="hasTooltip"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_TICKET_BATCH_NAME');?></label> : 
						<label id="bname_label"class="hidden-lbl"></label></div>
						<div class="controls"><input type="hidden" name="bname" value="">
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label id="jform_domain-lbl" for="jform_domain" class="hasTooltip"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_TICKET_COST');?></label> (<span>&#8377;</span>)</div>
						<div class="controls"><input type="text" id="pcode" name="cost" value="" class="search-query">
					</div>
				</div>
		</div>
		<div class="span4">
				<div class="control-group">
					<div class="control-label">
						<label id="jform_domain-lbl" for="jform_domain" class="hasTooltip"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_TICKET_BATCH_CODE');?></label> : 
						<label id="bcode_label"class="hidden-lbl"></label></div>
						<div class="controls"><input type="hidden" name="bcode" value="">
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label id="jform_domain-lbl" for="jform_domain" class="hasTooltip"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_TICKET_BATCH_LOCATION');?></label> : 
						<label id="location_label"class="hidden-lbl"></label></div>
						<div class="controls"><input type="hidden" name="location" value="">
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label id="jform_domain-lbl" for="jform_domain" class="hasTooltip"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_TICKET_BATCH_DATE');?></label> : 
						<label id="bdate_label"class="hidden-lbl"></label> <?php echo JText::_('COM_HIERARCHY_RESCHEDULES_WORKFLOW_DATE_TO');?> <label id="edate_label"class="hidden-lbl"></label></div>
						<div class="controls"><input type="hidden" name="bdate" value="">
						<input type="hidden" name="edate" value="">
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label id="jform_domain-lbl" for="jform_domain" class="hasTooltip"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_BH_HEAD_EAMIL');?></label> : <span id="bhnot" style="display:none; color:red;"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_BH_HEAD_EAMIL_NOT_ALLOCATED');?></span> 
						<label id="bh_label"class="hidden-lbl"></label></div>
						<div class="controls"><input type="hidden" name="bh" value="">
					</div>
				</div>
				

		</div>
		<div class="span4">
				<div class="control-group">
					<div class="control-label">
						<label id="jform_domain-lbl" for="jform_domain" class="hasTooltip"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_TICKET_MANAGER').'/'.JText::_('COM_HIERARCHY_RESCHEDULES_TICKET_NOMINATION');?></label> : 
						<label id="manager_label"class="hidden-lbl"></label></div>
						<div class="controls"><input type="hidden" name="manager" value="">
						<input type="hidden" name="manager_email" value="">
						
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label id="jform_domain-lbl" for="jform_domain" class="hasTooltip"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_TICKET_DEPARTMENT');?></label> : 
						<label id="department_label"class="hidden-lbl"></label></div>
						<div class="controls"><input type="hidden" name="department" value="">
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label id="jform_domain-lbl" for="jform_domain" class="hasTooltip"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_TICKET_SUB_DEPARTMENT');?></label> : 
						<label id="sub_department_label"class="hidden-lbl"></label></div>
						<div class="controls"><input type="hidden" name="sub_department" value="">
					</div>
				</div>
				
			</div>
			<!--span6-->
		</div>
		<!--row-fluid-->
		<div class="row-fluid">
			<div class="span12 center" >
				<button type="submit" class="btn btn-primary" id="reschedule_btn"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_TICKET_RESCHEDULE');?></button>
			</div>
		</div>
		<input type="hidden" name="task" value="reschedules.save" />
		<input type="hidden" name="uid" value="" />
	</form>
	
	

 </div>
</div>
<div id="alredy_div" style="display:none;">

			
	  <h4><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_HISTORY');?></h4>

	<table class="table table-condensed">
		
    <thead>
      <tr>
        <th><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_TICKET_ID');?></th>
        <th><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_TICKET_BATCH_NAME');?></th>
        <th style="width:15%"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_TICKET_BATCH_DATE');?></th>
        <th><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_TICKET_BATCH_LOCATION');?></th>
        <th><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_USER');?></th>
        <th><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_BH');?></th>
        <th><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_HR');?></th>
        <th><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_LAST_MODIFIED');?></th>
        <th><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_RESCEDULE_ID');?></th>
      </tr>
    </thead>
    <tbody>

    </tbody>
  </table>
	</div>
			</div>
	</div>

<?php
}
?>
</div>



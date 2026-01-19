<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
	
	<div class="form-inline form-inline-header">
		<div class="control-group" id="field_group_title">
	<div class="control-label">
<?php echo $this->form->getLabel('title'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('title'); ?>
	</div>
</div>	</div>

	
<div class="form-horizonal">	
	<ul class="nav nav-tabs">		<li  class="active"  id="form_li_overview">
			<a href="#form_tab_overview" data-toggle="tab"><?php echo JText::_("Details"); ?></a>
		</li>
</ul>	<div class="tab-content">		<div class="tab-pane active" id="form_tab_overview">
										<div class="form-horizontal">
					<div class="control-group" id="field_group_alias">
	<div class="control-label">
<?php echo $this->form->getLabel('alias'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('alias'); ?>
	</div>
</div><div class="control-group" id="field_group_canned_id">
	<div class="control-label">
<?php echo $this->form->getLabel('canned_id'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('canned_id'); ?>
	</div>
</div><div class="control-group" id="field_group_tab">
	<div class="control-label">
<?php echo $this->form->getLabel('tab'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('tab'); ?>
	</div>
</div><div class="control-group" id="field_group_fieldtype">
	<div class="control-label">
<?php echo $this->form->getLabel('fieldtype'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('fieldtype'); ?>
	</div>
</div>
<div id="field_fieldtype_extra">
<?php $fo = $this->form->getField("fieldtype"); ?>
<?php if (method_exists($fo, "getExtra")) echo $fo->getExtra(); ?>
</div><div class="control-group" id="field_group_spacer">
	<div class="control-label">
<?php echo $this->form->getLabel('spacer'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('spacer'); ?>
	</div>
</div>				</div>
					</div>
</div></div>

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
</div><div class="control-group" id="field_group_alias">
	<div class="control-label">
<?php echo $this->form->getLabel('alias'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('alias'); ?>
	</div>
</div>	</div>

	
<div class="form-horizonal">	
	<ul class="nav nav-tabs">		<li  class="active"  id="form_li_overview">
			<a href="#form_tab_overview" data-toggle="tab"><?php echo JText::_("Overview"); ?></a>
		</li>
		<li  id="form_li_fields">
			<a href="#form_tab_fields" data-toggle="tab"><?php echo JText::_("Fields"); ?></a>
		</li>
		<li  id="form_li_filters">
			<a href="#form_tab_filters" data-toggle="tab"><?php echo JText::_("Filters"); ?></a>
		</li>
</ul>	<div class="tab-content">		<div class="tab-pane active" id="form_tab_overview">
										<div class="form-horizontal">
					<div class='row-fluid'>
<div class='span9 '><div class="control-group" id="field_group_description">
	<div class="control-label">
<?php echo $this->form->getLabel('description'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('description'); ?>
	</div>
</div></div><div class='span3 '><div class="control-group" id="field_group_state">
	<div class="control-label">
<?php echo $this->form->getLabel('state'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('state'); ?>
<span class="help-inline"></span>
	</div>
</div></div></div>
				</div>
					</div>
		<div class="tab-pane " id="form_tab_fields">
										<div class="">
					<div class="control-group" id="field_group_fielddata">
	<div class="controls">
<?php echo $this->form->getInput('fielddata'); ?>
	</div>
</div>				</div>
					</div>
		<div class="tab-pane " id="form_tab_filters">
										<div class="form-horizontal">
					<div class="control-group" id="field_group_filterdata">
	<div class="controls">
<?php echo $this->form->getInput('filterdata'); ?>
	</div>
</div>				</div>
					</div>
</div></div>

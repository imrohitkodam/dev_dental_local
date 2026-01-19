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
			<a href="#form_tab_overview" data-toggle="tab"><?php echo JText::_("Template"); ?></a>
		</li>
		<li  id="form_li_fields">
			<a href="#form_tab_fields" data-toggle="tab"><?php echo JText::_("Fields"); ?></a>
		</li>
		<li  id="form_li_javascript">
			<a href="#form_tab_javascript" data-toggle="tab"><?php echo JText::_("Javascript"); ?></a>
		</li>
		<li  id="form_li_css">
			<a href="#form_tab_css" data-toggle="tab"><?php echo JText::_("CSS"); ?></a>
		</li>
		<li  id="form_li_notes">
			<a href="#form_tab_notes" data-toggle="tab"><?php echo JText::_("Notes"); ?></a>
		</li>
		<li  id="form_li_advanced">
			<a href="#form_tab_advanced" data-toggle="tab"><?php echo JText::_("Advanced"); ?></a>
		</li>
</ul>	<div class="tab-content">		<div class="tab-pane active" id="form_tab_overview">
										<div class="">
					<div class='row-fluid'>
<div class='span9 '><div class="control-group" id="field_group_subject">
	<div class="control-label">
<?php echo $this->form->getLabel('subject'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('subject'); ?>
	</div>
</div><div class="control-group" id="field_group_description">
	<div class="control-label">
<?php echo $this->form->getLabel('description'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('description'); ?>
	</div>
</div></div><div class='span3 '><div class="control-group" id="field_group_category">
	<div class="control-label">
<?php echo $this->form->getLabel('category'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('category'); ?>
<span class="help-inline"><?php echo JText::_(''); ?></span>
	</div>
</div><div class="control-group" id="field_group_showfor">
	<div class="control-label">
<?php echo $this->form->getLabel('showfor'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('showfor'); ?>
<span class="help-inline"><?php echo JText::_(''); ?></span>
	</div>
</div><div class="control-group" id="field_group_parsetype">
	<div class="control-label">
<?php echo $this->form->getLabel('parsetype'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('parsetype'); ?>
<span class="help-inline"><?php echo JText::_(''); ?></span>
	</div>
</div><div class="control-group" id="field_group_language">
	<div class="control-label">
<?php echo $this->form->getLabel('language'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('language'); ?>
<span class="help-inline"></span>
	</div>
</div><div class="control-group" id="field_group_state">
	<div class="control-label">
<?php echo $this->form->getLabel('state'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('state'); ?>
<span class="help-inline"></span>
	</div>
</div><div class="control-group" id="field_group_access">
	<div class="control-label">
<?php echo $this->form->getLabel('access'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('access'); ?>
<span class="help-inline"></span>
	</div>
</div></div></div>
				</div>
					</div>
		<div class="tab-pane " id="form_tab_fields">
		<div class='fsj_inline_frame_outer'><div class='fsj_inline_frame_wait' id='fsjframe_frame_fields_wait'><div style='margin-bottom: 16px;'><img src='<?php echo JURI::root(true); ?>/libraries/fsj_core/assets/images/misc/ajax-loader.gif'></div><div><?php echo JText::_('FSJ_PLEASE_WAIT_LOADING'); ?></div></div><?php echo $this->form->getInput('frame_fields'); ?></div>		</div>
		<div class="tab-pane " id="form_tab_javascript">
										<div class="">
					<div class="control-group" id="field_group_javascript">
	<div class="control-label">
<?php echo $this->form->getLabel('javascript'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('javascript'); ?>
	</div>
</div>				</div>
					</div>
		<div class="tab-pane " id="form_tab_css">
										<div class="">
					<div class="control-group" id="field_group_css">
	<div class="control-label">
<?php echo $this->form->getLabel('css'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('css'); ?>
	</div>
</div>				</div>
					</div>
		<div class="tab-pane " id="form_tab_notes">
										<div class="">
					<div class="control-group" id="field_group_notes">
	<div class="control-label">
<?php echo $this->form->getLabel('notes'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('notes'); ?>
	</div>
</div>				</div>
					</div>
		<div class="tab-pane " id="form_tab_advanced">
										<div class="form-horizontal">
					<div class="control-group" id="field_group_usestatus">
	<div class="control-label">
<?php echo $this->form->getLabel('usestatus'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('usestatus'); ?>
	</div>
</div><div class="control-group" id="field_group_statuslist">
	<div class="control-label">
<?php echo $this->form->getLabel('statuslist'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('statuslist'); ?>
	</div>
</div><div class="control-group" id="field_group_newstatus">
	<div class="control-label">
<?php echo $this->form->getLabel('newstatus'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('newstatus'); ?>
	</div>
</div>				</div>
					</div>
</div></div>

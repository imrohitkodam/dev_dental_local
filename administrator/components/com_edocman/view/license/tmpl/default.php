<?php
/**
 * @version        1.9.7
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
if (version_compare(JVERSION, '3.0', 'ge')) {
	//EdocmanHelper::addSideBarmenus('license');
	$sidebar = JHtmlSidebar::render();
}
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'cancel' || document.formvalidator.isValid(document.id('license-form'))) 
		{
			Joomla.submitform(task, document.getElementById('license-form'));
		}
		else 
		{
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_edocman&view=license&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="license-form" class="form-validate">
<div class="row-fluid">	
	<fieldset class="adminform">
		<legend><?php echo JText::_('EDOCMAN_FIELDSET_DETAILS'); ?></legend>
		<table width="100%" >
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
					<?php echo $this->form->getLabel('id'); ?>
				</td>
				<td>
					<?php echo $this->form->getInput('id'); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo $this->form->getLabel('default_license'); ?>
				</td>
				<td>
					<?php echo $this->form->getInput('default_license'); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo $this->form->getLabel('published'); ?>
				</td>
				<td>
					<?php echo $this->form->getInput('published'); ?>
				</td>
			</tr>
		</table>              		   		
			<div class="clearfix"></div>
			<?php echo $this->form->getLabel('description'); ?>
			<div class="clearfix"></div>
			<?php echo $this->form->getInput('description'); ?>            
	</fieldset>
</div>
<input type="hidden" name="option" id="option" value="com_edocman" />
<input type="hidden" name="task" value="document.sefoptimize" id="task" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHtml::_('form.token'); ?>
</form>
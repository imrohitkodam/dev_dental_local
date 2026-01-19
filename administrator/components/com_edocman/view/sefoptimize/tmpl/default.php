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
?>
<form action="<?php echo JRoute::_('index.php?option=com_edocman&task=sefoptimize'); ?>" method="post" name="adminForm" id="sef-form" class="form-validate">

<div id="j-main-container">
	<div class="row-fluid">	
		<div class="span12" style="text-align:center;">
			<div class="span3"></div>
			<div class="span6">
				<h3>
					<?php echo JText::_('EDOCMAN_SEF_URLS_OPTIMIZATION');?>
				</h3>
				<div class="clearfix"></div>
				<div class="img-polaroid" style="text-align:left;">
					<?php echo JText::_('EDOCMAN_SEF_URLS_OPTIMIZATION_EXPLAIN');?>
				</div>
				<div class="clearfix"></div>
				<br />
				<input type="submit" class="btn btn-info" value="<?php echo JText::_('EDOCMAN_YES_I_AGREE');?>" />
				<input type="button" onclick="javascript:returnControlPanel();" class="btn btn-warning" value="<?php echo JText::_('EDOCMAN_NO_I_DO_NOT_AGREE');?>" />
			</div>
			<div class="span3"></div>
		</div>
	</div>
</div>
<input type="hidden" name="option" id="option" value="com_edocman" />
<input type="hidden" name="task" value="sefoptimize" id="task" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHtml::_('form.token'); ?>
<div class="clr"></div>
</form>
<script type="text/javascript">
function returnControlPanel(){
	location.href = "index.php?option=com_edocman";
}
</script>
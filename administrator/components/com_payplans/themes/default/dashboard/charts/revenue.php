<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="panel">
	<div class="flex">
		<div class="flex-grow">
			<b class="panel-head-title">
				<?php echo JText::_('COM_PP_DASHBOARD_REVENUE_OVERVIEW'); ?>
			</b>
			<div class="panel-info"><?php echo JText::_('COM_PP_DASHBOARD_REVENUE_OVERVIEW_DESC'); ?></div>
		</div>
		<div class="">
			<?php echo $this->fd->html('button.link', 
				'index.php?option=com_payplans&view=analytics&layout=sales', 'COM_PP_VIEW_MORE', 'default-o', 'xs', [
				'class' => 'whitespace-nowrap'
			]); ?>
		</div>
	</div>

	<div data-dashboard-content-tab class="is-loading" style="padding: 20px;">
		<?php echo $this->fd->html('loader.block', [
			'class' => 'flex items-center',
			'loaderClass' => 'block',
			'text' => JText::_('COM_PP_RETRIEVE_CHART_DATA')
		]); ?>

		<div class="">
			<canvas id="chart-revenue" height="250"></canvas>
		</div>
	</div>
</div>

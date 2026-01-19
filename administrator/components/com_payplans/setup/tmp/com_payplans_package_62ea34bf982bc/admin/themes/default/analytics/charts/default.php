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
<div class="charts-wrapper p-xl bg-white border border-solid border-gray-200 rounded-md">
	<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
		<div class="col-span-1 md:col-span-<?php echo $renderPlan === true ? '8' : '12'; ?> w-auto space-y-md">
			<div class="data-line-chart" data-line-chart>
				<?php echo $this->output('admin/analytics/charts/chart'); ?>
			</div>

			<div data-pp-analytics-label></div>
		</div>

		<?php if ($renderPlan) { ?>
		<div class="col-span-1 md:col-span-4 w-auto" style="xborder-left: 1px solid #DEE3E9">
			<div class="" data-plan-chart>
				<?php echo $this->output('admin/analytics/charts/plans'); ?>
			</div>
			
		</div>
		<?php } ?>
	</div>
</div>

<div class="border border-solid border-gray-200 rounded-md">
	<div class="panel-table" data-chart-listings></div>
</div>

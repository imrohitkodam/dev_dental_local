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
<div class="flex gap-md w-full justify-between items-center">

	<?php foreach ($chartLabelData as $label) { ?>
	<div class="flex gap-md bg-gray-50 rounded-md px-sm py-sm
	<?php //echo isset($customClass) ? $customClass : ''; ?>
	 w-full items-center
	">
		<div class="flex-grow">
			<div class="text-gray-800 font-bold text-lg leading-lg">
				<b><?php echo $label->value; ?></b>
			</div>
			<div class="text-gray-500 text-xs leading-xs">
				<?php echo $label->title; ?>
			</div>
		</div>

		<?php if (isset($label->showPercentageDifference) && $label->showPercentageDifference) { ?>
		<div>
			<div class="text-success-500 flex gap-xs items-center text-xs">
				<i class="fdi fa fa-arrow-up"></i>
				<div class="">55%</div>
			</div>
		</div>
		<?php } ?>
	</div>
	<?php } ?>
</div>

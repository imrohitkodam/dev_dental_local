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
<?php foreach ($priceVariations as $priceVariation) { ?>
<select name="priceVariation" class="o-form-control" data-pricevariation-selection>
	<option value="default" selected="selected">
		<?php echo $plan->getTitle(); ?> <?php echo $plan->getCurrency(); ?><?php echo $plan->getPrice(); ?> <?php echo $separator; ?> <?php echo $this->html('html.plantime', PPHelperPlan::convertIntoTimeArray($plan->getRawExpiration()), ['isRecurring' => $plan->isRecurring()]); ?>
	</option>
	<?php foreach ($priceVariation->options as $option) { ?>
		<option value="<?php echo $option->title;?>_<?php echo $option->price;?>_<?php echo $option->time; ?>_<?php echo $priceVariation->app_id;?>">
			<?php echo $option->title; ?> <?php echo $plan->getCurrency(); ?><?php echo $option->price; ?> <?php echo $separator; ?> <?php echo $this->html('html.plantime', PPHelperPlan::convertIntoTimeArray($option->time), ['isRecurring' => $plan->isRecurring()]); ?>
		</option>
	<?php } ?>
</select>
<?php } ?>
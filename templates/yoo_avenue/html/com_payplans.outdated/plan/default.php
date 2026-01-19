<?php
/**
* @copyright	Copyright (C) 2009 - 2009 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		PayPlans
* @subpackage	Frontend
* @contact 		shyam@readybytes.in
*/
if(defined('_JEXEC')===false) die();
?>
<div class="pp-plan-subscribe container-fluid clearfix">
	<form action="<?php echo $uri; ?>" method="post" name="site<?php echo $this->getName(); ?>Form">
		<div class="row-fluid page-header">

				<h2>
						<span class="span9"><?php echo XiText::_('COM_PAYPLANS_PLAN_SELECT_PLAN_HEADING');?></span>
						<?php if(!empty($link)):?>
							<!--span class="span3">
								<a id="go-to-subscribe-link" class="pp-backlink pull-right" title="<?php echo XiText::_('COM_PAYPLANS_PLAN_BACK_TO_SUBSCRIBE_MSG');?>" href="<?php echo $link;?>"><?php echo XiText::_('COM_PAYPLANS_PLAN_BACK_TO_SUBSCRIBE')?></a>
							</span-->
						<?php else:?>
							<span class="span3">&nbsp;
							</span>
						<?php endif;?>
						</h2>
		</div>

		<div class="pp-module-top clearfix row-fluid">
		<?php
			  $position = 'payplans-plan-select-top';
			  echo $this->loadTemplate('partial_position',compact('plugin_result','position'));
		    ?>
		</div>

		<?php
			 $counter      	= 0;
	         $total         = count($groups)+count($plans);

	         if($total == 0)
	         {
	         	$gridclasses = array();
	         	$row_plans   = array();
	         }
	         else{
	         	list($gridclasses,$row_plans)  = (!$vertical_layout)?PayplansHelperPlan::buildPlanCloumnClasses($row_plans, $total):array(array(),array_fill(1,$total,1));
	         }
	         $instances = array();

	         foreach($groups as $key => $record ){
	         	$instances[] = PayplansGroup::getInstance($record->group_id, null, $record);
	         }

	         foreach ($plans as $key=>$record){
	         	$instances[] = PayplansPlan::getInstance($record->plan_id, null,$record);
	         }
		?>

			<?php foreach ($row_plans as $key=>$rowCount):?>
				<div class="pp-plan-row row-fluid">
				<?php for($i=0 ; $i < $rowCount; $i++,$counter++):?>
					<?php $plan_grid_class = ($vertical_layout)?' pp-vertical': $gridclasses[$counter];?>
					<?php if($instances[$counter] instanceof PayplansPlan):?>
						<?php $plan = $instances[$counter]; ?>
						<?php echo $this->loadTemplate('plan',compact('plan', 'plan_grid_class', 'plugin_result'));  ?>
					<?php else:?>
					<?php $group = $instances[$counter]; ?>
					<?php echo $this->loadTemplate('group', compact('group', 'plan_grid_class','total', 'counter'));  ?>
					<?php endif;?>
				<?php endfor;?>
				</div>
				<br>
			<?php endforeach;?>

		<div class="pp-module-bottom clearfix">
			<?php
			 $position = 'payplans-plan-select-bottom';
		     echo $this->loadTemplate('partial_position',compact('plugin_result','position'));
		    ?>
		</div>

		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="task" value="subscribe"/>
	</form>
</div>

<?php if(!$vertical_layout) : ?>
	<script type="text/javascript">
	xi.jQuery(window).load(function(){
		xi.plan.fixHeights(".pp-plan-row",".pp-plan-description");
		xi.plan.fixHeights(".pp-plan-row",".pp-plan-price");
		xi.plan.fixHeights(".pp-plan-row",".pp-plan-title");

	});
</script>
<?php endif;?>

<?php

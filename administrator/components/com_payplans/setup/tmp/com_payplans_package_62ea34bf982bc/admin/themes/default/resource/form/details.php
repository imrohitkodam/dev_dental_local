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
<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_RESOURCE_DETAILS'); ?>

			<div class="panel-body">

				<div class="<?php echo $_('form.resource');?> hover:bg-gray-100">
					<?php echo $this->fd->html('form.label', 'COM_PP_ID', 'id', '', '', false); ?>

					<div class="<?php echo $_('form.input');?>">
						<?php echo $this->fd->html('label.standard', $resource->resource_id);?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_RESOURCE_VALUE', 'value'); ?>

					<div class="flex-grow">
						<?php echo $resource->value;?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PAYPLANS_RESOURCE_TITLE', 'title'); ?>

					<div class="flex-grow">
						<?php echo $resource->title;?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PAYPLANS_RESOURCE_SUBSCRIPTIONS', 'subscription_ids'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.usersubscriptions', 'subscription_ids', $resource->subscription_ids, '', '', $resource->user_id); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PAYPLANS_RESOURCE_COUNT', 'count'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.text', 'count', $resource->count); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PAYPLANS_RESOURCE_USER', 'user_id'); ?>

					<div class="flex-grow">
						<a href="index.php?option=com_payplans&view=user&layout=form&id=<?php echo $user->getId();?>"><?php echo $user->getUsername();?></a>
					</div>
				</div>
				
			</div>
		</div>
	</div>
</div>
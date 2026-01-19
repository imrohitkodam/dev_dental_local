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
<form class="o-form-horizontal" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" data-pp-form>
	<div data-fd-tab-wrapper>

		<?php echo $this->fd->html('admin.tabs', function() use ($activeTab, $isEdit) {
			$tabs = [
				(object) [
					'id' => 'details',
					'title' => 'COM_PP_DETAILS',
					'active' => $activeTab === 'details'
				]
			];

			if ($isEdit) {
				$tabs[] = (object) [
					'id' => 'consumers',
					'title' => 'Usage',
					'active' => $activeTab === 'consumers'
				];
			}

			return $tabs;
		}); ?>

		<div class="tab-content">
			<div id="details" class="t-hidden <?php echo !$activeTab || $activeTab == 'details' ? 't-block' : '';?>" data-fd-tab-contents>
				<?php echo $this->output('admin/discounts/form/details'); ?>
			</div>

			<?php if ($isEdit) { ?>
			<div id="consumers" class="t-hidden <?php echo $activeTab == 'consumers' ? 't-block' : '';?>" data-fd-tab-contents>
				<?php echo $this->output('admin/discounts/form/consumers'); ?>
			</div>
			<?php } ?>
		</div>
	</div>

	<?php echo $this->html('form.action', 'discounts', ''); ?>
	<?php echo $this->html('form.hidden', 'id', $discount->getId()); ?>
	<?php echo $this->html('form.hidden', 'type', 'upgrade'); ?>
</form>
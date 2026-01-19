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
<form class="o-form-horizontal" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<div data-fd-tab-wrapper>
		<?php echo $this->fd->html('admin.tabs', function() use ($activeTab, $transactionParams) {
			$tabs = [
				(object) [
					'id' => 'details',
					'title' => 'COM_PP_DETAILS',
					'active' => !$activeTab || $activeTab === 'details'
				]
			];

			if ($transactionParams) {
				$tabs[] = (object) [
					'id' => 'params',
					'title' => 'COM_PP_TRANSACTION_DATA',
					'active' => $activeTab === 'params'
				];
			}

			return $tabs;
		}); ?>

		<div class="tab-content">
			<div id="details" class="t-hidden <?php echo !$activeTab || $activeTab === 'details' ? 't-block' : '';?>">
				<?php echo $this->output('admin/transaction/form/details'); ?>
			</div>

			<?php if ($transactionParams) { ?>
			<div id="params" class="t-hidden <?php echo $activeTab == 'params' ? 't-block' : '';?>">
				<?php echo $this->output('admin/transaction/form/params'); ?>
			</div>
			<?php } ?>
		</div>
	</div>

	<?php echo $this->html('form.action', 'transaction'); ?>
	<?php echo $this->html('form.hidden', 'from', base64_encode($from)); ?>
</form>
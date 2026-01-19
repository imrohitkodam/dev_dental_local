<?php
/**
* @package      PayPlans
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form class="o-form-horizontal" action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" data-eb-form>
	<div data-fd-tab-wrapper>
		<?php echo $this->fd->html('admin.tabs', [
			(object) [
				'id' => 'details',
				'title' => 'COM_PP_DETAILS',
				'active' => $activeTab === 'details'
			]
		]); ?>

		<div class="tab-content">
			<div id="details" class="t-hidden <?php echo $activeTab === 'details' ? 't-block' : '';?>">
				<?php echo $this->output('admin/advancedpricing/form/details'); ?>
			</div>
		</div>
	</div>

	<?php echo $this->html('form.action', 'advancedpricing', ''); ?>

	<?php echo $this->html('form.hidden', 'advancedpricing_id', $item->getId()); ?>
</form>

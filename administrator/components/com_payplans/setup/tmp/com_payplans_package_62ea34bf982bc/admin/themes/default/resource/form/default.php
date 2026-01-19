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
		<?php echo $this->fd->html('admin.tabs', $tabs); ?>

		<div class="tab-content">
			<div id="details" class="t-hidden <?php echo !$activeTab ? 't-block' : '';?>" data-fd-tab-contents>
				<?php echo $this->output('admin/resource/form/details'); ?>
			</div>
		</div>
	</div>

	<?php echo $this->html('form.action', 'resource', 'store'); ?>
	<?php echo $this->html('form.hidden', 'id', $resource->resource_id); ?>
</form>
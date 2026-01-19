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
					'id' => 'logs',
					'title' => 'COM_PP_LOGS',
					'active' => $activeTab === 'logs'
				];
			}

			return $tabs;
		}); ?>

		<div class="tab-content">
			<div id="details" class="t-hidden <?php echo $activeTab === 'details' ? 't-block' : '';?>">
				<?php echo $this->output('admin/group/form/details'); ?>
			</div>

			<?php if ($group->getId()) { ?>
				<div id="logs" class="t-hidden <?php echo $activeTab === 'logs' ? 't-block' : '';?>">
					<?php echo $this->output('admin/logs/default/default', array('logs' => $logs, 'pagination' => false, 'editable' => false, 'renderFilterBar' => false, 'sortable' => false, 'form' => false)); ?>
				</div>
			<?php } ?>
		</div>
	</div>

	<?php echo $this->html('form.action', 'group', 'store'); ?>
	<?php echo $this->html('form.hidden', 'group_id', $group->getId()); ?>
</form>

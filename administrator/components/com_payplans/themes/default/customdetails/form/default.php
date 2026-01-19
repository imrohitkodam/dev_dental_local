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
		<?php echo $this->fd->html('admin.tabs', function() use ($activeTab) {
			$tabs = [
				(object) [
					'id' => 'details',
					'title' => 'COM_PP_DETAILS',
					'active' => !$activeTab || $activeTab === 'details'
				]
			];

			return $tabs;
		}); ?>

		<div class="tab-content">
			<div id="details" class="t-hidden <?php echo !$activeTab || $activeTab === 'details' ? 't-block' : '';?>">
				<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
					<div class="col-span-1 md:col-span-4 w-auto">
						<div class="panel">
							<?php echo $this->html('panel.heading', 'COM_PP_CUSTOMDETAILS_GENERAL'); ?>

							<div class="panel-body">
								<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
									<?php echo $this->fd->html('form.label', 'COM_PP_CUSTOMDETAILS_TITLE', 'title'); ?>

									<div class="flex-grow">
										<?php echo $this->fd->html('form.text', 'title', $table->title, ''); ?>
									</div>
								</div>

								<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
									<?php echo $this->fd->html('form.label', 'COM_PP_CUSTOMDETAILS_PUBLISHED', 'published'); ?>

									<div class="flex-grow">
										<?php echo $this->fd->html('form.toggler', 'published', is_null($table->published) ? true : $table->published); ?>
									</div>
								</div>

								<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
									<?php echo $this->fd->html('form.label', 'COM_PP_CUSTOMDETAILS_ALL_PLANS', 'params[applyAll]'); ?>

									<div class="flex-grow">
										<?php echo $this->fd->html('form.toggler', 'params[applyAll]', $params->get('applyAll', true), 'params[applyAll]', '', [
											'dependency' => '[data-customdetails-plan]', 
											'dependencyValue' => 0
										]); ?>
									</div>
								</div>

								<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md <?php echo $params->get('applyAll') ? 't-hidden' : ''; ?>" data-customdetails-plan>
									<?php echo $this->fd->html('form.label', 'COM_PP_CUSTOMDETAILS_PLANS', 'params[plans]'); ?>

									<div class="flex-grow">
										<?php echo $this->html('form.plans', 'params[plans]', $params->get('plans'), true, true, '', [], ['theme' => 'fd']); ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-span-1 md:col-span-8 w-auto">
						<div class="panel">
							<?php echo $this->html('panel.heading', 'COM_PP_CUSTOMDETAILS_PARAMETERS'); ?>

							<div class="panel-body">
								<?php if ($editor) { ?>
									<?php echo $editor->display("data", $table->getData(), '100%', '400px', 80, 20, false, null, null, null, ['syntax' => 'xml', 'filter' => 'raw']); ?>
								<?php } else { ?>
									<?php echo $this->fd->html('alert.standard', 'COM_PP_JOOMLA_CODEMIRROR_PLUGIN_DISABLED', 'warning'); ?>
								<?php } ?>
							</div>

							<div class="panel-body mt-lg">
								<?php echo $this->fd->html('alert.standard', 'COM_PP_CUSTOMDETAILS_PARAMETERS_GUIDE', 'info', ['dismissible' => false]); ?>

								<div>
									<pre><code><?php echo $this->html('string.escape', file_get_contents(JPATH_ADMINISTRATOR . '/components/com_payplans/defaults/customdetails.xml')); ?></code></pre>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php echo $this->html('form.action', 'customdetails', ''); ?>
	<?php echo $this->html('form.hidden', 'id', $id); ?>
	<?php echo $this->html('form.hidden', 'type', $view); ?>
</form>
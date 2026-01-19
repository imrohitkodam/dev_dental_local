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

$unchecked = false;
?>
<div id="modules" class="addons-list" data-tab>

	<div class="mb-3">
		<div class="custom-control custom-checkbox">
			<input type="checkbox" class="custom-control-input" id="maintenance" checked="checked" disabled />
			<label class="custom-control-label" for="maintenance">Run Maintenance Scripts (Required)</label>
		</div>

		<div class="custom-control custom-checkbox mt-3">
			<input type="checkbox" class="custom-control-input" id="plugins" checked="checked" disabled />
			<label class="custom-control-label" for="plugins">Install Core Plugins (Required)</label>
		</div>

		<div class="custom-control custom-checkbox mt-3">
			<input type="checkbox" class="custom-control-input" id="module-all" checked="checked" data-select-all />
			<label class="custom-control-label" for="module-all">Install Modules (Optional)</label>
		</div>

		<div class="custom-control custom-checkbox mt-3">
			<input type="checkbox" class="custom-control-input" name="sample-data" id="sample-data" />
			<label class="custom-control-label" for="sample-data">Install Sample Data (Optional)</label>
		</div>
	</div>

	<?php if ($data->modules) { ?>				
	<div class="pp-card pp-container-overflow mb-4 p-3" style="max-height:15vh">
		<div>
			<?php foreach ($data->modules as $module) { ?>
				<div class="custom-control custom-checkbox mb-1">
					<input type="checkbox" class="custom-control-input" id="module-<?php echo $module->element; ?>" value="<?php echo $module->element;?>" <?php echo $module->checked ? 'checked="checked"' : '' ?> data-checkbox data-checkbox-module <?php echo $module->disabled ? 'disabled':''; ?> />
					<label class="custom-control-label" for="module-<?php echo $module->element; ?>"><?php echo $module->title;?></label>
				</div>

				<?php if (!$module->checked) { ?>
					<?php $unchecked = true; ?>
				<?php } ?>
			<?php } ?>
		</div>
	</div>
	<?php } ?>

	<?php if ($data->plugins) { ?>
		<?php foreach ($data->plugins as $pluginGrp) { ?>
			<input type="hidden" value="<?php echo $pluginGrp;?>" data-plugins />
		<?php } ?>
	<?php } ?>
</div>

<?php if ($unchecked) { ?>
<script type="text/javascript">
$('[data-select-all]').prop('checked', false);
</script>
<?php } ?>


<script type="text/javascript">
var selectAllCheckbox = $('[data-select-all]');
var wrapper = $('[data-tab]');

selectAllCheckbox.on('change', function() {
	var element = $(this);
	var checkbox = wrapper.find('[data-checkbox]').not(":disabled");

	checkbox.prop('checked', element.is(':checked'));
});

$('[data-checkbox-module]').on('click', function() {
	var selected = $(this).is(':checked');
	
	if (!selected) {
		selectAllCheckbox.prop('checked', false);
		return;
	}

	// find if there is any unchecked item or not.
	var unchecked = wrapper.find('[data-checkbox-module]').not(":checked");

	if (unchecked.length == 0) {
		selectAllCheckbox.prop('checked', true);
	}
});
</script>
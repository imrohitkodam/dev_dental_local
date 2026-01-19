<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'cancel') {
			window.location = '<?php echo JRoute::_("index.php?option=com_fsj_fssadd"); ?>';
		}
	}
</script>
<div id="j-main-container" class="span10">
	
	<h1>Form Based Canned Replies</h1>

	<h3>User side integration</h3>

	<?php if ($this->user_state == 0): ?>
		<div class="alert alert-error">
			<h4>Plugin not installed</h4>
			<p>The user gui plugin for Freestyle Support Portal is not installed. Please use the button below to install the file.</p>
			<p><a href="index.php?option=com_fsj_fssadd&view=canned_config&action=user.install" class="btn btn-default">Install Plugin File</a></p>
		</div>
	<?php elseif ($this->user_state == 1): ?>
		<div class="alert alert-danger">
			<h4>Plugin changed</h4>
			<p>The installed plugin file is different to the provided one. If you have not changed the plugin, then it may be out of date.</p>
			<p><a href="index.php?option=com_fsj_fssadd&view=canned_config&action=user.install" class="btn btn-default">Update Plugin File</a></p>
		</div>
	<?php endif; ?>

	<?php if ($this->user_state > 0): ?>
		<div>
			Plugin Status: 
			<?php if ($this->user_plugin): ?>
				<span class='label label-success'>Enabled</span>
				<a class="btn btn-mini" style="margin-left: 64px;" href="index.php?option=com_fsj_fssadd&view=canned_config&action=user.disable">Disable Plugin</a>
				<a class="btn btn-mini" style="margin-left: 8px;" href="index.php?option=com_fss&view=plugins&layout=configure&type=gui&name=form_canned_user" target="_blank">Options</a>
			<?php else: ?>
				<span class='label label-important'>Disabled</span>
				<a class="btn btn-mini" style="margin-left: 64px;" href="index.php?option=com_fsj_fssadd&view=canned_config&action=user.enable">Enable Plugin</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<h3>Handler side integration</h3>


	<?php if ($this->admin_state == 0): ?>
	<div class="alert alert-error">
	<h4>Plugin not installed</h4>
	<p>The handler gui plugin for Freestyle Support Portal is not installed. Please use the button below to install the file.</p>
	<p><a href="index.php?option=com_fsj_fssadd&view=canned_config&action=admin.install" class="btn btn-default">Install Plugin File</a></p>
	</div>
	<?php elseif ($this->admin_state == 1): ?>
		<div class="alert alert-danger">
			<h4>Plugin changed</h4>
			<p>The installed plugin file is different to the provided one. If you have not changed the plugin, then it may be out of date.</p>
			<p><a href="index.php?option=com_fsj_fssadd&view=canned_config&action=admin.install" class="btn btn-default">Update Plugin File</a></p>
		</div>
	<?php endif; ?>

	<?php if ($this->admin_state > 0): ?>
		<div>
			Plugin Status: 
			<?php if ($this->admin_plugin): ?>
				<span class='label label-success'>Enabled</span>
				<a class="btn btn-mini" style="margin-left: 64px;" href="index.php?option=com_fsj_fssadd&view=canned_config&action=admin.disable">Disable Plugin</a>
				<a class="btn btn-mini" style="margin-left: 8px;" href="index.php?option=com_fss&view=plugins&layout=configure&type=gui&name=form_canned_admin" target="_blank">Options</a>
			<?php else: ?>
				<span class='label label-important'>Disabled</span>
				<a class="btn btn-mini" style="margin-left: 64px;" href="index.php?option=com_fsj_fssadd&view=canned_config&action=admin.enable">Enable Plugin</a>
			<?php endif; ?>

		</div>
	<?php endif; ?>

</div>

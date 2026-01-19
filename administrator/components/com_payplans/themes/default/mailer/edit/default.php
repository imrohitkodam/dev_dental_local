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
	<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
		<div class="col-span-1 md:col-span-12 w-auto">
			<div class="panel">
				<div class="panel-body">
					<div class="o-form-group">
						<?php echo $editor->display('source', $data->contents, '100%', '400px', 80, 20, false, null, null, null, array('syntax' => 'php', 'filter' => 'raw')); ?>
					</div>

					<div class="o-form-group">
						<div class="o-control-input col-md-9">
							<?php echo $this->html('form.rewriter'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php echo $this->html('form.hidden', 'file', $data->relative ? base64_encode($data->relative) : '');?>
	<?php echo $this->html('form.action', 'mailer'); ?>
</form>

<style type="text/css">
.CodeMirror {
	min-width: 100%;
}
</style>
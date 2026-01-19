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
<div class="pp-appstore space-y-md">
	<?php foreach ($apps as $app) { ?>
		<?php echo $this->output('admin/app/create/item', array('app' => $app, 'view' => $view, 'layout' => $layout)); ?>
	<?php } ?>

	<?php if ($customApps) { ?>
		<div class="pp-appstore__item">
			<h2 class="pp-reset"><?php echo JText::_('Custom 3rd Party Applications');?></h2>
		</div>

		<?php foreach ($customApps as $app) { ?>
			<?php echo $this->output('admin/app/create/item', array('app' => $app, 'view' => $view, 'layout' => $layout)); ?>
		<?php } ?>
	<?php } ?>
</div>
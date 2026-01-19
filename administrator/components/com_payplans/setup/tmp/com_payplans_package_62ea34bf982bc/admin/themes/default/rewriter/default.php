<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div style="display: none;position: fixed;background: #000;bottom: 10%;left: 40%;color: #fff;padding: 5px 10px;border-radius: 20px;opacity: 0.9;" data-clipboard-message><?php echo JText::_('Copied to your clipboard');?></div>

<ul class="nav nav-tabs t-lg-mb--xl" style="margin-left: 0;">
	<?php foreach ($items as $key => $item) { ?>
		<li class="<?php echo ($key === array_key_first($items)) ? 'active' : ''; ?>">
			<a href="#<?php echo strtolower($key);?>" data-pp-toggle="tab">
				<?php echo JText::_($key); ?>
			</a>
		</li>
		<?php if ($key === array_key_last($items) && $apps) { ?>
			<?php foreach ($apps as $app) { ?>
				<?php echo $app[0]; ?>
			<?php } ?>
		<?php } ?>
	<?php }?>
</ul>

<div class="tab-content">
	<?php foreach ($items as $key => $item) { ?>
		<div class="tab-pane <?php echo ($key === array_key_first($items)) ? 'active' : ''; ?>" id="<?php echo strtolower($key);?>">
			<?php echo $this->output('admin/rewriter/dialogs/table', array('data' => $item)); ?>
		</div>
		<?php if ($key === array_key_last($items) && $apps) { ?>
			<?php foreach ($apps as $app) { ?>
				<?php echo $app[1]; ?>
			<?php } ?>
		<?php } ?>
	<?php }?>
</div>
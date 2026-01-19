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
<div id="fd">
	<div id="pp" class="pp-frontend pp-main <?php echo $view . $task . $object . $layout . $suffix; ?> <?php echo $this->isMobile() ? 'is-mobile' : 'is-desktop';?>" data-pp-structure>

		<?php echo $this->render('module', 'pp-general-top'); ?>

		<?php echo $this->render('module', 'stackideas-toolbar'); ?>

		<?php echo $this->render('module', 'pp-general-after-toolbar'); ?>

		<?php echo PP::info()->html(); ?>

		<?php echo $this->render('module', 'pp-general-before-contents'); ?>

		<?php echo $contents; ?>

		<?php echo $this->render('module', 'pp-general-bottom'); ?>

		<div><?php echo $scripts; ?></div>

		<div data-pp-popbox-error style="display:none;"><?php echo JText::_('Unable to load tooltip content.'); ?></div>

		<?php echo $this->fd->html('html.tooltip', $this->config->get('layout_appearance'), $this->config->get('layout_accent')); ?>
	</div>
</div>
<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="es">
	<div class="es-k2-author">
		<?php if ($friends) { ?>
			<?php echo $this->html('user.friends', $author); ?>
		<?php } ?>

		<?php if ($follow) { ?>
			<?php echo $this->html('user.subscribe', $author); ?>
		<?php } ?>

		<?php if ($messaging) { ?>
			<?php echo $this->html('user.conversation', $author); ?>
		<?php } ?>

		<?php if ($points) { ?>
		<a href="<?php echo ESR::points(array('layout' => 'history', 'userid' => $author->getAlias()));?>" class="btn btn-es-default-o btn-sm btn--es-conversations-compose">
			<?php echo JText::sprintf('PLG_K2_EASYSOCIAL_POINTS', $author->getPoints()); ?>
		</a>
		<?php } ?>

		<?php if($badges) { ?>
		<h3 class="es-h3 t-lg-mt--lg t-lg-mb--lg"><?php echo JText::_('PLG_K2_EASYSOCIAL_ACHIEVEMENTS'); ?></h3>
		<ul class="es-k2-author-achievements list-unstyled">
			<?php foreach ($badges as $badge) { ?>
			<li>
				<a href="<?php echo $badge->getPermalink();?>" class="badge-link" data-es-provide="tooltip" data-placement="top" data-original-title="<?php echo FD::string()->escape( $badge->get( 'title' ) );?>">
					<img src="<?php echo $badge->getAvatar();?>" class="badge-item" />
				</a>
			</li>
			<?php } ?>
		</ul>
		<?php } ?>
	</div>
</div>


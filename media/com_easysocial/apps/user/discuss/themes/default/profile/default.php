<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-container" data-ed-discussions data-id="<?php echo $user->id;?>">

	<?php echo $this->html('html.sidebar'); ?>

	<?php if ($this->isMobile()) { ?>
		<?php echo $this->output('apps/user/discuss/profile/mobile.filters'); ?>
	<?php } ?>

	<div class="es-content">
		<div class=" app-contents<?php echo !$posts ? ' is-empty' : '';?>" data-discuss-wrapper>
			<?php echo ES::themes()->html('html.loading'); ?>
			<?php echo ES::themes()->html('html.emptyBlock', 'APP_GROUP_DISCUSSIONS_EMPTY', 'fa-database'); ?>

			<div data-discuss-contents>
				<?php if ($posts) { ?>
					<?php echo $this->loadTemplate('themes:/apps/user/discuss/profile/items', array('posts' => $posts, 'pagination' => $pagination, 'config' => $config, 'user' => $user)); ?>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
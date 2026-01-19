<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="fb-root"></div>
<script async defer src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2"></script>

<div class="eb-fb-embedded-wrapper" style="max-width: 720px">
	<div class="fb-<?php echo $type; ?>" 
		data-href="<?php echo $block->data->source; ?>"

		<?php if ($type == 'video') { ?>
		data-width="auto"
		data-allowfullscreen="true"
		<?php } ?>
		>
	</div>
</div>
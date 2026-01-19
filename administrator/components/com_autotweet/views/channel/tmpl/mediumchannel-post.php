<?php

/**
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2020 Extly, CB. All rights reserved.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 *
 * @see        https://www.extly.com
 */
defined('_JEXEC') || exit;

$input = $this->input;
$message = $input->getString('message');
$image_url = $input->getString('image_url');
$url = $input->getString('url');
$org_url = $input->getString('org_url');

?>
<p>
	<?php echo TextUtil::autoLink($message); ?>
</p>
<?php

if (!empty($image_url)) {
    ?>
<p>
	<a href="<?php echo $org_url; ?>">
		<img src="<?php echo $image_url; ?>">
	</a>
</p>
<?php
}

if (!empty($org_url)) {
    ?>
<p>
	<a href="<?php echo $org_url; ?>">
		<?php echo $org_url; ?>
	</a>
</p>
<?php
}

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
$imageUrl = $input->getString('image_url');
$shortUrl = $input->getString('url');
$orgUrl = $input->getString('org_url');

$sitename = JFactory::getConfig()->get('sitename');
$siteUrl = RouteHelp::getInstance()->getRoot();

$message = TextUtil::autoLink($message);
$message = str_replace("\n", '<br/><br/>', $message);

?>
<p>
	<?php echo $message; ?>
</p>
<?php

if (!empty($imageUrl)) {
    ?>
<p>
	<a href="<?php echo $orgUrl; ?>">
		<img src="<?php echo $imageUrl; ?>">
	</a>
</p>
<?php
}

if (!empty($orgUrl)) {
    ?>
<p>
	<a href="<?php echo $orgUrl; ?>">
		<?php echo $orgUrl; ?>
	</a>
</p>
<?php
}
?>
<hr />
<p>
	<a href="<?php echo $siteUrl; ?>">
		<?php echo $sitename.' - '.$siteUrl; ?>
	</a>
</p>

<?php
/**
 * @package            Joomla
 * @subpackage         Edocman
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2018 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;
?>
<div class="gallery">
	<?php
	$rootUrl  = JUri::root(true);
	foreach ($images as $image)
	{
		$title      = $image->title;
		$filename   = basename($image->image);
		$thumbPath  = substr($image->image, 0, strlen($image->image) - strlen($filename));
		$thumb      = $rootUrl . '/' . $thumbPath . '/thumbs/' . $filename;
		$largeImage = $rootUrl . '/' . $image->image;
		?>
		<a href="<?php echo $largeImage ?>" data-caption="<?php echo $title; ?>">
			<img src="<?php echo $thumb; ?>" alt="<?php echo $title; ?>">
		</a>
		<?php
	}
	?>
</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        baguetteBox.run('.gallery', {});
    });
</script>

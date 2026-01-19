<?php
/**
 * @version    SVN: <svn_id>
 * @package    tjlms
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access.
defined('_JEXEC') or die();

JHtml::_('behavior.tooltip');
JHtml::_('behavior.framework');
JHtml::_('behavior.modal');

$document = JFactory::getDocument();
$document->addScript(JUri::root(true).'/components/com_tjlms/assets/js/masonry.pkgd.min.js');
$tjlmsparams = $this->tjlmsparams;

// Get pin width
$pin_width = $tjlmsparams->get('pin_width');

if (empty($pin_width))
{
	$pin_width = 170;
}

// Get pin padding
$pin_padding = $tjlmsparams->get('pin_padding');

if (empty($pin_padding))
{
	$pin_padding = 3;
}

// Calulate columnWidth (columnWidth = pin_width+pin_padding)
$columnWidth = $pin_width + $pin_padding;
?>

<style type="text/css">
.tjlms_pin_item { width: <?php echo $pin_width . 'px'; ?> !important; }
</style>

<script type="text/javascript">

	jQuery(document).ready(function()
	{
		var container_<?php echo $random_container;?> = document.getElementById(pin_container_<?php echo $random_container; ?>);
		var msnry = new Masonry( container_<?php echo $random_container;?>, {
			columnWidth: <?php echo $columnWidth; ?>,
			itemSelector: '.tjlms_pin_item'
		});

		setTimeout(function(){
			var container_<?php echo $random_container;?> = document.getElementById(pin_container_<?php echo $random_container; ?>);
			var msnry = new Masonry( container_<?php echo $random_container;?>, {
				columnWidth: <?php echo $columnWidth; ?>,
				itemSelector: '.tjlms_pin_item'
			});
		}, 1000);

		setTimeout(function(){
			var container_<?php echo $random_container;?> = document.getElementById(pin_container_<?php echo $random_container; ?>);
			var msnry = new Masonry( container_<?php echo $random_container;?>, {
				columnWidth: <?php echo $columnWidth; ?>,
				itemSelector: '.tjlms_pin_item'
			});
		}, 3000);
	});
</script>

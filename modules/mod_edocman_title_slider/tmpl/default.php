<?php
/**
 * Edocman title slider
 *
 * @package 	Edocman title slider
 * @subpackage 	Edocman title slider
 * @version   	1.0
 * @author    	Dang Thuc Dam
 * @copyright 	Copyright (C) 2010 - 2016 www.gopiplus.com, LLC
 * @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * http://www.joomdonation.com/
 */
// no direct access
defined('_JEXEC') or die;

$ats_speed 		= (int) $params->get('ats_speed', 700);
$ats_timeout 	= (int) $params->get('ats_timeout', 5000);
$ats_css 		= $params->get('ats_css', 'SLIDER9');
$ats_direction 	= $params->get('ats_direction', 'scrollLeft');

if(!is_numeric($ats_timeout))
{
	$ats_timeout = 5000;
}
if(!is_numeric($ats_speed))
{
	$ats_speed = 700;
}
	
if ( ! empty($items) ) 
{
	$ats_count = 0;
	echo '<div id="ARTICLE-TITLE-'.$ats_css.'">';
	foreach ( $items as $item ) 
	{
		$ats_title 	= $item->title;
		$ats_link 	= $item->links;	
		echo '<p><a href="'.$ats_link.'">'.$ats_title.'</a></p>';
		$ats_count++;
	}
	echo '</div>';
}
?>
<!-- start Edocman title slider -->
<script type="text/javascript">jQuery.noConflict();</script>
<script type="text/javascript">
jQuery(function() {
jQuery('#ARTICLE-TITLE-<?php echo $ats_css; ?>').cycle({
	fx: '<?php echo $ats_direction; ?>',
	speed: <?php echo $ats_speed; ?>,
	timeout: <?php echo $ats_timeout; ?>
});
});
</script>
<!-- end Edocman title slider -->
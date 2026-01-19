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
<div id="pp-<?php echo $lottieFileName;?>-svg"></div>

<?php if (!$rendered) { ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.5.9/lottie.min.js"></script>
<?php } ?>

<script type="text/javascript">
var animationRemote = bodymovin.loadAnimation({
	container: document.getElementById('pp-<?php echo $lottieFileName;?>-svg'),
	path: '<?php echo $lottieUrl;?>',
	autoplay: <?php echo $options['autoplay'] ? 'true' : 'false';?>,
	renderer: '<?php echo $options['renderer'];?>',
	loop: <?php echo $options['loop'] ? 'true' : 'false';?>
});
</script>
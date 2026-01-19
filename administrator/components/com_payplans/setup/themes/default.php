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
<!DOCTYPE html>
<html class="demo-mobile-horizontal" lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>PayPlans Installation</title>

	<link href="<?php echo PP_SETUP_URL;?>/assets/images/logo.png" rel="shortcut icon" type="image/vnd.microsoft.icon"/>

	<link type="text/css" href="//fonts.googleapis.com/css?family=Roboto:400,400italic,700,700italic,500italic,500,300italic,300" rel="stylesheet">
	<link type="text/css" href="<?php echo PP_SETUP_URL;?>/assets/styles/theme.css" rel="stylesheet" />

	<?php if (JVERSION < 4.0) { ?>
		<script src="<?php echo JURI::root(true);?>/media/jui/js/jquery.min.js"></script>
	<?php } else { ?>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.6/umd/popper.min.js"></script>
		<script src="<?php echo JURI::root(true);?>/media/vendor/jquery/js/jquery.min.js"></script>
	<?php } ?>

	<script>
	<?php require(JPATH_ROOT . '/administrator/components/com_payplans/setup/assets/scripts/script.js'); ?>
	</script>
</head>
<body class="install-step<?php echo $active;?>" data-gr-c-s-loaded="true">
	<div class="si-installer">
		<div class="si-installer__left">
			<div id="svg__ani-sidebarbg" class="si-installer__left-ani-bg"></div>
			<div class="si-installer__left-bd">
				<div class="si-installer__brand">
					<img src="<?php echo PP_SETUP_URL;?>/assets/images/v2/logo-payplans.svg" />
					<p>PayPlans is the best paid membership subscriptions for Joomla</p>
				</div>
			</div>
			<div class="si-installer__left-ft">
				<div class="si-installer-links-group">
					<div class="si-installer-links-group__item">
						<a href="https://facebook.com/StackIdeas" target="_blank">
							<img src="<?php echo PP_SETUP_URL;?>/assets/images/v2/icon-facebook.svg" alt="">&nbsp; Facebook
						</a>
					</div>
					<div class="si-installer-links-group__item">
						<a href="https://twitter.com/StackIdeas" target="_blank">
							<img src="<?php echo PP_SETUP_URL;?>/assets/images/v2/icon-twitter.svg" alt="">&nbsp; Twitter
						</a>
					</div>
					<div class="si-installer-links-group__item">
						<a href="https://stackideas.com/forums" target="_blank">
							<img src="<?php echo PP_SETUP_URL;?>/assets/images/v2/icon-help.svg" alt="">&nbsp; Need Help?
						</a>
					</div>
					
				</div>
			</div>
		</div>
		
		<div action="" class="si-installer__right">
			<div class="si-installer__right-bd">
				<div class="si-installer-content">
					<div class="si-installer-content__hd">
						<div class="si-installer-content__title">
							<b><?php echo JText::_($activeStep->title);?></b>
						</div>

						<?php if ($activeStep->template != 'complete') { ?>
						<div class="si-installer-content__sub-title">
							<?php echo JText::_($activeStep->desc);?>
						</div>
						<?php } ?>
					</div>
					<div class="si-installer-content__bd">
						<?php include(__DIR__ . '/steps/' . $activeStep->template . '.php'); ?>
					</div>
				</div>
			</div>

			<?php if ($active != 'complete') { ?>
			<div class="si-installer__right-ft">
				<?php include_once(__DIR__ . '/actions.php'); ?>
			</div>
			<?php } ?>
		</div>
	</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.5.9/lottie.min.js"></script>
<script type="text/javascript">
var animationRemoteBg = bodymovin.loadAnimation({
	container: document.getElementById('svg__ani-sidebarbg'),
	path: '<?php echo PP_SETUP_URL;?>/assets/images/background.json',
	autoplay: true,
	renderer: 'svg',
	loop: true
});
</script>
</body>
</html>

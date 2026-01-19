<?php if (isset($redirectUrl) && $redirectUrl) { ?>

	PayPlans.ready(function($) {

		window.onload = function(){
			setTimeout(function() {
				window.location.href = "<?php echo JRoute::_($redirectUrl, false);?>";
			}, 300);
		}
	});
<?php } ?>
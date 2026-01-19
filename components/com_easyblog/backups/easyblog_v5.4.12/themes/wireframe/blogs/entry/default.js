EasyBlog.require()
.script('site/posts/entry')
<?php if ($this->config->get('main_google_analytics_script')) { ?>
.script('https://www.googletagmanager.com/gtag/js?id=<?php echo $this->config->get("main_google_analytics_id"); ?>')
<?php } ?>
<?php if ($this->config->get('layout_dropcaps')) { ?>
.script('site/dropcap')
<?php } ?>
.done(function($) {
	var trackingId = '<?php echo $this->config->get("main_google_analytics_id"); ?>';

	<?php if ($gaEnabled) { ?>
		var gaExists = false;

		// Determine if similar GA function is exists on the page, eg: from the template. #1343
		if (typeof gtag === 'function' || typeof ga === 'function' || typeof _gaq === 'function') {
			gaExists = true;
		}

		// We still load our own gtag method to be use in infinite scroll.
		window.dataLayer = window.dataLayer || [];
		window.ezb.gtag = function() {
			dataLayer.push(arguments);
		}

		window.ezb.gtag('js', new Date());

		// Track the page for the first time
		if (!gaExists) {
			window.ezb.gtag('config', trackingId);
		}
	<?php } ?>

	$('[data-eb-posts]').implement(EasyBlog.Controller.Entry, {
		"autoload": true,
		"ga_enabled": <?php echo $gaEnabled ? 'true' : 'false'; ?>,
		"ga_tracking_id": trackingId,
		"currentPageUrl": "<?php echo $this->html('string.escape', $post->getExternalPermalink()); ?>",
		"isEntryView": 'true',
		"hasPinterestEmbedBlock": <?php echo $hasPinterestEmbedBlock ? 'true' : 'false'; ?>,
		"pinterestExternalShareBtnEnabled": <?php echo EB::isExternalPinterestShareEnabled() ? 'true' : 'false'; ?>
	});

	<?php if ($this->config->get('layout_dropcaps')) { ?>
	var item = $('.eb-entry-article[data-blog-content]');

	// Built-in composer
	// If user uses readmore or doesn't have automated truncation,
	// we can get the first composer block
	var block = $.getFirstTextBlock(item);

	if (block) {
		block.addClass('has-drop-cap');
		return;
	}

	// Handle legacy editor that uses readmore tag in the editor.
	var paragraph = $.getFirstParagraph(item);

	if (paragraph) {
		paragraph.addClass('has-drop-cap');
	}
	<?php } ?>
});

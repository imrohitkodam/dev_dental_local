EasyBlog.require()
.script('site/posts/listings')
.done(function($) {

	$('[data-blog-listings]').implement(EasyBlog.Controller.Listings, {
		"ratings": <?php echo $this->config->get('main_ratings') ? 'true' : 'false';?>,
		"autoload": <?php echo $showLoadMore ? 'true' : 'false'; ?>,
		"hasPinterestEmbedBlock": <?php echo $hasPinterestEmbedBlock ? 'true' : 'false'; ?>,
		"pinterestExternalShareBtnEnabled": <?php echo EB::isExternalPinterestShareEnabled() ? 'true' : 'false'; ?>
	});
});

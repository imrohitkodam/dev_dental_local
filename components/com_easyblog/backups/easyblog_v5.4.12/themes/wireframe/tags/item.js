EasyBlog.require()
.script('site/posts/posts')
.done(function($) {
	$('[data-blog-posts]').implement(EasyBlog.Controller.Posts, {
		"ratings": <?php echo $this->config->get('main_ratings') ? 'true' : 'false';?>,
		"hasPinterestEmbedBlock": <?php echo $hasPinterestEmbedBlock ? 'true' : 'false'; ?>,
		"pinterestExternalShareBtnEnabled": <?php echo EB::isExternalPinterestShareEnabled() ? 'true' : 'false'; ?>
	});
});

PayPlans.require()
.done(function($) {

	$('[data-articlelist-all]').click(function(ev){
		$('[data-article-item]').attr('checked', true);
		$('[data-article-item]').prop('checked', true);
	});

	$('[data-articlelist-none]').click(function(ev){
		$('[data-article-item]').attr('checked', false);
		$('[data-article-item]').prop('checked', false);
	});

});

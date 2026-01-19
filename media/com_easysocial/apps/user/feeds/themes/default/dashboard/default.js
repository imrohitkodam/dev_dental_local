
EasySocial.ready(function($) {

$('[data-feeds-create]' ).on('click', function() {
	
	EasySocial.dialog({
		content: EasySocial.ajax("apps/user/feeds/views/feeds/form" , { 'id' : '<?php echo $app->id;?>' } )
	});
});

$('[data-feeds-lists]')
	.on('click', '[data-feeds-item-remove]', function() {
		var button = $(this);
		var item = button.parents('[data-item]');
		var id = item.data('id');

		EasySocial.dialog({
			content	: EasySocial.ajax("apps/user/feeds/views/feeds/confirmDelete" , { 'id' : '<?php echo $app->id;?>' } ),
			bindings : {
				"{deleteButton} click" : function() {
					EasySocial.ajax( 'apps/user/feeds/controllers/feeds/delete', {
						"id"		: "<?php echo $app->id;?>",
						"feedId"	: id
					})
					.done(function() {
						EasySocial.dialog().close();

						item.remove();

						if ($('[data-feeds-lists]').children().length == 0) {
							$('[data-app-contents]').addClass('is-empty');
						}
					});
				}
			}
		});
	});


});

EasyBlog.require()
.script('layout/responsive')
.done(function($){

	$(document).on('click', '[data-blog-toolbar-logout]', function(event) {
		$('[data-blog-logout-form]').submit();
	});

    $('.btn-eb-navbar').click(function() {
        $('.eb-navbar-collapse').toggleClass("in");
        return false;
    });

	$('#ezblog-head #ezblog-search').bind('focus', function(){

        $(this).animate({
            width: '170'
        });
	});

	$('#ezblog-head #ezblog-search').bind( 'blur' , function(){
		$(this).animate({ width: '120'});
	});

	$('#ezblog-menu').responsive({at: 540, switchTo: 'narrow'});

	$('.eb-nav-collapse').responsive({at: 560, switchTo: 'nav-hide'});
	$('.btn-eb-navbar').click(function() {
		$('.eb-nav-collapse').toggleClass("nav-show");
		return false;
	});

});

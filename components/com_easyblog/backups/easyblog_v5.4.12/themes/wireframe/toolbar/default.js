EasyBlog.ready(function($){

	// Prevent closing
	$(document).on('click.toolbar', '[data-eb-toolbar-dropdown]', function(event) {
		event.stopPropagation();
	});

	// Logout
	$(document).on('click', '[data-blog-toolbar-logout]', function(event) {
		$('[data-blog-logout-form]').submit();
	});

	// Search
	$(document)
		.off('click.search.toggle')
		.on('click.search.toggle', '[data-eb-toolbar-search-toggle]', function() {
			var searchBar = $('[data-eb-toolbar-search]');
			var ebToolBar = $('[data-eb-toolbar]');

			ebToolBar.toggleClass('eb-toolbar--search-on');
		});

	<?php if (($this->isMobile() || $this->isTablet()) && $showToolbar && $canAccessToolbar && $showHamburgerIcon && $loadMMenu) { ?>
	EasyBlog.require()
	.script('site/mmenu')
	.done(function($) {

		new Mmenu("#eb-canvas", {
			"extensions": [
				"pagedim-black",
				"theme-dark",
				"fullscreen",
				"popup" // #2216
			],
			searchfield : {
				panel: true,
				placeholder: '<?php echo JText::_('COM_EASYBLOG_SEARCH', true);?>',
				noResults: '<?php echo JText::_('COM_EB_SEARCH_NO_RESULTS', true);?>'
			},
			"navbars": [
				{
					"position": "top",
					"content": [
						"searchfield",
						"close"
					]
				}
			],
			"navbar": {
				"title": "<?php echo JText::_("COM_EB_MENU", true);?>"
			}
		}
		<?php if ($this->input->get('option', 'com_easyblog', 'string') == 'com_easyblog') { ?>
		, {
			offCanvas: {
				page: {
					selector: "#eb.eb-component"
				}
			}
		}
		<?php } ?>
		);
	});
	<?php } ?>
});

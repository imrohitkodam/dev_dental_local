PayPlans
.ready(function($) {

	$('[data-pp-user-item]').on('click', function(event) {
		event.preventDefault();

		var item = $(this);
		var obj = {
				'id': item.data('id'),
				'title': item.data('title')
		};

		window.parent['<?php echo JFactory::getApplication()->input->get('jscallback', '', 'cmd');?>'].apply(null, [obj]);
	});

	var boxChecked = $('[data-table-grid-box-checked]');

	var updateBoxChecked = function() {
		var total = $('[data-table-grid-id]:checked').length;
		boxChecked.val(total)
			.trigger('change');
	};

	// Clicking on table row api
	$(document).on('click.check', '[data-table-grid] tbody > tr', function(event) {
		var target = event.target.tagName;

		if (target != 'TD') {
			return;
		}

		// Get the row
		var row = $(this);
		var checkbox = row.find('[data-table-grid-id]');
		var checked = checkbox.is(':checked');

		// Check or uncheck it depending on the current state
		// console.log(row.children('[data-table-grid-id]'));

		if (!checked) {
			checkbox.attr('checked', !checked);
		} else {
			checkbox.removeAttr('checked');
		}

		updateBoxChecked();

		checkbox.trigger('change');
	});

	$(document).on('change.checkbox', '[data-table-grid] tbody > tr [data-table-grid-id]', function(event) {

		var checked = $(this).is(':checked');
		var isRadio = $(this).is(':radio');
		var row = $(this).parents('tr');

		if (isRadio) {
			row.removeClass('is-checked');
		}

		row.toggleClass('is-checked', checked);

		updateBoxChecked();
	});

	// table filter on change
	$('[data-table-grid-filter]').on('change', function() {
		$('[data-table-grid]').submit();
	});

	// ordering up
	$('[data-grid-column] [data-grid-order-up]').on('click', function() {
		var row = $(this).closest('tr');
		var checkbox = $(row).find('input[name=cid\\[\\]]');

		var element = $(this);
		var task = element.data('task');

		// Ensure that the checkbox is checked
		$(checkbox).prop('checked', true);

		$('[data-table-grid-task]').val(task);
		$('[data-table-grid]').submit();
	});

	// ordering down
	$('[data-grid-column] [data-grid-order-down]').on('click', function() {

		var row = $(this).closest('tr');
		var checkbox = $(row).find('input[name=cid\\[\\]]');

		var element = $(this);
		var task = element.data('task');

		// Ensure that the checkbox is checked
		$(checkbox).prop('checked', true);
		
		$('[data-table-grid-task]').val(task);
		$('[data-table-grid]').submit();
	});

	// Search filters
	$(document).on('keyup.search', '[data-table-grid-search-input]', function(event) {
		var input = $(this);
		var spacer = $('[data-table-grid-search-spacer]');
		var reset = $('[data-table-grid-search-reset]');

		if (event.keyCode == 13 && input.val() != '') {
			$.Joomla('submitform');
		}

		if (input.val() !== '') {
			reset.removeClass('t-hidden');
			spacer.addClass('t-hidden');
			return;
		}

		reset.addClass('t-hidden');
		spacer.removeClass('t-hidden');
	});

	$(document).on('click.search', '[data-table-grid-search-reset]', function(event) {
		var reset = $(this);
		var submit = $('[data-table-grid-search-submit]');
		var input = reset.parents('[data-table-grid-search]').find('[data-table-grid-search-input]');

		input.val('');

		$.Joomla('submitform');
		// submit.click();
	});

	$(document).on('click.searchbutton', '[data-table-grid-search-button]', function(event) {
		var input = $('[data-table-grid-search-input]');

		if (input.val() == '') {
			return;
		}

		$.Joomla('submitform');
	});


	// Retrieves a list 
	PayPlans.getSelectedIds = function() {
		var selected = [];
		var checked = $('[data-table-grid]').find('[data-table-grid-id]:checked');

		if (checked.length <= 0) {
			return selected;
		}

		$(checked).each(function(i, checkbox) {
			var value = $(checkbox).val();
			selected.push(value);
		});


		return selected;
	};


	// Sorting
	$('[data-table-grid-sort]').on('click', function() {
		var element = $(this);
		var direction = element.data('direction');
		var column = element.data('sort');

		$('[data-table-grid-ordering]').val(column);
		$('[data-table-grid-direction]').val(direction);
		$('[data-table-grid]').submit();
	});

	boxChecked.on('change', function() {
		var isJoomla4 = $('body').hasClass('is-joomla-4');
		if (!isJoomla4) {
			return;
		}

		// We only process these in Joomla 4
		
		var element = $(this);
		var count = element.val();

		// determine if we need to disable the toolbar buttons or not.
		var disable = count > 0 ? false: true;

		var items = document.querySelectorAll('joomla-toolbar-button');
		$(items).each(function(index, item) {
			if (item.listSelection) {
				item.setDisabled(disable);
			}
		});

	});

});

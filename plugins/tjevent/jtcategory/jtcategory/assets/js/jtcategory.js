/** global: cat_id */
if (jQuery("#lesson_formatjtcategorycategory").val() == cat_id)
{
	jQuery('#eventdiv').show();
	/** global: lessonParams */
	jQuery('input[name=mark_as_complete]').val(lessonParams);
}
else
{
	jQuery('#type_result, #eventInfo').hide();
}

jQuery(".lesson_format #lesson_formatjtcategorycategory").on('change', function()
{
	jQuery('#eventInfo table tbody').html('');

	var category_id = jQuery("#lesson_formatjtcategorycategory").val();

	if(category_id != 0)
	{
		jQuery('#eventdiv').show();

		jQuery.ajax({
		type:'POST',
		url:root_url + 'index.php?option=com_jticketing&format=json&task=event.getCategorySpecificEventCount',
		data: {catid:category_id},
		success:function(response){
			jQuery("#complete_mark").attr({"max" : response.data});
			},
		});
	}
	else
	{
		jQuery('#eventInfo').hide();
	}
});

/* Function to load the loading image. */
function validateeventjtcategory(formid, format, subformat, media_id) {
	var res = {
		check: 1,
		message: "PLG_TJEVENT_JTEVENT_VAL_PASSES"
	};

	var format_lesson_form = jQuery("#lesson-format-form_" + formid);
	minMaxId('#eventInfo .table-striped tbody tr');
	var event_number = jQuery('input[name=mark_as_complete]').val();

	var source = {numberOfEvents: event_number};


	jQuery("#category_params", format_lesson_form).val(JSON.stringify(source));

	return res;
}

var form_id, format_lesson_form, eventid, course_id;

function minMaxId(selector) {
	var min = null,
		max = null;
	var previousVal = 0;

	jQuery(selector, format_lesson_form).each(function(index) {

		if (index === 0) {
			previousVal = parseInt(jQuery(this).find('td').eq(3).text());
		} else {
			if (parseInt(jQuery(this).find('td').eq(3).text()) < previousVal) {
				previousVal = parseInt(jQuery(this).find('td').eq(3).text());
			}
		}
	});

	return previousVal;
}

jQuery(".btn-group.radio label").click(function()
{
	var label = jQuery(this);
	var input = jQuery('#' + label.attr('for'));

	if (!input.prop('checked'))
	{
		label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
		if (input.val() == '') {
			label.addClass('active btn-success');
		} else if (input.val() == 0) {
			label.addClass('active btn-danger');
			jQuery('#eventInfo').hide();
			jQuery('.tickettypes').prop('checked', false);
		} else {
			label.addClass('active btn-success');
			jQuery('#eventInfo').show();
			jQuery('.tickettypes').prop('checked', true);
		}
		input.prop('checked', true);
	}
});
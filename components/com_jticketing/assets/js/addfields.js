


	/*Function for login*/
	function jt_login(obj)
	{
		techjoomla.jQuery.ajax({
			url: jtBaseUrl + '?option=com_jticketing&task=buy.login_validate&tmpl=component',
			type: 'post',
			data: techjoomla.jQuery('#user-info-tab #login :input'),
			dataType: 'json',
			beforeSend: function(){
				techjoomla.jQuery('#button-login').attr('disabled', true);
				techjoomla.jQuery('#button-login').after('<span class="wait">&nbsp;Loading..</span>');
			},
			complete: function(){
				techjoomla.jQuery('#button-login').attr('disabled', false);
				techjoomla.jQuery('.wait').remove();
			},
			success: function(json){
				techjoomla.jQuery('.warning, .j2error').remove();

				if (json['error']){
					techjoomla.jQuery('#login').prepend('<div class="warning danger" >' + json['error']['warning'] + '<button data-dismiss="alert" class="close" type="button">×</button></div>');
					techjoomla.jQuery('.warning').fadeIn('slow');
				}
				else if (json['redirect']){
					updateBillingDetails();
					techjoomla.jQuery('#btnWizardNext').show();

				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
			}
		});
	}

	/*Get updated billing details.*/
	function updateBillingDetails()
	{
		techjoomla.jQuery('#btnWizardNext').removeAttr('disabled');

		techjoomla.jQuery.ajax({
			url: jtBaseUrl + '?option=com_jticketing&task=buy.getUpdatedBillingInfo&tmpl=component',
			type: 'post',
			data: techjoomla.jQuery('#user-info-tab #login :input'),
			dataType: 'json',
			beforeSend: function(){
			},
			complete: function(){
			},
			success: function(json){
				if (json['error']){
				}
				else if (json['billing_html'])
				{
					/*Update billing tab step HTML.*/
					techjoomla.jQuery('#step_billing_info').html(json['billing_html']);
					/*Update state selct list options.*/
					jticketing_generateState('country','','');
					techjoomla.jQuery('#billing_info_data').show();



				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
			}
		});
	}


	function addClone(rId,rClass)
	{
		var num=jQuery("."+rClass).length;
		var removeButton="<div class='pull-right jt_add_remove_button' >";
		var removeButton ="<button class='btn btn-small btn-sm btn-danger' type='button' id='remove"+num+"'";
		removeButton+="onclick=\"removeClone('jticketing_container"+num+"','jticketing_container');\" title= 'Remove Ticket type' >";
		removeButton+="<i class=\"icon-minus-sign\"></i></button>";
		removeButton+="</div>";
		var newElem=jQuery('#'+rId).clone().attr('id',rId+num);

		jQuery(newElem).children('.com_jticketing_repeating_block').children('.jticketing-form-group').children('.jticketing-controls').children('').children().each(function(){
			var kid=jQuery(this);

			if(kid.attr('id')!=undefined)
			{

				var idN=kid.attr('id');
				kid.attr('id',idN+num).attr('id',idN+num);

				if(kid.is('input:text') )
				{

					/*if input type is text then empty value*/
					kid.val("");
				}
			}

		});

		jQuery(newElem).children('.com_jticketing_repeating_block').children('.ticket_type_available_field').show();

		jQuery('.'+rClass+':last').after(newElem);

		if (num >=1)
		{
			jQuery('div.'+rClass + ":last "+ ' .jt_add_remove_button').append(removeButton);
			jQuery('#jticketing_container'+num+' #removebtn').hide();
		}

		jQuery("select").trigger("liszt:updated");  /* IMP : to update to chz-done selects*/

	}
	/* remove clone script */
	function removeClone(rId,rClass,ids)
	{
		var msg = Joomla.JText._("COM_JTICKETING_CONFIRM_TO_DELETE");

		if (confirm(msg) == true)
		{
			if(ids==undefined)
				jQuery('#'+rId).remove();
			else
				jQuery('#'+'jticketing_container'+ids).remove();
		}
	}


	techjoomla.jQuery(document).ready(function(){
		techjoomla.jQuery("select").trigger("liszt:updated");  /* IMP : to update to chz-done selects*/


		techjoomla.jQuery('#MyWizard').on('change', function(e, data) {
			values=techjoomla.jQuery('#ticketform').serialize();
			var ref_this = techjoomla.jQuery("#jticketing-steps li[class='active']");
			var stepId = ref_this[0].id;
			var collect_attendee_information=techjoomla.jQuery('#collect_attendee_information').val();

			if(stepId === "id_step_payment_info")
			{
				techjoomla.jQuery('#btnWizardNext').hide();
			}
			else
			{
				techjoomla.jQuery('#btnWizardNext').show();
			}

			/*for Step No.1*/
			if(stepId==="id_step_select_ticket" && data.direction === 'next')
			{
				var total_calc_amt=total_calc_amt1=0;
				techjoomla.jQuery('input[class*=\"type_ticketcounts\"]').each(function(){
					total_calc_amt1=parseFloat(total_calc_amt1)+parseFloat(techjoomla.jQuery(this).val())
					});

					if(parseInt(total_calc_amt1)<=0 && eventType==0 || (isNaN(total_calc_amt1)) && eventType==0 ||	(total_calc_amt1=='') && eventType==0)
					{
						alert("Please Enter No of Tickets");
						return e.preventDefault();
					}
					else
					{
						loadingImage();
						techjoomla.jQuery.ajax({

								url: root_url+'?option=com_jticketing&task=buy.save_step_selectTicket&tmpl=component',
								   type: 'POST',
								   data:values,
								   dataType: 'json',
								   async:'false',
								   beforeSend: function() {

								   },
								   complete: function() {

								   },
								   success: function(data)
								   {
									   /* Now Set Inner Html of Step No2 to Fill Attendee Fields*/
										if(data.attendee_html)
										{
											techjoomla.jQuery('#step_select_attendee').html(data.attendee_html)
										}
										var eventType = techjoomla.jQuery('#event_type').val();

										if(eventType == 0)
										{
											var btnName = Joomla.JText._('COM_JTICKETING_SAVE_AND_CLOSE');
											techjoomla.jQuery('#btnWizardNext').text(btnName);
										}
								   },
						   });

					}

			}

			/*for Step No.2*/
			if(stepId === "id_step_select_attendee" && (data.direction === 'next') ) {

					var f = document.attendee_field_form;
					if (!document.formvalidator.isValid(f))
					{
						if(data.direction === 'next')
						{
							alert("Please Fill All Required Fields");
							return e.preventDefault();
						}
					}
					else
					{
						techjoomla.jQuery(".alert-error").hide();
						loadingImage();

						values=techjoomla.jQuery('#attendee_field_form').serialize();
						techjoomla.jQuery.ajax({

								url: root_url+'?option=com_jticketing&task=buy.save_step_selectAttendee&tmpl=component',
								   type: 'POST',
								   data:values,
								   dataType: 'json',
								   beforeSend: function() {

								   },
								   complete: function() {

								   },
								   success: function(data)
								   {
									   /* Now Set Inner Html of Step No2 to Fill Attendee Fields*/
									   if(data.attendee_html)
										techjoomla.jQuery('#step_select_attendee').html(data.attendee_html)
								   },

						   });

					}

			}
			/*for Step No.3*/
			if(stepId === "id_step_billing_info" && data.direction === 'next')
			{

				var f = document.billing_info_form;
				if (!document.formvalidator.isValid(f))
				{

					alert("Please Fill All Required Fields");
					techjoomla.jQuery('#btnWizardNext').show();

					return e.preventDefault();

				}
				else if (parseInt(terms_enabled)===1)
				{
					if(document.getElementById('accpt_terms').checked===true)
					{
						techjoomla.jQuery('#btnWizardNext').show();
					}
					else
					{
						techjoomla.jQuery('#btnWizardNext').show();
						alert("Please accept terms and conditions.");
						return e.preventDefault();
					}
				}

				techjoomla.jQuery(".alert-error").hide();
				values=techjoomla.jQuery('#billing_info_form').serialize();
				techjoomla.jQuery.ajax({
					url: root_url+'?option=com_jticketing&task=buy.save_step_billinginfo&tmpl=component',
					   type: 'POST',
					   data:values,
					   dataType: 'json',
					   async:false,
					   beforeSend: function() {
						    loadingImage();

					   },
					   complete: function() {
						hideImage();
					   },
					   success: function(data)
					   {
						   /* Now Set Inner Html of Step No2 to Fill Attendee Fields*/
							if(data.payment_html)
							{
								techjoomla.jQuery('#step_payment_info').html(data.payment_html);
							}

							/*if(data.redirect_events_view)
							{
								document.location=data.redirect_events_view;
							}*/

							if(data.redirect_invoice_view)
							{
								document.location=data.redirect_invoice_view;
							}
					   },

				   });

			}

			setTimeout(function(){ hideImage() },10);
			techjoomla.jQuery('html,body').animate({scrollTop: techjoomla.jQuery("#jticketing-steps").offset()},'slow');

		});

		techjoomla.jQuery('#MyWizard').on('changed', function(e, data) {

			var thisactive = techjoomla.jQuery("#jticketing-steps li[class='active']");
			stepthisactive = thisactive[0].id;


			if(stepthisactive == techjoomla.jQuery("#jticketing-steps li").first().attr('id'))
				techjoomla.jQuery(".jticketing-form #btnWizardPrev").hide();
			else
				techjoomla.jQuery(".jticketing-form #btnWizardPrev").show();

			if(stepthisactive ==techjoomla.jQuery("#jticketing-steps li").last().attr('id')){
				techjoomla.jQuery(".jticketing-form .prev_next_wizard_actions").hide();
				var prev_button_html='<button id="btnWizardPrev" onclick="techjoomla.jQuery(\'#MyWizard\').wizard(\'previous\');"	type="button" class="btn btn-prev pull-left" > <i class="icon-arrow-left" ></i>Prev</button>';
				if(stepthisactive =="id_step_payment_info" ){
					techjoomla.jQuery('#jticketing-payHtmlDiv div.form-actions').prepend( prev_button_html );
					techjoomla.jQuery('#jticketing-payHtmlDiv div.form-actions input[type="submit"]').addClass('pull-right');
				}
			}
			else
			{
				techjoomla.jQuery(".jticketing-form .prev_next_wizard_actions").show();
				techjoomla.jQuery('#btnWizardNext').show();

			}

			// If billing info step
			if (stepthisactive=='id_step_billing_info')
			{
				if (techjoomla.jQuery('#user-info-tab').is(':visible'))
				{
					techjoomla.jQuery('#btnWizardNext').hide();
				}
				else
				{
					techjoomla.jQuery('#btnWizardNext').show();

				}
			}


		});
		techjoomla.jQuery('#MyWizard').on('finished', function(e, data) {
			var ref_this = techjoomla.jQuery("#jticketing-steps li[class='active']");
			var stepId = ref_this[0].id;
			var collect_attendee_information=techjoomla.jQuery('#collect_attendee_information').val();

		});
		techjoomla.jQuery('#btnWizardPrev').on('click', function() {
			var ref_this = techjoomla.jQuery("#jticketing-steps li[class='active']");
			var stepId = ref_this[0].id;
			var collect_attendee_information=techjoomla.jQuery('#collect_attendee_information').val();
			techjoomla.jQuery('#btnWizardNext').show();
			techjoomla.jQuery('#MyWizard').wizard('previous');


		});
		techjoomla.jQuery('#btnWizardNext').on('click', function() {
			var ref_this = techjoomla.jQuery("#jticketing-steps li[class='active']");
			var stepId = ref_this[0].id;
			console.log(stepId);
			var captch_enabled = techjoomla.jQuery('#captch_enabled').val()

			if(parseInt(captch_enabled)==1 && stepId.toString()=='id_step_select_ticket')
			{
				var captcha_response = techjoomla.jQuery("textarea#g-recaptcha-response").val();

				if (captcha_response)
				{
					techjoomla.jQuery('#MyWizard').wizard('next');
				}
				else
				{
					alert("Please validate captcha");
				}
			}
			else
			{
				techjoomla.jQuery('#MyWizard').wizard('next');
			}

		});
		techjoomla.jQuery('#btnWizardStep').on('click', function() {
		  var item = techjoomla.jQuery('#MyWizard').wizard('selectedItem');
		});
		techjoomla.jQuery('#MyWizard').on('stepclick', function(e, data) {

			var ref_this = techjoomla.jQuery("#jticketing-steps li[class='active']");
			var stepId = ref_this[0].id;
			var collect_attendee_information=techjoomla.jQuery('#collect_attendee_information').val();
			if(stepId==="id_step_payment_info")
			{
				techjoomla.jQuery('#btnWizardNext').show();
			}


			if(stepId ==="id_step_select_attendee" && collect_attendee_information==1)
			{
					var f = document.attendee_field_form;
					if (!document.formvalidator.isValid(f))
					{
						alert("Please Fill All Required Fields");
						return e.preventDefault();
					}
					else
					{
						techjoomla.jQuery(".alert-error").hide();
						values=techjoomla.jQuery('#attendee_field_form').serialize();
						techjoomla.jQuery.ajax({

								url: root_url+'?option=com_jticketing&task=buy.save_step_selectAttendee&tmpl=component',
								   type: 'POST',
								   data:values,
								   dataType: 'json',
								   beforeSend: function() {


								   },
								   complete: function() {

								   },
								   success: function(data)
								   {
									   /* Now Set Inner Html of Step No2 to Fill Attendee Fields*/
									   if(data.attendee_html)
										techjoomla.jQuery('#step_select_attendee').html(data.attendee_html)
								   },

						   });

					}
			}

		});

		/* optionally navigate back to 2nd step*/
		techjoomla.jQuery('#btnStep2').on('click', function(e, data) {
		  techjoomla.jQuery('[data-target=#step2]').trigger("click");
		});

	});

	function loadingImage()
	{
		techjoomla.jQuery('<div id="appsloading"></div>')
		.css("background", "rgba(255, 255, 255, .8) url('"+root_url+"components/com_jticketing/assets/images/loading_data.gif') 50% 15% no-repeat")
		.css("top", jQuery('#TabConetent').position().top - jQuery(window).scrollTop())
		.css("left", jQuery('#TabConetent').position().left - jQuery(window).scrollLeft())
		.css("width", jQuery('#TabConetent').width())
		.css("height", jQuery('#TabConetent').height())
		.css("position", "fixed")
		.css("z-index", "1000")
		.css("opacity", "0.80")
		.css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity = 80)")
		.css("filter", "alpha(opacity = 80)")
		.appendTo('#TabConetent');
	}

	function hideImage()
	{
		techjoomla.jQuery('#appsloading').remove();
	}

	function goToByScroll(id)
	{
		 techjoomla.jQuery('html,body').animate({
				 scrollTop: techjoomla.jQuery("#"+id).offset().top},
				 'slow');
	}

	function Jticketing_chkbillmail(logoutmessage,userid)
	{

		email=techjoomla.jQuery('#email1').val();
		techjoomla.jQuery('#btnWizardNext').removeAttr('disabled');


		/*if logged in user*/
		if(userid>0)
		return true;

		var	isguest=techjoomla.jQuery('input[name="account_jt"]:checked').val();

		//if(isguest!="register")
		//return true;

		techjoomla.jQuery.ajax({
		url: root_url+'?option=com_jticketing&task=buy.chkmail&email='+email+'&tmpl=component&view=buy',
		type: 'GET',
		dataType: 'json',
		success: function(data)
		{
			if(data[0] == 1){
				alert(logoutmessage);
				techjoomla.jQuery('#user-info-tab').show();
				techjoomla.jQuery('#user_info').show();
				techjoomla.jQuery('#btnWizardNext').hide();
			}
			else{
				techjoomla.jQuery('#billing_info_data').show();
				techjoomla.jQuery('#user-info-tab').hide();
				techjoomla.jQuery('#btnWizardNext').removeAttr('disabled');
				techjoomla.jQuery('#btnWizardNext').show();
			}
		}
		});
	}
	function toggleDisplay(Id,logoutmessage,logged_in_userid)
	{
		Jticketing_chkbillmail(logoutmessage,logoutmessage,logged_in_userid);
		techjoomla.jQuery('#' + Id).show();
	}



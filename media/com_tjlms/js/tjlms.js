jQuery(document).ready(function() {
	jQuery('[data-bs-toggle="tooltip"]').tooltip();
});

var tjlms = {
	courses: {
		init: function(pin_container, columnWidth) {
			jQuery(document).ready(function() {
				var container = document.getElementById('tjlms_pin_container');
				var msnry = new Masonry(document.getElementById('tjlms_pin_container'), {
					columnWidth: columnWidth,
					itemSelector: '.tjlmspin'
				});

				setTimeout(function() {
					var container = document.getElementById('tjlms_pin_container');
					var msnry = new Masonry(document.getElementById('tjlms_pin_container'), {
						columnWidth: columnWidth,
						itemSelector: '.tjlmspin'
					});
				}, 1000);


				setTimeout(function() {
					var container = document.getElementById('tjlms_pin_container');
					var msnry = new Masonry(document.getElementById('tjlms_pin_container'), {
						columnWidth: columnWidth,
						itemSelector: '.tjlmspin'
					});
				}, 3000);
			});
		}
	},
	course: {
		init: function(openModuleId, courseData, courseLayout) {

			/*Display enroll button before module's for mobile view*/
			jQuery(document).ready(function() {
				// jQuery('button').attr('disabled','disabled');
				// jQuery('a').addClass('inactiveLink');

				if (courseLayout !== undefined)
				{
					jQuery('body').addClass(courseLayout);
				}

				if(jQuery(window).width() < 767)
				{
					jQuery('.enrollHtml').insertBefore( ".tjlms_course_toc" );
				}
			
				// jQuery('a').removeClass('inactiveLink');
				// jQuery('button').removeAttr('disabled','disabled');

				/*show the last accessed lesson module open */
				if (openModuleId) {
					var panelCollapse = jQuery('#modlist_' + openModuleId + ' [data-jstoggle="collapse"]');

					jQuery(panelCollapse).removeClass('collapsed');
					var target = jQuery(panelCollapse).attr('data-target');
					jQuery(target).addClass('in');
				}

				jQuery('[data-jstoggle="collapse"]').click(function(){
					jQuery(this).toggleClass('collapsed');
					var target = jQuery(this).attr('data-target');
					jQuery(target).toggleClass('in');
				});

				jQuery("#paid_course_button").click(function()
				{
					try{
						var courseInfo = JSON.parse(courseData);
					}
					catch(e){
						var course_info = JSON.stringify(courseData);
						courseInfo = JSON.parse(course_info);
					}

					/** global: tjanalytics */
					if (typeof tjanalytics !== "undefined")
					{
						tjanalytics.ga.addProduct(courseInfo);
						tjanalytics.ga.setAction(courseInfo['0']);
					}
				});
			});
		},
		retakeCourse: function(courseId, userId)
		{
			jQuery.ajax({
				url:Joomla.getOptions('system.paths').root + "/index.php?option=com_tjlms&task=course.expireCertificate&format=json",
				type: "POST",
				data:{course_id:courseId, user_id:userId},
				dataType: 'json',
				beforeSend: function() {
		            jQuery.LoadingOverlay("show", {
						image : "media/com_tjlms/images/loader/loader.gif"
					});
		        },
		        complete: function() {
		            jQuery('#ajax_loader').hide();
		        },
		        success: function(result) {
		            location.reload();
		        }
			});
		}
	},
	lesson: {
		init: function(openModuleId) {

			jQuery(document).ready(function() {
				/** global: localStorage */
				var sidebarState = localStorage.getItem('sidebarState');

				tjlms.lesson.hideRightPanel();
				heighttominus = typeof heighttominus !== 'undefined' ? heighttominus : 46;
				iframeheighttominus = 50;

				/* Set the height of the lesson playlist and right panel*/
				var height = jQuery(".tjlms_lesson_screen", top.document).height();
				if (!height)
					height = jQuery(document).height();

				if (!height)
					height = "400";

				jQuery("[data-js-attr='tjlms-lesson']").css("height", height - heighttominus);
				jQuery("[data-js-attr='tjlms-lesson-iframe']").css("height", height - iframeheighttominus);

				if (launchLessonFullScreen == 'popup' && launchMode != 'preview') {
					window.parent.document.body.style.overflow = "hidden";
				}

				if (jQuery(window).width() < 768) {
					jQuery("[data-js-attr='lesson-toolbar-content']").insertBefore('[data-js-attr="lesson-player"]');
					/*jQuery('.playlist-container').removeClass("playlist-hidden");*/
				}

				if (showLessonPlaylist == 1) {

					/*show the last accessed lesson module open */
					if (openModuleId) {
						var panelCollapse = jQuery('#modlist_' + openModuleId + ' [data-jstoggle="collapse"]');
						jQuery(panelCollapse).toggleClass('collapsed');
						var target = jQuery(panelCollapse).attr('data-target');
						jQuery(target).toggleClass('in');
					}

					jQuery('[data-jstoggle="collapse"]').click(function(){
						jQuery(this).toggleClass('collapsed');
						var target = jQuery(this).attr('data-target');
						jQuery(target).toggleClass('in');
					});

					if (sidebarState == null)
					{
						jQuery("[data-js-id='playlist-hide']").show()
						jQuery("[data-js-attr='lesson-playlist']").removeClass('hidden');
						tjlms.lesson.toggleLessonPanels();
					}
					else
					{
						jQuery("[data-js-id='playlist-open']").show();
					}

					jQuery("[data-js-attr='tjlms-lesson__playlist-toggle']").click(function() {

						jQuery("[data-js-attr='lesson-playlist']").toggle()
						if (jQuery(window).width() < 767)
						{
							jQuery("[data-js-attr='lesson-playlist']").removeClass('hidden hidden-xs');
						}
						else
						{
							jQuery("[data-js-attr='lesson-playlist']").removeClass('hidden');
						}

						tjlms.lesson.toggleLessonPanels();

						if (jQuery(".tjlms-lesson__playlist-container").is(":visible"))
						{
							jQuery("[data-js-id='playlist-open']").hide();
							jQuery("[data-js-id='playlist-hide']").show();
							localStorage.removeItem('sidebarState');
						}
						else
						{
							jQuery("[data-js-id='playlist-hide']").hide();
							jQuery("[data-js-id='playlist-open']").show();
							localStorage.setItem('sidebarState', 'collapsed');
						}
					});
				}

				jQuery("[data-js-attr='toolbar_buttons']").click(function() {
					if (jQuery(this).hasClass('active')) {
						jQuery(this).removeClass('active');
						tjlms.lesson.hideRightPanel();
						tjlms.lesson.toggleLessonPanels();

						return;
					}

					jQuery('.toolbar_buttons').not("[data-ref='jliketoolbar-menu']").removeClass('active');
					jQuery(this).addClass('active');
					tjlms.lesson.showRightPanel();
					jQuery('.toolbar-content').hide();
					var refDiv = jQuery(this).data('ref');
					jQuery('#' + refDiv).show();

					/* if comment box is opened, add comment should be opened*/
					if (refDiv == 'comments') {
						jQuery("#divaddcomment a.jlike_comment_msg").trigger('click');
					}

					tjlms.lesson.toggleLessonPanels();
				});

				jQuery("[data-ref='jliketoolbar-menu']").click(function() {
					jQuery(this).toggleClass('active');
					jQuery("#jlikeToolbar").toggleClass('open');

					if (!jQuery(this).hasClass('active')) {
						jQuery('.toolbar_buttons').removeClass('active');
						tjlms.lesson.hideRightPanel();
					}

					tjlms.lesson.toggleLessonPanels();
				});

				jQuery("[data-js-attr='jlikeToolbar-close'][data-js-id!='test-premise-close'], .resumewindowclose[data-js-id!='test-premise-close']").click(function() {

					if (confirm(Joomla.JText._("COM_TJLMS_LESSON_CONFIRM_BOX")) == true) {
						if (typeof lessonStartTime != 'undefined') {
							var lessonStoptime = new Date();
							var timespentonLesson = lessonStoptime - lessonStartTime;
							var timeinseconds = Math.round(timespentonLesson / 1000);
							plugdataObject["time_spent"] = timeinseconds;
							updateData(plugdataObject, true);
						} else if (typeof jQuery.fn.onBeforeUnloadLessonPageUnload != 'undefined') {
							jQuery.fn.onBeforeUnloadLessonPageUnload();
							closePopup(launchLessonFullScreen, returnUrl);
						}
						else
						{
							closePopup(launchLessonFullScreen, returnUrl);
						}
					}
				});

				jQuery("[data-js-id='test-premise-close']").click(function() {
					closePopup(launchLessonFullScreen, returnUrl)
				});

			});
		},
		toggleLessonPanels: function() {
				var lessonPlayerClass = 'col-sm-12';

				if (jQuery("[data-js-attr='lesson-playlist']").is(':hidden') &&
					!jQuery("[data-js-attr='lesson-toolbar-content']").is(':hidden')) {
					lessonPlayerClass = 'col-sm-8';
				} else if (!jQuery("[data-js-attr='lesson-playlist']").is(':hidden') &&
					jQuery("[data-js-attr='lesson-toolbar-content']").is(':hidden')) {
					lessonPlayerClass = 'col-sm-9';
				} else if (!jQuery("[data-js-attr='lesson-playlist']").is(':hidden') &&
					!jQuery("[data-js-attr='lesson-toolbar-content']").is(':hidden')) {
					lessonPlayerClass = 'col-sm-5';
				}

				jQuery("[data-js-attr='lesson-player']").attr("class", 'tjlms_lesson__player tjlms-lesson-player ' + lessonPlayerClass);


				if(jQuery(window).width() < 768)
				{
					if(!jQuery("[data-js-attr='lesson-toolbar-content']").is(':hidden'))
					{
						jQuery("[data-js-attr='tjlms-lesson']").addClass("y-scroll");
						jQuery("[data-js-attr='lesson-player']").addClass("hidden");
					}
					else
					{
						jQuery("[data-js-attr='tjlms-lesson']").removeClass("y-scroll");
						jQuery("[data-js-attr='lesson-player']").removeClass("hidden");
					}
				}

		},
		showRightPanel: function() {
			jQuery("[data-js-attr='lesson-toolbar-content']").show();
		},
		hideRightPanel: function() {
			jQuery("[data-js-attr='lesson-toolbar-content']").hide();
		}
	},
	printDiv: function() {
		var printContents = document.getElementById("printDiv").innerHTML;
		var originalContents = document.body.innerHTML;
		document.body.innerHTML = printContents;
		window.print();
		document.body.innerHTML = originalContents;
	},
	tjAdjustFormClass: function() {
		if (jQuery(window).width() < 767) {
			jQuery('#coupon-form, #payment-info').removeClass('form-horizontal');
		} else {
			jQuery('#coupon-form, #payment-info').addClass('form-horizontal');
		}

		jQuery('#characterLeft').text('250 characters left');
		jQuery('#addr').keyup(function() {
			var max = 250;
			var len = jQuery(this).val().length;
			if (len >= max) {
				jQuery('#characterLeft').text(' you have reached the limit');
			} else {
				var ch = max - len;
				jQuery('#characterLeft').text(ch + ' characters left');
			}
		});
	},
	resizeForm: function() {
		jQuery(document).ready(function($) {
			tjlms.tjAdjustFormClass();
			jQuery(window).on('resize', function() {
				tjlms.tjAdjustFormClass();
			})
			jQuery(document).on("changed", "#MyWizard", function(e, t) {
				tjlms.tjAdjustFormClass();
			});
		});
	},
	coupon: {
		init: function() {
			tjlms.resizeForm();

			jQuery('.coupon-courses').change(function(){
				tjlms.coupon.loadSubscription();
			});

		},
		loadSubscription: function(){
			var course_id = jQuery('#jform_course_id').val();

			if (course_id)
			{
				jQuery.ajax
				({
					url:Joomla.getOptions('system.paths').root + "/index.php?option=com_tjlms&task=coupon.loadSubscription&format=json",
					type: "POST",
					data:{course_id:course_id},
					dataType: 'json',
					beforeSend: function(){jQuery("#jform_subscription_id").empty(); },
					success: function(data)
					{
						if (data.data)
						{
							var info = data.data;
							jQuery.each((info), function (ind, ele) {
							var opt = jQuery('<optgroup id="'+ ele.id +'" label="'+ ele.title +'" ></optgroup>');
							jQuery.each((ele.subscription), function (index, element) {
									var op = jQuery('<option value="'+element.id+'">'+ element.name+'</option>');
									opt.append(op);
								});

								 jQuery('#jform_subscription_id').append(opt);
								 jQuery('#jform_subscription_id').trigger("liszt:updated");
								 jQuery("#jform_subscription_id").trigger("chosen:updated");
							});
						}
		 			}
				});
			}
		}
	},
	subscription: {
		init: function() {
			jQuery('#coupon_chk').prop('checked', false);
			jQuery('#total_amt_inputbox').val(0);
			jQuery('#net_amt_pay_inputbox').val(0);
			jQuery('#coupon_code').val();
			jQuery('#order_tax').val(0);
			array_sub_plan = JSON.parse(array_sub_plan);
			tjlms.subscription.caltotal(jQuery('#coursesubsplan_radio').val());

			jQuery("#coupon_code").keypress('input', function(event) {
				if (event.key === "Enter")
				{
					event.preventDefault();
				}
			});
		},
		show_cop: function() {
			jQuery("#dis_amt").show();
			jQuery("#net_amt_pay_inputbox").val(parseFloat(jQuery("#total_amt_inputbox").val()));
			jQuery("#net_amt_pay").html(parseFloat(jQuery("#total_amt_inputbox").val()));

			if (jQuery("#coupon_chk").is(":checked")) {
				total_calc_amt2 = 0;
				get_selected_plan = jQuery("#coursesubsplan_radio").val();

				if (get_selected_plan)
					total_calc_amt2 = array_sub_plan[get_selected_plan].price;

				if (!total_calc_amt2) {
					alert(Joomla.JText._("TJLMS_SELECT_SUBS_PLAN"));
					document.getElementById("coupon_chk").checked = false;
					return;
				}

				document.getElementById("coup_button").removeAttribute("disabled");
				jQuery("#cop_tr, #coupon_code, #coup_button").show();
			} else {
				var totalamt = parseFloat(jQuery("#net_amt_pay_inputbox").val())
				var allow_taxation = jQuery("#allow_taxation").val();
				if (allow_taxation == 1) {
					tjlms.subscription.calculatetax(totalamt);
				}

				jQuery("#cop_tr, #coupon_code, #coup_button, #dis_cop").hide();
				jQuery("#dis_amt").show();
				jQuery("#dis_cop_amt").html();
				jQuery("#coupon_code").val("");
			}
		},

		applycoupon: function() {
			if (jQuery('#coupon_chk').is(':checked')) {
				if (jQuery('#coupon_code').val() == '') {
					document.getElementById("coup_button").removeAttribute("disabled");
					alert(Joomla.JText._("COM_TJLMS_ENTER_COP_COD"));
					jQuery('#net_amt_pay').html(parseFloat(jQuery('#total_amt_inputbox').val()));
					jQuery('#dis_cop').hide();
					jQuery('#coupon_code').val('');
					jQuery('#coupon_code').focus();
				} else {
					var coupon_code = document.getElementById('coupon_code').value;
					var course_id = document.getElementById('course_id').value;
					var selected_plan = jQuery('#coursesubsplan_radio').val();

					jQuery.ajax({
						url: tjlms_baseurl + '?option=com_tjlms&task=buy.getcoupon&coupon_code=' + document.getElementById('coupon_code').value,
						type: 'POST',
						data: {
							coupon_code: coupon_code,
							course_id: course_id,
							selected_plan: selected_plan
						},
						dataType: 'json',
						success: function(data) {
							amt = 0;
							val = 0;
							if (parseInt(data[0].error) == 1) {
								alert(data[0].msg);
								jQuery('#net_amt_pay').html(parseFloat(jQuery('#total_amt_inputbox').val()));
								jQuery('#dis_cop').hide();
								jQuery('#coupon_code').val('');
								jQuery('#coupon_code').focus();
								return;
							}
							if (parseFloat(data[0].value) > 0) {
								if (data[0].val_type == 1)
									value = (data[0].value / 100) * document.getElementById('total_amt_inputbox').value;
								else
									value = data[0].value;

								val = parseFloat(value).toFixed(2);
								finalvar = 0;
								get_selected_plan = jQuery('#coursesubsplan_radio').val();
								finalvar = array_sub_plan[get_selected_plan].price;
								amount = parseFloat(finalvar) - parseFloat(val);

								if (parseFloat(amount) <= 0) {
									amount = 0;
								}

								if (isNaN(finalvar)) {
									amount = 0;
								}

								amt = parseFloat(amount).toFixed(2);
								jQuery('#net_amt_pay_inputbox').val(amt)
								jQuery('#net_amt_pay').html(amt);
								var allow_taxation = jQuery('#allow_taxation').val();

								if (allow_taxation == 1) {
									tjlms.subscription.calculatetax(amt);
								}
								jQuery('#dis_cop_amt').html('' + val);
								jQuery('#dis_amt, #dis_cop').show();
							}
						}
					});
				}
			}
		},
		calculatetax: function(amt) {
			jQuery.ajax({
				url: tjlms_baseurl + '?option=com_tjlms&task=buy.applytax&tmpl=component&total_calc_amt=' + amt,
				type: 'GET',
				dataType: 'json',
				success: function(taxdata) {
					if (taxdata != null && parseFloat(taxdata.taxvalue) > 0) {
						jQuery('#order_tax').val(parseFloat(taxdata.taxvalue));
						var taxamount = jQuery('#order_tax').val();
						var taxamt = tjlms.subscription.round(taxamount, 2);
						jQuery('#tax_to_pay').html(taxamt);
						jQuery('#tax_to_pay_inputbox').val(taxamt);
						var amt_after_tax = parseFloat(taxamt) + parseFloat(amt);
						var net_amt_after_tax = tjlms.subscription.round(amt_after_tax, 2);
						jQuery('#net_amt_after_tax').html(net_amt_after_tax);
						jQuery('#net_amt_after_tax_inputbox').val(net_amt_after_tax);
						jQuery('#tax_tr').show();
					} else {
						jQuery('#order_tax').val(0);
						jQuery('#tax_to_pay').html(0);
						jQuery('#tax_to_pay_inputbox').val(0);
						var net_amt_after_tax = parseFloat(amt)
						jQuery('#net_amt_after_tax').html(amt);
						jQuery('#net_amt_after_tax_inputbox').val(amt);
						jQuery('#tax_tr').hide();
					}
				}
			});
		},

		caltotal: function(get_selected_plan) {
			total_calc_amt = array_sub_plan[get_selected_plan].price;
			var couponenable = 0;

			if (parseInt(total_calc_amt) == 0) {
				jQuery('#cooupon_troption').hide();
			} else {
				couponenable = 1;
				jQuery('#cooupon_troption').show();
			}

			if (couponenable == 1) {
				if (jQuery('#coupon_chk').is(':checked') && jQuery('#coupon_code').val() != '') {
					tjlms.subscription.applycoupon();
				}
			}

			if (isNaN(total_calc_amt)) {
				total_calc_amt = 0;
			}

			jQuery('#total_amt').html('<b>' + total_calc_amt + currency);
			jQuery('#total_amt_inputbox').val(total_calc_amt);
			jQuery('#net_amt_pay').html(total_calc_amt);
			jQuery('#net_amt_pay_inputbox').val(total_calc_amt);
			var allow_taxation = jQuery('#allow_taxation').val();

			if (allow_taxation == 1) {
				tjlms.subscription.calculatetax(total_calc_amt);
			}
			document.getElementById('selected_plan').value = get_selected_plan;
		},

		round: function(n) {
			return Math.round(n * 100 + ((n * 1000) % 10 > 4 ? 1 : 0)) / 100;
		}
	},
	billing: {
		init: function(Dbvalue, totalprice) {
			tjlms.billing.generateState('country', Dbvalue, totalprice)
		},
		generateoption: function(data, countryId, Dbvalue) {
			var country = jQuery('#' + countryId).val();
			var options, index, select, option;

			if (countryId == 'country') {
				select = jQuery('#state');
				default_opt = 'Select State';
			}

			select.find('option').remove().end();

			selected = "selected=\"selected\"";
			var op = '<option ' + selected + ' value="">' + default_opt + '</option>';
			if (countryId == 'country') {
				jQuery('#state').append(op);
			}

			if (data.length > 0) {
				jQuery('#state_star').text('*');
				jQuery('#state').attr('required','required');
				options = data.options;
				for (index = 0; index < data.length; ++index) {
					var name = data[index]['id'];
					selected = "";
					if (name == Dbvalue)
						selected = "selected=\"selected\"";
					var op = '<option ' + selected + ' value=\"' + data[index]['id'] + '\">' + data[index]['region'] + '</option>';

					if (countryId == 'country') {
						jQuery('#state').append(op);
					}

				} // end of for
			}
			else
			{
				jQuery('#state_lbl').removeClass('invalid');
				jQuery('#state_star').text('');
				jQuery('#state').removeAttr('required','required');
			}
		},

		generateState: function(countryId, Dbvalue, totalprice) {
			var country = jQuery('#' + countryId).val();
			if (country == undefined || country == '') {
				return false;
			}
			jQuery.ajax({
				url: root_url + 'index.php?option=com_tjlms&task=buy.loadState&country=' + country + '&tmpl=component',
				type: 'GET',
				dataType: 'json',
				success: function(data) {
					if (countryId == 'country') {
						statebackup = data;
					}

					tjlms.billing.generateoption(data, countryId, Dbvalue);
				}
			});
		}
	},
	gateway: {
		init: function() {
			tjlms.resizeForm();
		},
		gatewayHtml: function(ele, orderid) {
			var prev_button_html = '<div class="col-xs-12 col-sm-3 list-group-item-heading">'
			prev_button_html += '<button id="btnWizardPrev1" onclick="jQuery(\'#MyWizard\').wizard(\'previous\');" type="button" class="col-xs-12 btn btn-prev pull-left">';
			prev_button_html += '<i class="fa fa-arrow-circle-o-left"></i>';
			prev_button_html += ' ' + Joomla.JText._('COM_TJLMS_PREV_BUTTON');
			prev_button_html += '</button>';
			prev_button_html += '</div>';
			var confirmContainer = '<div class="col-xs-12 col-sm-3 pull-right list-group-item-heading">';

			jQuery.ajax({
				url: root_url + 'index.php?option=com_tjlms&task=payment.changegateway&gateways=' + ele + '&order_id=' + orderid,
				type: 'POST',
				data: '',
				dataType: 'text',
				success: function(data) {
					if (data) {
						jQuery('#tjlms-payHtmlDiv').html(data);
						jQuery('#tjlms-payHtmlDiv div.form-actions').append(prev_button_html);
						jQuery('#tjlms-payHtmlDiv div.form-actions input[type="submit"]').addClass('pull-right').css('min-width', '100%').wrap(confirmContainer);
						jQuery('#tjlms-payHtmlDiv div.form-actions input[type="submit"]').addClass('paymentButton');
						jQuery('.paymentButton').click(function() {
							if (typeof tjanalytics !== "undefined")
							{
								tjlms.gateway.addGatewayStep(orderid);
							}
						});
						jQuery('#tjlms-payHtmlDiv div.form-actions').addClass('tjlms-confirm-payment-div');
						jQuery('#payment-info').find('.form-horizontal').removeClass('form-horizontal');
					}
				}
			});
		},
		addGatewayStep: function(orderid){
			jQuery.ajax({
				url: root_url + 'index.php?option=com_tjlms&task=buy.generateOrderData&order_id=' + orderid,
				type: 'POST',
				data: '',
				dataType: 'text',
				success: function(data) {
					if (data) {
						data = JSON.parse(data);
						tjanalytics.ga.addProduct(data);
						tjanalytics.ga.setAction(data['0']);
					}
				}
			});
		}
	},
	utility: {
		inIframe: function() {
			try {
				return window.self !== window.top;
			} catch (e) {
				return true;
			}
		}
	},
	test: {
		autosaveTimeout:1000,
		autosaveAnswers:1,
		totalQuestions: 0,
		gradingType : '',
		currentPage : 0,
		totalPages : 0,
		inviteId : 0,
		questionNumber: 0,
		pagesVisited: [],
		init: function(lessonLaunch, redirectURL, layout, resumeAllowed) {

			if (!layout || typeof(layout) === 'undefined')
			{
				layout = "default";
			}

			var thisTest = this;

			jQuery(window).on('load', function() {

				jQuery("[data-js-id='test-premise-close']").click(function() {
					closePopup(lessonLaunch, redirectURL)
				});

				if (layout == 'default')
				{
					const  inviteId 	= thisTest.inviteId;
					const  testId	 	= thisTest.testId;
					const  gradingtype	= thisTest.gradingType;
					const  currentPage 	= thisTest.currentPage;

					tjlms.test.showQuestionsList(testId, inviteId, currentPage);

					// init bootpag
					jQuery('#tmt-page-selection').bootpag({
						total: tjlms.test.totalPages,
						page: currentPage,
						maxVisible: 5,
					}).on("page", function(event, /* page number here */ pageToFetch){
						tjlms.test.saveEachPageQueAnswers();
						tjlms.test.showQuestionsList(testId, inviteId, pageToFetch);
						tjlms.test.checkNotAttemptedCompulsory();
					});

					function getTImeStartTime() {
						return new Date(new Date().valueOf() + testTimeremaning);
					}

					var ltId = jQuery('#invite_id').val()
					//var testId = jQuery('#id').val()
					var $clock = jQuery(".test__timer #countdown_timer");
					var formToken = jQuery('[data-js-id="form-token"]').attr('name');
					var data = {};
					data[formToken] = 1;
					data['ltId']=ltId;
					data['testId']=testId;
					data['timeSpent'] = 3;

					finalDate = getTImeStartTime();
					var elapseFlag = true;

					if (testTimeDuration != 0)
					{
						elapseFlag = false;
					}

					$clock.countdown(finalDate, {elapse: elapseFlag})
					.on('update.countdown', function(event) {

						jQuery(this).html(event.strftime('%H:%M:%S'));

						// if time remaining to finish the test is less than the time alert
						if (timeToShowFinishAlert != 0 && event.strftime('%T') < timeToShowFinishAlert)
						{
							jQuery(this).addClass("text-error");
						}

						if (event.offset.seconds % 3 == 0)
						{
							jQuery.ajax({
								url: root_url+ 'index.php?option=com_tmt&task=test.updateTimeSpent&format=json',
								dataType: 'json',
								type: 'POST',
								data:data,
								success: function (data) {
								}
							});
						}
					})
					.on('finish.countdown', function(event) {
						var data1 = {};
						data1[formToken] = 1;
						data1['ltId']=ltId;
						data1['testId']=testId;

						jQuery.ajax({
							url: root_url+ 'index.php?option=com_tmt&task=test.updateTimeSpent&format=json',
							dataType: 'json',
							type: 'POST',
							data:data1,
							success: function (data) {
							}
						});
						let timerOut = 1;
						if (tjlms.test.submitTest(timerOut))
						{
							window.location = jQuery("#thankYouLink").val();
						}
					});

					/* Save the answers given by user by ajax*/
					var timeoutId;

						/* if user is uploading the file*/
					jQuery(document).on("input propertychange","[data-js-id='test-question'][data-js-type='textarea'] textarea, [data-js-id='test-question'][data-js-type='text'] input, [data-js-id='test-question'][data-js-type='radio'] input, [data-js-id='test-question'][data-js-type='checkbox'] input,[data-js-id='test-question'][data-js-type='rating'] input", function(){
						if (parseInt(tjlms.test.autosaveAnswers) === 1)
						{
							var element = jQuery(this);
							/*jQuery(".tmt_test__footer__navbutton").attr("disabled", "disabled");*/
							if (jQuery(this).attr('type') == "radio" || jQuery(this).attr('type') == "checkbox")
							{
								tjlms.test.saveEachAnswer(element);
							}
							else
							{
								clearTimeout(timeoutId);
								timeoutId = setTimeout(function() {
									if (tjlms.test.validateQuestionAnswer(element))
									{
										tjlms.test.saveEachAnswer(element);
									}

								}, 3000);
							}
						}
					});

					/* If user clicks on the close button from test page*/
					jQuery("[data-js-id='test-close']").click(function()
					{
						let unAttemptedCompulsoryCnt = 0;

						// Once form is submiited check if any mandatory Question is not Attempted
						tjlms.test.checkNotAttemptedCompulsory();
						unAttemptedCompulsoryCnt = jQuery("#unAttemptedCompulsoryCnt").val();

						if (parseInt(unAttemptedCompulsoryCnt) > 0)
						{
							alert(Joomla.JText._('COM_TMT_TEST_SUBMIT_VALIDATION_FOR_COMPULSORY_MSG'));
						}
						else if (confirm(Joomla.JText._("COM_TJLMS_QUIZ_CONFIRM_BOX")) == true)
						{
							var redirect = 1;

							if (resumeAllowed == 0)
							{
								redirect = 0;

								if (tjlms.test.submitTest())
								{
									redirect = 1;
								}
							}

							if (redirect == 1)
							{
								if(lessonLaunch == 'popup') {
									window.parent.SqueezeBox.close();
								} else if(lessonLaunch == 'tab') {
									if (opener) {
										opener.location.reload();
									}
									window.close();
								} else{
									window.location = redirectURL;
								}
							}
						}
					});

					jQuery("[data-js-id='submittest']").click(function()
					{
						var msg = "";

						switch (gradingtype)
						{
							case "exercise" :
								msg = "COM_TMT_TEST_APPEAR_FINISH_EXERCISE";
								break;
							case "feedback" :
								msg = "COM_TMT_TEST_APPEAR_FINISH_FEEDBACK";
								break;
							case "quiz" :
								msg = "COM_TMT_TEST_APPEAR_FINISH_QUIZ";
						}

						let unAttemptedCompulsoryCnt = 0;

						// Once form is submiited check if any mandatory Question is not Attempted
						tjlms.test.checkNotAttemptedCompulsory();
						unAttemptedCompulsoryCnt = jQuery("#unAttemptedCompulsoryCnt").val();

						if (parseInt(unAttemptedCompulsoryCnt) > 0)
						{
							alert(Joomla.JText._('COM_TMT_TEST_SUBMIT_VALIDATION_FOR_COMPULSORY_MSG'));
						}
						else if (confirm(Joomla.JText._(msg)) == true)
						{
							if (tjlms.test.submitTest())
							{
								window.location = jQuery("#thankYouLink").val();
							}
						}
					});

					jQuery("[data-js-id='toolbar'] [data-js-id='toolbar-next'] button, [data-js-id='toolbar'] [data-js-id='toolbar-prev'] button").click(function()
					{
						tjlms.test.saveEachPageQueAnswers();

						let pageToFetch =  0;

						if (jQuery(this).closest(".toolbar__span").attr("data-js-id") == 'toolbar-next')
						{
							pageToFetch = parseInt(tjlms.test.currentPage) + 1;
						}
						else if (jQuery(this).closest(".toolbar__span").attr("data-js-id") == 'toolbar-prev')
						{
							pageToFetch = parseInt(tjlms.test.currentPage) - 1;
						}

						jQuery('#tmt-page-selection').bootpag({page: pageToFetch});
						tjlms.test.showQuestionsList(testId, inviteId, pageToFetch);
					});


					jQuery("[data-js-id='drafttest']").click(function()
					{
						if (confirm(Joomla.JText._("COM_TMT_TEST_DRAFT_CONFIRM_BOX")) == true)
						{
							tjlms.test.saveEachPageQueAnswers(jQuery("#thankYouLink").val());
						}
					});

					/* if user is uploading the file*/
					jQuery(document).on("change","[data-js-type='file_upload'] input[type='file']", function(){

						/*remove status bar if already appneded*/
						/*jQuery(thisfile).closest('.questions ').children( ".statusbar" ).remove();*/
						thisfile = jQuery(this);
						/* Get uploaded file object */
						var uploadedfile	=	jQuery(thisfile)[0].files[0];

						if (!uploadedfile)
							return false;

						// Once form is submiited check if any mandatory Question is not Attempted
						tjlms.test.checkNotAttemptedCompulsory();

						if (parseInt(jQuery("#unAttemptedCompulsoryCnt").val()) > 0)
						{
							jQuery(".tmt_test__footer__navbutton").attr("disabled", "disabled");
						}

						var formData = new FormData();
						formData.append( 'FileInput', uploadedfile );

						var qid = jQuery(this).closest('[data-js-id="test-question"]').attr('data-js-itemid');
						var testid = jQuery('[name="test[id]"]').val();
						var ltid = jQuery('[name="invite_id"]').val();
						formData.append( 'mediaformat', 'quiz' );
						formData.append( 'subformat', 'answer');

						formData.append('formatData[quiz][answer][qid]', qid);
						formData.append('formatData[quiz][answer][testid]', testid );
						formData.append('formatData[quiz][answer][ltid]', ltid );

						tjLmsCommon.file.$file = thisfile;
						tjLmsCommon.file.formData = formData;
						tjLmsCommon.file.allowedSize = lessonUploadSize;
						/*tjLmsCommon.file.allowedExtensions = '';

						let qfileFormatelement = jQuery("[name='testqparam[" + qid + "][file_format]']");
						if (qfileFormatelement.length && qfileFormatelement.val() != '')
						{
							tjLmsCommon.file.allowedExtensions = qfileFormatelement.val();
						}*/

						let qfileSizeelement = jQuery("[name='testqparam[" + qid + "][file_size]']");
						if (qfileSizeelement.length && qfileSizeelement.val() > 0)
						{
							tjLmsCommon.file.allowedSize = qfileSizeelement.val();
						}

						tjLmsCommon.file.showstatusbar = true;

						let qfileuploadCnt = jQuery("[name='testqparam[" + qid + "][file_count]']");
						var uploadedFilesCnt = jQuery('[data-js-itemid="'+ qid +'"] [data-js-id="uploaded-file-list"] > div[data-js-id="each-file"]').length;
						if (qfileuploadCnt.length && qfileuploadCnt.val() != '' && uploadedFilesCnt >= qfileuploadCnt.val())
						{
							jQuery("#msg_" + qid).attr("class", "alert alert-error").text(Joomla.JText._('COM_TJLMS_MAX_NUMBER_OF_FILE_UPLOAD_ERROR_MSG').replace("%s", qfileuploadCnt.val()));
							jQuery(".tmt_test__footer__navbutton").removeAttr("disabled");

							return false;
						}

						var returnvar = tjLmsCommon.file.upload();

					});

					jQuery(document).on("click","[data-js-type='file_upload'] [data-js-id='delete']", function(){
						var r = confirm("Are you sure you want to delete this uploaded file?");
						if (r == true) {
							var fileContainer =  jQuery(this).closest('[data-js-id="each-file"]');

							var qid = jQuery(this).closest('[data-js-id="test-question"]').attr('data-js-itemid');
							var answerId = jQuery(this).closest('[data-js-id="each-file"]').attr("data-js-answerid");
							var answerMediaId = jQuery(this).closest('[data-js-id="each-file"]').attr("data-js-itemid");
							var formToken = jQuery('[data-js-id="form-token"]').attr('name');

							var url= rootUrl + 'index.php?option=com_tmt&task=test.removeFileuploadAnswer&tmpl=component&format=json';

							var formData = {};
							formData[formToken] = 1;
							formData['answerId']=answerId;
							formData['answerMediaId']=answerMediaId;

							var promise = tjService.postData(url,formData);
							promise.fail(
								function(response) {
									var result = [];
									result['error'] = '1';
									result['msg'] = response.responseText;
									tjlms.test.eachQuestionSaveMsg(qid, result);
								}
							).done(function(response) {
								var result = [];
								result['error'] = '0';
								if (!response.success) {
									result['error'] = '1';
								}
								else{
									jQuery(fileContainer).remove();
									jQuery('input[name="questions[upload]['+qid+'][]"][value="'+answerMediaId+'"]').remove();

									if(jQuery('[data-js-itemid="'+qid+'"] [data-js-id="uploaded-file-list"] > div[data-js-id="each-file"]').length == 0)
									{
										jQuery('[data-js-itemid="'+qid+'"] .test-question__answers').find('[data-js-id="uploaded-file-list-header"]').addClass('d-none');
									}
								}
								result['msg'] = response.message;
								tjlms.test.eachQuestionSaveMsg(qid, result)
							});
						}

						return true;
					});

					jQuery(document).on("keyup", "[data-js-type='textarea'] textarea", function(){
						let usedcharlength = parseInt(jQuery(this).val().length);
						let maxlength = parseInt(jQuery(this).attr("maxlength"));
						let minlength = parseInt(jQuery(this).attr("minlength"));
						let availablecharlength = maxlength - usedcharlength;
						jQuery(this).closest("[data-js-type='textarea']").find(".charscontainer_remaining").text(availablecharlength);

						jQuery(this).closest("[data-js-type='textarea']").find(".charscontainer").removeClass("invalid");

						if (minlength > 0 && usedcharlength == 0)
						{
							jQuery(this).closest("[data-js-type='textarea']").find(".charscontainer").addClass("invalid");
						}

						if (maxlength > 0 && usedcharlength > maxlength)
						{
							jQuery(this).closest("[data-js-type='textarea']").find(".charscontainer").addClass("invalid");
						}

						if (usedcharlength != 0 && usedcharlength < minlength)
						{
							jQuery(this).closest("[data-js-type='textarea']").find(".charscontainer").addClass("invalid");
							jQuery(".tmt_test__footer__navbutton").attr("disabled", "disabled");
						}
						else
						{
							jQuery(this).closest("[data-js-type='textarea']").find(".charscontainer").removeClass("invalid");
						}
					});

				}
			});
		},
		validateQuestionAnswer: function(answerElement){
			if (answerElement.is("textarea"))
			{
				let usedcharlength = parseInt(answerElement.val().length);
				let maxlength = parseInt(answerElement.attr("maxlength"));
				let minlength = parseInt(answerElement.attr("minlength"));
				if ((minlength > 0 && usedcharlength < minlength && usedcharlength !== 0) || (maxlength > 0 && usedcharlength > maxlength))
				{
					jQuery(".tmt_test__footer__navbutton").attr("disabled", "disabled");
					return false;
				}
				else if (minlength || maxlength)
				{
					jQuery(".tmt_test__footer__navbutton").removeAttr("disabled");
				}
			}
			return true;
		},
		resetAnswerOptions: function(questionType, questionId){
			let element = '';

			switch (questionType)
			{
				case 'checkbox':
				case 'radio':
					jQuery('input[name="questions[mcqs][' + questionId + '][]"]').attr('checked', false);
					element = jQuery('input[name="questions[mcqs][' + questionId + '][]"]');
				break;

				case 'rating':
					jQuery('input[name="questions[rating][' + questionId + ']"]').attr('checked', false);
					element = jQuery('input[name="questions[rating][' + questionId + ']"]');

				break;

				case 'text':
				case 'textarea':
					/*jQuery('input[name="questions[subjective][' + questionId + ']"]').val('').change();*/
					jQuery('#questions' + questionId).val('').change();
					element = jQuery('#questions' + questionId);

				break;
			}

			if (tjlms.test.validateQuestionAnswer(element))
			{
				tjlms.test.saveEachAnswer(element);
			}
		},
		flagQuestion: function(ele, qId) {
			let flagQuestionUrl     = rootUrl + "index.php?option=com_tmt&task=test.flagQuestion&format=json";
			let formData            = {'testId' : testId, 'qId': qId, 'invite_id': inviteId};
			let flagQuestionPromise = tjService.postData(flagQuestionUrl, formData);
			let doProcess           = false;
			let responseObj;

			flagQuestionPromise.fail(
				function(response) {
					var messages = { "error": [response]};
					Joomla.renderMessages(messages);
				}
			).done(function(response) {
				if (!response.success && response.message){
					var messages = { "error": [response.message]};
					Joomla.renderMessages(messages);
					doProcess =  false;
				}

				if (response.messages) {
					Joomla.renderMessages(response.messages);
				}

				if (response.success)
				{
					doProcess = true;
					responseObj = response.data;

					/*Update overview*/
					if (jQuery('#question' + qId + ' > span').hasClass('fa fa-flag'))
					{
						jQuery('#question' + qId + ' > span').removeClass('fa fa-flag');
						jQuery(ele).text(Joomla.JText._('COM_TMT_QUESTION_FLAG'));
					}
					else
					{
						jQuery('#question' + qId + ' > span').addClass('fa fa-flag');
						jQuery(ele).text(Joomla.JText._('COM_TMT_QUESTION_UNFLAG'));
					}
				}
			});

			if (doProcess)
			{
				return responseObj;
			}

			return doProcess;
		},
		showQuestionsList: function(testId, inviteId, pageno = 1) {

			if (tjlms.test.pagesVisited.indexOf(pageno) != "-1")
			{
				jQuery(".questions_container .tjlms-test-page").hide();
				jQuery(".questions_container .tjlms-test-page#testPage" + pageno).show();
				tjlms.test.currentPage = pageno;
				tjlms.test.testactions();
				return;
			}

			/*Get test data*/
			let testData = tjlms.test.getTestSectionsQuestions(testId, inviteId, pageno);

			// First/Starting question number on a particular page
			tjlms.test.questionNumber = (pageno * testPaginationLimit) - testPaginationLimit + 1;

			/*Get section data*/
			if (testData.sections.length < 1)
			{
				// Show error
				return false;
			}

			jQuery(testData.sections).each(function(index, section){
				if (section.questions.length > 0)
				{
					const pageElement = jQuery("<div class='tjlms-test-page' id='testPage" + pageno + "'></div>");
					const sectionElement = jQuery("<div class='tjlms-test-section'></div>");
					const sectionTitle = jQuery('<h3 class="tjlms-test-section__title"></h3>').appendTo(sectionElement);
					sectionTitle.text(section.title);

					jQuery(sectionElement).appendTo(pageElement);
					jQuery(".questions_container").append(pageElement);

					/*Show section in questios overview*/
					jQuery('#attempted_qlist_container').append('<div class="clearfix center"><strong>' +  section.title + '</strong></div>');

					let sQuestions = section.questions;

					jQuery(section.questions).each(function(index, question){
						doProcess = true;

						/*Skipped*/
						if (question.userAnswer == '') {
							qClass = '';
						}
						/*Attempted*/
						else {
							qClass = 'tmt-circle--attempted';
						}

						jQuery('#attempted_qlist_container').append('<a href="' + currentPageUrl + '#question-' + question.question_id + '">  <div id="question' + question.question_id + '" class="tmt-circle tmt-circle--margin pull-left ' + qClass + '"> <span class="tmt-circle--flag">&nbsp;</span>' + tjlms.test.questionNumber + ' </div> </a>');

						/*Flagged*/
						if (question.flagged == 1) {
							qClass = 'fa fa-flag';
							jQuery('#question' + question.question_id + ' > span').addClass('fa fa-flag');
						}


						var url = Joomla.getOptions('system.paths').root + "/" + "index.php?option=com_tmt&task=test.getQuestionHtml&format=json";
						var promise = tjService.postData(url, {'test' : testData.test, 'question' : question, 'qNo' : tjlms.test.questionNumber});
						promise.fail(function(questionres) {
								doProcess =  false;
								var messages = { "error": [questionres]};
								Joomla.renderMessages(messages);
						}).done(function(questionres) {
						if (!questionres.success && questionres.message){
							var messages = { "error": [questionres.message]};
							Joomla.renderMessages(messages);
							doProcess =  false;
						}
						if (questionres.messages){
							Joomla.renderMessages(response.messages);
						}

						doProcess = true;

						if (questionres.data){
							//jQuery('.questions_container').append(questionres.data);
							jQuery(questionres.data).appendTo(pageElement);
							tjlms.test.questionNumber++;
						}
						});
					});
				}
				SqueezeBox.assign(jQuery('a.tjmodal'), {parse: 'rel'});
			});

			jQuery(".questions_container .tjlms-test-page").hide();
			jQuery(".questions_container .tjlms-test-page#testPage" + pageno).show();
			tjlms.test.currentPage = pageno;
			tjlms.test.testactions();
			tjlms.test.pagesVisited.push(parseInt(pageno));
		},
		getTestSectionsQuestions: function(testId, inviteId, pageNo) {
			let getTestUrl     = Joomla.getOptions('system.paths').root + "/" + "index.php?option=com_tmt&task=test.getTestSectionsQuestions&format=json";
			let formData       = {'id' : testId, 'invite_id': inviteId, 'pageNo': pageNo};
			let getTestPromise = tjService.postData(getTestUrl, formData);
			let doProcess      = false;
			let testObj;

			getTestPromise.fail(
				function(response) {
					var messages = { "error": [response]};
					Joomla.renderMessages(messages);
				}
			).done(function(response) {
				if (!response.success && response.message){
					var messages = { "error": [response.message]};
					Joomla.renderMessages(messages);
					doProcess =  false;
				}

				if (response.messages) {
					Joomla.renderMessages(response.messages);
				}

				if (response.success)
				{
					doProcess = true;
					testObj = response.data;
					jQuery('.quiz_content').scrollTop('0');
				}
			});

			if (doProcess)
			{
				return testObj;
			}

			return doProcess;
		},
		saveEachAnswer: function (answerElement){
			/* Used to save the question on each answer submit*/
			var questionId = jQuery(answerElement).closest('[data-js-id="test-question"]').attr('data-js-itemid');
			var testId = jQuery('[name="test[id]"]').val();
			var ltId = jQuery('[name="invite_id"]').val();
			var answer = jQuery(answerElement).val();
			var formToken = jQuery('[data-js-id="form-token"]').attr('name');

			if (jQuery(answerElement).attr('type') === 'radio' || jQuery(answerElement).attr('type') === 'checkbox')
			{
				var a = [];
				jQuery(answerElement).closest('[data-js-id="test-question"]').find(":" + jQuery(answerElement).attr('type')).each(function () {
					if (this.checked) {
					a.push(jQuery(this).val());
					}
				});
				answer = a.join();
			}
			else if (jQuery(answerElement).attr('type') === 'file')
			{
				var a = [];
				jQuery(answerElement).closest('[data-js-id="test-question"]').find('[data-js-id="each-file"]').each(function () {
					a.push(jQuery(this).attr("data-js-itemid"));
				});

				// In case of file type questions, answer array doesn't contain actual answer ids but media ids.
				answer = a.join();
			}

			var flagQueSave = true;

			var formData = {};
			 formData['testId'] = testId;
			 formData['ltId'] = ltId;
			 formData['questionId'] = questionId;
			 formData['answer'] = answer;
			 formData[formToken] = 1;

			var saveurl= rootUrl + 'index.php?option=com_tmt&task=test.saveQuestionAnswer&tmpl=component&format=json';
			var promise = tjService.postData(saveurl, formData);

			promise.fail(
				function(response) {
					var result = [];
					result['error'] = '1';
					result['msg'] = response.responseText;
					tjlms.test.eachQuestionSaveMsg(questionId, result);
					flagQueSave = false;
				}
			).done(function(response) {

				var result = [];
				result['msg'] = response.message;
				result['error'] = '0';
				if (!response.success){
					result['error'] = '1';
					tjlms.test.eachQuestionSaveMsg(questionId, result);
					flagQueSave = false;
				}
				else {

					flagQueSave = true;

					/* Show the no of attempted questions on progress bar*/
					var attemptedCount = tjlms.test.getAttemptedQuestionsCnt(testId, ltId);
					var questionProgress =(100 * attemptedCount ) / tjlms.test.totalQuestions;
					var msg = Joomla.JText._('COM_TMT_TEST_APPEAR_ATTEMPTED_OF').replace("%s", attemptedCount).replace("%s", tjlms.test.totalQuestions);

					jQuery('[data-js-id="test-controls"] .progress .progress-bar').width(questionProgress + "%").attr("aria-valuenow", questionProgress);
					jQuery('[data-js-id="test-controls"] .progress .progress-bar .progress_bar_text').text(msg);
					tjlms.test.checkNotAttemptedCompulsory();

					/*Update overview*/
					if (answer !== '')
					{
						jQuery('#question' + questionId).addClass('tmt-circle--attempted');
					}
					else if (answer == '')
					{
						jQuery('#question' + questionId).removeClass('tmt-circle--attempted');
					}
				}

				/* In any case show msg of the result*/
				/* tjlms.test.eachQuestionSaveMsg(questionId, result); */
			});

			return flagQueSave;
		},
		getAttemptedQuestionsCnt: function(testId, ltId) {
			var formData = {'testId' : testId, 'ltId' :ltId};
			var saveurl= rootUrl + 'index.php?option=com_tmt&task=test.getTotalAttemptedQuestion&tmpl=component&format=json';
			var promise = tjService.postData(saveurl, formData);

			var res = 0;
			promise.fail(
				function(response) {
					res = 0;
				}
			).done(function(response) {
				res = response.data;
			});

			return res;
		},
		showFileinUploadlist: function (response){
			var fileElement = '<div class="col-sm-6" data-js-id="each-file" data-js-itemid="'+response.media_id+'" data-js-answerid="'+response.answer_id+'">'+
								'<a class="mr-5" href="'+ response.path +'">'+
										response.org_filename +
								'</a>' +
								'<a href="javascript:void(0)" data-js-id="delete" title="'+Joomla.JText._("COM_TMT_DELETE_ITEM")+'">' +
									'<i class="fa fa-trash" aria-hidden="true"></i>' +
								'</a>' +
							  '</div>' +
							  '<input type="hidden" name="questions[upload][' + response.qid + '][]" value="' + response.media_id + '"/>'
			jQuery('[data-js-itemid="'+ response.qid +'"] [ data-js-id="uploaded-file-list-header"]').removeClass('d-none');
			jQuery(fileElement).appendTo(jQuery('[data-js-itemid="'+ response.qid +'"] [data-js-id="uploaded-file-list"]'));

			var ret = tjlms.test.saveEachAnswer(jQuery('[data-js-itemid="'+ response.qid +'"] input[type="file"]'));

			if (ret)
			{
				jQuery(".tmt_test__footer__navbutton").removeAttr("disabled");
			}

			var uploadedFilesCnt = jQuery('[data-js-itemid="'+ response.qid +'"] [data-js-id="uploaded-file-list"] > div[data-js-id="each-file"]').length;

			let qfileuploadCnt = jQuery("[name='testqparam[" + response.qid + "][file_format]']");

			if (qfileuploadCnt.length && qfileuploadCnt.val() != '' && uploadedFilesCnt >= qfileuploadCnt.val())
			{
				jQuery('[data-js-itemid="'+ response.qid +'"].fileupload .btn-file').attr("disabled");
			}
		},
		eachQuestionSaveMsg : function (qid, result){
			/*Each question on test page has the div to message. This is called to show success or error message when each answer of question is saved.*/
			var element = jQuery('[data-js-id="test-question"][data-js-itemid="'+ qid +'"]');
			if (result['error'] == "1")
			{
				jQuery('[data-js-id="test-question-msg"] .alert', element).addClass("alert-error");
				jQuery('[data-js-id="test-question-msg"] .alert', element).html(result['msg']);
				jQuery('[data-js-id="test-question-msg"]', element).removeClass('d-none');
			}
			else
			{
				jQuery('[data-js-id="test-question-msg"] .alert', element).addClass("alert-success");
				jQuery('[data-js-id="test-question-msg"] .alert', element).html(result['msg']);
				jQuery('[data-js-id="test-question-msg"]', element).removeClass('d-none');
			}
		},
		checkNotAttemptedCompulsory: function (){
			let testUnAnswered = 0;
			jQuery(".questions_container .tjlms-test-page").each(function (){
				const pageElement = jQuery(this);
				const pageNo = jQuery(this).attr("id").replace("testPage", '');
				let pageUnAnswered = 0;

				jQuery(pageElement).find("[data-js-compulsory='1']").each(function () {

					let answer = '';

					if (jQuery(this).attr("data-js-type") == "text")
					{
						answer = jQuery(this).find("input").val();
					}

					if (jQuery(this).attr("data-js-type") == "textarea")
					{
						answer = jQuery(this).find("textarea").val();
					}

					if (jQuery(this).attr("data-js-type") === 'radio' || jQuery(this).attr("data-js-type") === 'checkbox' || jQuery(this).attr("data-js-type") === 'rating')
					{
						var a = [];
						var ansType = jQuery(this).attr("data-js-type");

						if (jQuery(this).attr("data-js-type") === 'rating')
						{
							ansType = "radio";
						}

						jQuery(this).find(":" + ansType).each(function () {
							if (this.checked) {
								a.push(jQuery(this).val());
							}
						});
						answer = a.join();
					}
					else if (jQuery(this).attr("data-js-type") === 'file_upload')
					{
						var a = [];
						jQuery(this).find('[data-js-id="each-file"]').each(function () {
							a.push(jQuery(this).attr("data-js-itemid"));
						});
						answer = a.join();
					}

					if (answer == '')
					{
						pageUnAnswered++;
					}
				});

				jQuery("#tmt-page-selection [data-lp='"+pageNo+"'] .mandatory-notification").remove();
				testUnAnswered = testUnAnswered + pageUnAnswered;
				jQuery("#unAttemptedCompulsoryCnt").val(testUnAnswered);
				if (pageUnAnswered > 0)
				{
					jQuery("#tmt-page-selection [data-lp='"+pageNo+"']").not('.prev').not('.next').append("<span class='mandatory-notification'>" +pageUnAnswered+ "</span>");
				}
			});

		},
		saveEachPageQueAnswers: function () {
			var formData = jQuery("#adminForm").serialize();

			var saveurl= rootUrl + 'index.php?option=com_tmt&task=test.saveEachPageQueAnswers&tmpl=component&format=json';
			var promise = tjService.postData(saveurl, formData);

			promise.fail(
				function(response) {
					var messages = { "error": [response.responseText]};
					Joomla.renderMessages(messages);
				}
			).done(function(response) {
				if (!response.success && response.message)
				{
					var messages = { "error": [response.message]};
					Joomla.renderMessages(messages);
				}

				if (response.messages){
					Joomla.renderMessages(response.messages);
				}

				if (response.success) {
					//window.location = link;
				}
			});
		},
		submitTest: function (timerOut){
			var formData = jQuery("#adminForm").serialize();
			var saveurl= rootUrl + 'index.php?option=com_tmt&task=test.saveEachPageQueAnswers&tmpl=component&format=json';
			var promise = tjService.postData(saveurl, formData);

			var doProcess = true;

			promise.fail(
				function(response) {
					doProcess = false;
					var messages = { "error": [response.responseText]};
					Joomla.renderMessages(messages);
				}
			).done(function(response) {
				if (!response.success && response.message)
				{
					doProcess = false;
					var messages = { "error": [response.message]};
					Joomla.renderMessages(messages);
				}

				if (response.messages){
					Joomla.renderMessages(response.messages);
				}

				if (response.success) {

					let unAttemptedCompulsoryCnt = 0;

					if (!timerOut)
					{
						// Once form is submiited check if any mandatory Question is not Attempted
						tjlms.test.checkNotAttemptedCompulsory();
						unAttemptedCompulsoryCnt = jQuery("#unAttemptedCompulsoryCnt").val();
					}

					if (parseInt(unAttemptedCompulsoryCnt) > 0)
					{
						alert(Joomla.JText._('COM_TMT_TEST_SUBMIT_VALIDATION_FOR_COMPULSORY_MSG'));
						doProcess = false;
					}
					else
					{
						var formToken = jQuery('[data-js-id="form-token"]').attr('name');
						var testId = jQuery('[name="test[id]"]').val();
						var ltId = jQuery('[name="invite_id"]').val();
						var formData = {};

						formData['testId'] = testId;
						formData['ltId'] = ltId;
						formData[formToken] = 1;

						var saveurl= rootUrl + 'index.php?option=com_tmt&task=test.submitTest&tmpl=component&format=json';
						var promise = tjService.postData(saveurl, formData);

						promise.fail(
							function(response) {
								doProcess =  false;
								var messages = { "error": [response.responseText]};
								Joomla.renderMessages(messages);
							}
						).done(function(response) {
							if (!response.success && response.message)
							{
								doProcess = false;
								var messages = { "error": [response.message]};
								Joomla.renderMessages(messages);
							}

							if (!response.success && response.messages){
								Joomla.renderMessages(response.messages);
							}

							doProcess = true;
						});
					}
				}
			});

			return doProcess;
		},
		checkcount: function (count, qid, value, gradingtype)
		{
			var total = jQuery('input[name="questions[mcqs]['+qid+'][]"]:checked').length;
			var result = [];

			if (total > count && gradingtype != 'feedback')
			{
				jQuery('input[value="'+value+'"]').removeAttr('checked');
				result['error'] = '1';
				result['msg'] = Joomla.JText._('COM_TMT_TEST_MAX_OPTION_ATTEMPT_VALIDATION');

				tjlms.test.eachQuestionSaveMsg(qid, result);
			}
		},
		testactions : function (){
			let isFirst = (this.currentPage == 1) ? 1 : 0;
			let totalPages = parseInt(this.totalPages);
			let gradingtype = parseInt(this.gradingType);
			let isLast = (totalPages == this.currentPage) ? 1 : 0;

			jQuery("[data-js-id='toolbar']").addClass("d-inline-block").removeClass("d-none");
			jQuery(".toolbar__span").hide();

			if (totalPages == 1 || isLast){
				jQuery("[data-js-id='toolbar'] [data-js-id='toolbar-final']").show();

				if (gradingtype === 'exercise'){
					jQuery("[data-js-id='toolbar'] [data-js-id='toolbar-final']").show();
				}
			}

			if (totalPages > 1 && !isFirst && !isLast)
			{
				jQuery("[data-js-id='toolbar'] [data-js-id='toolbar-prev']").show();
				jQuery("[data-js-id='toolbar'] [data-js-id='toolbar-next']").show();
			}

			if (totalPages > 1 && isFirst && !isLast)
			{
				jQuery("[data-js-id='toolbar'] [data-js-id='toolbar-next']").show();
			}

			if (totalPages > 1 && !isFirst && isLast)
			{
				jQuery("[data-js-id='toolbar'] [data-js-id='toolbar-prev']").show();
			}

			jQuery('.quiz_content').scrollTop('0');
		},
	},
	assessment: {
		$form	: null,
		getUrl: function(){
				var rootUrl = (typeof root_url == 'undefined') ? '' : root_url;
				return rootUrl + "index.php?option=com_tjlms&task=assessment.submit&format=json";
			},
		init: function(redirectUrl)
		{
			jQuery(window).on('load', function() {
				var heighttominus =  46;
				var iframeheighttominus = 50;

				/* Set the height of the lesson playlist and right panel*/
				height = jQuery(document).height();

				jQuery("[data-js-attr='tjlms-lesson']").css("height", height - heighttominus);
				jQuery("[data-js-attr='tjlms-lesson-iframe']").css("height", height - iframeheighttominus);
				jQuery("[data-js-id='tjlms-sidebar']").css("height", height - heighttominus);


				if (jQuery(window).width() < 768) {
					jQuery("[data-js-attr='lesson-toolbar-content']").insertBefore('[data-js-attr="lesson-player"]');
					/*jQuery('.playlist-container').removeClass("playlist-hidden");*/
				}

				jQuery( ".closeBtn" ).click(function()
				{
					if (confirm(Joomla.JText._("COM_TJLMS_QUIZ_CONFIRM_BOX")) == true)
					{
						window.location = redirectUrl;
					}
				});

				jQuery(".collapse:first").addClass('in');

				jQuery('[data-jstoggle="collapse"]').click(function(){
					jQuery(this).toggleClass('collapsed');
					var target = jQuery(this).attr('data-target');
					jQuery(target).toggleClass('in');
				});

				tjlms.assessment.limitScoreInput();
			});

			jQuery(document).ready(function(){
				jQuery("#assessment-form.noEditAccess input, #assessment-form.noEditAccess select, #assessment-form.noEditAccess textarea, #assessment-form.noEditAccess button:not(.closeBtn)").prop( "disabled", true );
			})
		},
		limitScoreInput: function() {
			var regex  = /^[0-9]*(?:\.[0-9]*)?$/;
			jQuery('#assessment-form').find(".textinput").on("keyup",function (event) {
					var result = !jQuery(this).val() || jQuery(this).val().match(regex);

					if(!result || jQuery(this).val() > jQuery(this).data("maxval"))
					{
						jQuery(this).parents('.assessment_field').find('.msg').show().removeClass('hide');
						jQuery(this).val("");
						return false;
					}
					else
					{
						jQuery(this).parents('.assessment_field').find('.msg').hide().addClass('hide');
					}
				});
		},
		validate: function(state) {
			if(parseInt(state) == 1){
				var regex  = /^[0-9]*(?:\.[0-9]*)?$/;
				var gradingType =jQuery("input[name='gradingtype']").val();
				var isValid = true;
				/*if quiz check if marks given are valid for question marks*/
				if (gradingType == "quiz"){
					this.$form.find("[data-js-id='test-review-question']").each(function(){
						var marks = jQuery("[data-js-id='test-review-marks']", jQuery(this)).val();
						var qmarks = parseInt(jQuery("[data-js-id='test-review-qmarks']", jQuery(this)).val(),10);
						jQuery("[data-js-id='test-review-msg']",jQuery(this)).addClass("hide");

						if (!marks.match(regex) || isNaN(marks) || marks > qmarks){
							if (isValid == true)
							{
								isValid = false;
							}
							jQuery("[data-js-id='test-review-msg']", jQuery(this)).show().removeClass('hide');
							jQuery("[data-js-id='test-review-marks']", jQuery(this)).val("");
							return;
						}
					});

					if(!isValid)
					{
						return false;
					}
				}
				else
				{  var isValid = true;
					this.$form.find(".assessment_field").each(function(e,elem){
						if(jQuery(elem).find("[name$='[rating_value]']").length){
							isValid = false;
							isValid = isValid || !!jQuery(elem).find("input[type='radio'][name$='[rating_value]']:checked").length;
							isValid = isValid || !!jQuery(elem).find("input[type='checkbox'][name$='[rating_value]']").length;
							isValid = isValid || !!jQuery(elem).find("input[type='text'][name$='[rating_value]']").val();
							isValid = isValid || !!jQuery(elem).find("select[name$='[rating_value]']").val();

							if(!isValid)
							{
								return false;
							}
						}
					});

					if(!isValid)
					{
						var errorMsg = Joomla.JText._('COM_TJLMS_ASSESSMENTS_ARE_YOU_SURE');
						return confirm(errorMsg);
					}

				}
			}
			return true;
		},
		submit: function(state, thisCanEdit)
		{
			this.$form = jQuery('#assessment-form');
			var doProcess = this.validate(state);
			if(!doProcess){
				return false;
			}

			var parentObj = this;
			this.$form.find("button").prop("disabled",true);

			var params = this.$form.serializeArray();
			params.push({name:'review_status', value:state});
			var promise = tjService.postData(this.getUrl(), params);

			promise.fail(
				function(response) {
					jQuery(".assessment-form_msg .alert").removeClass('hide').removeClass('alert-success').addClass('alert-error').html(response.responseText);
					parentObj.$form.find("button").prop("disabled",false);
				}
			).done(function(response) {
				parentObj.$form.find("button").prop("disabled",false);
				if (!response.success) {
					var messages = { "error": [response.message]};
					parentObj.$form.find(".assessment-form_msg .alert").removeClass('hide').removeClass('alert-success').addClass('alert-error').html(response.message);
				}
				else
				{	var messages = { "success": [response.message]};
					parentObj.$form.find(".assessment-form_msg .alert").removeClass('hide').removeClass('alert-error').addClass('alert-success').html(response.message);
					parentObj.$form.find(".assessment-form_score label strong").text(Joomla.JText._('COM_TJLMS_ASSESSMENTS_SCORE').replace("%s", response.data));

					if (parseInt(state) == 1)
					{
						window.location = onSuceessredirectUrl;
					}
				}
			});
		},
	}
}

jQuery(document).ready(function() {

	var $ppc = jQuery('.progress-pie-chart'),
		percent = parseInt($ppc.data('percent')),
		deg = 360 * percent / 100;
	if (percent > 50) {
		$ppc.addClass('gt-50');
	}
	jQuery('.ppc-progress-fill').css('transform', 'rotate(' + deg + 'deg)');
	jQuery('.ppc-percents span').html(percent + '%');


	jQuery('.r-more').click(function() {

		var long_desc = jQuery(this).closest('.long_desc');
		var long_desc_extend = jQuery(long_desc).siblings('.long_desc_extend');

		jQuery(long_desc).hide();
		jQuery(".r-more", long_desc).hide();
		jQuery(long_desc_extend).show();
		jQuery(".r-less", long_desc_extend).show();

	});
	jQuery('.r-less').click(function() {

		var long_desc_extend = jQuery(this).closest('.long_desc_extend');
		var long_desc = jQuery(long_desc_extend).siblings('.long_desc');

		jQuery(long_desc_extend).hide();
		jQuery(".r-less", long_desc_extend).hide();
		jQuery(long_desc).show();
		jQuery(".r-more", long_desc).show();

	});

	jQuery('.tjlms_section_title').click(function() {
		var section_tr = jQuery(this).parent().attr('id');
		var module_id = section_tr.replace("modlist_", "");
		toggleModuleAccordion(module_id);
	});

	jQuery('.tjlms_playlist_mod_title').click(function() {
		var section_div = jQuery(this).parent().attr('id');
		var module_id = section_div.replace("modlist_", "");
		jQuery(this).toggleClass("open")
		jQuery("div.module_container_" + module_id).toggle();
	});

	togglestate();

	jQuery('.tjlms_enrolUsers').on('shown.bs.tab', '#myTabTabs.nav-tabs li a', function(e) {
		var href = jQuery(this).attr('href');
		href = href.replace("#", '');

		var assignModalFooter = jQuery("#tjlms-assign .modal-footer .assign-footer");
		var isAssign = jQuery("#tjlms-assign #select-option").prop("checked");

		if (href == 'groups' && isAssign == true) {
			jQuery(".assign-groups-btn", assignModalFooter).show();
			jQuery(".enroll-groups-btn, .enroll-btn, .assign-btn", assignModalFooter).hide();
		} else if (href == 'groups') {
			jQuery(".enroll-groups-btn", assignModalFooter).show();
			jQuery(".assign-groups-btn, .enroll-btn, .assign-btn", assignModalFooter).hide();
		} else if (href == 'users' && isAssign == true) {
			jQuery(".assign-btn", assignModalFooter).show();
			jQuery(".assign-groups-btn, .enroll-groups-btn, .enroll-btn", assignModalFooter).hide();
		} else if (true) {
			jQuery(".enroll-btn", assignModalFooter).show();
			jQuery(".assign-groups-btn, .enroll-groups-btn, .assign-btn", assignModalFooter).hide();
		}

		adjustModalHeight();
	});
});

function toggleexpand(thisparent) {
	if (jQuery(thisparent).hasClass('tjcollapsed')) {
		jQuery(thisparent).removeClass('tjcollapsed');
		jQuery(thisparent).addClass('tjexpanded');
	} else if (jQuery(thisparent).hasClass('tjexpanded')) {
		jQuery(thisparent).removeClass('tjexpanded');
		jQuery(thisparent).addClass('tjcollapsed');
	}

	togglestate();
}

function togglestate() {
	jQuery('.tjcollapsed').each(function() {
		var parent = jQuery(this).attr('parentfor');
		jQuery("tr[childof=" + parent + "]").hide();
		jQuery("i", this).addClass('icon-folder-close');
		jQuery("i", this).removeClass('icon-folder-open');
	});
	jQuery('.tjexpanded').each(function() {
		var parent = jQuery(this).attr('parentfor');
		jQuery("tr[childof=" + parent + "]").show();
		jQuery("i", this).addClass('icon-folder-open');
		jQuery("i", this).removeClass('icon-folder-close');

	});
}

function redirect(detail_link) {
	window.location = detail_link;
}

function redirect_course(attempt, course_id, scorm_type) {
	document.getElementById('course_id').value = course_id;
	document.getElementById('scorm_type').value = scorm_type;
	document.adminForm.submit();
}

function generate_reports(thislitask, course_id) {
	jQuery.ajax({

		url: root_url + 'index.php?option=com_tjlms&controller=course&task=' + thislitask + '&course_id=' + course_id,
		type: 'POST',
		dataType: 'json',
		timeout: 3500,
		error: function() {
			console.log('Problem with AJAX Request in LMSCommit()');
		},
		success: function(response) {
			jQuery("#course_report").find("tr:gt(0)").remove();

			if (response.length != 0) {

				for (var i = 0; i < response.length; i++) {
					var k = i + 1;
					var newRow = '<tr>';
					newRow += '<td class="center">' + k + '</td>';
					newRow += '<td class="center">' + response[i].username + '</td>';
					newRow += '<td class="center">' + response[i].attempts + '</td>';
					newRow += '<td class="center">' + response[i].grade_score + '</td>';
					newRow += '</tr>';
					jQuery("#course_report").append(newRow);
				}
			} else {
				var newRow = '<tr>';
				newRow += '<td class="center" colspan="3">No data to display</td>';
				newRow += '</tr>';
				jQuery("#course_report").append(newRow);
			}
		}
	});
}

function generate_myreports(thislitask, course_id, userid) {
	jQuery.ajax({

		url: root_url + 'index.php?option=com_tjlms&controller=course&task=' + thislitask + '&course_id=' + course_id + '&userid=' + userid,
		type: 'POST',
		dataType: 'html',
		timeout: 3500,
		error: function() {
			console.log('ERROR_IN_GENERATING_REPORT');
		},
		success: function(response) {
			jQuery("#course_myreport").html(response);
		}
	});
}

function generate_nonscormreport(course_id) {
	jQuery.ajax({

		url: root_url + 'index.php?option=com_tjlms&controller=course&task=getnonscorm_coursereport&course_id=' + course_id,
		type: 'POST',
		dataType: 'json',
		timeout: 3500,
		error: function() {
			console.log('ERROR_IN_GENERATING_MY_COURSE_REPORT');
		},
		success: function(response) {
			jQuery("#nonscormcourse_report").find("tr:gt(0)").remove();

			if (response.length != 0) {

				for (var i = 0; i < response.length; i++) {
					var k = i + 1;
					var newRow = '<tr>';
					newRow += '<td class="center">' + k + '</td>';
					newRow += '<td class="center">' + response[i].username + '</td>';
					newRow += '<td class="center">' + response[i].start_date + '</td>';
					newRow += '<td class="center">' + response[i].stage_page + '</td>';
					newRow += '<td class="center">' + response[i].score + '</td>';
					newRow += '</tr>';
					jQuery("#nonscormcourse_report").append(newRow);
				}
			} else {
				var newRow = '<tr>';
				newRow += '<td class="center" colspan="3">No data to display</td>';
				newRow += '</tr>';
				jQuery("#nonscormcourse_report").append(newRow);
			}
		}
	});
}

function showloading(show) {
	/*
		if(show == 1)
			jQuery('.loading_image').show();
		else
		{
			jQuery('.loading_image').hide();
			//jQuery("#appsloading").remove();
		}*/
}


/* Function to load the loading image. */
function loadingImage(divId, captureWholeScreen) {
	if (typeof(captureWholeScreen) === 'undefined') {
		captureWholeScreen = 0;
	}

	if (divId) {
		divId = '#' + divId;
	} else {
		divId = 'body';
	}

	if (captureWholeScreen == 1) {
		imgwidth = jQuery(document).width();
		imgheight = jQuery(document).height();
	} else {
		imgwidth = jQuery(divId).width();
		imgheight = jQuery(divId).height();
	}

	jQuery("<div id='appsloading'></div>")
		.css("background", "rgba(255, 255, 255, 1) url('" + root_url + "/components/com_tjlms/assets/images/ajax.gif') 50% 45% no-repeat")
		.css("top", jQuery(divId).position().top - jQuery(divId).scrollTop())
		.css("width", imgwidth)
		.css("height", imgheight)
		.css("position", "fixed")
		.css("z-index", "1000")
		.css("opacity", "1")
		.css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity = 80)")
		.css("filter", "alpha(opacity = 80)")
		.appendTo(divId);
}
/* Function to close the loading image. */
function hideImage() {
	jQuery('#appsloading').remove();
}

/*function open_lessonforattempt(course_id, lesson_id, format, attemptsdonebyuser, attempts_allowed, openinnewwindow)*/
function open_lessonforattempt(lessonurl, openinnewwindow, courseId, lessonType) {
	/*var lessonurl = "index.php?option=com_tjlms&view=lesson&lesson_id="+lesson_id+"&tmpl=component";*/

	if (courseId !='0' && lessonType != '1')
	{
		jQuery.ajax({
			url:root_url + 'index.php?option=com_tjlms&task=course.userEnrollAction',
			type:"POST",
			data:{cId:courseId},
			success: function (result)
			{
				console.log(Joomla.JText._("COM_TJLMS_ENROL_SUCCESS"));

				if (openinnewwindow == 'tab') {
					window.open(lessonurl, '_blank');
				} else {
					window.location = lessonurl;
				}
			}
		});
	}else
	{
		if (openinnewwindow == 'tab') {
			window.open(lessonurl, '_blank');
		} else {
			window.location = lessonurl;
		}
	}

	/*if(openinnewwindow == 'popup')
	{
		var wwidth = jQuery(window).width();
		var wheight = jQuery(window).height();
		SqueezeBox.open(lessonurl, {
			handler: 'iframe',
			closable:false,
			size: {x: wwidth, y: wheight},
			sizeLoading: { x: wwidth, y: wheight },
			classWindow: 'tjlms_lesson_screen',
			classOverlay: 'tjlms_lesson_screen_overlay',
			onClose: function() {
				window.parent.document.location.reload(true);
			},
			onOpen:function() {

				jQuery('iframe').load( function() {
					jQuery('iframe').height(jQuery('.tjlms_lesson_screen').height());
					jQuery('iframe').width(jQuery('.tjlms_lesson_screen').width());
					jQuery('iframe').contents().find("section").css('width',wwidth-50);
					jQuery('iframe').contents().find("section").css('padding','0px');
					jQuery('iframe').contents().find("section > .row").css('margin','0px');
					jQuery('iframe').contents().find("section .row .t3-content").css('width','100%');
					jQuery('iframe').contents().find("section .row .t3-content").css('margin','0px');
				});
			}
		});
	}*/
}

function open_scoforattempt(sco_id, lesson_id, attempts_done_byuser, attempts_allowed, last_attempt_status, openinnewwindow) {
	var attempt = 1;
	if (attempts_done_byuser == 0) {
		attempt = 1;
	} else {
		if (last_attempt_status == 'completed' || last_attempt_status == 'passed' || last_attempt_status == 'failed') {
			if (attempts_allowed > 0) {
				if (attempts_done_byuser < attempts_allowed){
					attempt = Number(attempts_done_byuser) + 1;
				}
				else{
					attempt = attempts_done_byuser;
				}
			} else{
				attempt = Number(attempts_done_byuser) + 1;
			}
		} else{
			attempt = attempts_done_byuser;
		}
	}

	var lessonurl = 'index.php?option=com_tjlms&view=lesson&lesson_id=' + lesson_id + '&attempt=' + attempt + '&sco_id=' + sco_id;

	if (openinnewwindow == 'popup') {

		var wwidth = jQuery(window).width() - 100;
		var wheight = jQuery(window).height() - 80;

		lessonurl = lessonurl + '&tmpl=component&fs=1';
		SqueezeBox.open(lessonurl, {
			handler: 'iframe',
			size: {
				x: wwidth,
				y: wheight
			},
			onClose: function() {
				window.parent.document.location.reload(true);
			}
		});
	} else
		window.location = lessonurl;
}

/* This funstion is triggered by each plugin to update the tracking data*/
function updateData(plugObject, isClosed) {
	jQuery.ajax({
		url: root_url + "index.php?option=com_tjlms&task=callSysPlgin&plgType=" + plugObject["plgtype"] + "&plgName=" + plugObject["plgname"] + "&plgtask=" + plugObject["plgtask"] + "&mode=" + plugObject["mode"],
		dataType: "json",
		type: "POST",
		data: {
			lesson_status: plugObject["lesson_status"],
			lesson_id: plugObject["lesson_id"],
			attempt: plugObject["attempt"],
			total_content: plugObject["total_content"],
			current_position: plugObject["current_position"],
			lesson_status: plugObject["lesson_status"],
			time_spent: plugObject["time_spent"]
		},
		success: function(response) {
			return response;
		},
		error: function(response) {
			console.log('something went wrong');
		},
		complete: function(response) {
			if (isClosed === true)
			{
				closePopup(launchLessonFullScreen, returnUrl);
			}
		}
	});
}

/* triggered to close lesson popup*/
function closePopup(lessonLaunch, redirectUrl) {
	if (lessonLaunch == 'tab') {
		if (opener){
			opener.location.reload();
		}
		window.close();
	} else if (lessonLaunch == 'preview_popup' || lessonLaunch == 'popup') {
		window.parent.SqueezeBox.close();
	} else {
		window.location = redirectUrl;
	}
}

function enrollUser(id) {
	var adminFormToc = "adminFormToc" + id;
	jQuery('#free_course_button').attr("disabled", "disabled");
	jQuery('#' + adminFormToc).submit();
}

/*triggered from resume window of lesson - startover OR resume*/
function askforaction(action, lesson_id, lessonUrl, attempt, format) {
	jQuery.ajax({
		url: root_url + 'index.php?option=com_tjlms&task=lesson.askforaction',
		type: 'POST',
		async: false,
		data: {
			action: action,
			lesson_id: lesson_id,
			attempt: attempt,
			lessonformat: format
		},
		dataType: 'json',
		timeout: 3500,
		error: function() {
			alert('COM_TJLMS_ERROR_IN_SETTING_ATTEMPT');
		},
		beforeSend: function() {
			jQuery('#askforattempt').hide();
			showloading(1);
		},
		success: function(response) {
			if (response == 1) {
				window.location = lessonUrl;
			}
		},
	});
}

function togglePaylistSidebar(action) {
	jQuery('#playlist-container').toggleClass('playlist-hidden');
	jQuery('.lesson-left-panel-header').toggleClass('playlist-hidden');

	if (action == 'close') {
		jQuery('#lesson-main-container').removeClass('span9').addClass('span12 expanded');
	} else {
		jQuery('#lesson-main-container').removeClass('span12 expanded').addClass('span9');
	}
}

function toggleJlikePanel() {
	jQuery('.main-lesson').toggleClass('span8');
	jQuery('.right-panel').toggleClass('right-panel-hidden');
}


function printcertificate(divName) {
	var printContents = document.getElementById(divName).innerHTML;
	var originalContents = document.body.innerHTML;
	document.body.innerHTML = printContents;
	window.print();
	document.body.innerHTML = originalContents;
}

/*
function toggleModuleAccordion(module_id){
	if (!module_id)
	{
		var section_tr	=	jQuery('.tjlms_section_title').first().parent().attr('id');
		 module_id	=	section_tr.replace("modlist_", "");
	}

	var isOpen = jQuery('#modlist_' + module_id).find('.tjlms_section_title').hasClass("open");
		jQuery('.tjlms_lesson').hide()
		jQuery('.tjlms_section_title').removeClass( "open" );

	if (!isOpen)
	{
		jQuery("tr.tjlms_lesson_"+module_id).show();
		jQuery('#modlist_' + module_id).find('.tjlms_section_title').toggleClass( "open" );
	}
}*/

function closeModalPopup() {
	jQuery('.modal-header button[data-dismiss="modal"]', window.parent.document).click();
}

function courseAssignRecommend(action, task) {
	if (action == "assign") {
		var startDateField = jQuery('#start_date');
		var dueDateField = jQuery('#due_date');
		var startDateVal = startDateField.val();
		var dueDateVal = dueDateField.val();

		if (startDateVal == "" || dueDateVal == "") {
			alert(Joomla.JText._("COM_TJLMS_SELECT_FILL_DATES"));

			if (startDateVal == "") {
				startDateField.focus();
			} else if (dueDateVal == "") {
				dueDateField.focus();
			}

			return false;
		}

		if (checkDateFormat(startDateVal) == false) {
			alert(Joomla.JText._("COM_TJLMS_INVALID_DATE_FORMAT") + startDateVal);
			startDateField.val("");
			return false;
		}

		if (checkDateFormat(dueDateVal) == false) {
			alert(Joomla.JText._("COM_TJLMS_INVALID_DATE_FORMAT") + dueDateVal);
			startDateField.val("");
			return false;
		}

		if ((startDateVal) > (dueDateVal)) {
			alert(Joomla.JText._("COM_TJLMS_START_DATE_GT_THAN_DUE_DATE"));
			return false;
		}

		/* Check for only end date*/
		if (dueDateVal != '') {
			var today = new Date();
			today.setHours(0, 0, 0, 0);
			assignEndDate = new Date(dueDateVal);
			assignEndDate.setHours(0, 0, 0, 0);

			if (assignEndDate < today) {
				alert(Joomla.JText._("COM_TJLMS_START_DATE_GT_THAN_TODAY"));
				return false;
			}
		}
	}

	jQuery('#task').val(task);
	document.adminForm.submit();
}

function openAssignRecommendPopups(link, modalId = 'assigntabModal', id = null) {
	var wwidth = jQuery(window).width() - 50;
	var wheight = jQuery(window).height() - 50;
	jQuery("#" + modalId + id).modal('show');
}

function closeAssignRecommendPopups() {
	window.parent.document.location.reload(true);
	window.parent.SqueezeBox.close();
}
var assignUser = function(task, operation) {
	var startDate = jQuery("#start_date").val();
	var dueDate = jQuery("#due_date").val();
	var senderMsg = jQuery("#sender_msg").val();
	var startDateField = jQuery("#start_date");
	var dueDateField = jQuery("#due_date");

	if (operation == 'enroll' || operation == 'enrollGroup') {
		if (jQuery('#selectedcourse').val() == '') {
			alert(Joomla.JText._('COM_TJLMS_SELECT_COURSE_TO_ENROLL'));

			return false;
		}

		if (operation == 'enroll') {
			if (document.adminForm.boxchecked.value == 0) {
				alert(Joomla.JText._("COM_TJLMS_MESSAGE_SELECT_ENROLL_ITEMS"));
				return false;
			}
		} else if (operation == 'enrollGroup') {
			if (!jQuery("#enrollusers").find('.tjlms_enrolUsers input.user_groups:checked').length) {
				alert(Joomla.JText._("COM_TJLMS_SELECT_GROUP_TO_ASSIGN"));
				return false;
			}
		}

		jQuery("#enrollusers #type").val(operation);
		Joomla.submitform('enrolluser.' + task);
	} else if (operation == 'assign' || operation == 'assignGroup') {
		if (jQuery('#selectedcourse').val() == '') {
			alert(Joomla.JText._('COM_TJLMS_SELECT_COURSE_TO_ENROLL'))
			return false;
		}

		if (document.adminForm.boxchecked.value == 0 && operation == 'assign') {
			alert(Joomla.JText._("COM_TJLMS_MESSAGE_SELECT_ASSIGN_ITEMS"));
			return false;
		} else if (!jQuery("#enrollusers").find('.tjlms_enrolUsers input.user_groups:checked').length && operation == 'assignGroup') {
			alert(Joomla.JText._("COM_TJLMS_SELECT_GROUP_TO_ASSIGN"));
			return false;
		} else {
			if (checkDateFormat(startDate) == false) {
				alert(Joomla.JText._("COM_TJLMS_INVALID_START_DATE_FORMAT"));
				startDateField.focus();
				return false;
			}

			if (checkDateFormat(dueDate) == false) {
				alert(Joomla.JText._("COM_TJLMS_INVALID_DUE_DATE_FORMAT"));
				dueDateField.focus();
				return false;
			}

			if ((startDate) > (dueDate)) {
				alert(Joomla.JText._("COM_TJLMS_START_DATE_GT_THAN_DUE_DATE"));
				return false;
			}

			/*Check for only end date*/
			if (dueDate != '') {
				var today = new Date();
				today.setHours(0, 0, 0, 0);
				assignEndDate = new Date(dueDate);
				assignEndDate.setHours(0, 0, 0, 0);

				if (assignEndDate < today) {
					alert(Joomla.JText._("COM_TJLMS_START_DATE_GT_THAN_TODAY"));
					return false;
				}
			}

			if (jQuery('.assign-groups-btn #update_existing_users').prop('checked') == true) {
				jQuery('.assign-groups-btn #update_existing_users').val(1);
			}

			jQuery("#enrollusers #type").val(operation);
			Joomla.submitform('enrolluser.' + task);
		}
	} else if (operation == 'reco') {
		if (document.adminForm.boxchecked.value == 0) {
			alert(Joomla.JText._("COM_TJLMS_MESSAGE_SELECT_RECO_ITEMS"));
			return false;
		}

		jQuery("#enrollusers #type").val(operation);
		Joomla.submitform('enrolluser.' + task);
	}
};

/*Validate date format*/
var checkDateFormat = function(datevalue) {
	/*regular expression to match required date format*/
	regExp = /^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/;

	if (datevalue && (datevalue.match(regExp))) {
		return true;
	}

	return false;
};

/*User Enrollment and Assign */
var showAssign = function(thisObj) {
	if (jQuery(".tjlms_enrolUsers .nav-tabs li:first-child").hasClass('active') == true) {
		jQuery('.show-assignment-fields, .assign-btn, .enroll-btn, .tjmodal-enroll-title, .tjmodal-assign-title').toggle();
		jQuery('.assign-groups-btn, .enroll-groups-btn').hide();
	} else {
		jQuery('.assign-groups-btn, .enroll-groups-btn, .show-assignment-fields, .tjmodal-enroll-title, .tjmodal-assign-title').toggle();
	}
	adjustModalHeight();

	return;
};
/*Resize assign and recommend popup modal*/
var adjustModalHeight = function() {
	if (jQuery('#recommend-table-container').length) { /*recommend*/
		var height = jQuery('html').height() - jQuery('.modal-footer').outerHeight() - jQuery('#recommend-table-container').offset().top;
		jQuery('#recommend-table-container').css('height', height);
	}
	if (jQuery('#assign-table-container').length) { /*assign*/
		var height = jQuery('html').height() - jQuery('.modal-footer').outerHeight() - jQuery('#myTabContent').offset().top;
		jQuery('#myTabContent').css('height', height);
	}
};

jQuery(document).ready(function() {
	if (jQuery('#tjlms-assign').length && tjlms.utility.inIframe()) {
		jQuery('.modal-body').prepend(jQuery('#system-message-container'));
		jQuery('html').addClass('popuphtml');
		jQuery('.modal-header', '.popuphtml').parents().each(function(i, j) {
			jQuery(j).addClass('fullheight');
		});

		jQuery('.field-calendar button').on('click', function() {
			jQuery('.calendar-container > table.table').removeClass('table');
		})

	}
})

var tjlmsfilter = {
	init : function() {
		jQuery(document).ready(function()
		{
			if (jQuery('#creator_filter').val() > 0)
			{
				jQuery('#creator_filter_chzn').addClass('filterActive');
			}

			if (jQuery('#category_filter').val() > 0)
			{
				jQuery('#category_filter_chzn').addClass('filterActive');
			}

			if (jQuery('#course_type').val() >= 0)
			{
				jQuery('#course_type_chzn').addClass('filterActive');
			}

			if(jQuery(window).width() < 767)
			{
				jQuery('[data-id="filter-category"]').addClass('col-xxs-6');
				jQuery('[data-id="filter-type"]').addClass('col-xxs-6');
				jQuery('[data-id="filter-author"]').addClass('col-xxs-6');
				jQuery('[data-id="filter-tag"]').addClass('col-xxs-6');
			}

			if (jQuery('[data-id="filter-search"]').hasClass('filterActive') ||
				jQuery('[data-id="filter-type"]').hasClass('filterActive')   ||
				jQuery('[data-id="filter-category"]').hasClass('filterActive') ||
				jQuery('[data-id="filter-author"]').hasClass('filterActive') ||
				jQuery('[data-id="filter-tag"]').hasClass('filterActive'))
			{
				jQuery('.tjlms-filters').show();
				jQuery('[data-identifier="tjlms-filters-menu"]').find('i').addClass('fa-angle-down').removeClass('fa-angle-right');
			}

		});
	},
	reset: function(){
		jQuery('#course_cat, #course_type, #creator_filter, #course_status, #filter_tag').prop('selectedIndex',0);
		jQuery('.course-fields').prop('selectedIndex',0);
		jQuery('#filter_search').val('');
	},
	toggle: function(element){
		jQuery('.tjlms-filters').toggle();

		if (jQuery('.tjlms-filters').is(':hidden'))
		{
			jQuery(element).children('i').addClass('fa-angle-right').removeClass('fa-angle-down');
		}
		else
		{
			jQuery(element).children('i').addClass('fa-angle-down').removeClass('fa-angle-right');
		}
	},
	toggleDiv: function(filterId)
	{
		jQuery('#'+filterId).toggleClass("active hide");
		jQuery('#displayFilter .plusminusIcon').toggleClass("fa-plus fa-minus");
	}
}
									
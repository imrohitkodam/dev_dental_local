var tmt = {
	eachform: {
		$form: null,
		saveurl: '',
		extraValidations: '',
		validate : function() {
			var isValid = document.formvalidator.isValid(document.getElementById(this.$form.attr('id')));
			var formElement = this.$form;

			if (isValid){
				if (this.extraValidations){

					var extravalid =  true;
					var arr = this.extraValidations.split(',');
					jQuery(arr).each(function (index, strFun){
						var func = window;
						var funcSplit = strFun.split('.');
						for (i = 0;i < funcSplit.length;i++){
							func = func[funcSplit[i]];
						}

						extravalid = func(formElement);

						if (!extravalid){
							return false;
						}
					});
					if (!extravalid)
					{
						isValid = false;
					}
				}
			}

			return isValid;
		},
		ajaxsave : function() {
			var doProcess = this.validate();

			if (doProcess){
				if (!this.saveurl){
					this.saveurl= jQuery(this.$form).attr('action');
				}
				var thisform = this.$form;
				var params = {};

				if (jQuery(thisform).attr("enctype") == "multipart/form-data")
				{
					var jformData = new FormData(thisform[0]);
					params['contentType'] = false;
					params['processData'] = false;
				}
				else
				{
					var jformData = thisform.serialize();
				}
				var promise = tjService.postData(this.saveurl,jformData, params);


				promise.fail(
					function(response) {
						doProcess =  false;
					}
				).done(
					function(response) {
						if (!response.success && response.message){
								var messages = { "error": [response.message]};
								Joomla.renderMessages(messages);
								doProcess =  false;
							}
							if (response.messages){
								Joomla.renderMessages(response.messages);
							}

						doProcess = response.data;
					}
				);
			}
		return doProcess;
		},
		ajaxSaveFormData : function() {
			var doProcess = this.validate();

			if (doProcess){
				if (!this.saveurl){
					this.saveurl= jQuery(this.$form).attr('action');
				}
				var thisform = this.$form;
				var jformData = new FormData(thisform[0]);

				var params = {};
				params['contentType'] = false;
				params['processData'] = false;

				var promise = tjService.postData(this.saveurl,jformData,params);

				promise.fail(
					function(response) {
						doProcess =  false;
					}
				).done(
					function(response) {
						if (!response.success && response.message){
								var messages = { "error": [response.message]};
								Joomla.renderMessages(messages);
								doProcess =  false;
							}
							if (response.messages){
								Joomla.renderMessages(response.messages);
							}

						doProcess = response.data;
					}
				);
			}
		return doProcess;
		}
	},
	testFormdetails :{
		afterSave : function(doProcess)
		{
			if (doProcess.section_id) {
				var sectionObj = tmt.section.getSection(doProcess.section_id);
				tmt.section.clone(sectionObj);
			}
		}
	},
	stepform: {
		ajaxSaveTabs: true,
		ifintmpl: true,
		init: function() {
			ifintmpl = this.ifintmpl;
			var stepFormObj = this;

			jQuery(document).ready(function(){
				if (ifintmpl){
					jQuery('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
						stepFormObj.formactions();
					});
				}

				stepFormObj.formactions();
			});

			jQuery(window).on("load", function () {
				/*validation on each tab click*/
				jQuery(".nav-tabs li").click(function(){
					var linumber  = jQuery(this).index();

					var result = stepFormObj.validateTabs(linumber);

					if (!result){
						return false;
					}
				});

				/*validation on each tab click*/
				jQuery("[data-js-attr='form-actions-next']").click(function(){
					jQuery(".nav-tabs li a.nav-link.active").closest('li').next('li').find(".nav-link")[0].click();
					jQuery("html, body").animate({scrollTop: 0}, 500);

					stepFormObj.formactions();
				});

				/*validation on each tab click*/
				jQuery("[data-js-attr='form-actions-prev']").click(function(){
					jQuery(".nav-tabs li a.nav-link.active").closest('li').prev('li').find(".nav-link")[0].click();

					stepFormObj.formactions();
				});

			});
		},
		formactions : function (){
			jQuery("#toolbar-prev button, #toolbar-next button, #toolbar-apply button, #toolbar-save button","[data-js-attr='form-actions']").hide();

			if(jQuery(".nav-tabs li a.nav-link.active").closest('li').is(':first-child')){
				jQuery("#toolbar-prev button","[data-js-attr='form-actions']").hide().addClass('d-none');
				jQuery("#toolbar-next button","[data-js-attr='form-actions']").show().removeClass('d-none');
			}
			else if(jQuery(".nav-tabs li a.nav-link.active").closest('li').is(':last-child')){
				jQuery("#toolbar-prev button", "[data-js-attr='form-actions']").show().removeClass('d-none');
				jQuery("#toolbar-next button", "[data-js-attr='form-actions']").hide().addClass('d-none');
				jQuery("#toolbar-save button", "[data-js-attr='form-actions']").show().removeClass('d-none');
			}
			else{
				jQuery("#toolbar-prev button","[data-js-attr='form-actions']").show().removeClass('d-none');
				jQuery("#toolbar-next button","[data-js-attr='form-actions']").show().removeClass('d-none');
			}

			if (jQuery("[data-js-id='item-id']").val()){
				jQuery("#toolbar-save", "[data-js-attr='form-actions']").show().removeClass('d-none');
			}
		},
		validateTabs : function(tabcounttovalidate) {
			var formprocessdone = true;

			for(var i=0 ; i < tabcounttovalidate ; i++){

				var navli = jQuery(".nav-tabs li").get(i);
				var thiscontenttab	= jQuery('a', jQuery(navli)).attr('href');

				tmt.eachform.extraValidations =  jQuery(".extra_validations", jQuery(thiscontenttab)).attr('data-js-validation-functions');

				if (this.ajaxSaveTabs){
					tmt.eachform.$form = jQuery('form', thiscontenttab);
					var task= jQuery("form [name='task']", thiscontenttab).val();
					tmt.eachform.saveurl= "index.php?option=com_tmt&task=" + task + "&format=json";
					formprocessdone = tmt.eachform.ajaxsave();
				}
				else{
					tmt.eachform.$form = jQuery(thiscontenttab);
					formprocessdone = tmt.eachform.validate();
				}

				if (!formprocessdone){
					break;
				}

				if (this.ajaxSaveTabs){
					jQuery.each(formprocessdone, function( key, value ) {
						jQuery("[data-js-id='" + key +"']").val(value);
					});
				}

				var formid = jQuery(tmt.eachform.$form).attr('id');
				if (window["tmt"][formid])
				{
					window["tmt"][formid]["afterSave"](formprocessdone);
				}

			}

			return formprocessdone;
		},
	},
	question: {
		init: function(ifintmpl, unique, gradingtype, target, forDynamic, isQuestionAttempted) {
			tmt.stepform.ajaxSaveTabs = false;
			tmt.stepform.ifintmpl = ifintmpl;
			tmt.stepform.init();

			var qtype = jQuery("#jform_type").val();

			if (qtype != 'radio' && qtype != 'checkbox')
			{
				jQuery("[data-js-id='for-mcqs']").hide();
			}

			if (!gradingtype)
			{
				var gradingtype = jQuery("#jform_gradingtype").val();
			}

			tmt.question.onquestionGradingTypeChange(gradingtype);

			jQuery(window).on("load", function (){

				jQuery("#jform_type").change(function(){

					tmt.question.onquestionTypeChange(jQuery(this).val());
				});

				jQuery("#jform_category_id").change(function()
				{
					if (isQuestionAttempted)
					{
						alert(Joomla.JText._('COM_TMT_Q_FROM_CATEGORY_CHANGE'));
					}
				});

				jQuery("#jform_gradingtype").change(function(){

					tmt.question.onquestionGradingTypeChange(jQuery(this).val());
				});

				jQuery(document).on("click",".answers_iscorrect", function(){
					tmt.question.onCorrectAnsCheck(jQuery(this));
				});

				jQuery(document).on("change", '[data-js-id="answer-media-type"]', function (){
					tmt.question.onAnswerMediaTypeChange(jQuery(this));
				});

				tmt.question.updateAnswerMediaOptionsOnPageLoad();

				//tmt.question.getTotalMarks();
			});

			Joomla.submitbutton = function(task)
			{
				if (task !== "question.cancel")
				{
					var tabscount  = jQuery("#questionformTabs li").length;
					tmt.stepform.ajaxSaveTabs = false;
					var result = tmt.stepform.validateTabs(tabscount);

					if (!result){
						return false;
					}
				}

				if(ifintmpl && task !== "question.cancel")
				{
					tmt.eachform.$form = jQuery('#questionForm');
					tmt.eachform.saveurl= "index.php?option=com_tmt&task=question.save&format=json";
					formprocessdone = tmt.eachform.ajaxSaveFormData();

					if(formprocessdone)
					{
						var arr = unique.split("_");
						var test_id = arr[0];
						var section_id = arr[1];

						if (target == 'section')
						{
							doProcess = tmt.test.addQuestionToSection(test_id, section_id, formprocessdone)

							if (doProcess)
							{
								window.parent.Joomla.Modal.getCurrent().close();
							}
						}
						if (target == 'rule')
						{
							if (forDynamic == 1)
							{
								tmt.test.fetchSetRuleQuestions(gradingtype, unique);
							}
							else
							{
								tmt.test.fetchRuleQuestions(gradingtype, unique, window.parent.document);
							}

							window.parent.Joomla.Modal.getCurrent().close();
							/*Add question is opened from autoPickRule*/
						}
					}
				}
				else if(ifintmpl && task == "question.cancel")
				{
					window.parent.Joomla.Modal.getCurrent().close();
				}
				else
				{
					Joomla.submitform(task, document.getElementById("questionForm"));
				}
			}
		},
		batchaddToSection: function (unique) {
			var arr = unique.split("_");
			var test_id = arr[0];
			var section_id = arr[1];

			var checkedQues = jQuery("input[id*='cb']:checked").length;

			if(checkedQues==0)
			{
				alert(Joomla.JText._("COM_TMT_MESSAGE_SELECT_ITEMS"));
				return false;
			}

			var c = 1;
			jQuery("input[id*='cb']:checked").each(function() {
				var question = tmt.test.addQuestionToSection(test_id, section_id, jQuery(this).val())
				if (question)
				{
					if (c == checkedQues)
					{
						// parent.SqueezeBox.close();
						window.parent.Joomla.Modal.getCurrent().close();
					}

					c++;
				}
			});
		},
		validateQuestion: function (){

			var gradingtype = jQuery("#jform_gradingtype").val();
			var questiontype = jQuery("#jform_type").val();

			if (gradingtype == 'quiz') {

				var questionMarks = jQuery("#jform_marks").val();

				if (questionMarks <= 0)
				{
					var res = { "error": [Joomla.JText._('COM_TMT_VALID_MARKS')]};
					Joomla.renderMessages(res);
					return false;
				}
			}

			if(questiontype == 'rating')
			{
				var upper_range = lower_range = 0;
				lower_range = parseInt (jQuery("[data-js-id='answers_lower_text']").val());
				upper_range = parseInt (jQuery("[data-js-id='answers_upper_text']").val());
				var rating_labels_lenghth = 0;

				if (jQuery("#jform_params_rating_label1").val())
				{
					labels = jQuery("#jform_params_rating_label1").val().trim();

					if (labels)
					{
						rating_labels_lenghth = labels.split(',').length;
					}
				}

				if(lower_range > upper_range)
				{
					var res = { "error": [Joomla.JText._('COM_TMT_QUESTION_RATING_TYPE_VALIDATION')]};
					Joomla.renderMessages(res);
					return false;
				}

				var list = [];

				for (var i = lower_range; i <= upper_range; i++) {
					list.push(i);
				}

				if (rating_labels_lenghth > 0)
				{
					if (list.length != rating_labels_lenghth)
					{
						var res = { "error": [Joomla.JText._('COM_TMT_QUESTION_RATING_LABEL_ERROR') + list.length]};
						Joomla.renderMessages(res);
						return false;
					}
				}

			}

			if (questiontype == 'textarea')
			{
				var max_length = min_length = 0;
				max_length = parseInt(jQuery("[data-js-id='answers_max_length']").val());
				min_length = parseInt(jQuery("[data-js-id='answers_min_length']").val());

				if (min_length > max_length)
				{
					var res = { "error": [Joomla.JText._('COM_TMT_QUESTION_TEXTAREA_TYPE_VALIDATION')]};
					Joomla.renderMessages(res);
					return false;
				}
			}

			if (questiontype == 'file_upload')
			{
				let maxUploadSize = parseInt(lessonUploadSize);
				let fileSize = parseInt(jQuery("[name='jform[params][file_size]']").val());

				if (fileSize > maxUploadSize)
				{
					jQuery("[name='jform[params][file_size]']").val(maxUploadSize);
					var res = { "error": [Joomla.JText._('COM_TMT_Q_FORM_PARAMS_FILE_SIZE_MSG').replace("%s", maxUploadSize)]};
					Joomla.renderMessages(res);
					return false;
				}
			}

			if (questiontype !== 'radio' && questiontype !== 'checkbox') {
				return true;
			}

			var seen = {};
			var noduplicates = true;
			var answercnt = sum = noMarksForcorrect = correctMarked = marksForNocorrect = flag = 0;
			var messages = [];
			var questionMarks = parseInt(jQuery("#jform_marks").val());

			jQuery("#questionanswers textarea.answers_text").each(function() {

				var txt = jQuery(this).val();

				if (txt != "")
				{
					answercnt++;

					if (seen[txt])
						noduplicates =  false;
					else
						seen[txt] = true;

					var currentID;
					currentID=jQuery(this).attr('id').substr(12); /* Length of string 'answers_marks' is 13. */

					var ansMarks = jQuery("#answers_marks"+currentID).val();
					jQuery("#answers_marks"+currentID).val(Math.round(ansMarks));
					ansMarks = parseInt(jQuery("#answers_marks"+currentID).val());

					if(jQuery("#answers_iscorrect"+currentID).is(":checked"))
					{
						correctMarked ++;

						if (ansMarks > 0)
						{
							sum += parseInt(ansMarks,10); /* 10 is for decimal system*/

							if(ansMarks == questionMarks)
							{
								flag = 1;
							}

							if(ansMarks > questionMarks && questiontype == 'radio')
							{
								noMarksForcorrect++;
							}
						}
						else
						{
							noMarksForcorrect++;
						}
					}
					else
					{
						if (ansMarks > 0)
						{
							marksForNocorrect++;
						}
					}

				}
			});

			jQuery('#total-marks-content').html(sum);

			if (gradingtype != 'feedback' && answercnt <= 1) {
				messages.push(Joomla.JText._('COM_TMT_QUESTION_MCQ_MRQ_ATLEAST_TWO_ANSWERS'));
			}

			if (!noduplicates) {
					messages.push(Joomla.JText._('COM_TMT_Q_DUPLICATE_ANS'));
				}

			if (gradingtype == 'quiz' || gradingtype == 'exercise') {

				if (!correctMarked) {
						messages.push(Joomla.JText._('COM_TMT_QUESTION_NO_CORRECT_ANS_MSG'));
					}
			}

			if (gradingtype == 'quiz') {

				if (noMarksForcorrect)  {
					messages.push(Joomla.JText._('COM_TMT_Q_FORM_NO_MARK_FOR_CORRECT_ANSWER'));
				}

				if (marksForNocorrect){
					messages.push(Joomla.JText._('COM_TMT_QUESTION_MARKS_FOR_NOTCORRECT_ANSWER'));
				}

				if (questionMarks > 0 && sum != questionMarks && questiontype == 'checkbox')
				{
					messages.push(Joomla.JText._('COM_TMT_Q_FORM_MARKS_MISMATCH'));
				}

				if(flag != 1 && questiontype == 'radio')
				{
					messages.push(Joomla.JText._('COM_TMT_Q_FORM_MARKS_NOTMATCH_FOR_MCQ'));
				}
			}

			if (messages.length > 0)
			{
				var res = { "error": messages};
				Joomla.renderMessages(res);
				return false;
			}

			return true;
		},
		findDuplicatesAnswers: function (){
			var seen = {};
			var noduplicates = true;
			jQuery("#questionanswers textarea.answers_text").each(function() {
				var txt = jQuery(this).val();

				if (seen[txt])
					noduplicates =  false;
				else
					seen[txt] = true;
			});
			if (!noduplicates)
			{
				var messages = { "error": [Joomla.JText._('COM_TMT_Q_DUPLICATE_ANS')]};
				Joomla.renderMessages(messages);
			}

			return noduplicates;
		},
		/*correctAnsMarks: function (){
			var sum=0;
			jQuery("input[id^='answers_marks']").each(function() {
				var currentID;
				currentID=jQuery(this).attr('id').substr(13);

				if(jQuery(this).val())
				{
					var eachAnsMarks = jQuery(this).val();
					jQuery(this).val(Math.round(eachAnsMarks));

					if( jQuery(this).val() && jQuery("#answers_iscorrect"+currentID).is(":checked"))
					{
						sum += parseInt(jQuery(this).val(),10);
					}
				}
			});

			jQuery('#total-marks-content').html(sum);

			return sum;
		},*/
		onCorrectAnsCheck: function (element)
		{
			console.log(element);
			var qType = jQuery("#jform_type").val();
			var	qMarks = jQuery('#jform_marks').val();

			var thisAnsrow = jQuery(element).closest(".answer-template");
			var thisansMarks = jQuery(thisAnsrow).find(".answers_marks");

			jQuery(".answers_iscorrect_hidden").val(0);
			var checkedCnt = 0;

			jQuery(".answers_iscorrect").each(function() {
				var thisAnsrow = jQuery(this).closest(".answer-template");
				var thisansIsCorrectHidden = jQuery(thisAnsrow).find(".answers_iscorrect_hidden");
				var thisansMarks = jQuery(thisAnsrow).find(".answers_marks");

				if( jQuery(this).is(":checked") )
				{
					jQuery(thisansIsCorrectHidden).val(1);
					checkedCnt++;
				}
				else
				{
					jQuery(thisansMarks).val(0);
				}
			});

			if(jQuery(element).is(":checked"))
			{
				if (checkedCnt == 1)
				{
					jQuery(thisansMarks).val(qMarks);
				}

				jQuery(thisansMarks).focus();
			}
		},
		onquestionTypeChange: function(newType)
		{
			jQuery("[data-js-id='for-mcqs']").hide();
			jQuery('[data-js-id="answer-media"]').hide();

			if(newType == 'radio')
			{
				newType = 'checkbox';
			}

			if (newType == 'radio' || newType == 'checkbox')
			{
				jQuery("[data-js-id='for-mcqs']").show();
				jQuery('[data-js-id="answer-media"]').show();
			}

			jQuery(".answers-container").html("");

			addAnswerClone(newType, 'answers-container');
		},
		onquestionGradingTypeChange: function (gradingType)
		{
			jQuery('[data-js-type="quiz"]').hide();
			jQuery('[data-js-id="mcq-correct"]').show();
			jQuery('[data-js-id="textinput-input"]').show();
			jQuery('[data-js-id="textinput-messsage"]').hide();

			if(gradingType == 'quiz')
			{
				jQuery('[data-js-type="quiz"]').show();
			}
			if (gradingType == 'feedback')
			{
				jQuery('[data-js-id="mcq-correct"]').hide();
				jQuery('[data-js-id="textinput-input"]').hide();
				jQuery('[data-js-id="textinput-messsage"]').show();
			}
		},
		onAnswerMediaTypeChange: function (element)
		{
			let mediaType = element.val();

			element.siblings('[data-js-id^="answer-media-"]').addClass("d-none");

			element.siblings('[data-js-id="answer-media-' + mediaType + '"]').removeClass("d-none");

			element.siblings('.video-note, .file-note, .audio-note, .image-note').addClass("d-none");

			if (mediaType)
			{
				element.siblings('.' + mediaType + '-note').removeClass("d-none");
			}
		},
		updateAnswerMediaOptionsOnPageLoad: function ()
		{
			jQuery('[data-js-id="answer-media-type"]').each(function(){
				let element = jQuery(this);
				let mediaType = jQuery(element).val();
				element.siblings('[data-js-id^="answer-media-"]').addClass("d-none");
				if (mediaType.trim() != '')
				{
					element.siblings('[data-js-id="answer-media-' + mediaType + '"]').removeClass("d-none");

					if (mediaType)
					{
						element.siblings('.' + mediaType + '-note').removeClass("d-none");
					}
				}
			});
		},
		opentmtSqueezeBox: function(link, modalId = 'quizModal', id = null)
		{
			jQuery("#" + modalId + id).modal('show');
		}
	},
	tests: {
		addToCourse : function (testId, courseId, modId){
					var formToken = jQuery('[data-js-id="form-token"]').attr('name');
					var saveurl= "index.php?option=com_tmt&task=test.addTestTocourse&format=json";

					var formData = {};
					formData[formToken] = 1;
					formData['testId'] = testId;
					formData['courseId'] = courseId;
					formData['modId'] = modId;

					var promise = tjService.postData(saveurl,formData);

					var doProcess =  false;
					promise.fail(
						function(response) {
							var messages = { "error": [response]};
							Joomla.renderMessages(messages);
							doProcess =  false;
						}
					).done(function(response) {
						if (!response.success && response.message){
							var messages = { "error": [response.message]};
							Joomla.renderMessages(messages);
							doProcess = false;
						}

						if (response.success)
						{
							window.parent.location.href = response.data.redirect_url;
						}
					});
		},
	},
	test: {
		init: function(gradingtype, ifintmpl, cid, mid, assessment, maxattempt, livetrackReviews) {

			jQuery("[data-js-attr='set-options']").hide();
			jQuery("[data-js-attr='plain-options']").show();

			if (jQuery('#jform_time_duration').val().trim() == '' || jQuery('#jform_time_duration').val() == 0)
			{
				jQuery('#jform_show_time label').addClass("disabledradio");
				jQuery('#jform_show_time_finished label').addClass("disabledradio");
				jQuery('#show_duration').hide();
			}

			if (gradingtype == 'quiz' && jQuery("[name='jform[type]']:checked").val() === 'set'){
				jQuery("[data-js-attr='set-options']").show();
				jQuery("[data-js-attr='plain-options']").hide();
			}

			jQuery(document).ready(function(){
				/*If attempts done against a quiz do not let add or delete questions or change total and passing marks*/
				if (maxattempt > 0)
				{
					jQuery("#questions #toolbar a").addClass("disabled").css("pointer-events", "none");
					jQuery("#questions [data-js-id='section-create-action']").addClass("disabled").css("pointer-events", "none");
				}

				if (livetrackReviews > 0)
				{
					jQuery("#testFormAssessment .subform-repeatable").addClass("disabled").css("pointer-events", "none");
					jQuery("[data-js-id='disable-if-reviewed']").addClass("disabled").css("pointer-events", "none");
				}

				jQuery('[data-js-id="test-section"]', window.parent.document).each(function(){

					var sectionQuestionCnt = jQuery(this).find("[data-js-id='section-header'] [data-js-id='questions']").text();

					if (parseInt(sectionQuestionCnt) > 0 && maxattempt > 0)
					{
						console.log(maxattempt)
						// jQuery(this).find("[data-js-id='delete-question']").addClass("disabled").css("pointer-events", "none");

						jQuery(this).find("[data-js-id='delete-section']").addClass("disabled").css("pointer-events", "none");

						jQuery(this).find("[data-js-id='change-section-state']").addClass("disabled").css("pointer-events", "none");
					}
				});

			})

			jQuery(window).on("load", function (){

				if (jQuery("[name$='[show_all_questions]']:checked").val() === '1'){
					jQuery("[name$='[pagination_limit]']").val(1);
				}

				jQuery("[name$='[show_all_questions]']").click(function(){
					if (jQuery("[name$='[show_all_questions]']:checked").val() == '1')
					{
						jQuery("[name$='[pagination_limit]']").val(1);
					}
					else
					{
						jQuery("[name$='[pagination_limit]']").val(5);
					}
				})

				jQuery("[name='jform[type]']").click(function(){
					if (jQuery("[name='jform[type]']:checked").val() == 'set')
					{
						jQuery("[data-js-attr='set-options']").show();
						jQuery("[data-js-attr='plain-options']").hide();
					}
					else
					{
						jQuery("[data-js-attr='set-options']").hide();
						jQuery("[data-js-attr='plain-options']").show();
					}

					tmt.test.updateTest(jQuery("[data-js-id='id']", jQuery("#testFormquestions")).val());
				});

				jQuery(document).on("click", "[data-js-id='compulsory-question']", function(){
					var section = jQuery(this).closest("[data-js-id='test-section']").attr('data-js-unique');
					var temp = section.split('_');
					var testId = temp[0];
					var sectionId = temp[1];
					var questionId = jQuery(this).closest("[data-js-id='section-question']").attr('data-js-itemid');
					var compulsory = jQuery(this).is(':checked') ? 1 : 0;
					var res = tmt.section.changeCompulsoryState(testId, sectionId, questionId, compulsory);
				});

				jQuery(document).on("click", "[data-js-id='delete-question']", function(){

					var section = jQuery(this).closest("[data-js-id='test-section']").attr('data-js-unique');
					var temp = section.split('_');
					var testId = temp[0];
					var sectionId = temp[1];
					var questionId = jQuery(this).closest("[data-js-id='section-question']").attr('data-js-itemid');
					var res = tmt.section.deleteTestQuestion(testId, sectionId, questionId);

					if (res)
					{
						jQuery(this).closest('[data-js-id="section-question"]').remove();
						/*tmt.quiz.updateSection(sectionId, testId);*/
						tmt.test.updateTest(testId);
					}
				});

				jQuery(document).on("click", "[data-js-id='test-section'] .test-section__header-edit-action", function(event){
					event.stopPropagation();
				});

				jQuery(document).on("click", '[data-js-attr="action-edit-title"]',  function(event){
					event.stopPropagation();
					jQuery(this).closest('[data-js-id="test-section"]').find("[data-js-id='section-edit-form']").show();
					jQuery(this).hide();
				});

				jQuery(document).on("click", '[data-js-id="action-save-section"]', function(event){
					var sectionId = jQuery(this).closest("[data-js-id='testFormsection']").attr("data-js-itemid");
					tmt.section.save(sectionId);
				});

				jQuery(document).on("click", '[data-js-id="action-cancelsave-section"]', function(event){
					var sectionId = jQuery(this).closest("[data-js-id='testFormsection']").attr("data-js-itemid");

					if (!sectionId)
					{
						jQuery('[data-js-id="section-create-form"]').find('#testFormsection #section_title').removeClass("required");
						jQuery('[data-js-id="section-create-form"]').find('#testFormsection #section_title').val("");
						jQuery('[data-js-id="section-create-form"]').find('#testFormsection #section_description').val("");
					}

					tmt.section.toggleSave(sectionId);
				});

				jQuery(document).on("click", "[data-js-id='test-section'] [data-js-id='delete-section']", function(){
					var section = jQuery(this).closest("[data-js-id='test-section']").attr('data-js-unique');
					var temp = section.split('_');
					var testId = temp[0];
					var sectionId = temp[1];
					var res = tmt.section.deleteSection(sectionId);

					if (res)
					{
						jQuery(this).closest("[data-js-id='test-section']").remove();
						tmt.test.updateTest(testId);
					}
				});

				jQuery(document).on("click", "[data-js-id='test-section'] [data-js-id='change-section-state']", function(){
					var section = jQuery(this).closest("[data-js-id='test-section']").attr('data-js-unique');
					var temp = section.split('_');
					var testId = temp[0];
					var sectionId = temp[1];

					var currentState = jQuery(this).closest("[data-js-id='test-section']").find('[data-js-id="section-state"]').val();

					var targetState = (parseInt(currentState) === 1) ? 0 : 1;
					var res = tmt.section.changeState(sectionId, targetState);

					if (res)
					{
						tmt.test.updateTest(testId);
					}
				});

				jQuery(document).on("click", "[data-js-id='test-section'] [data-js-id='fetch-set-rule-Questions']", function(){
					var unique = jQuery(this).closest("[data-js-id='test-section']").attr('data-js-unique');
					var gradingType = jQuery("[data-js-id='gradingtype']", jQuery("#testFormquestions")).val();
					tmt.test.fetchSetRuleQuestions(gradingType, unique);
				});

				jQuery("#jform_time_finished_duration").change(function(){
					if( jQuery('#jform_time_duration').val() !== '' && jQuery('#jform_time_finished_duration').val() !=='' )
					{
						var atd = (jQuery('#jform_time_duration').val() - jQuery('#jform_time_finished_duration').val());

						jQuery('#time_finished_duration_minute').html('');

						if(! isNaN(atd) && (atd > 0) )
						{
							jQuery('#time_finished_duration_minute').html('<span class="center"><em>' + Joomla.JText._('COM_TMT_TEST_FORM_TIME_FINISHED_ALERT_MSG_1') + ' ' + atd + Joomla.JText._('COM_TMT_TEST_FORM_TIME_FINISHED_ALERT_MSG_2') +'</em></span>');
						}
					}
				});


				jQuery("[data-js-id='test-set-refresh']").click(function(){

					var testId = jQuery("[data-js-id='id']").val();
					/*Remove all the section questions as we need to generate new set on each time Fetch is clicked*/
					var saveurl= "index.php?option=com_tmt&task=test.deleteTestRulesQuestions&format=json";
					var formData = {'testId' : testId};
					var promise = tjService.postData(saveurl,formData);

					window.doProcess =  false;
					promise.fail(
						function(response) {
							var messages = { "error": [response]};
							Joomla.renderMessages(messages);
							window.doProcess =  false;
						}
					).done(function(response) {
						if (!response.success && response.message){
							var messages = { "error": [response.message]};
							Joomla.renderMessages(messages);
							window.doProcess =  false;
						}

						if (response.success)
						{
							doProcess = true;
						}
					});

					if (doProcess)
					{
						jQuery("[data-js-id='test-sections'] [data-js-id='test-section']").each(function(){
							var unique = jQuery(this).attr("data-js-unique");
							var parentSection = jQuery("[data-js-unique='" + unique +"']", window.parent.document)
							jQuery("[data-js-id='questions_container'] [data-js-id='section-question']", parentSection).remove();
							tmt.test.fetchRuleQuestions("quiz", unique, parentSection, 1);
						});

						tmt.test.updateTest(testId);
					}
				});

				tmt.test.findDuplicatesQuestions();

				if (assessment == 1)
				{
					tjlmsAdmin.assessmentform.$form = jQuery("#testFormAssessment");
					tjlmsAdmin.assessmentform.init();
				}

				tmt.test.sort();
			});

			Joomla.submitbutton = function(task)
			{
				if(task != "quiz.cancel")
				{
					var tabscount  = jQuery("#testformTabs li").length;
					var result = tmt.stepform.validateTabs(tabscount);

					if (!result){
						return false;
					}

					jQuery(".com_tmt_button").attr("disabled", true);

					if (cid && mid)
					{
						let testId = jQuery("#testFormdetails [data-js-id='id']").val();
						let lessonId = jQuery("#testFormdetails [data-js-id='lesson_id']").val();

						var saveurl= "index.php?option=com_tmt&task=test.addTestMedia&format=json";

						let formData = {};

						formData["no_of_attempts"] = jQuery("#testFormdetails [name='jform[no_of_attempts]']").val();
						formData["attempts_grade"] = jQuery("#testFormdetails [name='jform[attempts_grade]']").val();
						formData["consider_marks"] = jQuery("#testFormdetails [name='jform[consider_marks]']:checked").val();
						formData["resume"] = jQuery("#testFormdetails [name='jform[resume]']:checked").val();
						formData["eligibility_criteria"] = jQuery("#testFormdetails [name='jform[eligibility_criteria][]']").val();
						formData["in_lib"] = jQuery("#testFormdetails [name='jform[in_lib]']:checked").val();
						formData["resume"] = jQuery("#testFormdetails [name='jform[resume]']:checked").val();
						formData['catid'] = jQuery("#testFormdetails [name='jform[catid]']").val();
						formData["courseId"] = cid;
						formData["modId"] = mid;
						formData["testId"] = testId;
						formData["lessonId"] = lessonId;
						formData["gradingtype"] = gradingtype;

						var promise = tjService.postData(saveurl,formData);

						var doProcess =  false;
						promise.fail(
							function(response) {
								console.log(response);
								var messages = { "error": [response.responseText]};
								Joomla.renderMessages(messages);
								doProcess =  false;
							}
						).done(function(response) {
							if (!response.success && response.message){
								var messages = { "error": [response.message]};
								Joomla.renderMessages(messages);
								doProcess =  false;
							}

							if (response.success)
							{
								doProcess =  true;
							}
						});

						if (!doProcess)
						{
							return false;
						}
					}
				}

				if(ifintmpl && task == "question.cancel")
				{
					parent.SqueezeBox.close();
				}
				else if(cid)
				{
					window.location = "index.php?option=com_tjlms&view=modules&course_id=" + cid;
				}
				else
				{
					window.location = "index.php?option=com_tjlms&view=lessons";
				}
			}
		},
		addQuestionToSection: function (testId, sectionId, questionId) {
			if (!testId || !sectionId || !questionId)
			{
				return false;
			}
			var saveurl= "index.php?option=com_tmt&task=test.addQuestionToSection&format=json";
			var formData = {'test_id' : testId,'section_id' :sectionId, 'question_id' : questionId};
			var promise = tjService.postData(saveurl,formData);

			var doProcess =  false;

			promise.fail(
				function(response) {
					var messages = { "error": [response]};
					Joomla.renderMessages(messages);
					doProcess =  false;
				}
			).done(function(response) {
					if (!response.success && response.message){
							var messages = { "error": [response.message]};
							Joomla.renderMessages(messages);
							doProcess =  false;
						}
						if (response.messages){
							Joomla.renderMessages(response.messages);
						}

					doProcess = true;

					var url = "index.php?option=com_tmt&task=question.getQuestionRowHtml&format=json";
					var promise = tjService.postData(url, {'question_id' : response.data.question_id, 'test_id' : testId});
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
							var unique = testId + "_" + sectionId;

							if (jQuery(".section_question[data-js-itemid='"+ questionId + "']", parent.document).length > 0)
							{
								jQuery(".section_question[data-js-itemid='"+ questionId + "']", parent.document).replaceWith(questionres.data);
							}
							else
							{
								jQuery("[data-js-unique='"+unique+"'] [data-js-id='questions_container']", parent.document).append(questionres.data);
							}


							tmt.test.findDuplicatesQuestions();
							/*tmt.quiz.updateSection(sectionId, testId);*/
							tmt.test.updateTest(testId);
						}
					});
			});

			return doProcess;
		},
		updateTest: function(testId){
			/*Get total marks according to published sections and their questions*/
			var testMarks = 0;
			jQuery('[data-js-id="test-section"]', window.parent.document).each(function(){

				var section = jQuery(this);
				var sectionMarks = 0;
				var qcnt = 0;

				if (jQuery("[name='jform[type]']",  window.parent.document).length == 0 || jQuery("[name='jform[type]']:checked",  window.parent.document).val() == 'plain')
				{
					jQuery(section, window.parent.document).find("[data-js-id='questions_container'] [data-js-id='section-question']").each(function(){
							var question = jQuery(this);

							sectionMarks += parseInt(jQuery(question).find("[data-js-id='marks']").text());

							qcnt ++;
					});
				}
				else
				{
					jQuery(section, window.parent.document).find("[data-js-id='test-rule']:not('.rule-template--danger')").each(function(){
						temp =  jQuery(this).find(".questions_count").val() * jQuery(this).find(".questions_marks").val();
						sectionMarks += parseInt(temp||0);
						qcnt += parseInt(jQuery(this).find(".questions_count").val() || 0);
					});
				}

				jQuery(section).find("[data-js-id='section-header'] [data-js-id='marks']").text(sectionMarks);
				jQuery(section).find("[data-js-id='section-header'] [data-js-id='questions']").text(qcnt);
			});

			/*Remove all the section questions as we need to generate new set on each time Fetch is clicked*/
			var saveurl= "index.php?option=com_tmt&task=test.setTestMarksbyQuestions&format=json";
			var formData = {'testId' : testId, 'testType': jQuery("[name='jform[type]']:checked").val()};
			var promise = tjService.postData(saveurl,formData);

			window.doProcess =  false;

			promise.fail(
				function(response) {
					var messages = { "error": [response]};
					Joomla.renderMessages(messages);
					window.doProcess =  false;
				}
			).done(function(response) {
				if (!response.success && response.message){
					var messages = { "error": [response.message]};
					Joomla.renderMessages(messages);
					window.doProcess =  false;
				}

				if (response.success)
				{
					jQuery("[name='jform[total_marks]']", window.parent.document).val(response.data);
				}
			});
		},
		validateBasic: function() {
			let form = jQuery("#testFormdetails");
			var attemptField = jQuery(form).find("[name$='[no_of_attempts]']");
			var newNoOfAttempts = jQuery(form).find("[name$='[no_of_attempts]']").val();
			var oldNoOfAttempts = jQuery(form).find('#no_attempts').val();
			var maxAttemptsDone = jQuery(form).find('#max_attempt').val();

			if (isNaN(newNoOfAttempts)){
				jQuery(attemptField).val(0);
			}

			// Check if attempt is less than original attempt
			if (newNoOfAttempts != 0 && newNoOfAttempts < maxAttemptsDone)
			{
				var msg = Joomla.JText._('COM_TJLMS_MAX_ATTEMPT_VALIDATION_MSG').replace("%s", maxAttemptsDone);
				Joomla.renderMessages({"error":[msg]});
				jQuery(attemptField).val(oldNoOfAttempts).focus();
				return false;
			}

			var start_date = jQuery(form).find('#jform_start_date').val();
			var end_date = jQuery(form).find('#jform_end_date').val();

			// Validate time_finished_duration < time_duration
			if (start_date != '' && end_date != '' )
			{
				if (start_date > end_date)
				{
					Joomla.renderMessages({"error":[Joomla.JText._('COM_TMT_DATE_ISSUE')]});
					return false;
				}
			}

			if (end_date != '')
			{
				var today = new Date();
				today.setHours(0, 0, 0, 0);
				var quizEndDate = new Date(end_date);
				quizEndDate.setHours(0, 0, 0, 0);

				if(quizEndDate < today)
				{
					Joomla.renderMessages({"error":[Joomla.JText._('COM_TMT_END_DATE_CANTBE_GRT_TODAY')]});
					return false;
				}
			}

			return true;
		},
		validateTime: function(){
			let form = jQuery("#testFormtime");
			let durationTime = jQuery("#jform_time_duration", form).val();
			let alertBeforeTime = jQuery("#jform_time_finished_duration", form).val();
			let showtimefinishalert = jQuery("[name$='[show_time_finished]']:checked").val();

			var error = 0;

			if (durationTime !== '' && showtimefinishalert == 1 && alertBeforeTime !== '')
			{
				var atd = durationTime - alertBeforeTime;

				if (!isNaN(atd) && (atd <= 0))
				{
					error = 1;
				}
			}

			if (error == 1)
			{
				var msg = Joomla.JText._('COM_TMT_TEST_MSG_TIME_FINISHED_INVALID');
				Joomla.renderMessages({"error":[msg]});
				return false;
			}

			return true;
		},
		validateQuestionsMarks: function (){
			let form = jQuery("#testFormquestions");
			let testId = jQuery("[data-js-id='id']", form).val();
			let gradingtype = jQuery("[data-js-id='gradingtype']", form).val();

			/*check if questions added or not*/
			let qc = jQuery("[data-js-id='test-sections'] [data-js-id='section-question']").length;

			if (qc <= 0)
			{
				Joomla.renderMessages({"error":[Joomla.JText._('COM_TMT_TEST_MSG_NO_QUESTIONS')]});
				return false;
			}

			if (gradingtype == 'quiz')
			{
				tmt.test.updateTest(testId);

				let total_marks = parseInt(jQuery("#jform_total_marks", form).val());
				let passing_marks = parseInt(jQuery("#jform_passing_marks", form).val());

				if ((isNaN(total_marks) || total_marks <= 0))
				{
					Joomla.renderMessages({"error":[Joomla.JText._('COM_TMT_TEST_ASSESSMENT_TOTAL_MARKS_NONZERO')]});
					return false;
				}
			}

			return true;
		},
		findDuplicatesQuestions: function(){
		  var hasDuplicates = false;

			/*jQuery("#questions .section_question input[type='checkbox']", window.parent.document).each(function () {
				var $inputsWithSameValue = jQuery('#questions .section_question input[value=' + jQuery(this).val() + ']',  window.parent.document);
				var inputDuplicates = ($inputsWithSameValue.length > 1);

				if (inputDuplicates) {
					if (!hasDuplicates){
						hasDuplicates = true;
					}

					jQuery($inputsWithSameValue).closest('.section_question').addClass('alert-danger');
				}

				return hasDuplicates;
			});*/
		},
		sort : function(){
			jQuery("[data-js-id='test-sections']").sortable({
				handle:'.sectionSortingHandler',
				connectWith: "[data-js-id='test-sections']",
				scroll: false,
				collapsible: true,
				start: function() {
					console.log("sorting");
				},
				update: function() {
					var section = jQuery("[data-js-id='test-sections']").find('[data-js-id="test-section"]');
					var sectionUnique = jQuery(section).attr('data-js-unique');
					var temp = sectionUnique.split('_');
					var testId = temp[0];
					var sections = [];

					var j= 0;
					jQuery("[data-js-id='test-sections']").find('[data-js-id="test-section"]').each(function(j){
						sections[j] = jQuery(this).attr("data-js-itemid");
						j++;
					});

					var formData = {'testId': testId, 'sections' : sections};
					var url= "index.php?option=com_tmt&task=test.sortSections&format=json";
					var promise = tjService.postData(url,formData);

					promise.fail(
						function(response) {
							var messages = { "error": [response]};
							Joomla.renderMessages(messages);
						}
					).done(function(response) {
						if (!response.success && response.message){
							var messages = { "error": [response.message]};
							Joomla.renderMessages(messages);
						}
					});
				}


			});
			jQuery("[data-js-id='questions_container']").sortable({
					handle:'.sortable-handler',
					connectWith: "[data-js-id='questions_container']",
						scroll: false,
					collapsible: true,
					start: function() {
						console.log("sorting");
					},
					update: function() {
						var section = jQuery(this).closest("[data-js-id='test-section']");
						var sectionUnique = jQuery(section).attr('data-js-unique');
						var temp = sectionUnique.split('_');
						var testId = temp[0];
						var sectionId = temp[1];
						var lessons = [];

						var j= 0;
						jQuery(section).find('[data-js-id="section-question"]').each(function(j){
							lessons[j] = jQuery(this).attr("data-js-itemid");
							j++;
						});

						var formData = {'testId': testId, 'sectionId' : sectionId, 'lessons': lessons};
						var url= "index.php?option=com_tmt&task=test.sortSectionQuestions&format=json";
						var promise = tjService.postData(url,formData);

						promise.fail(
							function(response) {
								var messages = { "error": [response]};
								Joomla.renderMessages(messages);
							}
						).done(function(response) {
							if (!response.success && response.message){
								var messages = { "error": [response.message]};
								Joomla.renderMessages(messages);
							}
							tmt.test.updateTest(testId);
						});
					}
				});
		},
		cloneRule: function(element, forDynamic){
			var lastRule = jQuery(element).closest("[data-js-id='test-rule']");
			var lastInd = jQuery(lastRule).attr('id').replace('test-rule-', '');
			var num = parseInt(lastInd) + 1;
			var newElem = jQuery(lastRule).clone().attr('id', "test-rule-"+num);

			jQuery(newElem).children().find("input, select").each(function()
			{
				var kid=jQuery(this);
				if(kid.attr('id')!=undefined)
				{
					var idOrig=kid.attr('id'); /* e.g. id-> answers_marks */
					kid.attr('id',idOrig+num).attr('id',idOrig+num); /* e.g. id-> answers_marks2 */
					kid.val('');
				}
			});
			jQuery(lastRule).find('[data-js-id="test-add-rule"]').hide();
			jQuery(lastRule).find('[data-js-id="test-remove-rule"]').show();
			jQuery(newElem).find('[data-js-id="test-remove-rule"]').hide();
			jQuery(newElem).find('[data-js-id="test-add-rule"]').show();
			jQuery(newElem).find('[data-js-id="rule-add-question"]').addClass('d-none');
			jQuery(newElem).find('[data-js-id="rule-question-available"]').removeClass('rule-question-available').text("").show();

			if (forDynamic == 1)
			{
				var section = jQuery(element).closest("[data-js-id='test-section']");
				jQuery('[data-js-id="test-rules-block"]', section).append(newElem);
			}
			else
			{
				jQuery('[data-js-id="test-rules-block"]').append(newElem);
			}

		},
		removeRule: function(element){
			jQuery(element).closest('[data-js-id="test-rule"]').remove();
		},
		removeRuleQuestion: function(element) {
			jQuery(element).closest('[data-js-id="section-question"]').remove();
			var queLength = jQuery("[data-js-id='questions_container'] [data-js-id='section-question']").length;

			if (queLength === 0)
			{
				jQuery('.add-rules').addClass('disabled');
			}
		},
		addRuleQuestionstoSections: function(unique) {
			var arr = unique.split("_");
			var test_id = arr[0];
			var section_id = arr[1];

			var checkedQues = jQuery("input[id*='cb']:checked:not('#cb0')").length;

			var c = 1;
			jQuery("input[id*='cb']:checked").each(function() {
				var question = tmt.test.addQuestionToSection(test_id, section_id, jQuery(this).val())

				if (question)
				{
					if (c === checkedQues)
					{
						window.parent.Joomla.Modal.getCurrent().close();
					}
					c++;
				}
			});
		},
		removeSetRule: function(element){
			var ruleElement = jQuery(element).closest('[data-js-id="test-rule"]');
			var ruleId = jQuery("[name='rule_id[]']", ruleElement).val();

			/*Remove all the section questions as we need to generate new set on each time Fetch is clicked*/
			var saveurl= "index.php?option=com_tmt&task=test.deleteTestRule&format=json";
			var formData = {'ruleId':ruleId};
			var promise = tjService.postData(saveurl,formData);

			promise.fail(
				function(response) {
					var messages = { "error": [response]};
					Joomla.renderMessages(messages);
					window.doProcess =  false;
				}
			).done(function(response) {
				if (!response.success && response.message){
					var messages = { "error": [response.message]};
					Joomla.renderMessages(messages);
					window.doProcess =  false;
				}

				if (response.message){
					var messages = { "success": [response.message]};
					Joomla.renderMessages(messages);
				}

				if (response.success)
				{
					jQuery(ruleElement).remove();
				}
			});
		},
		fetchSetRuleQuestions: function (gradingType, unique){
			var arr = unique.split("_");
			var testId = arr[0];
			var sectionId = arr[1];

			/*Remove all the section questions as we need to generate new set on each time Fetch is clicked*/
			var saveurl= "index.php?option=com_tmt&task=test.deleteSectionRulesQuestions&format=json";
			var formData = {'testId' : testId, 'sectionId' :sectionId};
			var promise = tjService.postData(saveurl,formData);

			window.doProcess =  false;

			promise.fail(
				function(response) {
					var messages = { "error": [response]};
					Joomla.renderMessages(messages);
					window.doProcess =  false;
				}
			).done(function(response) {
				if (!response.success && response.message){
					var messages = { "error": [response.message]};
					Joomla.renderMessages(messages);
					window.doProcess =  false;
				}

				if (response.success)
				{
					doProcess = true;
				}
			});

			if(doProcess)
			{
				var parentSection = jQuery("[data-js-unique='" + unique +"']", window.parent.document)
				jQuery("[data-js-id='questions_container'] [data-js-id='section-question']", parentSection).remove();
				tmt.test.fetchRuleQuestions(gradingType, unique, parentSection, 1);
				tmt.test.updateTest(testId);
			}
		},
		fetchRuleQuestions: function (gradingType, unique, opener, forDynamic = 0){
			var arr = unique.split("_");
			var testId = arr[0];
			var sectionId = arr[1];

			jQuery("[data-js-id='test-rule-questions'] [data-js-id='section-question']:not('#section_question_0')").remove();
			jQuery("[data-js-id='test-rule-questions']").hide();

			jQuery("[data-js-attr='test-rules'] [data-js-id='test-rule']", opener).each(function(){

				var ruleElement = jQuery(this);

				if (forDynamic == 0)
				{
					var pullCount = jQuery(ruleElement).find('.pull_questions_count').val();
					jQuery(ruleElement).find('.questions_count').val(pullCount);
				}

				if (!tmt.test.validateRule(jQuery(this), gradingType, forDynamic))
				{
					Joomla.renderMessages({"error":[Joomla.JText._('COM_TMT_TEST_MSG_INVALID_RULE')]});

					return;
				}

				var question_count = jQuery(ruleElement).find('.questions_count').val();
				var params = [];
				//params.push({name : 'id',value:jQuery("[name='rule_id[]']", jQuery(this)).val()});
				params.push({name : 'id',value:""});
				params.push({name : 'questions_count',value:jQuery("[name='questions_count[]']", jQuery(this)).val()});
				params.push({name : 'marks',value:jQuery("[name='questions_marks[]']", jQuery(this)).val()});
				params.push({name : 'pull_questions_count',value:jQuery("[name='pull_questions_count[]']", jQuery(this)).val()});
				params.push({name : 'category',value:jQuery("[name='questions_category[]']", jQuery(this)).val()});
				params.push({name : 'difficulty_level',value:jQuery("[name='questions_level[]']", jQuery(this)).val()});
				params.push({name : 'question_type',value:jQuery("[name='questions_type[]']", jQuery(this)).val()});
				params.push({name : 'gradingType',value:gradingType});
				params.push({name : 'testId', value: testId});
				params.push({name : 'sectionId', value: sectionId});
				params.push({name : 'forDynamic', value: forDynamic});

				/*Questions already fetched other rules*/
				questionsArray = jQuery('input[name="cid[]"]').serializeArray();
				jQuery.merge(params,questionsArray);

				var saveurl = "index.php?option=com_tmt&task=test.fetchQuestionsforRules&format=json";
				var promise = tjService.postData(saveurl,params);

				/* Response given
					que_available = question count matching to the rule (rule question_count * multiplication factor)
					que_remaining = question count remaining to make a pool
					questions = actual questions array
				*/
				var doProcess = false;
				promise.fail(
					function(response) {
						var messages = { "error": [response]};
						//Joomla.renderMessages(messages);
					}
				).done(function(response) {
					if (!response.success && response.message){
						var messages = { "error": [response.message]};
					}
					if (response.messages){
						Joomla.renderMessages(response.messages);
					}

					if (response.success)
					{
						/* If the questions fetched are greater than 0*/

						if (response.data.que_available > 0 && response.data.que_available >= question_count)
						{
							jQuery("[data-js-id='test-rule-questions']", opener).show();

							jQuery.each(response.data.questions, function (i, q)
							{
								var newElem = jQuery("[data-js-id='test-rule-questions'] [data-js-id='section-question']", opener).filter(":last").clone().attr('data-js-itemid', q.id).attr('id', "section_question_" + q.id).removeClass('hide');

								var lastQ = jQuery("[data-js-id='test-rule-questions'] [data-js-id='section-question']:last", opener);

								var num = jQuery("[data-js-id='test-rule-questions'] [data-js-id='section-question']", opener).length + 1;

								jQuery(newElem).find("input[type='checkbox']").attr('id', 'cb' + num).val(q.id);
								/*jQuery(newElem).find("[data-js-id='section-header']").attr('data-target', '#collapse_' + sectionData.id);*/

								jQuery(newElem).find("[data-js-id='section-question-title']").text(q.title);
								jQuery(newElem).find("[data-js-id='section-question-marks']").text(q.marks);
								jQuery(newElem).find("[data-js-id='section-question-category']").text(q.category);
								jQuery(newElem).find("[data-js-id='section-question-type']").text(q.type);
								jQuery(newElem).find("[data-js-id='section-question-level']").text(q.level);
								jQuery("[data-js-id='questions_container']", opener).append(newElem);
								//jQuery(newElem).insertBefore(jQuery("[data-js-id='test-rule-questions'] .form-actions", opener));

							});

							var queLength = jQuery("[data-js-id='questions_container'] [data-js-id='section-question']").length;

							if (queLength)
							{
								jQuery('.add-rules').removeClass('disabled');
							}
						}

						var temp = response.data.que_available;

						jQuery('[data-js-id="rule-question-available"]',jQuery(ruleElement)).addClass('rule-question-available').text(temp).show();

						jQuery(ruleElement).removeClass('rule-template--perfect').removeClass('rule-template--warning').removeClass('rule-template--danger');

						jQuery('[data-js-id="rule-add-question"]').removeClass('d-none');

						/* If the questions got i.e. response.que_available are exact to make pull*/
						if(response.data.que_remaining == 0)
						{
							jQuery(ruleElement).addClass('rule-template--perfect');
							jQuery('[data-js-id="rule-add-question"]').addClass('d-none');
						}
						else if(response.data.que_remaining != 0)
						{
							/*If the questions got i.e. response.que_available are less than the questions demanded show pick / add button against the rule*/
							jQuery(".addButtons",ruleElement).show();

							if (response.data.que_available < question_count)
							{
								/* If the questions fetched are not enough to make single quiz only*/
								jQuery(ruleElement).addClass('rule-template--danger');
							}
							else if (response.data.que_available >= question_count)
							{
								/* This means the questions are not sufficient to make the pool but is sufficient to make a Quiz */
								jQuery(ruleElement).addClass('rule-template--warning');
							}
						}
					}
				});
			});
		},
		validateRule: function (ruleElement, gradingType, forDynamic)
		{
			jQuery(ruleElement).removeClass('rule-template--perfect').removeClass('rule-template--danger').removeClass('rule-template--warning');

			var questionCount = jQuery(ruleElement).find('.questions_count').val();
			var pullCount = jQuery(ruleElement).find('.pull_questions_count').val();
			var questionsMarks = jQuery(ruleElement).find('.questions_marks').val();

			if(!parseInt(pullCount) || parseInt(pullCount) < parseInt(questionCount) || (gradingType=='quiz' && !parseInt(questionsMarks)) || (pullCount % 1 !== 0) || (gradingType=='quiz' && questionCount % 1 !== 0) || (gradingType=='quiz' && questionsMarks % 1 !== 0))
			{
				jQuery(ruleElement).addClass('rule-template--danger');
				return false;
			}

			return true;

		}
	},
	section: {
		toggleSave: function(sectionId) {

			if (!sectionId)
			{
				jQuery("[data-js-id='section-create-action']").toggle();
				jQuery("[data-js-id='section-create-form']").toggle();
				jQuery("[data-js-id='section-create-form']").removeClass('d-none');
			}
			else
			{
				jQuery('#test_section_' + sectionId).find("[data-js-id='section-edit-form']").hide();
				jQuery('#test_section_' + sectionId).find('[data-js-attr="action-edit-title"]').show();
			}

			return false;
		},
		deleteSection: function (sectionId) {
			var comfirmDelete = confirm(Joomla.JText._('COM_TMT_TEST_CONFIRM_SECTION_DELETE'));
			var doProcess = false;

			if(comfirmDelete)
			{
				var saveurl= "index.php?option=com_tmt&task=test.deleteSection&format=json";
				var formData = {'id' : sectionId};
				var promise = tjService.postData(saveurl,formData);
				promise.fail(
					function(response) {
						var messages = { "error": [response]};
						Joomla.renderMessages(messages);
					}
				).done(function(response) {

					if (response.messages){
						Joomla.renderMessages(response.messages);
					}

					if (!response.success && response.message){
						var messages = { "error": [response.message]};
					}

					if (response.success)
					{
						doProcess = true;
						var messages = { "success": [response.message]};
						jQuery("#test_section_" + sectionId).remove();
					}

					Joomla.renderMessages(messages);
				});
			}

			return doProcess;
		},
		changeState : function (sectionId, state){
			var saveurl= "index.php?option=com_tmt&task=test.changeSectionState&format=json";
			var formData = {'id' : sectionId, 'state': state};
			var promise = tjService.postData(saveurl,formData);

			var doProcess = false;
			sectionObj = [];
			promise.fail(
				function(response) {
					var messages = { "error": [response]};
					Joomla.renderMessages(messages);
				}
			).done(function(response) {
				if (!response.success && response.message){
					var messages = { "error": [response.message]};
				}
				if (response.messages){
					Joomla.renderMessages(response.messages);
				}

				if (response.success)
				{
					doProcess = true;
					var messages = { "success": [response.message]};
					var iconClass = (state) ? "publish" : "unpublish";
					var sectionClass = (state) ? "published" : "unpublished";
					var targetState = (response.data.state == 1) ? 0 : 1;
					var tooltip = (state) ? Joomla.JText._("COM_TMT_TEST_UNPUBLISH_SECTION") : Joomla.JText._("COM_TMT_TEST_PUBLISH_SECTION");

					jQuery('#test_section_' + sectionId).removeClass('published').removeClass('unpublished').addClass(sectionClass);
					jQuery('#test_section_' + sectionId + ' [data-js-id="section-state-icon"]').removeClass('icon-publish').removeClass('icon-unpublish').addClass('icon-' + iconClass);
					jQuery('#test_section_' + sectionId + ' [data-js-id="section-state"]').val(state);
					jQuery('#test_section_' + sectionId + ' [data-js-id="change-section-state"]').attr('title',tooltip);
				}

				Joomla.renderMessages(messages);
			});

			return doProcess;
		},
		getSection: function(sectionId) {
			var saveurl= "index.php?option=com_tmt&task=test.getSection&format=json";
			var formData = {'id' : sectionId};
			var promise = tjService.postData(saveurl,formData);

			var doProcess = false;
			sectionObj = [];
			promise.fail(
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
				if (response.messages){
					Joomla.renderMessages(response.messages);
				}

				if (response.success)
				{
					doProcess = true;
					sectionObj = response.data;
				}
			});

			if (doProcess)
			{
				return sectionObj;
			}

			return doProcess;
		},
		save: function(sectionId) {

			if (!sectionId)
			{
				jQuery('[data-js-id="section-create-form"]').find('#testFormsection #section_title').addClass("required");
				tmt.eachform.$form = jQuery('[data-js-id="section-create-form"]').find('#testFormsection');
			}
			else
			{
				tmt.eachform.$form = jQuery('#testFormsection' + sectionId);
			}

			tmt.eachform.extraValidations = '';
			tmt.eachform.saveurl= "index.php?option=com_tmt&task=test.saveSection&format=json";
			formprocessdone = tmt.eachform.ajaxsave();

			if (formprocessdone)
			{
				if (!sectionId)
				{
					this.clone(formprocessdone);
					jQuery('[data-js-id="section-create-form"]').find('#testFormsection #section_title').val('').removeClass("required");
					this.toggleSave();
				}
				else
				{
					jQuery('#test_section_' + sectionId).find("[data-js-id='section-edit-form']").hide();
					jQuery('#test_section_' + sectionId).find("[data-js-id='section-title']").text(formprocessdone.title);
					jQuery('#test_section_' + sectionId).find('[data-js-attr="action-edit-title"]').show();
					this.toggleSave(sectionId);
				}
			}
		},
		clone: function(sectionData) {
				var quiz_id = jQuery("[data-js-id='id']", jQuery("#testFormquestions")).val();
				var newElem = jQuery( ".test_section.forclone").clone().attr('id', 'test_section_' + sectionData.id).attr('data-js-unique', quiz_id + "_" + sectionData.id).attr('data-js-itemid', sectionData.id).removeClass('d-none').removeClass('unpublished').removeClass('published').removeClass('forclone');

				var sectionClass = (sectionData.state == 1) ? "published" : "unpublished";
				var iconClass = (sectionData.state == 1) ? 'icon-publish' : 'icon-unpublish';

				newElem.addClass(sectionClass);
				jQuery(newElem).find("[data-js-id='section-state-icon']").removeClass('icon-publish').removeClass('icon-unpublish').addClass(iconClass)
				jQuery(newElem).find("[data-js-id='section-state']").val(sectionData.state);
				jQuery(newElem).find("[data-js-id='panel-collapse']").attr('id', 'collapse' + sectionData.id);
				jQuery(newElem).find("[data-js-id='section-header'] [data-bs-toggle='collapse']").attr('data-bs-target', '#collapse' + sectionData.id);

				jQuery(newElem).find("[data-js-id='section-title']").text(sectionData.title);
				jQuery(newElem).find("[data-js-id='questions']").text(sectionData.qcnt);
				jQuery(newElem).find("[data-js-id='marks']").text(sectionData.marks);

				/* Section edit form variables*/
				jQuery(newElem).find("[data-js-id='testFormsection']").attr("id", "testFormsection" + sectionData.id).attr("data-js-itemid", sectionData.id);
				jQuery(newElem).find("[data-js-id='testFormsection'] [name='section[id]']").val(sectionData.id).attr("id", "section_title" + sectionData.id);
				jQuery(newElem).find("[data-js-id='testFormsection'] [data-js-attr='section-title'] label").attr("for", "section_title" + sectionData.id);
				jQuery(newElem).find("[data-js-id='testFormsection'] [name='section[title]']").val(sectionData.title);

				jQuery("[data-js-id='test-sections']").append(newElem);
				tmt.test.sort();
				//this.toggleCollapse(jQuery("[data-target='#collapse_"+ sectionData.id+"']"));
		},
		/*toggleCollapse: function (handler){
			var panel_head = jQuery(handler);//.closest("[data-js-attr='section-header']");
			jQuery(panel_head).toggleClass('collapsed');
			var target = jQuery(handler).attr('data-target');

			jQuery(target).toggleClass('in');

			if (jQuery(panel_head).hasClass("collapsed"))
			{
				jQuery('.test-section__header-edit-action', jQuery(panel_head)).hide();
			}
			else
			{
				jQuery('.test-section__header-edit-action', jQuery(panel_head)).show();
			}
		},*/
		openQuestionPopups: function(handler, modalId = 'addModal', id = null, )
		{
			var unique = jQuery(handler).closest("[data-js-id='test-section']").attr('data-js-unique');
			var linkModal = jQuery("#" + modalId).attr('data-url');
			link = linkModal + "&unique=" + unique;

			jQuery("#" + modalId).attr('data-url' , link);
			jQuery("#" + modalId).modal('show');

			jQuery("#" + modalId).find('.iframe').attr('src' , link);
		},
		changeCompulsoryState: function (testId, sectionId, questionId, compulsory){
				var saveurl= "index.php?option=com_tmt&task=test.changeCompulsoryState&format=json";
				var formData = {'testId' : testId, 'sectionId' :sectionId, 'questionId' : questionId, 'compulsory' : compulsory};
				var promise = tjService.postData(saveurl,formData);

				window.doProcess =  false;

				promise.fail(
					function(response) {
						var messages = { "error": [response]};
						Joomla.renderMessages(messages);
						window.doProcess =  false;
					}
				).done(function(response) {
					if (!response.success && response.message){
						var messages = { "error": [response.message]};
						Joomla.renderMessages(messages);
						window.doProcess =  false;
					}

					if (response.messages){
						Joomla.renderMessages(response.messages);
					}

					if (response.success)
					{
						doProcess = true;
						Joomla.renderMessages({"success":[response.message]});
					}
				});

				return doProcess;
		},
		deleteTestQuestion: function (testId, sectionId, questionId){

			var confirmdelete = confirm(Joomla.JText._('COM_TMT_TEST_CONFIRM_QUESTION_DELETE'));

			if(confirmdelete == true)
			{
				var saveurl= "index.php?option=com_tmt&task=test.deleteTestQuestion&format=json";
				var formData = {'testId' : testId, 'sectionId' :sectionId, 'questionId' : questionId};
				var promise = tjService.postData(saveurl,formData);

				window.doProcess =  false;

				promise.fail(
					function(response) {
						var messages = { "error": [response]};
						Joomla.renderMessages(messages);
						window.doProcess =  false;
					}
				).done(function(response) {
					if (!response.success && response.message){
						var messages = { "error": [response.message]};
						Joomla.renderMessages(messages);
						window.doProcess =  false;
					}

					if (response.messages){
						Joomla.renderMessages(response.messages);
					}

					if (response.success)
					{
						doProcess = true;
						Joomla.renderMessages({"success":[response.message]});
					}
				});

				return doProcess;
			}
		},
	},
	tjMedia: {
		deleteMedia : function (clientId, client, mediaId)
		{
			if(!confirm(Joomla.JText._('COM_TMT_TEST_CONFIRM_QUESTION_MEDIA_DELETE')))
			{
				return false;
			}

			var url = "index.php?option=com_tmt&format=json&task=question.deleteMedia";
			var formData = { client_id : clientId, client : client, media_id : mediaId};
			var promise = tjService.postData(url,formData);

			promise.fail(
				function(response) {
					var messages = { "error": [response]};
					Joomla.renderMessages(messages);
				}
			).done(function(response) {

				if (response.messages){
					Joomla.renderMessages(response.messages);
				}

				if (!response.success && response.message){
					var messages = { "error": [response.message]};
				}

				if (response.success)
				{
					doProcess = true;
					var messages = { "success": [response.message]};
				}

				Joomla.renderMessages(messages);

				window.location.reload();
			});
		}
	}
}

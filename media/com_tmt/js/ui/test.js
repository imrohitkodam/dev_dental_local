import { Test } from "../services/test";

export class TestUI {

	testId                  = 0;
	inviteId                = 0;
	totalQuestions          = 0;
	totalPages              = 0;
	currentPage             = 1;
	gradingType             = '';
	visitedPages            = [];
	remainingTime           = 0;
	testDuration            = 0;
	timerFrequency          = 30; // In seconds
	ansSaveFrequency        = 1; // In seconds
	timeToShowFinishAlert   = 0;
	lessonLaunchType        = 'window';
	courseUrl               = '';
	resumeAllowed           = 0;
	template                = '';

	constructor(testId, inviteId) {
		this.testId      = testId;
		this.inviteId    = inviteId;
		this.testService = new Test(testId, inviteId);
	}

	getTotalQuestions() {
		return this.totalQuestions;
	}

	setTotalQuestions(value) {
		this.totalQuestions = value;
	}

	getTotalPages() {
		return this.totalPages;
	}

	setTotalPages(value) {
		this.totalPages = value;
	}

	getCurrentPage() {
		return this.currentPage;
	}

	setCurrentPage(value) {
		this.currentPage = value;
	}

	getGradingType() {
		return this.gradingType;
	}

	setGradingType(value) {
		this.gradingType = value;
	}

	getVisitedPages() {
		return this.visitedPages;
	}

	setVisitedPages(value) {
		this.visitedPages.push(parseInt(value))
	}

	getRemainingTime() {
		return this.remainingTime;
	}

	setRemainingTime(value)	{
		this.remainingTime = value;
	}

	getTestDuration() {
		return this.testDuration;
	}

	setTestDuration(value)
	{
		this.testDuration = value;
	}

	getTimerFrequency() {
		return this.timerFrequency;
	}

	setTimerFrequency(value) {
		this.timerFrequency = parseInt(value);
	}

	getAnsSaveFrequency() {
		return this.ansSaveFrequency;
	}

	setAnsSaveFrequency(value) {
		this.ansSaveFrequency = parseInt(value);
	}

	getTimeToShowFinishAlert() {
		return this.timeToShowFinishAlert;
	}

	setTimeToShowFinishAlert(value) {
		this.timeToShowFinishAlert = parseInt(value);
	}

	getLessonLaunchType() {
		return this.lessonLaunchType;
	}

	setLessonLaunchType(value) {
		this.lessonLaunchType = value;
	}

	getCourseUrl() {
		return this.courseUrl;
	}

	setCourseUrl(value) {
		this.courseUrl = value;
	}

	getResumeAllowed() {
		return this.resumeAllowed;
	}

	setResumeAllowed(value) {
		this.resumeAllowed = parseInt(value);
	}

	getSiteRoot() {
		return Joomla.getOptions('system.paths').root;
	}

	getTemplate(tmplName) {
		return jQuery("#" + tmplName).html();
	}

	validateTextArea(ele) {
		if (ele.is("textarea"))
		{
			let usedCharLength = parseInt(ele.val().length),
			maxlength          = parseInt(ele.attr("maxlength")),
			minlength          = parseInt(ele.attr("minlength"));

			if ((minlength > 0 && usedCharLength < minlength && usedCharLength != 0) || (maxlength > 0 && usedCharLength > maxlength))
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
	}

	initTest() {
		let me = this;

		jQuery(document).ready(function () {
			// Get test questions
			me.getQuestions();
		});

		jQuery(window).on('load',function(){
			me.updateTimer();
			me.resetAnswer();
			me.flagQuestion();
			me.testActions();
			me.pageCloseActions();
		});
	}

	getQuestions() {
		let me = this;

		// Check if page is already called and rendered
		if (me.getVisitedPages().indexOf(me.getCurrentPage()) != "-1")
		{
			jQuery(".questions_container .tjlms-test-page").hide();
			jQuery(".questions_container .tjlms-test-page#testPage" + me.getCurrentPage()).show();
			me.bottomToolbarActions();

			return;
		}

		let cb = function (error, res) {
		if (error) {
			console.log(error);
		}
		else if (res) {
			if (res.success === true) {
					let response = res.data,
					section      = me.buildQuestionData(response.sections),
					testData     = response.test;

					if (testData.show_questions_overview == "0")
					{
						testData.show_questions_overview = "";
					}

					let qUntilPrevPage = 1;

					for (let q = me.getCurrentPage() - 1; q >= 1; q--) {
						qUntilPrevPage = parseInt(qUntilPrevPage) + parseInt(questionsPerPage[q]);
					}

					let data = {
						"pageNo": me.getCurrentPage(),
						"section": section,
						"testData": testData,
						"tjMediaPath": tjMediaPath,
						"currentPageUrl": window.location.href,
						index: function() {
							if (window['INDEX'] === undefined || window['INDEX'] === null)
							{
								return window['INDEX'] = parseInt(qUntilPrevPage);
							}

					        return ++window['INDEX'];
					    },
					    resetIndex: function() {
					    	window['INDEX'] = null;

					    	return;
					    },
					    paletteIndex: function() {
					        return ++window['P_INDEX']||(window['P_INDEX']=1);
					    }
					};

					let containerTmpl = '<div class="que-container">{{>recursive_partial}}</div>';

					let rendered = Mustache.render(containerTmpl, data, { recursive_partial: me.getTemplate('questionMustache') });
					jQuery('#renderTest').append(rendered);

					// Render question palette
					if (testData.show_questions_overview != "")
					{
						let paletteContainerTmpl = '<div class="questions_palette">{{>recursive_partial}}</div>';

						let rendered = Mustache.render(paletteContainerTmpl, data, { recursive_partial: me.getTemplate('paletteMustache') });
						jQuery('#question_palette').html(rendered);
					}

					// Since Mustache's render function is synchronous, we have written the next set of functions immediately after the render statement

					// Hide all pages first and then show recently fetched page
					jQuery(".questions_container .tjlms-test-page").hide();
					jQuery(".questions_container .tjlms-test-page#testPage" + me.getCurrentPage()).show();

					// Enable image popup
					Object.assign(jQuery(".questions_container .tjlms-test-page#testPage" + me.getCurrentPage()).find('a.tjmodal') , {parse: 'rel'});

					me.bottomToolbarActions();
					me.setVisitedPages(parseInt(me.getCurrentPage()));
				}
			}
		};

		let questionObj             = {};
		questionObj[me.formToken()] = 1;
		questionObj['pageNo']       = me.getCurrentPage();

		this.testService.getQuestions(questionObj, cb);
	}

	buildQuestionData(section) {
		let me = this;

		for (let s = 0, len = section.length; s < len; s++) {

			for (let i = 0, len = section[s].questions.length; i < len; i++) {

				// Support multilingual - start
				section[s].questions[i].title = Joomla.Text._(section[s].questions[i].title, section[s].questions[i].title);

				section[s].questions[i].description = Joomla.Text._(section[s].questions[i].description, section[s].questions[i].description);

				// Support multilingual - end

				// Mustache: Unset flagged key to "" if 0
				if (section[s].questions[i].flagged == "0")
				{
					section[s].questions[i].flagged = "";
				}

				// Mustache: Palette related condition
				if (section[s].questions[i].userAnswer != '')
				{
					section[s].questions[i].attemptedClass = "tmt-circle--attempted";
				}

				// Mustache: Create object with question type key.
				// e.g. type = radio, then new key will be section[s].questions[i]['type_radio']
				section[s].questions[i]['type_' + section[s].questions[i].type] = '1';

				// Convert question params from JSON to Object and assign it to new array key as `q_params`
				if (section[s].questions[i].params != '')
				{
					section[s].questions[i]['q_params'] = JSON.parse(section[s].questions[i].params);
				}

				// Convert rating label into arrays for rating type questions
				if (section[s].questions[i].type == 'rating')
				{
					if (section[s].questions[i].q_params !== undefined && section[s].questions[i].q_params.rating_label != 'undefined' && section[s].questions[i].q_params.rating_label.indexOf(",") != "-1")
					{
						section[s].questions[i].ratingLabel = section[s].questions[i].q_params.rating_label.split(",");

						// Support multilingual - start
						for (let lb = 0, lb_len = section[s].questions[i].ratingLabel.length; lb < lb_len; lb++)
						{
							section[s].questions[i].ratingLabel[lb] = Joomla.Text._(section[s].questions[i].ratingLabel[lb].trim(), section[s].questions[i].ratingLabel[lb].trim());
						}

						// Support multilingual - end
					}

					section[s].questions[i].ratingRange = [];

					for (let r = section[s].questions[i].answers[0].answer, len = section[s].questions[i].answers[1].answer; r <= len; r++)
					{
						// Check for user answer
						let isChecked = '';

						if (section[s].questions[i].userAnswer != 'undefined' && parseInt(section[s].questions[i].userAnswer) == parseInt(r))
						{
							isChecked = 'checked';
						}

						section[s].questions[i].ratingRange.push({"value": parseInt(r), "isChecked": isChecked });
					}

					// Rating type questions has two elements in answers array (start and end range). We are iterating over answers array in Mustache to render answer, to stop rendering answer twice, we had to remove one element.
					section[s].questions[i].answers.pop();
				}

				if (section[s].questions[i].type == 'file_upload')
				{
					let uploadFileSizeLimit = 0;

					if (section[s].questions[i].q_params !== undefined)
					{
						uploadFileSizeLimit = (section[s].questions[i].q_params.file_size == "") ? lmsLessonUploadSize : parseInt(section[s].questions[i].q_params.file_size);
					}

					uploadFileSizeLimit = Math.min(uploadFileSizeLimit, lmsLessonUploadSize);

					let fileBrowseLang = "Browse";
					let dNoneClass     = "";

					if (section[s].questions[i].userAnswer != "")
					{
						fileBrowseLang = "Change";
						dNoneClass     = "d-none";
					}

					section[s].questions[i].uploadFileSizeLimit = uploadFileSizeLimit;
					section[s].questions[i].fileBrowseLang      = fileBrowseLang;
					section[s].questions[i].dNoneClass          = dNoneClass;
				}

				let gradingType = section[s].questions[i].gradingtype;

				if (gradingType == 'quiz')
				{
					section[s].questions[i].isQuiz = '1';

					if (section[s].questions[i].marks > 1)
					{
						section[s].questions[i].marksGreaterThanOne = '1';
					}
				}

				if (section[s].questions[i].is_compulsory == "0")
				{
					section[s].questions[i].is_compulsory = "";
				}

				let qMediaId   = section[s].questions[i].media_id;
				let qMediaType = section[s].questions[i].media_type;

				if (qMediaId !== null && qMediaType !== null)
				{
					let qMediaTypeTmpl = me.getMediaType(qMediaType);
					section[s].questions[i][qMediaTypeTmpl] = '1';

					if (qMediaTypeTmpl == 'videoMedia')
					{
						let qVideoType = me.getVideoType(qMediaType);
						section[s].questions[i][qVideoType] = '1';
					}
				}

				// Check answers are in array format and are not of rating grading type
				if (Array.isArray(section[s].questions[i].answers) && gradingType != 'rating')
				{
					if (section[s].questions[i].answers.length >= 1)
					{
						// Get media id and type in answer if exist
						for (let j = 0, len = section[s].questions[i].answers.length; j < len; j++) {

							// Support multilingual - start
							section[s].questions[i].answers[j].answer = Joomla.Text._(section[s].questions[i].answers[j].answer, section[s].questions[i].answers[j].answer);

							if (section[s].questions[i].answers[j].comments !== null)
							{
								section[s].questions[i].answers[j].comments = Joomla.Text._(section[s].questions[i].answers[j].comments, section[s].questions[i].answers[j].comments);
							}

							// Support multilingual - end

							let aMediaId   = section[s].questions[i].answers[j].media_id;
							let aMediaType = section[s].questions[i].answers[j].media_type;

							if (aMediaId !== null && aMediaType !== null)
							{
								let aMediaTypeTmpl = me.getMediaType(aMediaType);
								section[s].questions[i].answers[j]['a_' + aMediaTypeTmpl] = '1';

								if (aMediaTypeTmpl == 'videoMedia')
								{
									let aVideoType = me.getVideoType(aMediaType);
									section[s].questions[i].answers[j]['a_' + aVideoType] = '1';
								}
							}

							// Check for user answer and create a new key as checked to keep the answer checked
							if (section[s].questions[i].userAnswer != 'undefined' && jQuery.inArray(section[s].questions[i].answers[j].id, section[s].questions[i].userAnswer) >= 0)
							{
								section[s].questions[i].answers[j].checked = 'checked';
							}
						}
					}
					else if(section[s].questions[i].type == 'text' || section[s].questions[i].type == 'textarea')
					{
						// Exercise and feedback grading type subjective anwsers are not required so we add 'noAnswer' as answer to render answer using question mustache. Text and textarea have only one answer so only for first index 'noAnswer' is added.
						section[s].questions[i].answers[0] = 'noAnswer';
					}
				}
			}
		}

		return section;
	}

	getMediaType(mediaType) {
		if (mediaType.indexOf("image") != "-1")
		{
			return 'imageMedia';
		}
		else if (mediaType.indexOf("audio") != "-1")
		{
			return 'audioMedia';
		}
		else if (mediaType.indexOf("video") != "-1")
		{
			return 'videoMedia';
		}
		else
		{
			return 'fileMedia';
		}
	}

	getVideoType(mediaType) {
		if (mediaType.indexOf("vimeo") != "-1")
		{
			return 'vimeoMedia';
		}
		else if (mediaType.indexOf("youtube") != "-1")
		{
			return 'youtubeMedia';
		}
	}

	formToken() {
		return jQuery('[data-js-id="form-token"]').attr('name');
	}

	formData() {
		return jQuery("#adminForm").serialize();
	}

	timerStartTime() {
		let me = this;

		return new Date(new Date().valueOf() + me.getRemainingTime());
	}

	updateTimer() {
		let me         = this;
		let cb         = function (error, res) {};
		let $clock     = jQuery(".test__timer #countdown_timer");
		let elapseFlag = true;

		if (me.getTestDuration() != 0)
		{
			elapseFlag = false;
		}

		$clock.countdown(me.timerStartTime(), {elapse: elapseFlag})
		.on('update.countdown', function(event) {

			jQuery(this).html(event.strftime('%H:%M:%S'));

			// If time remaining to finish the test is less than the time alert
			if (me.getTimeToShowFinishAlert() != 0 && parseInt(event.strftime('%T')) < me.getTimeToShowFinishAlert())
			{
				jQuery(this).addClass("text-error");
				// jQuery('#countdown_timer_msg').html(Joomla.Text._('COM_TMT_TEST_APPEAR_MSG_LAST_N_MINUTES'));
			}

			if (event.offset.seconds % me.getTimerFrequency() == 0)
			{
				let timerObj             = {};
				timerObj[me.formToken()] = 1;
				timerObj["timeSpent"]    = me.getTimerFrequency();

				me.testService.updateTimer(timerObj, cb);
			}
		})
		.on('finish.countdown', function(event) {
			let timerObj             = {};
			timerObj[me.formToken()] = 1;
			timerObj["timeSpent"]    = me.getTimerFrequency();

			me.testService.updateTimer(timerObj, cb);

			let cBack = function (error, res) {
				if (error) {
					let msg = { "error": [res.responseText]};
					Joomla.renderMessages(msg);
				}
				else if (res) {
					if (res.success === true) {
						me.thankYouPageRedirect();
					}
				}
			};

			me.submitTest(1, cBack);
		});
	}

	saveAnswer(ele) {
		let me          = this;
		let questionId  = jQuery(ele).closest('[data-js-id="test-question"]').attr('data-js-itemid');
		let answer      = jQuery(ele).val();
		let answerArray = [];

		// Manipulate "answer" variable based on question type
		if (jQuery(ele).attr('type') === 'radio' || jQuery(ele).attr('type') === 'checkbox')
		{
			jQuery(ele).closest('[data-js-id="test-question"]').find(":" + jQuery(ele).attr('type')).each(function () {
				if (this.checked) {
					answerArray.push(jQuery(this).val());
				}
			});

			answer = answerArray.join();
		}
		else if (jQuery(ele).attr('type') === 'file')
		{
			jQuery(ele).closest('[data-js-id="test-question"]').find('[data-js-id="each-file"]').each(function () {
				answerArray.push(jQuery(this).attr("data-js-itemid"));
			});

			// In case of file type questions, answer array doesn't contain actual answer ids but media ids.
			answer = answerArray.join();
		}

		let answerObj             = {};
		answerObj[me.formToken()] = 1;
		answerObj['questionId']   = questionId;
		answerObj['answer']       = answer;

		let cb = function (error, res) {
		if (error) {
			let msgArr      = [];
			msgArr['msg']   = res.responseText;
			msgArr['error'] = 1;
			TMT.UI.TestUI.saveAnswerMsg(questionId, msgArr);
		}
		else if (res) {
			if (res.success === true) {
					jQuery(".tmt_test__footer__navbutton").removeAttr("disabled");

					// Update Palette
					if (answer !== '')
					{
						jQuery('#question' + questionId).addClass('tmt-circle--attempted');
					}
					else if (answer == '')
					{
						jQuery('#question' + questionId).removeClass('tmt-circle--attempted');
					}

					// Update progress bar
					me.attemptedQueCount();
				}
				else {
					let msgArr      = [];
					msgArr['msg']   = res.message;
					msgArr['error'] = 1;
					TMT.UI.TestUI.saveAnswerMsg(questionId, msgArr);
				}
			}
		}

		me.testService.saveAnswer(answerObj, cb);
	}

	static saveAnswerMsg(qId, result) {
		// Each question on test page has the div to message. This is called to show success or error message when each answer of question is saved.
		let ele        = jQuery('[data-js-id="test-question"][data-js-itemid="'+ qId +'"]');
		let alertClass = "alert-success";

		if (result['error'] == "1")
		{
			alertClass = "alert-error"
		}

		jQuery('[data-js-id="test-question-msg"] .alert', ele).addClass(alertClass);
		jQuery('[data-js-id="test-question-msg"] .alert', ele).html(result['msg']);
		jQuery('[data-js-id="test-question-msg"]', ele).removeClass('d-none');

		setTimeout(function(){
		  jQuery('[data-js-id="test-question-msg"]', ele).addClass('d-none');
		}, 5000);
	}

	attemptedQueCount() {
		let me = this;

		let cb = function (error, res) {
			if (error) {
				console.log(error);
			}
			else if (res) {
				if (res.success === true) {
					let response         = res.data;
					let attemptedCount   = response;
					let questionProgress = (100 * attemptedCount) / me.getTotalQuestions();
					let msg              = Joomla.Text._('COM_TMT_TEST_APPEAR_ATTEMPTED_OF').replace("%s", attemptedCount).replace("%s", me.getTotalQuestions());

					jQuery('[data-js-id="test-controls"] .progress .progress-bar').width(questionProgress + "%").attr("aria-valuenow", questionProgress);
					jQuery('[data-js-id="test-controls"] .progress .progress-bar .progress_bar_text').text(msg);
				}
			}
		};

		me.testService.attemptedQueCount(cb);
	}

	static validateCheckBox(correctAnsCnt, qId, aId, gradingType) {
		let total  = jQuery('input[name="questions[mcqs][' + qId + '][]"]:checked').length;
		let result = [];

		if (total > correctAnsCnt && gradingType != 'feedback')
		{
			jQuery('input[value="' + aId + '"]').removeAttr('checked');
			result['error'] = '1';
			result['msg']   = Joomla.Text._('COM_TMT_TEST_MAX_OPTION_ATTEMPT_VALIDATION');

			TMT.UI.TestUI.saveAnswerMsg(qId, result);
		}
	}

	resetAnswer() {
		let me = this;

		jQuery(document).on("click", "[data-js-skip]", function () {
			let questionId   = jQuery(this).data('js-question');
			let questionType = jQuery(this).data('js-type');
			let element      = '';

			switch (questionType)
			{
				case 'checkbox':
				case 'radio':
					jQuery('input[name="questions[mcqs][' + questionId + '][]"]').prop('checked', false);
					element = jQuery('input[name="questions[mcqs][' + questionId + '][]"]');
					break;

				case 'rating':
					jQuery('input[name="questions[rating][' + questionId + ']"]').attr('checked', false);
					element = jQuery('input[name="questions[rating][' + questionId + ']"]');
					break;

				case 'text':
				case 'objtext':
				case 'textarea':
					jQuery('#questions' + questionId).val('').change();
					element = jQuery('#questions' + questionId);
					break;
			}

			if (me.validateTextArea(element))
			{
				me.saveAnswer(element);
			}
		});
	}

	flagQuestion() {
		let me = this;

		jQuery(document).on("click", "[data-js-flag]", function () {
			let ele        = jQuery(this);
			let questionId = ele.data('js-question');

			let cb = function (error, res) {
				if (error) {
					let msg = { "error": [res.responseText]};
					Joomla.renderMessages(msg);
				}
				else if (res) {
					if (res.success === true) {

						if (jQuery('#question' + questionId + ' > span').hasClass('fa fa-flag'))
						{
							jQuery('#question' + questionId + ' > span').removeClass('fa fa-flag');
							jQuery(ele).text(Joomla.Text._('COM_TMT_QUESTION_FLAG'));
						}
						else
						{
							jQuery('#question' + questionId + ' > span').addClass('fa fa-flag');
							jQuery(ele).text(Joomla.Text._('COM_TMT_QUESTION_UNFLAG'));
						}
					}
				}
			};

			let obj             = {};
			obj[me.formToken()] = 1;
			obj['qId']          = questionId;

			me.testService.flagQuestion(obj, cb);
		});
	}

	bottomToolbarActions() {
		let me          = this;
		let isFirst     = (me.getCurrentPage() == 1) ? 1 : 0;
		let totalPages  = parseInt(me.getTotalPages());
		let gradingtype = parseInt(me.getGradingType());
		let isLast      = (totalPages == me.getCurrentPage()) ? 1 : 0;

		jQuery("[data-js-id='toolbar']").addClass("d-inline-block").removeClass("d-none");
		jQuery(".toolbar__span").addClass("d-none");

		if (totalPages == 1 || isLast) {
			jQuery("[data-js-id='toolbar'] [data-js-id='toolbar-final']").removeClass('d-none');

			if (gradingtype === 'exercise') {
				jQuery("[data-js-id='toolbar'] [data-js-id='toolbar-final']").removeClass('d-none');
			}
		}

		if (totalPages > 1 && !isFirst && !isLast) {
			// jQuery("[data-js-id='toolbar'] [data-js-id='toolbar-prev']").show();
			jQuery("[data-js-id='toolbar'] [data-js-id='toolbar-next']").removeClass('d-none');
		}

		if (totalPages > 1 && isFirst && !isLast) {
			jQuery("[data-js-id='toolbar'] [data-js-id='toolbar-next']").removeClass('d-none');
		}

		if (totalPages > 1 && !isFirst && isLast) {
			// jQuery("[data-js-id='toolbar'] [data-js-id='toolbar-prev']").show();
		}

		jQuery('.quiz_content').scrollTop('0');
	}

	unAttemptedCompulsoryQue() {
		let me             = this;
		let testUnAnswered = 0;
		let pageNo         = me.getCurrentPage();

		jQuery(".questions_container").each(function (){
			let pageElement    = jQuery(this);
			let pageUnAnswered = 0;

			jQuery(pageElement).find("[data-js-compulsory='1']").each(function () {

				let answer = '';

				if (jQuery(this).data("js-type") == "text" || jQuery(this).data("js-type") == "objtext")
				{
					answer = jQuery(this).find("input").val();
				}

				if (jQuery(this).data("js-type") == "textarea")
				{
					answer = jQuery(this).find("textarea").val();
				}

				if (jQuery(this).data("js-type") === 'radio' || jQuery(this).data("js-type") === 'checkbox' || jQuery(this).data("js-type") === 'rating')
				{
					let a       = [];
					let ansType = jQuery(this).data("js-type");

					if (jQuery(this).data("js-type") === 'rating')
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
				else if (jQuery(this).data("js-type") === 'file_upload')
				{
					var a = [];
					jQuery(this).find('[data-js-id="each-file"]').each(function () {
						a.push(jQuery(this).data("js-itemid"));
					});
					answer = a.join();
				}

				if (answer == '')
				{
					pageUnAnswered++;
				}
			});

			jQuery("#tmt-page-selection [data-lp='" + pageNo + "'] .mandatory-notification").remove();
			testUnAnswered = testUnAnswered + pageUnAnswered;
			jQuery("#unAttemptedCompulsoryCnt").val(testUnAnswered);

			if (pageUnAnswered > 0)
			{
				jQuery("#tmt-page-selection [data-lp='" + pageNo + "']").not('.prev').not('.next').append("<span class='mandatory-notification'>" + pageUnAnswered + "</span>");
			}
		});
	}

	saveAllAnswersOnPage(cb) {
		let me = this;

		me.testService.saveAllAnswersOnPage(me.formData(), cb);
	}

	submitTest(timeOut, cBack) {
		let me = this;

		// Callback for saveAllAnswersOnPage()
		let cb = function (error, res) {
			if (error) {
				let msg = { "error": [res.responseText]};
				Joomla.renderMessages(msg);
			}
			else if (res) {
				if (res.success === true) {

					let unAttemptedCompulsoryCnt = 0;

					if (!timeOut)
					{
						// Once form is submiited check if any mandatory Question is not Attempted
						me.unAttemptedCompulsoryQue();
						unAttemptedCompulsoryCnt = jQuery("#unAttemptedCompulsoryCnt").val();
					}

					if (parseInt(unAttemptedCompulsoryCnt) > 0)
					{
						alert(Joomla.Text._('COM_TMT_TEST_SUBMIT_VALIDATION_FOR_COMPULSORY_MSG'));
					}
					else
					{
						let testObj = {};
						testObj[me.formToken()] = 1;

						me.testService.submitTest(testObj, cBack);
					}
				}
			}
		};

		me.saveAllAnswersOnPage(cb);
	}

	testCloseRedirect() {
		let me = this;

		if (me.getLessonLaunchType() == 'popup') {
			window.parent.SqueezeBox.close();
		}
		else if (me.getLessonLaunchType() == 'tab') {
			// opener is a global window object
			if (opener) {
				opener.location.reload();
			}

			window.close();
		}
		else {
			window.location = me.getCourseUrl();
		}
	}

	thankYouPageRedirect() {
		window.location = jQuery("#thankYouLink").val();
	}

	pageCloseActions() {
		let me = this;

		jQuery(document).on('click', '[data-js-id="test-premise-close"]', function () {
			me.testCloseRedirect();
		});

		// Actions on test close
		jQuery("[data-js-id='test-close']").click(function() {
			let unAttemptedCompulsoryCnt = 0;
			let msg = "";

			let attemptedpercent = jQuery('[data-js-id="test-controls"] .progress .progress-bar').attr("aria-valuenow");

			if (attemptedpercent < 100)
			{
				msg = "COM_TMT_TEST_NOT_ATTEMPTED_QUE";
			}
			else
			{
				msg = "COM_TMT_QUIZ_CONFIRM_BOX";
			}

			// Once form is submiited check if any mandatory Question is not Attempted
			me.unAttemptedCompulsoryQue();
			unAttemptedCompulsoryCnt = jQuery("#unAttemptedCompulsoryCnt").val();

			if (parseInt(unAttemptedCompulsoryCnt) > 0 && me.getResumeAllowed() == 0)
			{
				alert(Joomla.Text._('COM_TMT_TEST_SUBMIT_VALIDATION_FOR_COMPULSORY_MSG'));
			}
			else if (confirm(Joomla.Text._(msg)) == true)
			{
				if (me.getResumeAllowed() == 0)
				{
					let cBack = function (error, res) {
						if (error) {
							let msg = { "error": [res.responseText]};
							Joomla.renderMessages(msg);
						}
						else if (res) {
							if (res.success === true) {
								me.testCloseRedirect();
							}
						}
					};

					me.submitTest(0, cBack);
				}
				else
				{
					me.testCloseRedirect();
				}
			}
		});
	}

	showAnswerUploadedFiles(response) {
		let me = this;
		let fileElement =
		'<div class="col-sm-6" data-js-id="each-file" data-js-itemid="' + response.media_id + '" data-js-answerid="' + response.answer_id + '">' +
			'<a class="mr-5" href="' + response.path + '">' + response.org_filename + '</a>' +
			'<a href="javascript:void(0)" data-js-id="delete" title="' + Joomla.Text._("COM_TMT_DELETE_ITEM") + '">' +
				'<i class="fa fa-trash" aria-hidden="true"></i>' +
			'</a>' +
		'</div>' +
		'<input type="hidden" name="questions[upload][' + response.qid + '][]" value="' + response.media_id + '"/>';

		jQuery('[data-js-itemid="' + response.qid + '"] [ data-js-id="uploaded-file-list-header"]').removeClass('d-none');
		jQuery(fileElement).appendTo(jQuery('[data-js-itemid="' + response.qid + '"] [data-js-id="uploaded-file-list"]'));

		// Save answer
		me.saveAnswer(jQuery('[data-js-itemid="' + response.qid + '"] input[type="file"]'));

		let uploadedFilesCnt = jQuery('[data-js-itemid="' + response.qid + '"] [data-js-id="uploaded-file-list"] > div[data-js-id="each-file"]').length;

		let qfileuploadCnt = jQuery("[name='testqparam[" + response.qid + "][file_format]']");

		if (qfileuploadCnt.length && qfileuploadCnt.val() != '' && uploadedFilesCnt >= qfileuploadCnt.val())
		{
			jQuery('[data-js-itemid="' + response.qid + '"].fileupload .btn-file').attr("disabled");
		}
	}

	testActions() {
		let me = this;
		let timeoutId;

		// Actions on input/answer changes. Call save function on answer change. e.g. Text/textarea/objtext change, radio/checkbox selection, file upload etc.
		jQuery(document).on("input propertychange","[data-js-id='test-question'][data-js-type='textarea'] textarea, [data-js-id='test-question'][data-js-type='text'] input, [data-js-id='test-question'][data-js-type='objtext'] input, [data-js-id='test-question'][data-js-type='radio'] input, [data-js-id='test-question'][data-js-type='checkbox'] input,[data-js-id='test-question'][data-js-type='rating'] input", function(){

			let element = jQuery(this);

			if (jQuery(this).attr('type') == "radio" || jQuery(this).attr('type') == "checkbox")
			{
				me.saveAnswer(element);
			}
			else
			{
				clearTimeout(timeoutId);

				timeoutId = setTimeout(function() {
					if (me.validateTextArea(element))
					{
						me.saveAnswer(element);
					}
				}, me.getAnsSaveFrequency() * 1000);
			}
		});

		// Actions on test submission
		jQuery("[data-js-id='submittest']").click(function() {
			let msg = "";

			let attemptedpercent = jQuery('[data-js-id="test-controls"] .progress .progress-bar').attr("aria-valuenow");

			switch (me.getGradingType())
			{
				case "exercise" :

					if (attemptedpercent < 100)
					{
						msg = "COM_TMT_TEST_NOT_ATTEMPTED_QUE";
					}
					else
					{
						msg = "COM_TMT_TEST_APPEAR_FINISH_EXERCISE";
					}

					break;
				case "feedback" :

					if (attemptedpercent < 100)
					{
						msg = "COM_TMT_TEST_NOT_ATTEMPTED_QUE";
					}
					else
					{
						msg = "COM_TMT_TEST_APPEAR_FINISH_FEEDBACK";
					}

					break;
				case "quiz" :

				if (attemptedpercent < 100)
				{
					msg = "COM_TMT_TEST_NOT_ATTEMPTED_QUE";
				}
				else
				{
					msg = "COM_TMT_TEST_APPEAR_FINISH_QUIZ";
				}
			}

			let unAttemptedCompulsoryCnt = 0;

			// Once form is submitted check if any mandatory Question is not Attempted
			me.unAttemptedCompulsoryQue();
			unAttemptedCompulsoryCnt = jQuery("#unAttemptedCompulsoryCnt").val();

			if (parseInt(unAttemptedCompulsoryCnt) > 0)
			{
				alert(Joomla.Text._('COM_TMT_TEST_SUBMIT_VALIDATION_FOR_COMPULSORY_MSG'));
			}
			else if (confirm(Joomla.Text._(msg)) == true)
			{
				let cBack = function (error, res) {
					if (error) {
						let msg = { "error": [res.responseText]};
						Joomla.renderMessages(msg);
					}
					else if (res) {
						if (res.success === true) {
							me.thankYouPageRedirect();
						}
					}
				};

				me.submitTest(0, cBack);
			}
		});

		// Actions on bottom toolbars
		jQuery("[data-js-id='toolbar'] [data-js-id='toolbar-next'] button, [data-js-id='toolbar'] [data-js-id='toolbar-prev'] button").click(function()	{

			if (jQuery(this).closest(".toolbar__span").data("js-id") == 'toolbar-next')
			{
				me.setCurrentPage(parseInt(me.getCurrentPage()) + 1);
			}
			else if (jQuery(this).closest(".toolbar__span").data("js-id") == 'toolbar-prev')
			{
				me.setCurrentPage(parseInt(me.getCurrentPage()) - 1);
			}

			let cBack = function (error, res) {
				if (error) {
					let msg = { "error": [res.responseText]};
					Joomla.renderMessages(msg);
				}
				else if (res) {
					if (res.success === true) {
						me.getQuestions();
						jQuery('#tmt-page-selection').bootpag({page: me.getCurrentPage()});
					}
				}
			};

			me.saveAllAnswersOnPage(cBack);
		});

		jQuery("[data-js-id='drafttest']").click(function()
		{
			if (confirm(Joomla.Text._("COM_TMT_TEST_DRAFT_CONFIRM_BOX")) == true)
			{
				let cBack = function (error, res) {};
				me.saveAllAnswersOnPage(cBack);
			}
		});

		// Build pagination
		let paginationObj = {
			total: parseInt(me.getTotalPages()),
			page: me.getCurrentPage(),
			maxVisible: 5,
			leaps: true,
			next:'Next',
			prev:'Prev',
		};

		if (parseInt(me.getTotalPages()) >= 3)
		{
			let paginationObjExtend = {
				firstLastUse: true,
				first: 'FIRST',
				last: 'LAST',
			};

			// Merge paginationObjExtend into paginationObj
			jQuery.extend(paginationObj, paginationObjExtend);
		}

		jQuery('#tmt-page-selection').bootpag(paginationObj).on("page", function(event, /* page number here */ pageToFetch){

			me.setCurrentPage(parseInt(pageToFetch));

			let cBack = function (error, res) {
				if (error) {
					let msg = { "error": [res.responseText]};
					Joomla.renderMessages(msg);
				}
				else if (res) {
					if (res.success === true) {
						me.getQuestions();
					}
				}
			};

			me.saveAllAnswersOnPage(cBack);
		});

		// Textarea activity tracker at runtime
		jQuery(document).on("keyup", "[data-js-type='textarea'] textarea", function(){
			let usedCharLength      = parseInt(jQuery(this).val().length);
			let maxLength           = parseInt(jQuery(this).attr("maxlength"));
			let minLength           = parseInt(jQuery(this).attr("minlength"));
			let availableCharLength = maxLength - usedCharLength;

			jQuery(this).closest("[data-js-type='textarea']").find(".charscontainer_remaining").text(availableCharLength);

			jQuery(this).closest("[data-js-type='textarea']").find(".charscontainer").removeClass("invalid");

			if (minLength > 0 && usedCharLength == 0)
			{
				jQuery(this).closest("[data-js-type='textarea']").find(".charscontainer").addClass("invalid");
			}

			if (maxLength > 0 && usedCharLength > maxLength)
			{
				jQuery(this).closest("[data-js-type='textarea']").find(".charscontainer").addClass("invalid");
			}

			if (usedCharLength != 0 && usedCharLength < minLength)
			{
				jQuery(this).closest("[data-js-type='textarea']").find(".charscontainer").addClass("invalid");
			}
			else
			{
				jQuery(this).closest("[data-js-type='textarea']").find(".charscontainer").removeClass("invalid");
			}
		});

		// File upload
		jQuery(document).on("change", "[data-js-type='file_upload'] input[type='file']", function(){

			let thisfile     = jQuery(this);
			let uploadedfile = jQuery(thisfile)[0].files[0];

			if (!uploadedfile) {
				return false;
			}

			// Once form is submitted check if any mandatory Question is not Attempted
			me.unAttemptedCompulsoryQue();

			if (parseInt(jQuery("#unAttemptedCompulsoryCnt").val()) > 0)
			{
				jQuery(".tmt_test__footer__navbutton").attr("disabled", "disabled");
			}

			let formData = new FormData();
			formData.append('FileInput', uploadedfile);

			let qId = jQuery(this).closest('[data-js-id="test-question"]').attr('data-js-itemid');

			formData.append('mediaformat', 'quiz');
			formData.append('subformat', 'answer');
			formData.append('formatData[quiz][answer][qid]', qId);
			formData.append('formatData[quiz][answer][testid]', me.testId);
			formData.append('formatData[quiz][answer][ltid]', me.inviteId);

			tjLmsCommon.file.$file       = thisfile;
			tjLmsCommon.file.formData    = formData;
			tjLmsCommon.file.allowedSize = lmsLessonUploadSize;

			let qfileSizeElement = jQuery("[name='testqparam[" + qId + "][file_size]']");

			if (qfileSizeElement.length && qfileSizeElement.val() > 0)
			{
				tjLmsCommon.file.allowedSize = qfileSizeElement.val();
			}

			tjLmsCommon.file.showstatusbar = true;

			let qfileuploadCnt   = jQuery("[name='testqparam[" + qId + "][file_count]']");
			let uploadedFilesCnt = jQuery('[data-js-itemid="'+ qId +'"] [data-js-id="uploaded-file-list"] > div[data-js-id="each-file"]').length;

			if (qfileuploadCnt.length && qfileuploadCnt.val() != '' && uploadedFilesCnt >= qfileuploadCnt.val())
			{
				// jQuery("#msg_" + qId).attr("class", "alert alert-error").text(Joomla.Text._('COM_TMT_MAX_NUMBER_OF_FILE_UPLOAD_ERROR_MSG').replace("%s", qfileuploadCnt.val()));

				let msgArr      = [];
				msgArr['msg']   = Joomla.Text._('COM_TMT_MAX_NUMBER_OF_FILE_UPLOAD_ERROR_MSG').replace("%s", qfileuploadCnt.val());
				msgArr['error'] = 1;

				TMT.UI.TestUI.saveAnswerMsg(qId, msgArr);

				jQuery(".tmt_test__footer__navbutton").removeAttr("disabled");

				return false;
			}

			tjLmsCommon.file.afterProcessDone = function (res){
				me.showAnswerUploadedFiles(res);
			};

			tjLmsCommon.file.upload();
		});

		jQuery(document).on("click", "[data-js-type='file_upload'] [data-js-id='delete']", function(){
			if (confirm(Joomla.Text._("COM_TMT_TEST_DELETE_ANSWER_UPLOADED_FILE_CONFIRM_BOX")) == true) {

				let fileContainer = jQuery(this).closest('[data-js-id="each-file"]');
				let qId           = jQuery(this).closest('[data-js-id="test-question"]').data('js-itemid');
				let answerId      = jQuery(this).closest('[data-js-id="each-file"]').data("js-answerid");
				let answerMediaId = jQuery(this).closest('[data-js-id="each-file"]').data("js-itemid");

				let cb = function (error, res) {
					if (error) {
						let msgArr      = [];
						msgArr['msg']   = res.responseText;
						msgArr['error'] = 1;

						TMT.UI.TestUI.saveAnswerMsg(qId, msgArr);
					}
					else if (res) {
						if (res.success === true) {
							jQuery(fileContainer).remove();
							jQuery('input[name="questions[upload][' + qId + '][]"][value="' + answerMediaId + '"]').remove();

							if (jQuery('[data-js-itemid="' + qId + '"] [data-js-id="uploaded-file-list"] > div[data-js-id="each-file"]').length == 0)
							{
								jQuery('[data-js-itemid="' + qId + '"] .test-question__answers').find('[data-js-id="uploaded-file-list-header"]').addClass('d-none');
							}

							let msgArr      = [];
							msgArr['msg']   = res.message;
							msgArr['error'] = 0;

							TMT.UI.TestUI.saveAnswerMsg(qId, msgArr);

							me.saveAnswer(jQuery('[data-js-itemid="' + qId + '"] input[type="file"]'));
						}
					}
				};

				let answerObj              = {};
				answerObj[me.formToken()]  = 1;
				answerObj['answerId']      = answerId;
				answerObj['answerMediaId'] = answerMediaId;

				me.testService.deleteAnswerFile(answerObj, cb);
			}
		});
	}
}


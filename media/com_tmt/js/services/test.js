import { Base } from "./base";

export class Test {

	testId;
	inviteId;

	constructor(testId, inviteId) {
		this.testId   = testId;
		this.inviteId = inviteId;
    }

	getTestId()	{
		return this.testId;
	}

	getInviteId() {
		return this.inviteId;
	}

	urlEncodedFormContentType() {
		return 'application/x-www-form-urlencoded; charset=UTF-8';
	}

	getSiteRoot() {
		return Joomla.getOptions('system.paths').root;
	}

	getQuestionsUrl() {
		return this.getSiteRoot() + "/index.php?option=com_tmt&task=test.getTestSectionsQuestions&format=json";
	}

	getQuestions(obj, cb) {
		let data = {
			'id': this.getTestId(),
			'invite_id': this.getInviteId(),
		};

		// Merge objects
		data = Object.assign({}, data, obj);

		let baseObj = new Base(this.getQuestionsUrl(), data);
		baseObj.setContentType(this.urlEncodedFormContentType());
		baseObj.post(cb);
	}

	getTimerUrl() {
		return this.getSiteRoot() + "/index.php?option=com_tmt&task=test.updateTimeSpent&format=json";
	}

	updateTimer(obj, cb) {
		let data = {
			'testId': this.getTestId(),
			'ltId': this.getInviteId(),
		};

		// Merge objects
		data = Object.assign({}, data, obj);

		let baseObj = new Base(this.getTimerUrl(), data);
		baseObj.setContentType(this.urlEncodedFormContentType());
		baseObj.post(cb);
	}

	getSaveAnswerUrl() {
		return this.getSiteRoot() + "/index.php?option=com_tmt&task=test.saveQuestionAnswer&tmpl=component&format=json";
	}

	saveAnswer(obj, cb) {
		let data = {
			'testId': this.getTestId(),
			'ltId': this.getInviteId(),
		};

		// Merge objects
		data = Object.assign({}, data, obj);

		let baseObj = new Base(this.getSaveAnswerUrl(), data);
		baseObj.setContentType(this.urlEncodedFormContentType());

		baseObj.post(cb);
	}

	getAttemptedQueCountUrl() {
		return this.getSiteRoot() + "/index.php?option=com_tmt&task=test.getTotalAttemptedQuestion&tmpl=component&format=json";
	}

	attemptedQueCount(cb) {
		let data = {
			'testId': this.getTestId(),
			'ltId': this.getInviteId(),
		};

		let baseObj = new Base(this.getAttemptedQueCountUrl(), data);
		baseObj.setContentType(this.urlEncodedFormContentType());

		baseObj.post(cb);
	}

	getFlagQuestionUrl() {
		return this.getSiteRoot() + "/index.php?option=com_tmt&task=test.flagQuestion&format=json";
	}

	flagQuestion(obj, cb) {
		let data = {
			'testId': this.getTestId(),
			'invite_id': this.getInviteId(),
		};

		// Merge objects
		data = Object.assign({}, data, obj);

		let baseObj = new Base(this.getFlagQuestionUrl(), data);
		baseObj.setContentType(this.urlEncodedFormContentType());

		baseObj.post(cb);
	}

	getSaveAllAnswersOnPageUrl() {
		return this.getSiteRoot() + "/index.php?option=com_tmt&task=test.saveEachPageQueAnswers&tmpl=component&format=json";
	}

	saveAllAnswersOnPage(obj, cb) {
		let baseObj = new Base(this.getSaveAllAnswersOnPageUrl(), obj);
		baseObj.setContentType(this.urlEncodedFormContentType());

		return baseObj.post(cb);
	}

	getSubmitTestUrl() {
		return this.getSiteRoot() + "/index.php?option=com_tmt&task=test.submitTest&tmpl=component&format=json";
	}

	submitTest(obj, cb) {
		let data = {
			'testId': this.getTestId(),
			'ltId': this.getInviteId(),
		};

		// Merge objects
		data = Object.assign({}, data, obj);

		let baseObj = new Base(this.getSubmitTestUrl(), data);
		baseObj.setContentType(this.urlEncodedFormContentType());

		return baseObj.post(cb);
	}

	getDeleteAnswerFile() {
		return this.getSiteRoot() + "/index.php?option=com_tmt&task=test.removeFileuploadAnswer&tmpl=component&format=json";
	}

	deleteAnswerFile(obj, cb) {
		let baseObj = new Base(this.getDeleteAnswerFile(), obj);
		baseObj.setContentType(this.urlEncodedFormContentType());

		return baseObj.post(cb);
	}
}

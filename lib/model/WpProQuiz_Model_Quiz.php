<?php
class WpProQuiz_Model_Quiz extends WpProQuiz_Model_Model {
	
	const QUIZ_RUN_ONCE_TYPE_ALL = 1;
	const QUIZ_RUN_ONCE_TYPE_ONLY_USER = 2;
	const QUIZ_RUN_ONCE_TYPE_ONLY_ANONYM = 3;
	
	const QUIZ_TOPLIST_TYPE_ALL = 1;
	const QUIZ_TOPLIST_TYPE_ONLY_USER = 2;
	const QUIZ_TOPLIST_TYPE_ONLY_ANONYM = 3;
	
	const QUIZ_TOPLIST_SORT_BEST = 1;
	const QUIZ_TOPLIST_SORT_NEW = 2;
	const QUIZ_TOPLIST_SORT_OLD = 3;
	
	const QUIZ_TOPLIST_SHOW_IN_NONE = 0;
	const QUIZ_TOPLIST_SHOW_IN_NORMAL = 1;
	const QUIZ_TOPLIST_SHOW_IN_BUTTON = 2;
	
	const QUIZ_MODUS_NORMAL = 0;
	const QUIZ_MODUS_BACK_BUTTON = 1;
	const QUIZ_MODUS_CHECK = 2;
	const QUIZ_MODUS_SINGLE = 3;
	
	const QUIZ_EMAIL_NOTE_NONE = 0;
	const QUIZ_EMAIL_NOTE_REG_USER = 1;
	const QUIZ_EMAIL_NOTE_ALL = 2;
	
	protected $_id;
	protected $_name;
	protected $_text;
	protected $_resultText;
	protected $_titleHidden;
	protected $_btnRestartQuizHidden = false;
	protected $_btnViewQuestionHidden = false;
	protected $_questionRandom;
	protected $_answerRandom;
	protected $_timeLimit = 0;
	protected $_statisticsOn;
	protected $_statisticsIpLock;
	protected $_resultGradeEnabled = false;
	protected $_showPoints = false;
	protected $_quizRunOnce = false;
	protected $_quizRunOnceType = 0;
	protected $_quizRunOnceCookie = false;
	protected $_quizRunOnceTime = 0;
	protected $_numberedAnswer = false;
	protected $_hideAnswerMessageBox = false;
	protected $_disabledAnswerMark = false;
	protected $_showMaxQuestion = false;
	protected $_showMaxQuestionValue = 1;
	protected $_showMaxQuestionPercent = false;
	
	//0.19
	protected $_toplistActivated = false;
	protected $_toplistDataAddPermissions = 1;
	protected $_toplistDataSort = 1;
	protected $_toplistDataAddMultiple = false;
	protected $_toplistDataAddBlock = 1;
	protected $_toplistDataShowLimit = 1;
	protected $_toplistDataShowQuizResult = false;
	protected $_toplistDataShowIn = 0;
	protected $_toplistDataCaptcha = false;
	
	protected $_toplistData = array();
	
	protected $_showAverageResult = false;
	
	protected $_prerequisite = false;
	
	//0.22
	protected $_toplistDataAddAutomatic = false;
	protected $_quizModus = 0;
	protected $_showReviewQuestion = false;
	protected $_quizSummaryHide = false;
	protected $_skipQuestionDisabled = false;
	protected $_emailNotification = 0;
	
	//0.24
	protected $_userEmailNotification = false;
	protected $_showCategoryScore = false;
	protected $_hideResultCorrectQuestion = false;
	protected $_hideResultQuizTime = false;
	protected $_hideResultPoints = false;
	
	public function getId() {
		return $this->_id;
	}
	
	public function setId($id) {
		$this->_id = $id;
		return $this;
	}
	
	public function getName() {
		return $this->_name;
	}
	
	public function setName($name) {
		$this->_name = $name;
		return $this;
	}

	public function setText($text) {
		$this->_text = $text;
		return $this;
	}
	
	public function getText() {
		return $this->_text;
	}
	
	public function setResultText($_resultText) {
		$this->_resultText = $_resultText;
		return $this;
	}
	
	public function getResultText() {
		return $this->_resultText;
	}
	
	public function setTitleHidden($_titleHidden) {
		$this->_titleHidden = (bool)$_titleHidden;
		return $this;
	}
	
	public function isTitleHidden() {
		return $this->_titleHidden;
	}
	
	public function setQuestionRandom($_questionRandom) {
		$this->_questionRandom = (bool)$_questionRandom;
		return $this;
	}
	
	public function isQuestionRandom() {
		return $this->_questionRandom;
	}

	public function setAnswerRandom($_answerRandom) {
		$this->_answerRandom = (bool)$_answerRandom;
		return $this;
	}
	
	public function isAnswerRandom() {
		return $this->_answerRandom;
	}
	
	public function setTimeLimit($_timeLimit) {
		$this->_timeLimit = $_timeLimit;
		return $this;
	}
	
	public function getTimeLimit() {
		return $this->_timeLimit;
	}
	
	public function setStatisticsOn($_statisticsOn) {
		$this->_statisticsOn = (bool)$_statisticsOn;
		return $this;
	}
	
	public function isStatisticsOn() {
		return $this->_statisticsOn;
	}
	
	public function setStatisticsIpLock($_statisticsIpLock) {
		$this->_statisticsIpLock = (int)$_statisticsIpLock;
		return $this;
	}
	
	public function getStatisticsIpLock() {
		return $this->_statisticsIpLock;
	}
	
	public function setResultGradeEnabled($_resultGradeEnabled) {
		$this->_resultGradeEnabled = (bool)$_resultGradeEnabled;
		return $this;
	}
	
	public function isResultGradeEnabled() {
		return $this->_resultGradeEnabled;
	}
	
	public function setShowPoints($_showPoints) {
		$this->_showPoints = (bool)$_showPoints;
		return $this;
	}
	
	public function isShowPoints() {
		return $this->_showPoints;
	}
	
	public function fetchSumQuestionPoints() {
		$m = new WpProQuiz_Model_QuizMapper();
		
		return $m->sumQuestionPoints($this->_id);
	}
	
	public function fetchCountQuestions() {
		$m = new WpProQuiz_Model_QuizMapper();
	
		return $m->countQuestion($this->_id);
	}
	
	public function setBtnRestartQuizHidden($_btnRestartQuizHidden) {
		$this->_btnRestartQuizHidden = (bool)$_btnRestartQuizHidden;
		return $this;
	}
	
	public function isBtnRestartQuizHidden() {
		return $this->_btnRestartQuizHidden;
	}
	
	public function setBtnViewQuestionHidden($_btnViewQuestionHidden) {
		$this->_btnViewQuestionHidden = (bool)$_btnViewQuestionHidden;
		return $this;
	}
	
	public function isBtnViewQuestionHidden() {
		return $this->_btnViewQuestionHidden;
	}
	
	public function setQuizRunOnce($_quizRunOnce) {
		$this->_quizRunOnce = (bool)$_quizRunOnce;
		return $this;
	}
	
	public function isQuizRunOnce() {
		return $this->_quizRunOnce;
	}
	
	public function setQuizRunOnceCookie($_quizRunOnceCookie) {
		$this->_quizRunOnceCookie = (bool)$_quizRunOnceCookie;
		return $this;
	}
	
	public function isQuizRunOnceCookie() {
		return $this->_quizRunOnceCookie;
	}
	
	public function setQuizRunOnceType($_quizRunOnceType) {
		$this->_quizRunOnceType = (int)$_quizRunOnceType;
		return $this;
	}
	
	public function getQuizRunOnceType() {
		return $this->_quizRunOnceType;
	}
	
	public function setQuizRunOnceTime($_quizRunOnceTime) {
		$this->_quizRunOnceTime = (int)$_quizRunOnceTime;
		return $this;
	}
	
	public function getQuizRunOnceTime() {
		return $this->_quizRunOnceTime;
	}
	
	public function setNumberedAnswer($_numberedAnswer) {
		$this->_numberedAnswer = (bool)$_numberedAnswer;
		return $this;
	}
	
	public function isNumberedAnswer() {
		return $this->_numberedAnswer;
	}
	
	public function setHideAnswerMessageBox($_hideAnswerMessageBox) {
		$this->_hideAnswerMessageBox = (bool)$_hideAnswerMessageBox;
		return $this;
	}
	
	public function isHideAnswerMessageBox() {
		return $this->_hideAnswerMessageBox;
	}
	
	public function setDisabledAnswerMark($_disabledAnswerMark) {
		$this->_disabledAnswerMark = (bool)$_disabledAnswerMark;
		return $this;
	}
	
	public function isDisabledAnswerMark() {
		return $this->_disabledAnswerMark;
	}
	
	public function setShowMaxQuestion($_showMaxQuestion) {
		$this->_showMaxQuestion = (bool)$_showMaxQuestion;
		return $this;
	}
	
	public function isShowMaxQuestion() {
		return $this->_showMaxQuestion;
	}
	
	public function setShowMaxQuestionValue($_showMaxQuestionValue) {
		$this->_showMaxQuestionValue = (int)$_showMaxQuestionValue;
		return $this;
	}
	
	public function getShowMaxQuestionValue() {
		return $this->_showMaxQuestionValue;
	}
	
	public function setShowMaxQuestionPercent($_showMaxQuestionPercent) {
		$this->_showMaxQuestionPercent = (bool)$_showMaxQuestionPercent;
		return $this;
	}
	
	public function isShowMaxQuestionPercent() {
		return $this->_showMaxQuestionPercent;
	}
	
	public function setToplistActivated($_toplistActivated) {
		$this->_toplistActivated = (bool)$_toplistActivated;
		return $this;
	}
	
	public function isToplistActivated() {
		return $this->_toplistActivated;
	}
	
	public function setToplistDataAddPermissions($_toplistDataAddPermissions) {
		$this->_toplistDataAddPermissions = (int)$_toplistDataAddPermissions;
		return $this;
	}
	
	public function getToplistDataAddPermissions() {
		return $this->_toplistDataAddPermissions;
	}
	
	public function setToplistDataSort($_toplistDataSort) {
		$this->_toplistDataSort = (int)$_toplistDataSort;
		return $this;
	}
	
	public function getToplistDataSort() {
		return $this->_toplistDataSort;
	}
	
	public function setToplistDataAddMultiple($_toplistDataAddMultiple) {
		$this->_toplistDataAddMultiple = (bool)$_toplistDataAddMultiple;
		return $this;
	}
	
	public function isToplistDataAddMultiple() {
		return $this->_toplistDataAddMultiple;
	}
	
	public function setToplistDataAddBlock($_toplistDataAddBlock) {
		$this->_toplistDataAddBlock = (int)$_toplistDataAddBlock;
		return $this;
	}
	
	public function getToplistDataAddBlock() {
		return $this->_toplistDataAddBlock;
	}
	
	public function setToplistDataShowLimit($_toplistDataShowLimit) {
		$this->_toplistDataShowLimit = (int)$_toplistDataShowLimit;
		return $this;
	}
	
	public function getToplistDataShowLimit() {
		return $this->_toplistDataShowLimit;
	}
	
	public function setToplistData($_toplistData) {
		if(!empty($_toplistData)) {
			$d = unserialize($_toplistData);
			
			if($d !== false) {
				$this->setModelData($d);			
			}	
		}
			
		return $this;
	}
	
	public function getToplistData() {
		
		$a = array(
			'toplistDataAddPermissions' => $this->getToplistDataAddPermissions(),
			'toplistDataSort' => $this->getToplistDataSort(),
			'toplistDataAddMultiple' => $this->isToplistDataAddMultiple(),
			'toplistDataAddBlock' => $this->getToplistDataAddBlock(),
			'toplistDataShowLimit' => $this->getToplistDataShowLimit(),
			'toplistDataShowIn' => $this->getToplistDataShowIn(),
			'toplistDataCaptcha' => $this->isToplistDataCaptcha(),
			'toplistDataAddAutomatic' => $this->isToplistDataAddAutomatic()
		);
		
		return serialize($a);
	}
	
	public function setToplistDataShowIn($_toplistDataShowIn) {
		$this->_toplistDataShowIn = (int)$_toplistDataShowIn;
		return $this;
	}
	
	public function getToplistDataShowIn() {
		return $this->_toplistDataShowIn;
	}
	
	public function setToplistDataCaptcha($_toplistDataCaptcha) {
		$this->_toplistDataCaptcha = (bool)$_toplistDataCaptcha;
		return $this;
	}
	
	public function isToplistDataCaptcha() {
		return $this->_toplistDataCaptcha;
	}
	
	public function setShowAverageResult($_showAverageResult) {
		$this->_showAverageResult = (bool)$_showAverageResult;
		return $this;
	}
	
	public function isShowAverageResult() {
		return $this->_showAverageResult;
	}
	
	public function setPrerequisite($_prerequisite) {
		$this->_prerequisite = (bool)$_prerequisite;
		return $this;
	}
	
	public function isPrerequisite() {
		return $this->_prerequisite;
	}
	
	public function setToplistDataAddAutomatic($_toplistDataAddAutomatic) {
		$this->_toplistDataAddAutomatic = (bool)$_toplistDataAddAutomatic;
		return $this;
	}
	
	public function isToplistDataAddAutomatic() {
		return $this->_toplistDataAddAutomatic;
	}
	
	public function setQuizModus($_quizModus) {
		$this->_quizModus = (int)$_quizModus;
		return $this;
	}
	
	public function getQuizModus() {
		return $this->_quizModus;
	}
	
	public function setShowReviewQuestion($_showReviewQuestion) {
		$this->_showReviewQuestion = (bool)$_showReviewQuestion;
		return $this;
	}
	
	public function isShowReviewQuestion() {
		return $this->_showReviewQuestion;
	}
	
	public function setQuizSummaryHide($_quizSummaryHide) {
		$this->_quizSummaryHide = (bool)$_quizSummaryHide;
		return $this;
	}
	
	public function isQuizSummaryHide() {
		return $this->_quizSummaryHide;
	}
	
	public function setSkipQuestionDisabled($_skipQuestion) {
		$this->_skipQuestionDisabled = (bool)$_skipQuestion;
		return $this;
	}
	
	public function isSkipQuestionDisabled() {
		return $this->_skipQuestionDisabled;
	}
	
	public function setEmailNotification($_emailNotification) {
		$this->_emailNotification = (int)$_emailNotification;
		return $this;
	}
	
	public function getEmailNotification() {
		return $this->_emailNotification;
	}
	
	public function setUserEmailNotification($_userEmailNotification) {
		$this->_userEmailNotification = (bool)$_userEmailNotification;
		return $this;
	}
	
	public function isUserEmailNotification() {
		return $this->_userEmailNotification;
	}
	
	public function setShowCategoryScore($_showCategoryScore) {
		$this->_showCategoryScore = (bool)$_showCategoryScore;
		return $this;
	}
	
	public function isShowCategoryScore() {
		return $this->_showCategoryScore;
	}
	
	public function setHideResultCorrectQuestion($_hideResultCorrectQuestion) {
		$this->_hideResultCorrectQuestion = (bool)$_hideResultCorrectQuestion;
		return $this;
	}
	
	public function isHideResultCorrectQuestion() {
		return $this->_hideResultCorrectQuestion;
	}
	
	public function setHideResultQuizTime($_hideResultQuizTime) {
		$this->_hideResultQuizTime = (bool)$_hideResultQuizTime;
		return $this;
	}
	
	public function isHideResultQuizTime() {
		return $this->_hideResultQuizTime;
	}
	
	public function setHideResultPoints($_hideResultPoints) {
		$this->_hideResultPoints = (bool)$_hideResultPoints;
		return $this;
	}
	
	public function isHideResultPoints() {
		return $this->_hideResultPoints;
	}
}
<?php
class WpProQuiz_Helper_ExportXml {
	
	public function export($ids) {
		
		$dom = new DOMDocument('1.0', 'utf-8');
		
		$root = $dom->createElement('wpProQuiz');
		
		$dom->appendChild($root);
		
		$header = $dom->createElement('header');
		$header->setAttribute('version', WPPROQUIZ_VERSION);
		$header->setAttribute('exportVersion', 1);
		
		$root->appendChild($header);
		$data = $dom->createElement('data');
		
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		$questionMapper = new WpProQuiz_Model_QuestionMapper();
		$formMapper = new WpProQuiz_Model_FormMapper();

		foreach($ids as $id) {
			$quizModel = $quizMapper->fetch($id);
			
			if($quizModel->getId() <= 0)
				continue;
			
			$questionModel = $questionMapper->fetchAll($quizModel->getId());
			$forms = array();
			
			if($quizModel->isFormActivated())
				$forms = $formMapper->fetch($quizModel->getId());
			
			$quizElement = $this->getQuizElement($dom, $quizModel, $forms);
			
			$quizElement->appendChild($questionsElement = $dom->createElement('questions'));
			
			foreach($questionModel as $model) {
				$questionElement = $this->createQuestionElement($dom, $model);
				$questionsElement->appendChild($questionElement);
			}
			
			$data->appendChild($quizElement);
		}
		
		$root->appendChild($data);
		
		return $dom->saveXML();
	}
	
	/**
	 * @param DOMDocument $dom
	 * @param WpProQuiz_Model_Quiz $quiz
	 */
	private function getQuizElement($dom, $quiz, $forms) {
		$quizElement = $dom->createElement('quiz');
		
		$title = $dom->createElement('title');
		$title->appendChild($dom->createCDATASection($quiz->getName()));
		$title->setAttribute('titleHidden', $this->booleanToTrueOrFalse($quiz->isTitleHidden()));
		$quizElement->appendChild($title);
		
		$quizElement->appendChild($text = $dom->createElement('text'));
		$text->appendChild($dom->createCDATASection($quiz->getText()));
		
		if(is_array($quiz->getResultText())) {
			$resultArray = $quiz->getResultText();
			$result = $dom->createElement('resultText');
			$result->setAttribute('gradeEnabled', $this->booleanToTrueOrFalse($quiz->isResultGradeEnabled()));
			
			for($i = 0; $i < count($resultArray); $i++) {
				$r = $dom->createElement('text');
				$r->appendChild($dom->createCDATASection($resultArray['text'][$i]));
				$r->setAttribute('prozent', $resultArray['prozent'][$i]);
				
				$result->appendChild($r);
			}
			
			$quizElement->appendChild($result);
		} else {
			$result = $dom->createElement('resultText');
			$result->setAttribute('gradeEnabled', $this->booleanToTrueOrFalse($quiz->isResultGradeEnabled()));
			$result->appendChild($dom->createCDATASection($quiz->getResultText()));
			
			$quizElement->appendChild($result);
		}
		
		
		$quizElement->appendChild($dom->createElement('btnRestartQuizHidden', $this->booleanToTrueOrFalse($quiz->isBtnRestartQuizHidden())));
		$quizElement->appendChild($dom->createElement('btnViewQuestionHidden', $this->booleanToTrueOrFalse($quiz->isBtnViewQuestionHidden())));
		$quizElement->appendChild($dom->createElement('questionRandom', $this->booleanToTrueOrFalse($quiz->isQuestionRandom())));
		$quizElement->appendChild($dom->createElement('answerRandom', $this->booleanToTrueOrFalse($quiz->isAnswerRandom())));
		$quizElement->appendChild($dom->createElement('timeLimit', $quiz->getTimeLimit()));
		$quizElement->appendChild($dom->createElement('showPoints', $this->booleanToTrueOrFalse($quiz->isShowPoints())));
		
		$statistic = $dom->createElement('statistic');
		$statistic->setAttribute('activated', $this->booleanToTrueOrFalse($quiz->isStatisticsOn()));
		$statistic->setAttribute('ipLock', $quiz->getStatisticsIpLock());
		$quizElement->appendChild($statistic);
		
		
		$quizElement->appendChild($quizRunOnce = $dom->createElement('quizRunOnce', $this->booleanToTrueOrFalse($quiz->isQuizRunOnce())));
		$quizRunOnce->setAttribute('type', $quiz->getQuizRunOnceType());
		$quizRunOnce->setAttribute('cookie', $this->booleanToTrueOrFalse($quiz->isQuizRunOnceCookie()));
		$quizRunOnce->setAttribute('time', $quiz->getQuizRunOnceTime());
		
		$quizElement->appendChild($dom->createElement('numberedAnswer', $this->booleanToTrueOrFalse($quiz->isNumberedAnswer())));
		$quizElement->appendChild($dom->createElement('hideAnswerMessageBox', $this->booleanToTrueOrFalse($quiz->isHideAnswerMessageBox())));
		$quizElement->appendChild($dom->createElement('disabledAnswerMark', $this->booleanToTrueOrFalse($quiz->isDisabledAnswerMark())));
		
		$quizElement->appendChild($showMaxQuestion = $dom->createElement('showMaxQuestion', $this->booleanToTrueOrFalse($quiz->isShowMaxQuestion())));
		$showMaxQuestion->setAttribute('showMaxQuestionValue', $quiz->getShowMaxQuestionValue());
		$showMaxQuestion->setAttribute('showMaxQuestionPercent', $this->booleanToTrueOrFalse($quiz->isShowMaxQuestionPercent()));
		
		
		//Toplist
		$toplist = $dom->createElement('toplist');
		$toplist->setAttribute('activated', $this->booleanToTrueOrFalse($quiz->isToplistActivated()));
		$toplist->appendChild($dom->createElement('toplistDataAddPermissions', $quiz->getToplistDataAddPermissions()));
		$toplist->appendChild($dom->createElement('toplistDataSort', $quiz->getToplistDataSort()));
		$toplist->appendChild($dom->createElement('toplistDataAddMultiple', $this->booleanToTrueOrFalse($quiz->isToplistDataAddMultiple())));
		$toplist->appendChild($dom->createElement('toplistDataAddBlock', $quiz->getToplistDataAddBlock()));
		$toplist->appendChild($dom->createElement('toplistDataShowLimit', $quiz->getToplistDataShowLimit()));
		$toplist->appendChild($dom->createElement('toplistDataShowIn', $quiz->getToplistDataShowIn()));
		$toplist->appendChild($dom->createElement('toplistDataCaptcha', $this->booleanToTrueOrFalse($quiz->isToplistDataCaptcha())));
		$toplist->appendChild($dom->createElement('toplistDataAddAutomatic', $this->booleanToTrueOrFalse($quiz->isToplistDataAddAutomatic())));
		
		$quizElement->appendChild($toplist);
		
		$quizElement->appendChild($dom->createElement('showAverageResult', $this->booleanToTrueOrFalse($quiz->isShowAverageResult())));
		$quizElement->appendChild($dom->createElement('prerequisite', $this->booleanToTrueOrFalse($quiz->isPrerequisite())));
		$quizElement->appendChild($dom->createElement('showReviewQuestion', $this->booleanToTrueOrFalse($quiz->isShowReviewQuestion())));
		$quizElement->appendChild($dom->createElement('quizSummaryHide', $this->booleanToTrueOrFalse($quiz->isQuizSummaryHide())));
		$quizElement->appendChild($dom->createElement('skipQuestionDisabled', $this->booleanToTrueOrFalse($quiz->isSkipQuestionDisabled())));
		$quizElement->appendChild($dom->createElement('emailNotification', $quiz->getEmailNotification()));
		$quizElement->appendChild($dom->createElement('userEmailNotification', $this->booleanToTrueOrFalse($quiz->isUserEmailNotification())));
		$quizElement->appendChild($dom->createElement('showCategoryScore', $this->booleanToTrueOrFalse($quiz->isShowCategoryScore())));
		$quizElement->appendChild($dom->createElement('hideResultCorrectQuestion', $this->booleanToTrueOrFalse($quiz->isHideResultCorrectQuestion())));
		$quizElement->appendChild($dom->createElement('hideResultQuizTime', $this->booleanToTrueOrFalse($quiz->isHideResultQuizTime())));
		$quizElement->appendChild($dom->createElement('hideResultPoints', $this->booleanToTrueOrFalse($quiz->isHideResultPoints())));
		$quizElement->appendChild($dom->createElement('autostart', $this->booleanToTrueOrFalse($quiz->isAutostart())));
		$quizElement->appendChild($dom->createElement('forcingQuestionSolve', $this->booleanToTrueOrFalse($quiz->isForcingQuestionSolve())));
		$quizElement->appendChild($dom->createElement('hideQuestionPositionOverview', $this->booleanToTrueOrFalse($quiz->isHideQuestionPositionOverview())));
		$quizElement->appendChild($dom->createElement('hideQuestionNumbering', $this->booleanToTrueOrFalse($quiz->isHideQuestionNumbering())));
		
		//0.27
		$quizElement->appendChild($dom->createElement('sortCategories', $this->booleanToTrueOrFalse($quiz->isSortCategories())));
		$quizElement->appendChild($dom->createElement('showCategory', $this->booleanToTrueOrFalse($quiz->isShowCategory())));
		$quizModus = $dom->createElement('quizModus', $quiz->getQuizModus());
		$quizModus->setAttribute('questionsPerPage', $quiz->getQuestionsPerPage());
		$quizElement->appendChild($quizModus);
		
		$quizElement->appendChild($dom->createElement('startOnlyRegisteredUser', $this->booleanToTrueOrFalse($quiz->isStartOnlyRegisteredUser())));
		
		$formsElement = $dom->createElement('forms');
		$formsElement->setAttribute('activated', $this->booleanToTrueOrFalse($quiz->isFormActivated()));
		$formsElement->setAttribute('position', $quiz->getFormShowPosition());
		
		foreach($forms as $form) {
			/** @var WpProQuiz_Model_Form $form  **/
			
			$formElement = $dom->createElement('form');
			$formElement->setAttribute('type', $form->getType());
			$formElement->setAttribute('required', $this->booleanToTrueOrFalse($form->isRequired()));
			$formElement->setAttribute('fieldname', $form->getFieldname());
			
			if($form->getData() !== null) {
				$data = $form->getData();
				
				foreach($data as $d) {
					$formDataElement = $dom->createElement('formData', $d);
					$formElement->appendChild($formDataElement);
				}
			}
			
			$formsElement->appendChild($formElement);
		}
		
		$quizElement->appendChild($formsElement);
		
		return $quizElement;
	}
	
	/**
	 * @param DOMDocument $dom
	 * @param WpProQuiz_Model_Question $question
	 * @return DOMDocument
	 */
	private function createQuestionElement($dom, $question) {
		$qElement = $dom->createElement('question');
		$qElement->setAttribute('answerType', $question->getAnswerType());

		$qElement->appendChild($title = $dom->createElement('title'));
		$title->appendChild($dom->createCDATASection($question->getTitle()));
		
		$qElement->appendChild($dom->createElement('points', $question->getPoints()));
		
		$qElement->appendChild($questionText = $dom->createElement('questionText'));
		$questionText->appendChild($dom->createCDATASection($question->getQuestion()));
				
		$qElement->appendChild($correctMsg = $dom->createElement('correctMsg'));
		$correctMsg->appendChild($dom->createCDATASection($question->getCorrectMsg()));
		
		$qElement->appendChild($incorrectMsg = $dom->createElement('incorrectMsg'));
		$incorrectMsg->appendChild($dom->createCDATASection($question->getIncorrectMsg()));
		
		$qElement->appendChild($tipMsg = $dom->createElement('tipMsg'));
		$tipMsg->setAttribute('enabled', $this->booleanToTrueOrFalse($question->isTipEnabled()));
		$tipMsg->appendChild($dom->createCDATASection($question->getTipMsg()));
		
		$qElement->appendChild($dom->createElement('category', $question->getCategoryName()));
		
		$qElement->appendChild($dom->createElement('correctSameText', $this->booleanToTrueOrFalse($question->isCorrectSameText())));
		$qElement->appendChild($dom->createElement('showPointsInBox', $this->booleanToTrueOrFalse($question->isShowPointsInBox())));
		$qElement->appendChild($dom->createElement('answerPointsActivated', $this->booleanToTrueOrFalse($question->isAnswerPointsActivated())));
		$qElement->appendChild($dom->createElement('answerPointsDiffModusActivated', $this->booleanToTrueOrFalse($question->isAnswerPointsDiffModusActivated())));
		$qElement->appendChild($dom->createElement('disableCorrect', $this->booleanToTrueOrFalse($question->isDisableCorrect())));
		
		$answersElement = $dom->createElement('answers');
		
		$answerData = $question->getAnswerData();
		
		if(is_array($answerData)) {
			foreach ($answerData as $answer) {
				$answerElement = $dom->createElement('answer');
				$answerElement->setAttribute('points', $answer->getPoints());
				$answerElement->setAttribute('correct', $this->booleanToTrueOrFalse($answer->isCorrect()));
				
				$answerText = $dom->createElement('answerText');
				$answerText->setAttribute('html', $this->booleanToTrueOrFalse($answer->isHtml()));
				$answerText->appendChild($dom->createCDATASection($answer->getAnswer()));
				
				$answerElement->appendChild($answerText);
				
				$sortText = $dom->createElement('stortText');
				$sortText->setAttribute('html', $this->booleanToTrueOrFalse($answer->isSortStringHtml()));
				$sortText->appendChild($dom->createCDATASection($answer->getSortString()));
				
				$answerElement->appendChild($sortText);
				
				$answersElement->appendChild($answerElement);
			}
		}
		
		$qElement->appendChild($answersElement);
		
		return $qElement;		
	}
	
	private function booleanToTrueOrFalse($v) {
		return $v ? 'true' : 'false';
	}
}
<?php
class WpProQuiz_Controller_Statistics extends WpProQuiz_Controller_Controller {
	
	public function route() {
		$action = (isset($_GET['action'])) ? $_GET['action'] : 'show';
		
		switch ($action) {
			case 'reset':
				$this->reset($_GET['id']);
			case 'show':
			default:
				$this->show($_GET['id']);
		}
	}
	
	private function reset($quizId) {
		$questionMapper = new WpProQuiz_Model_QuestionMapper();
		$questionMapper->resetStatistics($quizId);
	}
	
	private function show($quizId) {
		$view = new WpProQuiz_View_Statistics();
		$questionMapper = new WpProQuiz_Model_QuestionMapper();
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		
		$view->quiz = $quizMapper->fetch($quizId);
		$view->question = $questionMapper->fetchAll($quizId);

		$view->show();
	}
	
	public function save() {
		$quizId = $this->_post['quizId'];
		$array = $this->_post['results'];
		$lockIp = $this->getIp();
		
		if($lockIp === false)
			exit;
		
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		$quiz = $quizMapper->fetch($quizId);
		
		if(!$quiz->isStatisticsOn())		
			exit;
		
		if($quiz->getStatisticsIpLock() > 0) {		
			$lockMapper = new WpProQuiz_Model_LockMapper();
			
			$lockMapper->deleteOldLock($quiz, time());

			if($lockMapper->isLock($quizId, $lockIp))
				exit;
			
			$lock = new WpProQuiz_Model_Lock();
			$lock->setQuizId($quizId)
				->setLockIp($lockIp)
				->setLockDate(time());
			
			$lockMapper->insert($lock);
		}
		
		$questionMapper = new WpProQuiz_Model_QuestionMapper();
		$questionMapper->updateStatistics($quizId, $array);
		
		exit;
	}
	
	private function getIp() {
		return filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
	}
}
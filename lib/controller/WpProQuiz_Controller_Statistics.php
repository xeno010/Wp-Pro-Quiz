<?php
class WpProQuiz_Controller_Statistics extends WpProQuiz_Controller_Controller {
	
	public function route() {
		$action = (isset($_GET['action'])) ? $_GET['action'] : 'show';
		
		switch ($action) {
			case 'load_statistics':
				$this->loadStatistics($_GET['id']);
				break;
			case 'reset':
				$this->reset($_GET['id']);
				break;
			case 'show':
			default:
				$this->show($_GET['id']);
		}
	}
	
	private function loadStatistics($quizId) {
		
		if(!current_user_can('wpProQuiz_show_statistics')) {
			echo json_encode(array());
			exit;
		}
		
		if(isset($this->_post['overview']) && $this->_post['overview'] == true) {
			$this->loadStatisticsOverview($quizId);
			
			exit;
		}
		
		$questionMapper = new WpProQuiz_Model_StatisticMapper();
		$questions = $questionMapper->fetchAll($quizId, $this->_post['userId']);
		
		$data = array(	'global' => array('cCorrect' => 0, 'cIncorrect' => 0, 'pCorrect' => 0, 'pIncorrect' => 0, 'cTip' => 0, 'cCorrectAnswerPoints' => 0), 
						'items' => array());
		
		$data['clear'] = $data['global'];

		foreach($questions as $question) {
			$sum = $question->getCorrectCount() + $question->getIncorrectCount();
			
			$data['global']['cCorrect'] += $question->getCorrectCount();
			$data['global']['cIncorrect'] += $question->getIncorrectCount();
			$data['global']['cTip'] += $question->getHintCount();
			$data['global']['cCorrectAnswerPoints'] += $question->getCorrectAnswerCount();

			$data['items'][] = array(
				'id' => $question->getQuestionId(),
				'cCorrect' => $question->getCorrectCount(),
				'cIncorrect' => $question->getIncorrectCount(),
				'cTip' => $question->getHintCount(),
				'pCorrect' => round((100 * $question->getCorrectCount() / $sum), 2),
				'pIncorrect' => round((100 * $question->getIncorrectCount() / $sum), 2),
				'cCorrectAnswerPoints' => $question->getCorrectAnswerCount()
			);
		}
		
		$sum = $data['global']['cCorrect'] + $data['global']['cIncorrect'];
		
		if($sum > 0) {
			$data['global']['pCorrect'] = round((100 * $data['global']['cCorrect'] / $sum), 2);
			$data['global']['pIncorrect'] = round((100 * $data['global']['cIncorrect'] / $sum), 2);
		}
				
		echo json_encode($data);
		
		exit;
	}
	
	private function loadStatisticsOverview($quizId) {
		$m = new WpProQuiz_Model_StatisticMapper();
	
		$page = (isset($this->_post['page']) && $this->_post['page'] > 0) ? $this->_post['page'] : 1;
		$limit = $this->_post['pageLimit'];
		$start = $limit * ($page - 1);
		
		$s = $m->fetchOverview($quizId, (bool)$this->_post['onlyCompleted'], $start, $limit);
		$data = array('items' => array());
		
		foreach($s as $r) {
			
			$sum = $r->getCorrectCount() + $r->getIncorrectCount();
			
			if($sum === 0) {
				$data['items'][] = array(
					'userId' => $r->getUserId(),
					'userName' => $r->getUserName(),
					'completed' => false
				);
				
				continue;
			}
			
			$data['items'][] = array(
				'userId' => $r->getUserId(),
				'userName' => $r->getUserName(),
				'cPoints' => $r->getPoints(),
				'totalPoints' => $r->getTotalPoints(),
				'cCorrect' => $r->getCorrectCount(),
				'cIncorrect' => $r->getIncorrectCount(),
				'cTip' => $r->getHintCount(),
				'pCorrect' => round((100 * $r->getCorrectCount() / $sum), 2),
				'pIncorrect' => round((100 * $r->getIncorrectCount() / $sum), 2),
				'completed' => true
			);	
		}
		
		if(isset($this->_post['generatePageNav']) && $this->_post['generatePageNav']) {
			$count = $m->countOverview($quizId, (bool)$this->_post['onlyCompleted']);
			$data['page'] = $count > 0 ? $count : 1; 
		}
		
		echo json_encode($data);
	}
	
	private function reset($quizId) {
		
		if(!current_user_can('wpProQuiz_reset_statistics')) {
			exit;
		}
		
		$statisticMapper = new WpProQuiz_Model_StatisticMapper();
		
		if(isset($this->_post['complete']) && $this->_post['complete']) {
			$statisticMapper->deleteByQuizId($quizId);			
		} else {
			$statisticMapper->delete($quizId, $this->_post['userId']);
		}
		
		exit;
	}
	
	private function show($quizId) {
		
		if(!current_user_can('wpProQuiz_show_statistics')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		
		$view = new WpProQuiz_View_Statistics();
		$questionMapper = new WpProQuiz_Model_QuestionMapper();
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		
		$view->quiz = $quizMapper->fetch($quizId);
		$view->question = $questionMapper->fetchAll($quizId);
		
		if(has_action('pre_user_query', 'ure_exclude_administrators')) {
			remove_action('pre_user_query', 'ure_exclude_administrators');
						
			$users = get_users(array('fields' => array('ID','user_login','display_name')));
			
			add_action('pre_user_query', 'ure_exclude_administrators');
			
		} else {
			$users = get_users(array('fields' => array('ID','user_login','display_name')));
		}
		
		$view->users = $users;
		$view->show();
	}
	
	public function save() {
		$quizId = $this->_post['quizId'];
		$array = $this->_post['results'];
		$lockIp = $this->getIp();
		
		if($lockIp === false)
			return false;
		
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		$quiz = $quizMapper->fetch($quizId);
		
		if(!$quiz->isStatisticsOn())		
			return false;
		
		if($quiz->getStatisticsIpLock() > 0) {		
			$lockMapper = new WpProQuiz_Model_LockMapper();
			$lockTime = $quiz->getStatisticsIpLock() * 60;
			
			
			$lockMapper->deleteOldLock($lockTime, $quiz->getId(), time(), WpProQuiz_Model_Lock::TYPE_STATISTIC);

			if($lockMapper->isLock($quizId, $lockIp, get_current_user_id(), WpProQuiz_Model_Lock::TYPE_STATISTIC))
				return false;
			
			$lock = new WpProQuiz_Model_Lock();
			$lock	->setQuizId($quizId)
					->setLockIp($lockIp)
					->setUserId(get_current_user_id())
					->setLockType(WpProQuiz_Model_Lock::TYPE_STATISTIC)
					->setLockDate(time());
			
			$lockMapper->insert($lock);
		}
		
		$questionMapper = new WpProQuiz_Model_StatisticMapper();
		$questionMapper->save($quizId, get_current_user_id(), $array);
		
		return true;
	}
	
	private function getIp() {
		if(get_current_user_id() > 0) 
			return '0';
		else
			return filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
	}
}
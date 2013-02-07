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
	
	public function getAverageResult($quizId) {
		$statisticMapper = new WpProQuiz_Model_StatisticMapper();
		$quizMapper = new WpProQuiz_Model_QuizMapper(); 
		
		$r = $statisticMapper->fetchByQuiz($quizId);
		$maxPoints = $quizMapper->sumQuestionPoints($quizId);
		$sumQuestion = $quizMapper->countQuestion($quizId);
		
		if($r['count'] > 0) {
			return round((100 * $r['points'] / ($r['count'] * $maxPoints / $sumQuestion)), 2);
		}
		
		return 0;
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
		
		$statisticMapper = new WpProQuiz_Model_StatisticMapper();
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		$statistics = $statisticMapper->fetchAll($quizId, $this->_post['userId']);
		
		$data = array(	'global' => array('cCorrect' => 0, 'cIncorrect' => 0, 'pCorrect' => 0, 'pIncorrect' => 0, 'cTip' => 0, 'cPoints' => 0, 'result' => 0), 
						'items' => array());
		
		$data['clear'] = $data['global'];
		
		$maxPoints = $quizMapper->sumQuestionPoints($quizId);
		$sumQuestion = $quizMapper->countQuestion($quizId);

		foreach($statistics as $statistic) {
			$sum = $statistic->getCorrectCount() + $statistic->getIncorrectCount();
			
			$data['global']['cCorrect'] += $statistic->getCorrectCount();
			$data['global']['cIncorrect'] += $statistic->getIncorrectCount();
			$data['global']['cTip'] += $statistic->getHintCount();
			$data['global']['cPoints'] += $statistic->getPoints();

			$data['items'][] = array(
				'id' => $statistic->getQuestionId(),
				'cCorrect' => $statistic->getCorrectCount(),
				'cIncorrect' => $statistic->getIncorrectCount(),
				'cTip' => $statistic->getHintCount(),
				'pCorrect' => round((100 * $statistic->getCorrectCount() / $sum), 2),
				'pIncorrect' => round((100 * $statistic->getIncorrectCount() / $sum), 2),
				'cPoints' => $statistic->getPoints()
			);
		}
		
		$sum = $data['global']['cCorrect'] + $data['global']['cIncorrect'];
		
		if($sum > 0) {
			$data['global']['pCorrect'] = round((100 * $data['global']['cCorrect'] / $sum), 2);
			$data['global']['pIncorrect'] = round((100 * $data['global']['cIncorrect'] / $sum), 2);
			$data['global']['result'] = round((100 * $data['global']['cPoints'] / ($sum * $maxPoints / $sumQuestion)), 2);
		}
				
		echo json_encode($data);
		
		exit;
	}
	
	private function loadStatisticsOverview($quizId) {
		$m = new WpProQuiz_Model_StatisticMapper();
		$quizMapper = new WpProQuiz_Model_QuizMapper();
	
		$page = (isset($this->_post['page']) && $this->_post['page'] > 0) ? $this->_post['page'] : 1;
		$limit = $this->_post['pageLimit'];
		$start = $limit * ($page - 1);
		
		$s = $m->fetchOverview($quizId, (bool)$this->_post['onlyCompleted'], $start, $limit);
		$data = array('items' => array());
		
		$maxPoints = $quizMapper->sumQuestionPoints($quizId);
		$sumQuestion = $quizMapper->countQuestion($quizId);
		
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
				'result' => round((100 * $r->getPoints() / ($sum * $maxPoints / $sumQuestion)), 2),
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
		$userId = get_current_user_id();
		
		if($lockIp === false)
			return false;
		
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		$quiz = $quizMapper->fetch($quizId);
		
		if(!$quiz->isStatisticsOn() || $quiz->isShowMaxQuestion())		
			return false;
		
		$values = $this->makeDataList($quizId, $array, $userId);
		
		if($values === false)
			return;
		
		if($quiz->getStatisticsIpLock() > 0) {
			$lockMapper = new WpProQuiz_Model_LockMapper();
			$lockTime = $quiz->getStatisticsIpLock() * 60;
			
			$lockMapper->deleteOldLock($lockTime, $quiz->getId(), time(), WpProQuiz_Model_Lock::TYPE_STATISTIC);

			if($lockMapper->isLock($quizId, $lockIp, $userId, WpProQuiz_Model_Lock::TYPE_STATISTIC))
				return false;
			
			$lock = new WpProQuiz_Model_Lock();
			$lock	->setQuizId($quizId)
					->setLockIp($lockIp)
					->setUserId($userId)
					->setLockType(WpProQuiz_Model_Lock::TYPE_STATISTIC)
					->setLockDate(time());
			
			$lockMapper->insert($lock);
		}
		
		$questionMapper = new WpProQuiz_Model_StatisticMapper();
		$questionMapper->save($values);
		
		return true;
	}
	
	private function makeDataList($quizId, $array, $userId) {
		
		$questionMapper = new WpProQuiz_Model_QuestionMapper();
		
		$question = $questionMapper->fetchAllList($quizId, array('id', 'points'));

		$ids = array();
		
		foreach($question as $q) {
			$ids[] = $q['id'];
			$v = $array[$q['id']];
			
			if(!isset($v) || $v['points'] > $q['points'] || $v['points'] < 0) {
				return false;
			}
		}
		
		unset($array['comp']);
		
		$ak = array_keys($array);
		
		if(array_diff($ids, $ak) !== array_diff($ak, $ids))
			return false;
		
		$values = array();
		
		foreach($array as $k => $v) {
			$s = new WpProQuiz_Model_Statistic();
			$s->setQuizId($quizId);
			$s->setQuestionId($k);
			$s->setUserId($userId);
			$s->setHintCount(isset($v['tip']) ? 1 : 0);
			$s->setCorrectCount($v['correct'] ? 1 : 0);
			$s->setIncorrectCount($v['correct'] ? 0 : 1);
			$s->setPoints($v['points']);	
		
			$values[] = $s;
		}
		
		return $values;
	}
	
	private function getIp() {
		if(get_current_user_id() > 0) 
			return '0';
		else
			return filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
	}
}
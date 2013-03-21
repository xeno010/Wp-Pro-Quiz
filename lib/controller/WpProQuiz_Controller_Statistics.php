<?php
class WpProQuiz_Controller_Statistics extends WpProQuiz_Controller_Controller {
	
	public function route() {
		$action = (isset($_GET['action'])) ? $_GET['action'] : 'show';
		
		switch ($action) {
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
	
	private function show($quizId) {
		
		if(!current_user_can('wpProQuiz_show_statistics')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		
		$view = new WpProQuiz_View_Statistics();
		$questionMapper = new WpProQuiz_Model_QuestionMapper();
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		$categoryMapper = new WpProQuiz_Model_CategoryMapper();
		
		$questions = $questionMapper->fetchAll($quizId);
		$category = $categoryMapper->fetchAll();
		$categoryEmpty = new WpProQuiz_Model_Category();
		
		$categoryEmpty->setCategoryName(__('No category', 'wp-pro-quiz'));
		
		$list = array();
		$cats = array();
		
		foreach($category as $c) {
			$cats[$c->getCategoryId()] = $c;
		}
		
		$cats[0] = $categoryEmpty;
		
		foreach($questions as $q) {
			$list[$q->getCategoryId()][] = $q;	
		}
		
		
		$view->quiz = $quizMapper->fetch($quizId);
		$view->questionList = $list;
		$view->categoryList = $cats;
		
		if(has_action('pre_user_query', 'ure_exclude_administrators')) {
			remove_action('pre_user_query', 'ure_exclude_administrators');
						
			$users = get_users(array('fields' => array('ID','user_login','display_name')));
			
			add_action('pre_user_query', 'ure_exclude_administrators');
			
		} else {
			$users = get_users(array('fields' => array('ID','user_login','display_name')));
		}
		
		array_unshift($users, (object)array('ID' => 0));
		
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
	
	public static function ajaxLoadStatistic($data, $func) {
		if(!current_user_can('wpProQuiz_show_statistics')) {
			return json_encode(array());
		}
		
		$userId = $data['userId'];
		$quizId = $data['quizId'];
	
		$statisticMapper = new WpProQuiz_Model_StatisticMapper();
		$questionMapper = new WpProQuiz_Model_QuestionMapper();
		
		$statistics = $statisticMapper->fetchAll($quizId, $userId);
		
		$maxPoints = 0;
		$sumQuestion = 0;
		
		$questionData = $questionMapper->fetchAllList($quizId, array('id', 'category_id', 'points'));
		$category = array();
		$categoryList = array();
		
		$empty = array(
			'questionId' => 0,
			'correct' => 0,
			'incorrect' => 0,
			'hint' => 0,
			'points' => 0,
			'result' => 0,
		);
		
		$ca = $sa = array();
		
		$ga = $empty;
		
		foreach($questionData as $cc) {
			$categoryList[$cc['id']] = $cc['category_id'];
				
			$c = &$category[$cc['category_id']];
				
			if(empty($c)) {
				$c = $cc;
				$c['sum'] = 1;
			} else {
				$c['points'] += $cc['points'];
				$c['sum']++;
			}
				
			$maxPoints += $cc['points'];
			$sumQuestion++;
			
			$sa[$cc['id']] = self::calcTotal($empty);
			$sa[$cc['id']]['questionId'] = $cc['id'];
			
			$ca[$cc['category_id']] = self::calcTotal($empty);
		}
		
		foreach($statistics as $statistic) {
			$s = $statistic->getCorrectCount() + $statistic->getIncorrectCount();
			
			if($s > 0) {
				$correct = $statistic->getCorrectCount().' ('.round((100 * $statistic->getCorrectCount() / $s), 2).'%)';
				$incorrect = $statistic->getIncorrectCount().' ('.round((100 * $statistic->getIncorrectCount() / $s), 2).'%)';
			} else {
				$incorrect = $correct = '0 (0%)';
			}
			
			$ga['correct'] += $statistic->getCorrectCount();
			$ga['incorrect'] += $statistic->getIncorrectCount();
			$ga['hint'] += $statistic->getHintCount();
			$ga['points'] += $statistic->getPoints();

			$cats = &$ca[$categoryList[$statistic->getQuestionId()]];
			
			if(!is_array($cats)) {
				$cats = $empty;
			}
			
			$cats['correct'] += $statistic->getCorrectCount();
			$cats['incorrect'] += $statistic->getIncorrectCount();
			$cats['hint'] += $statistic->getHintCount();
			$cats['points'] += $statistic->getPoints();
			
			
			$sa[$statistic->getQuestionId()] = array(
				'questionId' => $statistic->getQuestionId(),
				'correct' => $correct,
				'incorrect' => $incorrect,
				'hint' => $statistic->getHintCount(),
				'points' => $statistic->getPoints()
			);
		}
		
		foreach($ca as $catIndex => $cat) {
			$ca[$catIndex] = self::calcTotal($cat, $category[$catIndex]['points'], $category[$catIndex]['sum']);
		}
		
		$sa[0] = self::calcTotal($ga, $maxPoints, $sumQuestion);
		
		return json_encode(array(
			'question' => $sa,
			'category' => $ca
		));
	}
	
	public static function ajaxReset($data, $func) {
		if(!current_user_can('wpProQuiz_reset_statistics')) {
			return;
		}
		
		$statisticMapper = new WpProQuiz_Model_StatisticMapper();
		
		if(isset($data['full']) && $data['full']) {
			$statisticMapper->deleteByQuizId($data['quizId']);
		} else {
			$statisticMapper->delete($data['quizId'], $data['userId']);
		}
	}
	
	public static function ajaxLoadStatsticOverview($data, $func) {
		if(!current_user_can('wpProQuiz_show_statistics')) {
			return json_encode(array());
		}
		
		$statisticMapper = new WpProQuiz_Model_StatisticMapper();
		
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		
		$quizId = $data['quizId'];
		
		$page = (isset($data['page']) && $data['page'] > 0) ? $data['page'] : 1;
		$limit = $data['pageLimit'];
		$start = $limit * ($page - 1);
		
		$statistics = $statisticMapper->fetchOverview($quizId, (bool)$data['onlyCompleted'], $start, $limit);
		
		$d = array('items' => array());
		
		$maxPoints = $quizMapper->sumQuestionPoints($quizId);
		$sumQuestion = $quizMapper->countQuestion($quizId);
		
		foreach($statistics as $statistic) {
				
			$sum = $statistic->getCorrectCount() + $statistic->getIncorrectCount();
				
			if($sum > 0) {
				$correct = $statistic->getCorrectCount().' ('.round((100 * $statistic->getCorrectCount() / $sum), 2).'%)';
				$incorrect = $statistic->getIncorrectCount().' ('.round((100 * $statistic->getIncorrectCount() / $sum), 2).'%)';
				$hint = $statistic->getHintCount();
				$result = round((100 * $statistic->getPoints() / ($sum * $maxPoints / $sumQuestion)), 2).'%';
				$points = $statistic->getPoints();
			} else {
				$points = $result = $hint = $correct = $incorrect = '---';
			}
			
				
			$d['items'][] = array(
					'userId' => $statistic->getUserId(),
					'userName' => $statistic->getUserName(),
					'points' => $points,
					'correct' => $correct,
					'incorrect' => $incorrect,
					'hint' => $hint,
					'result' => $result
			);
		}
		
		if(isset($data['nav']) && $data['nav']) {
			$count = $statisticMapper->countOverview($quizId, (bool)$data['onlyCompleted']);
			$d['page'] = ceil(($count > 0 ? $count : 1) / $limit);
		}
		
		return json_encode($d);
		
	}
	
	private static function calcTotal($a, $maxPoints = null, $sumQuestion = null) {
		$s = $a['correct'] + $a['incorrect'];
		
		if($s > 0) {
			$a['correct'] = $a['correct'].' ('.round((100 * $a['correct'] / $s), 2).'%)';
			$a['incorrect'] = $a['incorrect'].' ('.round((100 * $a['incorrect'] / $s), 2).'%)';
			
			if($maxPoints !== null)
				$a['result'] = round((100 * $a['points'] / ($s * $maxPoints / $sumQuestion)), 2).'%';
		} else {
			$a['result'] = $a['correct'] = $a['incorrect'] = '0 (0%)';
		}
		
		return $a;
	}
}
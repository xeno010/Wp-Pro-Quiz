<?php
class WpProQuiz_Controller_QuizCompleted {
	
	private $data = array();
	
	public function __construct($data) {
		$this->data = $data;
	}
	
	public static function ajaxQuizCompleted($data, $func) {
		$completed = new WpProQuiz_Controller_QuizCompleted($data);
		
		return $completed->quizCompleted();
	}
	
	public function quizCompleted() {
		$lockMapper = new WpProQuiz_Model_LockMapper();
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		$categoryMapper = new WpProQuiz_Model_CategoryMapper();
		
		$statistics = new WpProQuiz_Controller_Statistics();
		
		$quiz = $quizMapper->fetch($this->data['quizId']);
		
		if($quiz === null || $quiz->getId() <= 0) {
			return;
		}
		
		$categories = $categoryMapper->fetchByQuiz($quiz->getId());
		$userId = get_current_user_id();
		$resultInPercent = floor($this->data['results']['comp']['result']);
		

		$this->setResultCookie($quiz);
		$this->emailNote($quiz, $this->data['results']['comp'], $categories);
		
		if(!$this->isPreLockQuiz($quiz)) {
			$statistics->save();
				
			do_action('wp_pro_quiz_completed_quiz');
			do_action('wp_pro_quiz_completed_quiz_'.$resultInPercent.'_percent');
				
			return;
		}
		
		$lockMapper->deleteOldLock(60*60*24*7, $this->_post['quizId'], time(), WpProQuiz_Model_Lock::TYPE_QUIZ, 0);
		
		$lockIp = $lockMapper->isLock($this->_post['quizId'], $this->getIp(), get_current_user_id(), WpProQuiz_Model_Lock::TYPE_QUIZ);
		$lockCookie = false;
		$cookieTime = $quiz->getQuizRunOnceTime();
		$cookieJson = null;
		
		if(isset($this->_cookie['wpProQuiz_lock']) && get_current_user_id() == 0 && $quiz->isQuizRunOnceCookie()) {
			$cookieJson = json_decode($this->_cookie['wpProQuiz_lock'], true);
				
			if($cookieJson !== false) {
				if(isset($cookieJson[$this->_post['quizId']]) && $cookieJson[$this->_post['quizId']] == $cookieTime) {
					$lockCookie = true;
				}
			}
		}
		
		if(!$lockIp && !$lockCookie) {
			$statistics->save();
		
			do_action('wp_pro_quiz_completed_quiz');
			do_action('wp_pro_quiz_completed_quiz_'.$resultInPercent.'_percent');
		
			if(get_current_user_id() == 0 && $quiz->isQuizRunOnceCookie()) {
				$cookieData = array();
		
				if($cookieJson !== null || $cookieJson !== false) {
					$cookieData = $cookieJson;
				}
		
				$cookieData[$this->_post['quizId']] = $quiz->getQuizRunOnceTime();
				$url = parse_url(get_bloginfo( 'url' ));
		
				setcookie('wpProQuiz_lock', json_encode($cookieData), time() + 60*60*24*60, empty($url['path']) ? '/' : $url['path']);
			}
		
			$lock = new WpProQuiz_Model_Lock();
				
			$lock->setUserId(get_current_user_id());
			$lock->setQuizId($this->_post['quizId']);
			$lock->setLockDate(time());
			$lock->setLockIp($this->getIp());
			$lock->setLockType(WpProQuiz_Model_Lock::TYPE_QUIZ);
				
			$lockMapper->insert($lock);
		}
		
		return;
	}
	
	private function setResultCookie(WpProQuiz_Model_Quiz $quiz) {
		$prerequisite = new WpProQuiz_Model_PrerequisiteMapper();
	
		if(get_current_user_id() == 0 && $prerequisite->isQuizId($quiz->getId())) {
			$cookieData = array();
				
			if(isset($this->_cookie['wpProQuiz_result'])) {
				$d = json_decode($this->_cookie['wpProQuiz_result'], true);
	
				if($d !== null && is_array($d)) {
					$cookieData = $d;
				}
			}
				
			$cookieData[$quiz->getId()] = 1;
				
			$url = parse_url(get_bloginfo( 'url' ));
	
			setcookie('wpProQuiz_result', json_encode($cookieData), time() + 60*60*24*300, empty($url['path']) ? '/' : $url['path']);
		}
	}
	
	private function emailNote(WpProQuiz_Model_Quiz $quiz, $result, $categories) {
		$globalMapper = new WpProQuiz_Model_GlobalSettingsMapper();
		
		$adminEmail = $globalMapper->getEmailSettings();
		$userEmail = $globalMapper->getUserEmailSettings();
		
		$user = wp_get_current_user();
		
		$r = array(
			'$userId' => $user->ID,
			'$username' => $user->display_name,
			'$quizname' => $quiz->getName(),
			'$result' => $result['result'].'%',
			'$points' => $result['points'],
			'$ip' => filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP),
			'$categories' => empty($result['cats']) ? '' : $this->setCategoryOverview($result['cats'], $categories)
		);
		
		if($user->ID == 0) {
			$r['$username'] = $r['$ip'];
		}
		
		if($quiz->isUserEmailNotification()) {
			$msg = str_replace(array_keys($r), $r, $userEmail['message']);
				
			$headers = '';
				
			if(!empty($userEmail['from'])) {
				$headers = 'From: '.$userEmail['from'];
			}

			if($userEmail['html'])
				add_filter('wp_mail_content_type', array($this, 'htmlEmailContent'));
			
			wp_mail($user->user_email, $userEmail['subject'], $msg, $headers);
			
			if($userEmail['html'])
				remove_filter('wp_mail_content_type', array($this, 'htmlEmailContent'));
		}
		
		if($quiz->getEmailNotification() == WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_ALL 
			|| (get_current_user_id() > 0 && $quiz->getEmailNotification() == WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_REG_USER)) {
			
			$msg = str_replace(array_keys($r), $r, $adminEmail['message']);
			
			$headers = '';
			
			if(!empty($adminEmail['from'])) {
				$headers = 'From: '.$adminEmail['from'];
			}
			
			if(isset($adminEmail['html']) && $adminEmail['html'])
				add_filter('wp_mail_content_type', array($this, 'htmlEmailContent'));
			
			wp_mail($adminEmail['to'], $adminEmail['subject'], $msg, $headers);
			
			if(isset($adminEmail['html']) && $adminEmail['html'])
				remove_filter('wp_mail_content_type', array($this, 'htmlEmailContent'));
		}
	}
	
	public function htmlEmailContent($contentType) {
		return 'text/html';
	}
	
	private function setCategoryOverview($catArray, $categories) {
		$cats = array();
	
		foreach($categories as $cat) {
			/* @var $cat WpProQuiz_Model_Category */
				
			if(!$cat->getCategoryId()) {
				$cat->setCategoryName(__('Not categorized', 'wp-pro-quiz'));
			}
				
			$cats[$cat->getCategoryId()] = $cat->getCategoryName();
		}
	
		$a = __('Categories', 'wp-pro-quiz').":\n";
	
		foreach($catArray as $id => $value) {
			if(!isset($cats[$id]))
				continue;
				
			$a .= '* '.str_pad($cats[$id], 35, '.').((float)$value)."%\n";
		}
	
		return $a;
	}
	
	private function isPreLockQuiz(WpProQuiz_Model_Quiz $quiz) {
		$userId = get_current_user_id();
	
		if($quiz->isQuizRunOnce()) {
			switch ($quiz->getQuizRunOnceType()) {
				case WpProQuiz_Model_Quiz::QUIZ_RUN_ONCE_TYPE_ALL:
					return true;
				case WpProQuiz_Model_Quiz::QUIZ_RUN_ONCE_TYPE_ONLY_USER:
					return $userId > 0;
				case WpProQuiz_Model_Quiz::QUIZ_RUN_ONCE_TYPE_ONLY_ANONYM:
					return $userId == 0;
			}
		}
	
		return false;
	}
	
	private function getIp() {
		if(get_current_user_id() > 0)
			return '0';
		else
			return filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
	}
}
<?php
class WpProQuiz_Controller_Quiz extends WpProQuiz_Controller_Controller {
	private $view;
		
	public function route() {
		$action = isset($_GET['action']) ? $_GET['action'] : 'show';
		
		switch ($action) {
			case 'show':
				$this->showAction();
				break;
			case 'add':
				$this->createAction();
				break;
			case 'edit':
				if(isset($_GET['id']))
					$this->editAction($_GET['id']);
				break;
			case 'delete':
				if(isset($_GET['id']))
					$this->deleteAction($_GET['id']);
				break;
			case 'reset_lock':
				$this->resetLock($_GET['id']);
				break;
		}
	}
	
	public function checkLock() {
		
		
		if($userId > 0) {
			$quizIds = $prerequisiteMapper->getNoPrerequisite($quizId, $userId);
		} else {
			$checkIds = $prerequisiteMapper->fetchQuizIds($quizId);
			
			if(isset($this->_post['wpProQuiz_result'])) {
				$r = json_encode($this->_post['wpProQuiz_result'], true);
				
				if($r !== null && is_array($r)) {
					foreach($checkIds as $id) {
						if(!isset($r[$id]) || !$r[$id]) {
							$quizIds[] = $id;
						}
					}					
				}
			} else {
				$quizIds = $checkIds;
			}
		}
		
		$names = $quizMapper->fetchCol($quizIds, 'name');
		
	}
	
	public function isLockQuiz($quizId) {
		$quizId = (int)$this->_post['quizId'];
		$userId = get_current_user_id();
		$data = array();
		
		$lockMapper = new WpProQuiz_Model_LockMapper();
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		$prerequisiteMapper = new WpProQuiz_Model_PrerequisiteMapper();
	
		$quiz = $quizMapper->fetch($this->_post['quizId']);
	
		if($quiz === null || $quiz->getId() <= 0) {
			return null;
		}
	
		if($this->isPreLockQuiz($quiz)) {
			$lockIp = $lockMapper->isLock($this->_post['quizId'], $this->getIp(), $userId, WpProQuiz_Model_Lock::TYPE_QUIZ);
			$lockCookie = false;
			$cookieTime = $quiz->getQuizRunOnceTime();
				
			if(isset($this->_cookie['wpProQuiz_lock']) && $userId == 0 && $quiz->isQuizRunOnceCookie()) {
				$cookieJson = json_decode($this->_cookie['wpProQuiz_lock'], true);
					
				if($cookieJson !== false) {
					if(isset($cookieJson[$this->_post['quizId']]) && $cookieJson[$this->_post['quizId']] == $cookieTime) {
						$lockCookie = true;
					}
				}
			}
			
			$data['lock'] = array(
				'is' => ($lockIp || $lockCookie), 
				'pre' => true
			);
		}
	
		if($quiz->isPrerequisite()) {
			$quizIds = array();
			
			if($userId > 0) {
				$quizIds = $prerequisiteMapper->getNoPrerequisite($quizId, $userId);
			} else {
				$checkIds = $prerequisiteMapper->fetchQuizIds($quizId);
					
				if(isset($this->_cookie['wpProQuiz_result'])) {
					$r = json_decode($this->_cookie['wpProQuiz_result'], true);

					if($r !== null && is_array($r)) {
						foreach($checkIds as $id) {
							if(!isset($r[$id]) || !$r[$id]) {
								$quizIds[] = $id;
							}
						}
					}
				} else {
					$quizIds = $checkIds;
				}
			}
			
			if(!empty($quizIds)) {
				$names = $quizMapper->fetchCol($quizIds, 'name');
				
				if(!empty($names)) {
					$data['prerequisite'] = implode(', ', $names);
				}
			}
			
		}
		
		return $data;
	}
	
	public function loadQuizData() {
		$quizId = (int)$_POST['quizId'];
		$userId = get_current_user_id();
		
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		$toplistController = new WpProQuiz_Controller_Toplist();
		$statisticController = new WpProQuiz_Controller_Statistics();
		
		$quiz = $quizMapper->fetch($quizId);
		$data = array();
		
		if($quiz === null || $quiz->getId() <= 0) {
			return array();
		}
			
		$data['toplist'] = $toplistController->getAddToplist($quiz);
		$data['averageResult'] = $statisticController->getAverageResult($quizId);
		
		return $data;
	}
	
	private function resetLock($quizId) {
		
		if(!current_user_can('wpProQuiz_edit_quiz')) {
			exit;
		}
		
		$lm = new WpProQuiz_Model_LockMapper();
		$qm = new WpProQuiz_Model_QuizMapper();
		
		$q = $qm->fetch($quizId);
		
		if($q->getId() > 0) {

			$q->setQuizRunOnceTime(time());
			
			$qm->save($q);
			
			$lm->deleteByQuizId($quizId, WpProQuiz_Model_Lock::TYPE_QUIZ);
		}
		
		exit;
	}
	
	private function showAction() {
		
		if(!current_user_can('wpProQuiz_show')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		
		$this->view = new WpProQuiz_View_QuizOverall();
		
		$m = new WpProQuiz_Model_QuizMapper();
		$this->view->quiz = $m->fetchAll();
		
		$this->view->show();
	}
	
	private function editAction($id) {
		
		if(!current_user_can('wpProQuiz_edit_quiz')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		
		$prerequisiteMapper = new WpProQuiz_Model_PrerequisiteMapper();
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		
		$this->view = new WpProQuiz_View_QuizEdit();
		$this->view->header = __('Edit quiz', 'wp-pro-quiz');
		
		$m = new WpProQuiz_Model_QuizMapper();
		
		if($m->exists($id) == 0) {
			WpProQuiz_View_View::admin_notices(__('Quiz not found', 'wp-pro-quiz'), 'error');
			return;
		}
		
		if(isset($this->_post['submit'])) {
			
			if(isset($this->_post['resultGradeEnabled'])) {
				$this->_post['result_text'] = $this->filterResultTextGrade($this->_post);
			}
			
			$quiz = new WpProQuiz_Model_Quiz($this->_post);
			$quiz->setId($id);
					
			if($this->checkValidit($this->_post)) {
				
				WpProQuiz_View_View::admin_notices(__('Quiz edited', 'wp-pro-quiz'), 'info');
				
				$prerequisiteMapper = new WpProQuiz_Model_PrerequisiteMapper();
				
				$prerequisiteMapper->delete($id);
				
				if($quiz->isPrerequisite() && !empty($this->_post['prerequisiteList'])) {
					$prerequisiteMapper->save($id, $this->_post['prerequisiteList']);
					$quizMapper->activateStatitic($this->_post['prerequisiteList'], 1440);
				}
				
				$quizMapper->save($quiz);
				
				$this->showAction();
				
				return;
			} else {
				WpProQuiz_View_View::admin_notices(__('Quiz title or quiz description are not filled', 'wp-pro-quiz'));
			}
		} else {
			$quiz = $m->fetch($id);
		}
		
		$this->view->quiz = $quiz;
		$this->view->prerequisiteQuizList = $prerequisiteMapper->fetchQuizIds($id);
		$this->view->quizList = $m->fetchAllAsArray(array('id', 'name'), array($id));
		$this->view->captchaIsInstalled = class_exists('ReallySimpleCaptcha');
		$this->view->show();
	}
	
	private function createAction() {
		
		if(!current_user_can('wpProQuiz_add_quiz')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		
		$this->view = new WpProQuiz_View_QuizEdit();
		$this->view->header = __('Create quiz', 'wp-pro-quiz');
		
		$m = new WpProQuiz_Model_QuizMapper();
		
		if(isset($this->_post['submit'])) {
			
			if(isset($this->_post['resultGradeEnabled'])) {
				$this->_post['result_text'] = $this->filterResultTextGrade($this->_post);
			}
			
			$quiz = new WpProQuiz_Model_Quiz($this->_post);
			$quizMapper = new WpProQuiz_Model_QuizMapper();
			
			if($this->checkValidit($this->_post)) {
				WpProQuiz_View_View::admin_notices(__('Create quiz', 'wp-pro-quiz'), 'info');
				$quizMapper->save($quiz);
				
				$id = $quizMapper->getInsertId();
				$prerequisiteMapper = new WpProQuiz_Model_PrerequisiteMapper();
				
				if($quiz->isPrerequisite() && !empty($this->_post['prerequisiteList'])) {
					$prerequisiteMapper->save($id, $this->_post['prerequisiteList']);
					$quizMapper->activateStatitic($this->_post['prerequisiteList'], 1440);
				}
				
				$this->showAction();
				return;
			} else {
				WpProQuiz_View_View::admin_notices(__('Quiz title or quiz description are not filled', 'wp-pro-quiz'));
			}
		} else {
			$quiz = new WpProQuiz_Model_Quiz();
		}
		
		$this->view->quiz = $quiz;
		$this->view->prerequisiteQuizList = array();
		$this->view->quizList = $m->fetchAllAsArray(array('id', 'name'));
		$this->view->captchaIsInstalled = class_exists('ReallySimpleCaptcha');
		$this->view->show();
	}
	
	private function deleteAction($id) {
		
		if(!current_user_can('wpProQuiz_delete_quiz')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		
		$m = new WpProQuiz_Model_QuizMapper();
		$qm = new WpProQuiz_Model_QuestionMapper();
		$lm = new WpProQuiz_Model_LockMapper();
		$srm = new WpProQuiz_Model_StatisticRefMapper();
		$pm = new WpProQuiz_Model_PrerequisiteMapper();
		$tm = new WpProQuiz_Model_ToplistMapper();
		
		$m->delete($id);
		$qm->deleteByQuizId($id);
		$lm->deleteByQuizId($id);
		$srm->deleteAll($id);
		$pm->delete($id);
		$tm->delete($id);
		
		WpProQuiz_View_View::admin_notices(__('Quiz deleted', 'wp-pro-quiz'), 'info');
		
		$this->showAction();
	}
	
	private function checkValidit($post) {
		return (isset($post['name']) && !empty($post['name']) && isset($post['text']) && !empty($post['text']));
	}
	
	private function filterResultTextGrade($post) {
		$activ = array_keys($post['resultTextGrade']['activ'], '1');
		$result = array();
		
		foreach($activ as $k) {
			$result['text'][] = $post['resultTextGrade']['text'][$k];
			$result['prozent'][] = (float)str_replace(',', '.', $post['resultTextGrade']['prozent'][$k]);
		}

		return $result;				
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
	
	public function completedQuiz() {
		
		$lockMapper = new WpProQuiz_Model_LockMapper();
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		$userId = get_current_user_id();
		
		$is100P = $this->_post['results']['comp']['result'] == 100;
		
		$quiz = $quizMapper->fetch($this->_post['quizId']);

		if($quiz === null || $quiz->getId() <= 0) {
			exit;
		}

		$this->setResultCookie($quiz);
		
		$this->emailNote($quiz, $this->_post['results']['comp']);
		
		if(!$this->isPreLockQuiz($quiz)) {
			$statistics = new WpProQuiz_Controller_Statistics();
			$statistics->save();
			
			do_action('wp_pro_quiz_completed_quiz');
			
			if($is100P)
				do_action('wp_pro_quiz_completed_quiz_100_percent');
			
			exit;
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
			$statistics = new WpProQuiz_Controller_Statistics();
			$statistics->save();

			do_action('wp_pro_quiz_completed_quiz');
			
			if($is100P)
				do_action('wp_pro_quiz_completed_quiz_100_percent');

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
		
		exit;
	}
	
	public function isPreLockQuiz(WpProQuiz_Model_Quiz $quiz) {
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
	
	private function emailNote(WpProQuiz_Model_Quiz $quiz, $result) {
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
			'$ip' => filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)
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
		
		if($quiz->getEmailNotification() != WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_NONE || ( get_current_user_id() == 0 
			&& $quiz->getEmailNotification() != WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_REG_USER)) {
			
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
}
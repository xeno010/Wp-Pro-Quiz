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
						
			$this->view->quiz = new WpProQuiz_Model_Quiz($this->_post);
			$this->view->quiz->setId($id);
					
			if($this->checkValidit($this->_post)) {
				
				WpProQuiz_View_View::admin_notices(__('Quiz edited', 'wp-pro-quiz'), 'info');
				
				$this->view->quiz->getMapper()->save($this->view->quiz);
				
				
				$this->showAction();
				
				return;
			} else {
				WpProQuiz_View_View::admin_notices(__('Quiz title or quiz description are not filled', 'wp-pro-quiz'));
			}
		} else {
			$this->view->quiz = $m->fetch($id);
		}
		
		$this->view->show();
	}
	
	private function createAction() {
		
		if(!current_user_can('wpProQuiz_add_quiz')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		
		$this->view = new WpProQuiz_View_QuizEdit();
		$this->view->header = __('Create quiz', 'wp-pro-quiz');
		
		if(isset($this->_post['submit'])) {
			
			if(isset($this->_post['resultGradeEnabled'])) {
				$this->_post['result_text'] = $this->filterResultTextGrade($this->_post);
			}
			
			$this->view->quiz = new WpProQuiz_Model_Quiz($this->_post);
			
			if($this->checkValidit($this->_post)) {
				WpProQuiz_View_View::admin_notices(__('Create quiz', 'wp-pro-quiz'), 'info');
				$this->view->quiz->getMapper()->save($this->view->quiz);
				$this->showAction();
				return;
			} else {
				//add_action('admin_notices', array('WpProQuiz_View_View', 'admin_notices'));
				WpProQuiz_View_View::admin_notices(__('Quiz title or quiz description are not filled', 'wp-pro-quiz'));
			}
		} else {
			$this->view->quiz = new WpProQuiz_Model_Quiz();
		}
		
		$this->view->show();
	}
	
	private function deleteAction($id) {
		
		if(!current_user_can('wpProQuiz_delete_quiz')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		
		$m = new WpProQuiz_Model_QuizMapper();
		$qm = new WpProQuiz_Model_QuestionMapper();
		$lm = new WpProQuiz_Model_LockMapper();
		$sm = new WpProQuiz_Model_StatisticMapper();
		
		$m->delete($id);
		$qm->deleteByQuizId($id);
		$lm->deleteByQuizId($id);
		$sm->deleteByQuiz($id);
		
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
	
	public function completedQuiz() {
		
		$lockMapper = new WpProQuiz_Model_LockMapper();
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		
		$quiz = $quizMapper->fetch($this->_post['quizId']);
		
		if($quiz === null || $quiz->getId() <= 0) {
			exit;
		}

		if(!$this->isPreLockQuiz($quiz)) {
			
			$statistics = new WpProQuiz_Controller_Statistics();
			$statistics->save();
			
			do_action('wp_pro_quiz_completed_quiz');
			
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
	
	public function isLockQuiz($quizId) {
		$lockMapper = new WpProQuiz_Model_LockMapper();
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		
		$quiz = $quizMapper->fetch($this->_post['quizId']);
		
		if($quiz === null || $quiz->getId() <= 0) {
			return array('is' => true, 'pre' => true);
		}
		
		if($this->isPreLockQuiz($quiz)) {
			$lockIp = $lockMapper->isLock($this->_post['quizId'], $this->getIp(), get_current_user_id(), WpProQuiz_Model_Lock::TYPE_QUIZ);
			$lockCookie = false;
			$cookieTime = $quiz->getQuizRunOnceTime();
			
			if(isset($this->_cookie['wpProQuiz_lock']) && get_current_user_id() == 0 && $quiz->isQuizRunOnceCookie()) {
				$cookieJson = json_decode($this->_cookie['wpProQuiz_lock'], true);
					
				if($cookieJson !== false) {
					if(isset($cookieJson[$this->_post['quizId']]) && $cookieJson[$this->_post['quizId']] == $cookieTime) {
						$lockCookie = true;
					}
				}
			}
			
			return array('is' => ($lockIp || $lockCookie), 'pre' => true);
		}
		
		return array('is' => false, 'pre' => false);
	}
	
	private function getIp() {
		if(get_current_user_id() > 0)
			return '0';
		else
			return filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
	}
}
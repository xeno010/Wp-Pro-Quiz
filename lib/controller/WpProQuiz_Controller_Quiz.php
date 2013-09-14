<?php
class WpProQuiz_Controller_Quiz extends WpProQuiz_Controller_Controller {
	private $view;
		
	public function route() {
		$action = isset($_GET['action']) ? $_GET['action'] : 'show';
		
		switch ($action) {
			case 'show':
				$this->showAction();
				break;
			case 'addEdit':
				$this->addEditQuiz();
				break;
// 			case 'add':
// 				$this->createAction();
// 				break;
// 			case 'edit':
// 				if(isset($_GET['id']))
// 					$this->editAction($_GET['id']);
// 				break;
			case 'delete':
				if(isset($_GET['id']))
					$this->deleteAction($_GET['id']);
				break;
			case 'reset_lock':
				$this->resetLock($_GET['id']);
				break;
		}
	}
	
	private function addEditQuiz() {
		$quizId = isset($_GET['quizId']) ? (int)$_GET['quizId'] : 0;
		
		if($quizId) {
			if(!current_user_can('wpProQuiz_edit_quiz')) {
				wp_die(__('You do not have sufficient permissions to access this page.'));
			}
		} else {
			if(!current_user_can('wpProQuiz_add_quiz')) {
				wp_die(__('You do not have sufficient permissions to access this page.'));
			}
		}
		
		$prerequisiteMapper	= new WpProQuiz_Model_PrerequisiteMapper();
		$quizMapper 		= new WpProQuiz_Model_QuizMapper();
		$formMapper 		= new WpProQuiz_Model_FormMapper();
		$templateMapper 	= new WpProQuiz_Model_TemplateMapper();
		
		$quiz = new WpProQuiz_Model_Quiz();
		$forms = null;
		$prerequisiteQuizList = array();
		
		
		if($quizId && $quizMapper->exists($quizId) == 0) {
			WpProQuiz_View_View::admin_notices(__('Quiz not found', 'wp-pro-quiz'), 'error');
			
			return;
		}
		
		if(isset($this->_post['template']) || (isset($this->_post['templateLoad']) && isset($this->_post['templateLoadId']))) {
			if(isset($this->_post['template']))
				$template = $this->saveTemplate();
			else
				$template = $templateMapper->fetchById($this->_post['templateLoadId']);
		
			$data = $template->getData();
			
			if($data !== null) {
				$quiz = $data['quiz'];
				$quiz->setId($quizId);
				
				$forms = $data['forms'];
				$prerequisiteQuizList = $data['prerequisiteQuizList'];
			}
		} else if(isset($this->_post['submit'])) {
				
			if(isset($this->_post['resultGradeEnabled'])) {
				$this->_post['result_text'] = $this->filterResultTextGrade($this->_post);
			}
				
			$quiz = new WpProQuiz_Model_Quiz($this->_post);
			$quiz->setId($quizId);
				
			if($this->checkValidit($this->_post)) {
				if($quizId)
					WpProQuiz_View_View::admin_notices(__('Quiz edited', 'wp-pro-quiz'), 'info');
				else
					WpProQuiz_View_View::admin_notices(__('quiz created', 'wp-pro-quiz'), 'info');
		
				$quizMapper->save($quiz);
				
				$quizId = $quiz->getId();
				
				$prerequisiteMapper->delete($quizId);
					
				if($quiz->isPrerequisite() && !empty($this->_post['prerequisiteList'])) {
					$prerequisiteMapper->save($quizId, $this->_post['prerequisiteList']);
					$quizMapper->activateStatitic($this->_post['prerequisiteList'], 1440);
				}
		
				if(!$this->formHandler($quiz->getId(), $this->_post)) {
					$quiz->setFormActivated(false);
					$quizMapper->save($quiz);
				}
				
				$forms = $formMapper->fetch($quizId);
				$prerequisiteQuizList = $prerequisiteMapper->fetchQuizIds($quizId);
				
			} else {
				WpProQuiz_View_View::admin_notices(__('Quiz title or quiz description are not filled', 'wp-pro-quiz'));
			}
		} else if($quizId) {
			$quiz = $quizMapper->fetch($quizId);
			$forms = $formMapper->fetch($quizId);
			$prerequisiteQuizList = $prerequisiteMapper->fetchQuizIds($quizId);
		}
		
		$this->view = new WpProQuiz_View_QuizEdit();
		
		$this->view->quiz 					= $quiz;
		$this->view->forms 					= $forms;
		$this->view->prerequisiteQuizList 	= $prerequisiteQuizList;
		$this->view->templates 				= $templateMapper->fetchAll(WpProQuiz_Model_Template::TEMPLATE_TYPE_QUIZ, false);
		$this->view->quizList 				= $quizMapper->fetchAllAsArray(array('id', 'name'), $quizId ? array($quizId) : array());
		$this->view->captchaIsInstalled 	= class_exists('ReallySimpleCaptcha');
		
		$this->view->header = $quizId ? __('Edit quiz', 'wp-pro-quiz') : __('Create quiz', 'wp-pro-quiz');

		$this->view->show();
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
		
		if($quiz->isStartOnlyRegisteredUser()) {
			$data['startUserLock'] = (int)!is_user_logged_in();
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
		$formMapper = new WpProQuiz_Model_FormMapper();
		$templateMapper = new WpProQuiz_Model_TemplateMapper();
		$m = new WpProQuiz_Model_QuizMapper();
		
		$this->view = new WpProQuiz_View_QuizEdit();
		$this->view->header = __('Edit quiz', 'wp-pro-quiz');
		
		$forms = $formMapper->fetch($id);
		$prerequisiteQuizList = $prerequisiteMapper->fetchQuizIds($id);
		
		
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
				
				if(!$this->formHandler($quiz->getId(), $this->_post)) {
					$quiz->setFormActivated(false);
				}
				
				$quizMapper->save($quiz);
				
				$this->showAction();
				
				return;
			} else {
				WpProQuiz_View_View::admin_notices(__('Quiz title or quiz description are not filled', 'wp-pro-quiz'));
			}
		} else if(isset($this->_post['template']) || isset($this->_post['templateLoad'])) {
			if(isset($this->_post['template']))
				$template = $this->saveTemplate();
			else 
				$template = $templateMapper->fetchById($this->_post['templateLoadId']);
			
			$data = $template->getData();
			
			if($data !== null) {
				$quiz = $data['quiz'];
				$forms = $data['forms'];
				$prerequisiteQuizList = $data['prerequisiteQuizList'];
			}
		} else {
			$quiz = $m->fetch($id);
		}
		
		$this->view->quiz = $quiz;
		$this->view->prerequisiteQuizList = $prerequisiteQuizList;
		$this->view->quizList = $m->fetchAllAsArray(array('id', 'name'), array($id));
		$this->view->captchaIsInstalled = class_exists('ReallySimpleCaptcha');
		$this->view->forms = $forms;
		$this->view->templates = $templateMapper->fetchAll(WpProQuiz_Model_Template::TEMPLATE_TYPE_QUIZ, false);
		$this->view->show();
	}
	
	private function createAction() {
		
		if(!current_user_can('wpProQuiz_add_quiz')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		
		$this->view = new WpProQuiz_View_QuizEdit();
		$this->view->header = __('Create quiz', 'wp-pro-quiz');
		
		$forms = null;
		$prerequisiteQuizList = array();
		
		$m = new WpProQuiz_Model_QuizMapper();
		$templateMapper = new WpProQuiz_Model_TemplateMapper();
		
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
				
				if(!$this->formHandler($id, $this->_post)) {
					$quiz->setFormActivated(false);
					$quizMapper->save($quiz);
				}
				
				$this->showAction();
				return;
			} else {
				WpProQuiz_View_View::admin_notices(__('Quiz title or quiz description are not filled', 'wp-pro-quiz'));
			}
		} else if(isset($this->_post['template']) || isset($this->_post['templateLoad'])) {
			if(isset($this->_post['template']))
				$template = $this->saveTemplate();
			else 
				$template = $templateMapper->fetchById($this->_post['templateLoadId']);
			
			$data = $template->getData();
			
			if($data !== null) {
				$quiz = $data['quiz'];
				$forms = $data['forms'];
				$prerequisiteQuizList = $data['prerequisiteQuizList'];
			}
		} else {
			$quiz = new WpProQuiz_Model_Quiz();
		}
		
		$this->view->quiz = $quiz;
		$this->view->prerequisiteQuizList = $prerequisiteQuizList;
		$this->view->quizList = $m->fetchAllAsArray(array('id', 'name'));
		$this->view->captchaIsInstalled = class_exists('ReallySimpleCaptcha');
		$this->view->forms = $forms;
		$this->view->templates = $templateMapper->fetchAll(WpProQuiz_Model_Template::TEMPLATE_TYPE_QUIZ, false);
		$this->view->show();
	}
	
	private function saveTemplate() {
		$templateMapper = new WpProQuiz_Model_TemplateMapper();
		
		if(isset($this->_post['resultGradeEnabled'])) {
			$this->_post['result_text'] = $this->filterResultTextGrade($this->_post);
		}
			
		$quiz = new WpProQuiz_Model_Quiz($this->_post);
			
		if($quiz->isPrerequisite() && !empty($this->_post['prerequisiteList']) && !$quiz->isStatisticsOn()) {
			$quiz->setStatisticsOn(true);
			$quiz->setStatisticsIpLock(1440);
		}
	
		$form = $this->_post['form'];
		
		unset($form[0]);
		
		$forms = array();
		
		foreach($form as $f) {
			$f['fieldname'] = trim($f['fieldname']);
				
			if(empty($f['fieldname']))
				continue;
				
			if((int) $f['form_id'] && (int) $f['form_delete']) {
				continue;
			}
				
			if($f['type'] == WpProQuiz_Model_Form::FORM_TYPE_SELECT || $f['type'] == WpProQuiz_Model_Form::FORM_TYPE_RADIO) {
				if(!empty($f['data'])) {
					$items = explode("\n", $f['data']);
					$f['data'] = array();
						
					foreach ($items as $item) {
						$item = trim($item);
		
						if(!empty($item))
							$f['data'][] = $item;
					}
				}
			}
				
			if(empty($f['data']) || !is_array($f['data']))
				$f['data'] = null;
				
			$forms[] = new WpProQuiz_Model_Form($f);
		}
		
		WpProQuiz_View_View::admin_notices(__('Template stored', 'wp-pro-quiz'), 'info');
		
		$data = array(
			'quiz' => $quiz,
			'forms' => $forms,
			'prerequisiteQuizList' => isset($this->_post['prerequisiteList']) ? $this->_post['prerequisiteList'] : array()
		);
		
		$template = new WpProQuiz_Model_Template();
		
		if($this->_post['templateSaveList'] == '0') {
			$template->setName(trim($this->_post['templateName']));
		} else {
			$template = $templateMapper->fetchById($this->_post['templateSaveList'], false);
		}
		
		$template->setType(WpProQuiz_Model_Template::TEMPLATE_TYPE_QUIZ);
		$template->setData($data);
		
		$templateMapper->save($template);
		
		return $template;
	}
	
	private function formHandler($quizId, $post) {
		if(!isset($post['form']))
			return false;
		
		$form = $post['form'];
		
		unset($form[0]);
		
		if(empty($form))
			return false;
		
		$formMapper = new WpProQuiz_Model_FormMapper();
		
		$deleteIds = array();
		$forms = array();
		$sort = 0;
		
		foreach($form as $f) {
			$f['fieldname'] = trim($f['fieldname']);
			
			if(empty($f['fieldname']))
				continue;
			
			if((int) $f['form_id'] && (int) $f['form_delete']) {
				$deleteIds[] = (int) $f['form_id'];
				continue;				
			}
			
			$f['sort'] = $sort++;
			$f['quizId'] = $quizId;
			
			if($f['type'] == WpProQuiz_Model_Form::FORM_TYPE_SELECT || $f['type'] == WpProQuiz_Model_Form::FORM_TYPE_RADIO) {
				if(!empty($f['data'])) {
					$items = explode("\n", $f['data']);
					$f['data'] = array();
					
					foreach ($items as $item) {
						$item = trim($item);
						
						if(!empty($item))
							$f['data'][] = $item;
					}
				}
			}
			
			if(empty($f['data']) || !is_array($f['data']))
				$f['data'] = null;
			
			$forms[] = new WpProQuiz_Model_Form($f);
		}
		
		if(!empty($deleteIds))
			$formMapper->deleteForm($deleteIds, $quizId);
		
		$formMapper->update($forms);
		
		return !empty($forms);
	}
	
	private function deleteAction($id) {
		
		if(!current_user_can('wpProQuiz_delete_quiz')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		
		$m = new WpProQuiz_Model_QuizMapper();
// 		$qm = new WpProQuiz_Model_QuestionMapper();
// 		$lm = new WpProQuiz_Model_LockMapper();
// 		$srm = new WpProQuiz_Model_StatisticRefMapper();
// 		$pm = new WpProQuiz_Model_PrerequisiteMapper();
// 		$tm = new WpProQuiz_Model_ToplistMapper();
		
// 		$m->delete($id);
// 		$qm->deleteByQuizId($id);
// 		$lm->deleteByQuizId($id);
// 		$srm->deleteAll($id);
// 		$pm->delete($id);
// 		$tm->delete($id);
		
		$m->deleteAll($id);
		
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
		$categoryMapper = new WpProQuiz_Model_CategoryMapper();
		
		$is100P = $this->_post['results']['comp']['result'] == 100;
		
		$userId = get_current_user_id();
		
		$quiz = $quizMapper->fetch($this->_post['quizId']);

		if($quiz === null || $quiz->getId() <= 0) {
			exit;
		}

		$categories = $categoryMapper->fetchByQuiz($quiz->getId());
		
		$this->setResultCookie($quiz);
		
		$this->emailNote($quiz, $this->_post['results']['comp'], $categories);
		
		if(!$this->isPreLockQuiz($quiz)) {
			$statistics = new WpProQuiz_Controller_Statistics();
			$statistics->save($quiz);
			
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
			$statistics->save($quiz);

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
}
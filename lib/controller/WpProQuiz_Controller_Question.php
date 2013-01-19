<?php
class WpProQuiz_Controller_Question extends WpProQuiz_Controller_Controller {
	
	private $_quizId;
	
	public function route() {
		
		if(!isset($_GET['quiz_id']) || empty($_GET['quiz_id'])) {
			WpProQuiz_View_View::admin_notices(__('Quiz not found', 'wp-pro-quiz'), 'error');
			return;
		}
		
		$this->_quizId = (int)$_GET['quiz_id'];
		$action = isset($_GET['action']) ? $_GET['action'] : 'show';
		
		$m = new WpProQuiz_Model_QuizMapper();
		
		if($m->exists($this->_quizId) == 0) {
			WpProQuiz_View_View::admin_notices(__('Quiz not found', 'wp-pro-quiz'), 'error');
			return;
		}
		
		switch ($action) {
			case 'add':
				$this->createAction();
				break;
			case 'show':
				$this->showAction();
				break;
			case 'edit':
				$this->editAction($_GET['id']);
				break;
			case 'delete':
				$this->deleteAction($_GET['id']);
				break;
			case 'save_sort':
				$this->saveSort($_GET['id']);
				break;
			case 'load_question':
				$this->loadQuestion($_GET['quiz_id']);
				break;
			case 'copy_question':
				$this->copyQuestion($_GET['quiz_id']);
				break;
		}
	}
	
	public function copyQuestion($quizId) {
		
		if(!current_user_can('wpProQuiz_edit_quiz')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		
		$m = new WpProQuiz_Model_QuestionMapper();
		
		$questions = $m->fetchById($this->_post['copyIds']);
		
		foreach($questions as $question) {
			$question->setId(0);
			$question->setQuizId($quizId);
			
			$m->save($question);
		}
		
		WpProQuiz_View_View::admin_notices(__('questions copied', 'wp-pro-quiz'), 'info');
		
		$this->showAction();
	}
	
	public function loadQuestion($quizId) {
		
		if(!current_user_can('wpProQuiz_edit_quiz')) {
			echo json_encode(array());
			exit;
		}
		
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		$questionMapper = new WpProQuiz_Model_QuestionMapper();
		$data = array();
		
		$quiz = $quizMapper->fetchAll();
		
		foreach($quiz as $qz) {
			
			if($qz->getId() == $quizId)
				continue;
			
			$question = $questionMapper->fetchAll($qz->getId());
			$questionArray = array();
			
			foreach($question as $qu) {
				$questionArray[] = array(
					'name' => $qu->getTitle(),
					'id' => $qu->getId()
				);
			}
			
			$data[] = array(
				'name' => $qz->getName(),
				'id' => $qz->getId(),
				'question' => $questionArray
			);
		}
		
		echo json_encode($data);
		
		exit;
	}
	
	public function saveSort($quizId) {
		
		if(!current_user_can('wpProQuiz_edit_quiz')) {
			exit;
		}
		
		$mapper = new WpProQuiz_Model_QuestionMapper();
		$map = $this->_post['sort'];
		
		foreach($map as $k => $v)
			$mapper->updateSort($v, $k);
		
		exit;
	}
	
	public function deleteAction($id) {
		
		if(!current_user_can('wpProQuiz_delete_quiz')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		
		$mapper = new WpProQuiz_Model_QuestionMapper();
		$mapper->delete($id);
		
		$sm = new WpProQuiz_Model_StatisticMapper();
		$sm->deleteByQuestionId($id);
		
		$this->showAction();
	}
	
	public function editAction($id) {
		
		if(!current_user_can('wpProQuiz_edit_quiz')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		
		$this->view	= new WpProQuiz_View_QuestionEdit();
		$this->view->header = __('Edit question', 'wp-pro-quiz');
		$mapper 	= new WpProQuiz_Model_QuestionMapper();
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		
		$this->view->quiz = $quizMapper->fetch($id);
		
		if($mapper->exists($id) == 0) {
			WpProQuiz_View_View::admin_notices(__('Question not found', 'wp-pro-quiz'), 'error');
			return;
		}
		
		if(isset($this->_post['submit'])) {
			$post = $this->clearPost($this->_post);
			$post['id'] = $id;
			
			$post['title'] = isset($post['title']) ? trim($post['title']) : '';
				
			if(empty($post['title'])) {
				$question = $mapper->fetch($id);

				$post['title'] = sprintf(__('Question: %d', 'wp-pro-quiz'), $question->getSort()+1);
			}

			$post['pointsAnswer'] = $post['points'];
			
			if(isset($post['pointsPerAnswer'])) {
				$post['points'] = $this->generatePointsAnswer($post);
			}
			
			$mapper->save(new WpProQuiz_Model_Question($post));
			WpProQuiz_View_View::admin_notices(__('Question edited', 'wp-pro-quiz'), 'info');
		}
		
		$this->view->question = $mapper->fetch($id);
		
		if($this->view->question->isPointsPerAnswer()) {
			$this->view->question->setPoints($this->view->question->getPointsAnswer());
		}
		
		$this->view->show();
	}
	
	public function createAction() {
		
		if(!current_user_can('wpProQuiz_add_quiz')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		
		$this->view = new WpProQuiz_View_QuestionEdit();
		$this->view->header = __('New question', 'wp-pro-quiz');
		$post = null;
		
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		
		$this->view->quiz = $quizMapper->fetch($this->_quizId);
		
		if(isset($this->_post['submit'])) {
			$post = $this->clearPost($this->_post);
			
			$questionMapper = new WpProQuiz_Model_QuestionMapper();
			
			$post['title'] = isset($post['title']) ? trim($post['title']) : '';
			
			if(empty($post['title'])) {
				$count = $questionMapper->count($this->_quizId);

				$post['title'] = sprintf(__('Question: %d', 'wp-pro-quiz'), $count+1);
			}
			
			$post['pointsAnswer'] = $post['points'];
			
			if(isset($post['pointsPerAnswer'])) {
				$post['points'] = $this->generatePointsAnswer($post);
			}
			
			$questionMapper->save(new WpProQuiz_Model_Question($post));
			
			WpProQuiz_View_View::admin_notices(__('Question added', 'wp-pro-quiz'), 'info');
			
			$this->showAction();
			return;
		}
		
		$this->view->question = new WpProQuiz_Model_Question($post);
		$this->view->question->setQuizId($this->_quizId);
		
		if($this->view->question->isPointsPerAnswer()) {
			$this->view->question->setPoints($this->view->question->getPointsAnswer());
		}
		
		$this->view->show();
	}
	
	public function clearPost($post) {
		$j = array();
		
		switch($post['answerType']) {
			
			
			case 'single':
			case 'multiple':
				$j = array('classic_answer' => $post['answerJson']['classic_answer']);
				break;
			case 'free_answer':
				$j = array('free_answer' => $post['answerJson']['free_answer']);
				break;
			case 'sort_answer':
				$j = array('answer_sort' => $post['answerJson']['answer_sort']);
				break;
			case 'matrix_sort_answer':
				$j = array('answer_matrix_sort' => $post['answerJson']['answer_matrix_sort']);
				break;
			case 'cloze_answer':
				$j = array('answer_cloze' => $post['answerJson']['answer_cloze']);
				break;
					
		}

		unset($post['answerJson']);
		
		$post['answerJson'] = $j;			
		$post['answerJson'] = $this->clear($post['answerJson']);
		$post['quizId'] = $this->_quizId;
		
		return $post;
	}
	
	public function clear($a) {
		foreach($a as $k => $v) {
			if(is_array($v)) {
 				$a[$k] = $this->clear($a[$k]);
			}

			if(is_string($a[$k])) {
				$a[$k] = trim($a[$k]);

				if($a[$k] != '') {
					continue;
				}
			}
			
			if(empty($a[$k])) {
				unset($a[$k]);				
			}
		}
		
		return $a;
	}
	
	public function showAction() {
		
		if(!current_user_can('wpProQuiz_show')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		
		$m = new WpProQuiz_Model_QuizMapper();
		$mm = new WpProQuiz_Model_QuestionMapper();
		
		$this->view = new WpProQuiz_View_QuestionOverall();
		$this->view->quiz = $m->fetch($this->_quizId);
		$this->view->question = $mm->fetchAll($this->_quizId);
		$this->view->show();
	}
	
	private function generatePointsAnswer($post) {
		switch($post['answerType']) {
			case 'single':
			case 'multiple':
				return count($post['answerJson']['classic_answer']['correct']) * $post['pointsAnswer'];
			case 'free_answer':
				return $post['pointsAnswer'];
			case 'sort_answer':
				return count($post['answerJson']['answer_sort']['answer']) * $post['pointsAnswer'];
			case 'matrix_sort_answer':
				return count($post['answerJson']['answer_matrix_sort']['answer']) * $post['pointsAnswer'];
			case 'cloze_answer':
				return preg_match_all('#\{(.*?)\}#', $post['answerJson']['answer_cloze']['text']) * $post['pointsAnswer'];
				break;
		}
	}
}
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
		}
	}
	
	public function saveSort($quizId) {
		$mapper = new WpProQuiz_Model_QuestionMapper();
		$map = $this->_post['sort'];
		
		foreach($map as $k => $v)
			$mapper->updateSort($v, $k);
		
		die();
	}
	
	public function deleteAction($id) {
		$mapper = new WpProQuiz_Model_QuestionMapper();
		$mapper->delete($id);
		
		$this->showAction();
	}
	
	public function editAction($id) {
		$this->view	= new WpProQuiz_View_QuestionEdit();
		$this->view->header = __('Edit question', 'wp-pro-quiz');
		$mapper 	= new WpProQuiz_Model_QuestionMapper();
		
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
			
			$mapper->save(new WpProQuiz_Model_Question($post));
			WpProQuiz_View_View::admin_notices(__('Question edited', 'wp-pro-quiz'), 'info');
		} 
		
		$this->view->question = $mapper->fetch($id);
		$this->view->show();
	}
	
	public function createAction() {
		$this->view = new WpProQuiz_View_QuestionEdit();
		$this->view->header = __('New question', 'wp-pro-quiz');
		$post = null;

		if(isset($this->_post['submit'])) {
			$post = $this->clearPost($this->_post);
			
			$questionMapper = new WpProQuiz_Model_QuestionMapper();
			
			$post['title'] = isset($post['title']) ? trim($post['title']) : '';
			
			if(empty($post['title'])) {
				$count = $questionMapper->count($this->_quizId);

				$post['title'] = sprintf(__('Question: %d', 'wp-pro-quiz'), $count+1);
			}
			
			$questionMapper->save(new WpProQuiz_Model_Question($post));
			
			WpProQuiz_View_View::admin_notices(__('Question added', 'wp-pro-quiz'), 'info');
			
			$this->showAction();
			return;
		}
		
		$this->view->question = new WpProQuiz_Model_Question($post);
		$this->view->question->setQuizId($this->_quizId);
		$this->view->show();
	}
	
	public function clearPost($post) {
		switch($post['answerType']) {
			case 'single':
			case 'multiple':
				unset($post['answerJson']['answer_sort']);
				unset($post['answerJson']['free_answer']);
				unset($post['answerJson']['matrix_sort_answer']);
				break;
			case 'free_answer':
				unset($post['answerJson']['answer_sort']);
				unset($post['answerJson']['classic_answer']);
				unset($post['answerJson']['matrix_sort_answer']);
				break;
			case 'sort_answer':
				unset($post['answerJson']['free_answer']);
				unset($post['answerJson']['classic_answer']);
				unset($post['answerJson']['matrix_sort_answer']);
				break;
			case 'matrix_sort_answer':
				unset($post['answerJson']['answer_sort']);
				unset($post['answerJson']['free_answer']);
				unset($post['answerJson']['classic_answer']);
				break;
					
		}
			
		$post['answerJson'] = $this->clear($post['answerJson']);
		$post['quizId'] = $this->_quizId;
		
		return $post;
	}
	
	public function clear($a) {
		foreach($a as $k => $v) {
			if(is_array($v)) {
 				$a[$k] = $this->clear($a[$k]);
			}
						
			if(empty($a[$k])) {
				unset($a[$k]);				
			}
		}
		
		return $a;
	}
	
	public function showAction() {
		$m = new WpProQuiz_Model_QuizMapper();
		$mm = new WpProQuiz_Model_QuestionMapper();
		
		$this->view = new WpProQuiz_View_QuestionOverall();
		$this->view->quiz = $m->fetch($this->_quizId);
		$this->view->question = $mm->fetchAll($this->_quizId);
		$this->view->show();
	}
}
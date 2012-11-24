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
		}
	}
	
	private function showAction() {
		$this->view = new WpProQuiz_View_QuizOverall();
		
		$m = new WpProQuiz_Model_QuizMapper();
		$this->view->quiz = $m->fetchAll();
		
		$this->view->show();
	}
	
	private function editAction($id) {
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
		$m = new WpProQuiz_Model_QuizMapper();
		$m->delete($id);
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
}
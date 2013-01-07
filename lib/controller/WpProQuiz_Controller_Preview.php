<?php
class WpProQuiz_Controller_Preview extends WpProQuiz_Controller_Controller {
	
	public function route() {
		
		wp_enqueue_script(
			'wpProQuiz_fron_javascript', 
			plugins_url('js/wpProQuiz_front.min.js', WPPROQUIZ_FILE),
			array('jquery', 'jquery-ui-sortable'),
			WPPROQUIZ_VERSION
		);
		
		wp_enqueue_style(
			'wpProQuiz_front_style', 
			plugins_url('css/wpProQuiz_front.min.css', WPPROQUIZ_FILE),
			array(),
			WPPROQUIZ_VERSION
		);
		
		$this->showAction($_GET['id']);
	}
	
	public function showAction($id) {
		$view = new WpProQuiz_View_FrontQuiz();
		
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		$questionMapper = new WpProQuiz_Model_QuestionMapper();
		
		$view->quiz = $quizMapper->fetch($id);
		$view->question = $questionMapper->fetchAll($id);
		
		$view->show(true);
	}
}
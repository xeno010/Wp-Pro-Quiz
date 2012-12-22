<?php
class WpProQuiz_Controller_GlobalSettings extends WpProQuiz_Controller_Controller {
	
	public function route() {
		$this->edit();
	}
	
	private function edit() {
		$mapper = new WpProQuiz_Model_GlobalSettingsMapper();
		$view = new WpProQuiz_View_GobalSettings();
		
		if(isset($this->_post['submit'])) {
			$mapper->save(new WpProQuiz_Model_GlobalSettings($this->_post));
			WpProQuiz_View_View::admin_notices(__('Settings saved', 'wp-pro-quiz'), 'info');
		}
				
		$view->settings = $mapper->fetchAll();
		$view->isRaw = !preg_match('[raw]', apply_filters('the_content', '[raw]a[/raw]'));
		
		$view->show();
	}
}
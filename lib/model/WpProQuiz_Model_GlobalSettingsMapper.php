<?php
class WpProQuiz_Model_GlobalSettingsMapper extends WpProQuiz_Model_Mapper {
	
	public function fetchAll() {
		$s = new WpProQuiz_Model_GlobalSettings();
		
		$s->setAddRawShortcode(get_option('wpProQuiz_addRawShortcode'))
			->setJsLoadInHead(get_option('wpProQuiz_jsLoadInHead'))
			->setTouchLibraryDeactivate(get_option('wpProQuiz_touchLibraryDeactivate'));
		
		return $s;
	}
	
	public function save(WpProQuiz_Model_GlobalSettings $settings) {
		
		if(add_option('wpProQuiz_addRawShortcode', $settings->isAddRawShortcode()) === false) {
			update_option('wpProQuiz_addRawShortcode', $settings->isAddRawShortcode());
		}
		
		if(add_option('wpProQuiz_jsLoadInHead', $settings->isJsLoadInHead()) === false) {
			update_option('wpProQuiz_jsLoadInHead', $settings->isJsLoadInHead());
		}
		
		if(add_option('wpProQuiz_touchLibraryDeactivate', $settings->isTouchLibraryDeactivate()) === false) {
			update_option('wpProQuiz_touchLibraryDeactivate', $settings->isTouchLibraryDeactivate());
		}
	}
	
	public function delete() {
		delete_option('wpProQuiz_addRawShortcode');
		delete_option('wpProQuiz_jsLoadInHead');
		delete_option('wpProQuiz_touchLibraryDeactivate');
	}
}
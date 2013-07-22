<?php
class WpProQuiz_Model_CategoryMapper extends WpProQuiz_Model_Mapper {
	
	public function fetchAll() {
		$r = array();
		
		$results = $this->_wpdb->get_results("SELECT * FROM {$this->_tableCategory}", ARRAY_A);
		
		foreach ($results as $row) {
			$r[] =  new WpProQuiz_Model_Category($row);
		}
		
		return $r;
	}
	
	public function fetchByQuiz($quizId) {
		$r = array();
		
		$results = $this->_wpdb->get_results($this->_wpdb->prepare('
			SELECT 
				c.*
			FROM
				'.$this->_tableCategory.' AS c
				RIGHT JOIN '.$this->_tableQuestion.' AS q
			        	ON c.category_id = q.category_id
			WHERE
				q.quiz_id = %d
			GROUP BY
		          q.category_id
			ORDER BY
       			c.category_name
		', $quizId), ARRAY_A);
		
		foreach($results as $row) {
			$r[] = new WpProQuiz_Model_Category($row);
		}
		
		return $r;
	}
	
	public function save(WpProQuiz_Model_Category $category) {
		$data = array('category_name' => $category->getCategoryName());
		$format = array('%s');
		
		if($category->getCategoryId() == 0) {
			$this->_wpdb->insert($this->_tableCategory, $data, $format);
			$category->setCategoryId($this->_wpdb->insert_id);
		} else {
			$this->_wpdb->update(
				$this->_tableCategory, 
				$data, 
				array('category_id' => $category->getCategoryId()),
				$format,
				array('%d'));
		}
		
		return $category;
	}
	
	public function delete($categoryId) {
		$this->_wpdb->update($this->_tableQuestion, array('category_id' => 0), array('category_id' => $categoryId), array('%d'), array('%d'));
		
		return $this->_wpdb->delete($this->_tableCategory, array('category_id' => $categoryId), array('%d'));
	}
	
	public function getCategoryArrayForImport() {
		$r = array();
		
		$results = $this->_wpdb->get_results("SELECT * FROM {$this->_tableCategory}", ARRAY_A);
		
		foreach ($results as $row) {
			$r[strtolower($row['category_name'])] = (int)$row['category_id'];
		}
		
		return $r;
	}
}
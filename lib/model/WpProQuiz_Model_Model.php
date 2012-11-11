<?php
class WpProQuiz_Model_Model {
	
	/**
	 * @var WpProQuiz_Model_QuizMapper
	 */
	protected $_mapper = null;
	
	public function __construct($array = null) {
		if($array != null) {
// 			$a = get_object_vars($this);
// 			foreach($array as $k => $v) {
// 				if(array_key_exists('_'.$k, $a)) {
// 					$this->{'_'.$k} = $v;
// 				}
// 			}
			
			foreach($array as $k => $v) {
// 				if(array_key_exists('_'.$k, $a)) {
// 					$this->{'_'.$k} = $v;
// 				}
				
				if(strpos($k, '_') !== false) {
					$k = str_replace(' ', '', ucwords(str_replace('_', ' ', $k)));
				}
				
				$this->{'set'.ucfirst($k)}($v);
			}
		}
	}
	
	public function __call($name, $args) {
	}
	
	/**
	 * 
	 * @return WpProQuiz_Model_QuizMapper
	 */
	public function getMapper() {
		if($this->_mapper === null) {
			$this->_mapper = new WpProQuiz_Model_QuizMapper();
		}

		return $this->_mapper;
	}
	
	/** 
	 * @param WpProQuiz_Model_QuizMapper $mapper
	 * @return WpProQuiz_Model_Model
	 */
	public function setMapper($mapper) {
		$this->_mapper = $mapper;
		return $this;
	}
}
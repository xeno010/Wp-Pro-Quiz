<?php
class WpProQuiz_Controller_Controller {
	protected $_post = null;
	
	public function __construct() {
		if($this->_post === null) {
			$this->_post = stripslashes_deep($_POST);
		}
	}
}
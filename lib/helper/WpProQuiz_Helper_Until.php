<?php
class WpProQuiz_Helper_Until {
	
	public static function saveUnserialize($str, &$into) {
		static $serializefalse;
		
		if ($serializefalse === null)
			$serializefalse = serialize(false);
		
		$into = @unserialize($str);
		
		return $into !== false || rtrim($str) === $serializefalse;
	}
}
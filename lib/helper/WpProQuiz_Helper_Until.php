<?php
class WpProQuiz_Helper_Until {
	
	public static function saveUnserialize($str, &$into) {
		static $serializefalse;
		
		if ($serializefalse === null)
			$serializefalse = serialize(false);
		
		$into = @unserialize($str);
		
		return $into !== false || rtrim($str) === $serializefalse;
	}
	
	public static function convertTime($time, $format) {
		$time = $time + get_option('gmt_offset') * 60 * 60;
		
		return date_i18n($format, $time);
	}
}
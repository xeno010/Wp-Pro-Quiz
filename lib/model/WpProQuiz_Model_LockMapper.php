<?php
class WpProQuiz_Model_LockMapper extends WpProQuiz_Model_Mapper {

	protected $_table;
	
	public function __construct() {
		parent::__construct();
	
		$this->_table = $this->_prefix.'lock';
	}
	
	public function insert(WpProQuiz_Model_Lock $lock) {
		$this->_wpdb->insert($this->_table, array(
			'quiz_id' => $lock->getQuizId(),
			'lock_ip' => $lock->getLockIp(),
			'lock_date' => $lock->getLockDate()
		), 
		array('%d', '%s', '%d'));
	}
	
	public function fetch($quizId, $lockIp) {
		$row = $this->_wpdb->get_row(
			$this->_wpdb->prepare(
				"SELECT
					*
				FROM
					". $this->_table. "
				WHERE
					quiz_id = %d 
				AND
					lock_ip = %s",
				$quizId, $lockIp)
		);
		
		if($row === null)
			return null;
		
		$model = new WpProQuiz_Model_Lock();
		$model->setQuizId($row->quiz_id)
				->setLockIp($row->lock_id)
				->setLockDate($row->lock_date);
		
		return $model;
	}
	
	public function isLock($quizId, $lockIp) {
		$c = $this->_wpdb->get_var(
				$this->_wpdb->prepare(
						"SELECT COUNT(*) FROM {$this->_table} 
						WHERE quiz_id = %d AND lock_ip = %s", $quizId, $lockIp));
		
		if($c === null || $c == 0)
			return false;
		
		return true;
	}
	
	public function deleteOldLock(WpProQuiz_Model_Quiz $quiz, $time) {
		$lockTime = $quiz->getStatisticsIpLock() * 60;
		
		return $this->_wpdb->query(
				$this->_wpdb->prepare(
					"DELETE FROM {$this->_table}
					WHERE
						quiz_id = %d AND (lock_date + %d) < %d",
					$quiz->getId(),
					$lockTime,
					$time
				)
		);
	}
}
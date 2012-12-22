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
			'user_id' => $lock->getUserId(),
			'lock_type' => $lock->getLockType(),
			'lock_date' => $lock->getLockDate(),
			'lock_type' => $lock->getLockType()
		), 
		array('%d', '%s', '%d', '%d', '%d', '%d'));
	}
	
	public function fetch($quizId, $lockIp, $userId) {
		$row = $this->_wpdb->get_row(
			$this->_wpdb->prepare(
				"SELECT
					*
				FROM
					". $this->_table. "
				WHERE
					quiz_id = %d 
				AND
					lock_ip = %s
				AND
					user_id = %d",
				$quizId, $lockIp, $userId)
		);
		
		if($row === null)
			return null;
		
		return new WpProQuiz_Model_Lock($row);
	}
	
	public function isLock($quizId, $lockIp, $userId, $type) {
		$c = $this->_wpdb->get_var(
				$this->_wpdb->prepare(
						"SELECT COUNT(*) FROM {$this->_table} 
						WHERE quiz_id = %d AND lock_ip = %s AND user_id = %d AND lock_type = %d", $quizId, $lockIp, $userId, $type));
		
		if($c === null || $c == 0)
			return false;
		
		return true;
	}
	
	public function deleteOldLock($lockTime, $quizId, $time, $type, $userId = false) {
		$user = '';
		
		if($userId !== false) {
			$user = 'AND user_id = '.((int) $userId);
		}
		return $this->_wpdb->query(
				$this->_wpdb->prepare(
					"DELETE FROM {$this->_table}
					WHERE
						quiz_id = %d AND (lock_date + %d) < %d AND lock_type = %d ".$user,
					$quizId,
					$lockTime,
					$time,
					$type
				)
		);
	}
	
	public function deleteByQuizId($quizId, $type = false) {
		
		$where = array('quiz_id' => $quizId);
		$whereP = array('%d');
		
		if($type !== false) {
			$where = array('quiz_id' => $quizId, 'lock_type' => $type);
			$whereP = array('%d', '%d');
		}
		
		return $this->_wpdb->delete($this->_tableLock, $where, $whereP);
	}
}
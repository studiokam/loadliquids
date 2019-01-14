<?php
class Company {

	private 
			$_db;

	public function __construct() {
		$this->_db = new Database();
	}

	public function isVerif($id) {

		$_user = $this->_db->getRow("SELECT * FROM users WHERE id = ?", [$id]);

		if ($_user['company_verif'] !== '0') {
			return true;
		} else {
			return false;
		}
		
	}

	public function userName($id) {
		$query = $this->_db->getRow("SELECT * FROM users WHERE id = ?", [$id]);
		return $query['username'];
	}

}
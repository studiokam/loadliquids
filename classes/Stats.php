<?php 
class Stats {
	private 
			$_db;

	public function __construct() {
		$this->_db = new Database();
	}

	// zwraca ilosc wynikow z dziś
	public function Today($table, $column) {
		$users = $this->_db->getRows("SELECT * FROM $table WHERE DATE(`$column`) = CURDATE()");
		return $this->_db->countRows();
	}

	// zwraca ilość wyników z ostatnich x miesięcy
	public function Montch($table, $column, $montch) {
		$users = $this->_db->getRows("SELECT * FROM $table WHERE $column >= DATE_SUB(CURDATE(), INTERVAL $montch MONTH)");
		return $this->_db->countRows();
	}

	// zwraca ilość wszystkich wyników
	public function All($table) {
		$users = $this->_db->getRows("SELECT * FROM $table");
		return $this->_db->countRows();
	}

	// zwraca ilość wszystkich kont które posiadają obecnie premium (gratisowe lub zakupione)
	public function AllPremium() {
		$allpremium = $this->_db->getRows("SELECT * FROM users WHERE DATE(`premium_date`) > CURDATE()");
		return $this->_db->countRows();
	}

	// zwraca ilość wszystkich kont które posiadają obecnie premium (gratisowe lub zakupione)
	public function AllPremiumBetween($start, $end) {
		$allpremium = $this->_db->getRows("
			SELECT * FROM users WHERE 
			premium_date > DATE_ADD(CURDATE(),INTERVAL $start DAY) AND 
			premium_date < DATE_ADD(CURDATE(),INTERVAL $end DAY)
			");
		return $this->_db->countRows();
	}

}
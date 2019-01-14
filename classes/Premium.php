<?php
class Premium {
	private 
			$_db,
			$_premium,
			$_tr,
			$_orderStatus;

	



	public function __construct() {
		$this->_db = new Database();
		$this->_tr = Lang::set('class.premium');
	}

	public function isPremium($id) {

		$_user = $this->_db->getRow("SELECT * FROM users WHERE id = ?", [$id]);

		if ($_user['premium_date'] > date('Y-m-d H:i:s')) {
			return true;
		}
		return false;
	}

	public function accountType($id) {
		$_user = $this->_db->getRow("SELECT * FROM users WHERE id = ?", [$id]);

		$time_left_inseconds = strtotime($_user['premium_date']) - time();

		if ($_user['premium_date'] > date('Y-m-d H:i:s')) {
			echo '<span class="account_type_premium">'.Lang::get($this->_tr,'premium.account').'</span> - '.Lang::get($this->_tr,'left').' ';
			echo $this->timeleft($time_left_inseconds );
		} else {
			echo '<a href="premium.php" class="btn btn-green btn-ssm ">'.Lang::get($this->_tr,'to.btn.buy.premium').'</a><span class="account_type">'.Lang::get($this->_tr,'account.type').'</span> ';
		}
		
	}


	/* returns an array of [days],[hours],[minutes],[seconds] time left from now to timestamp given */ 
	public function timeleft($time_left=0, $endtime=null) { 
	    if($endtime != null) 
	        $time_left = $endtime - time(); 
	    if($time_left > 0) { 
	        $days = floor($time_left / 86400); 
	        $time_left = $time_left - $days * 86400; 
	        $hours = floor($time_left / 3600); 
	        $time_left = $time_left - $hours * 3600; 
	        $minutes = floor($time_left / 60); 
	        $seconds = $time_left - $minutes * 60; 

			return $days.'d '.$hours.'h '.$minutes.'m '.$seconds.'s';

	    }
	}

	public function FreePremium() {
		$query = $this->_db->getRow("SELECT * FROM admin_settings");
		$freePremium = $query['free_premium'];
		return $freePremium;
	}

	function addDayswithdate($date,$days){

	    $date = strtotime("+".$days." days", strtotime($date));
	    return  date("Y-m-d H:i:s", $date);

	}

	
}
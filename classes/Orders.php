<?php 
class Orders {
	private 
			$_db,
			$_countries,
			$_orderStatus;

	public function __construct() {
		$this->_db = new Database();
	}

	public function country($id) {
		$_countries = $this->_db->getRow("SELECT * FROM countries WHERE id = ?", [$id]);
		return $_countries['country'];
	}

	public function shortcut($id) {
		$_countries = $this->_db->getRow("SELECT * FROM countries WHERE id = ?", [$id]);
		return $_countries['shortcut'];
	}

	public function carType($id) {
		$_cartype = $this->_db->getRow("SELECT * FROM car_types WHERE id = ?", [$id]);
		return $_cartype['car_type'];
	}
	
	// sprawdzenie globalnego numeru id zamówienia (globalnego - zamówienia są w load_orders i car_orders i tam amja swoje numery a w orders jest globalny numer) po podaniu numeru usera i typu zamówienia
	public function orderId($id, $type) {
		$orderId = $this->_db->getRow("SELECT * FROM orders WHERE car_or_load_id = ? AND order_type = ?", [$id, $type]);
		return $orderId['id'];
	}

	public function xxx($table, $id) {
		$orderId = $this->_db->getRow("SELECT * FROM $table WHERE id = ?", [$id]);
		return $orderId;
	}

	

	public function additional($type, $id) {
		$_cartype = $this->_db->getRow("SELECT * FROM $type WHERE id = ?", [$id]);
		
		if ($_cartype['compressor']) {
			$compressor = 'Kompresor, ';
		} else {
			$compressor = '';
		}

		if ($_cartype['pump']) {
			$pump = 'Pompa, ';
		} else {
			$pump = '';
		}

		if ($_cartype['adr']) {
			$adr = 'ADR, ';
		} else {
			$adr = '';
		}

		if ($_cartype['gps']) {
			$gps = 'GPS, ';
		} else {
			$gps = '';
		}

		if ($_cartype['ready_to_ride']) {
			$ready_to_ride = 'Gotowy do jazdy';
		} else {
			$ready_to_ride = '';
		}

		return $compressor .$pump .$adr. $gps. $ready_to_ride;
	}

	public function orderStatus($type, $id) {
		$_orderStatus = $this->_db->getRow("SELECT * FROM $type WHERE id = ?", [$id]);
		if ($_orderStatus['order_status'] === '1') {
			return 'Aktywne';
		} elseif($_orderStatus['order_status'] === '0') {
			return 'Zamknięte';
		} else {
			return '-';
			// return $_orderStatus['order_status'];
		}
	}

	public function orderStatusClose($user_id, $order_id, $order_type) {
		if ($order_type === 'load') {
			$type = 'load_orders';
		} elseif($order_type === 'car') {
			$type = 'car_orders';
		} else {
			$type = '';
		}

		$owner = $this->_db->getRow("SELECT * FROM $type WHERE id = ?", [$order_id]);

		if ($owner['user_id'] === $user_id ) {
			return true;
		} else {
			return false;
		}
		
	}

	
}
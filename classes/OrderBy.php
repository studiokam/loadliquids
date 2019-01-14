<?php 
class OrderBy {
	
	private $_title;
	

	public function sort($column) {
		switch ($column) {
			// rd - register date
			case 'rd': 
				$this->_title = 'Rejestracja od najnowszej';
				return 'ORDER BY register';
			break;

			case 'rdup':
				$this->_title = 'Rejestracja od najstarszej';
				return 'ORDER BY register DESC';
			break;

			case 'az':
				$this->_title = 'Alfabetycznie A-Z';
				return 'ORDER BY username';
			break;

			case 'za':
				$this->_title = 'Alfabetycznie Z-A';
				return 'ORDER BY username DESC';
			break;

			case 'eaz':
				$this->_title = 'Email A-Z';
				return 'ORDER BY email';
			break;

			case 'eza':
				$this->_title = 'Email Z-A';
				return 'ORDER BY email DESC';
			break;

			case 'date':
				$this->_title = 'Od najstarszego';
				return 'ORDER BY id';
			break;

			case 'dateup':
				$this->_title = 'Od najnowszego';
				return 'ORDER BY id DESC';
			break;
			
		}
	}

	// szuka w username i email
	public function search($search) {
		return "WHERE username LIKE '%$search%' OR email LIKE '%$search%' ";
	}

	// szuka id zamowienia
	public function searchInOrders($search) {
		return "WHERE id LIKE '%$search%' ";
	}

	public function title() {
		return $this->_title;
	}

	
}
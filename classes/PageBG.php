<?php 
//
// klasa wyswietla tło strony ustawiane w zapleczu
// 
class PageBG {

	public $bg;

	public function __construct() {
		$db = new Database();
		$settings = $db->getRow("SELECT * FROM admin_settings");
		$this->bg = $settings['bg_image'];
	}

	public function get() {
		return $this->bg;
	}

	
}
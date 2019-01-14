<?php 
class Validate {
	private $_passed = false,
			$_errors = array(),
			$_db = null;

	public function __construct() {
		$this->_db = DB::getInstance();
	}

	public function check($source, $items=array()) {
		foreach ($items as $item => $rules) {
			foreach ($rules as $rule => $rule_value) {
				
				$value = trim($source[$item]);
				$item = escape($item);

				if ($rule === 'required' && empty($value)) {
					$this->addError("{$item} is required");
				} else if(!empty($value)) {
					switch ($rule) {
						case 'min':
							if (strlen($value) < $rule_value) {
								$this->addError("{$item} must be a minimum of {$rule_value} characters.");
							}
						break;

						case 'max':
							if (strlen($value) > $rule_value) {
								
								if ($item === 'car_type_id') {
									$this->addError("Proszę wybrać typ auta");
								} elseif ($item === 'in_country_id') {
								$this->addError("Proszę wybrać Państwo załadunku.");
								} elseif ($item === 'out_country_id') {
								$this->addError("Proszę wybrać Państwo rozaładunku.");
								}else {
								$this->addError("{$item} must be a maximum of {$rule_value} characters.");
								}
							}
						break;

						case 'matches':
							if ($value != $source[$rule_value]) {
								$this->addError("{$rule_value} must match {$item}");
							}
						break;

						case 'unique':
							$check = $this->_db->get($rule_value, array($item, '=', $value));
							if ($check->count()) {
								$this->addError("{$item} already exists.");
							}
						break;

						case 'numeric':
							if (!is_numeric($value)) {
								$this->addError("{$item} musi być cyfrą/cyframi.");
							}
						break;


						case 'ereg':
							if (!preg_match("/^[0-9 ]+$/", $value)) {
								$this->addError("{$item} tylko cyfry.");
							}
						break;

						case 'az09':
							if (!preg_match("/^[0-9a-zA-Z!@#$%]+$/", $value)) {
								$this->addError("{$item} tylko cyfry, lirty, bez spacji. Dozwolone znaki specjalne: !@#$%");
							}
						break;

						case 'phone09':
							if (!preg_match("/^[0-9 +]+$/", $value)) {
								$this->addError("{$item} tylko cyfry, lirty, bez spacji. Dozwolone znaki specjalne: !@#$%");
							}
						break;

						case 'hour':
							if (!preg_match("/^([01][0-9]|2[0-3]):([0-5][0-9])$/", $value)) {
								$this->addError("{$item} tylko format 12:22 a zakres to od 00:00 do 23:59");
							}
						break;

						case 'az09_space_special':
							if (!preg_match("/^[0-9a-zA-Z!@#$% ]+$/", $value)) {
								$this->addError("{$item} tylko cyfry, lirty, bez spacji. Dozwolone znaki specjalne: !@#$%");
							}
						break;

						case 'match_in_db':
							$user = new User();
							if ($user->data()->password !== Hash::make($value, $user->data()->salt)) {
								$this->addError("{$item} błędne obecne hasło");
							}
						break;
						
						default:
							# code...
						break;
					}
				}
			}
		}

		if (empty($this->_errors)) {
			$this->_passed = true;
		}

		return $this;
	}

	private function addError($error) {
		$this->_errors[] = $error;
	}

	public function errors() {
		return $this->_errors;
	}

	public function passed() {
		return $this->_passed;
	}
}
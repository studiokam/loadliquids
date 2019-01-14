<?php 
class Drop {

	// funkcja potrzebuje w argumentach:
	// options - opcje wyświetlanej listy, klucz wartość gdzie klucz wstawiany jest do value
	// get - nazwa przycisku $_GET
	// in_db - przekazana aktualnie zapisana wartość w DB
	public static function getList($options, $get, $in_db) 
	{
		foreach ($options as $key => $value) {
			$test = (Input::get($get) == $key ? 'selected' : ($in_db == $key) ? 'selected' : '' );

			echo'<option value="' .$key. '" '. $test .'>'  .$value. '</option>';
		}
	}
}
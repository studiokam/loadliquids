<?php
class Lang
{

	public static function get($lang, $name)
	{
		return $lang[$name];
	}


	// lang z obsługa cache
	public static function set($page) 
	{
		$cache_file = 'cache/'.$page.'.txt';			// nazwa/ścieżka do pliku cache

		if (file_exists($cache_file)) {					// sprawdzenie czy jest plik cache 
			$file = file_get_contents($cache_file);		// odczytanie pliku
			$tr = unserialize($file);		
			return $tr;
		} else{
			
			$db = new Database();

			$main_lang = $db->getRow("SELECT lang FROM admin_settings");
			$lang_set = $main_lang['lang'];

			$lang = $db->getRows("SELECT lang_key, $lang_set FROM lang WHERE page = ?", [$page]);		// pobranie danego tłumaczenia

			$wynik = array_column($lang, 'lang_key');		// pobranie do tablicy tylko kluczy (lang_key)
			$wynik2 = array_column($lang, $lang_set);		// pobranie do tablicy tylko danych tłumaczeń
			$tr = array_combine($wynik, $wynik2);			// scalenie tablic do jednej (wyglad klucz:tlumaczenie)

			file_put_contents($cache_file, serialize($tr));	// utworzenie pliku cache 

			return $tr;
		}
	}

	

}
<?php 
require_once 'core/init.php';
$title = 'Zaplecze | loadliquids.com';

// pobranie ustawienia języka z GET
if (isset($_GET['lang'])) {
	$lang = $_GET['lang'];
}else {
	$lang = '';
	echo "brakuje wartosci get (lang)";
}

// sprawdzenie czy jest wyszukiwanie
if (isset($_GET['search'])) {
	$search = $_GET['search'];
}else {
	$search = '';
}

$db = new Database();

// policzenie wszytkich kluczy jezyka
$lang_keys = $db->getRows("SELECT lang_key FROM lang");
$count_lang_keys = $db->countRows();

// policzenie dla danego jezyka
$this_lang = $db->getRows("SELECT pl FROM lang WHERE pl <> '' ");
$count_this_all = $db->countRows();
$lang_percent_pl =  $count_lang_keys - $count_this_all; 


$all_data = $db->getRows("SELECT page, lang_key, $lang, pl FROM lang WHERE page LIKE '%$search%' OR  $lang LIKE '%$search%' ORDER BY $lang <> ''");

// aktualizacja wpisu tłumaczenia
if (isset($_POST['sent'])) {

	$key = array_keys($_POST['name']);
	$value = array_values($_POST['name']);

	$update = $db->updateRow("UPDATE lang SET $lang = ? WHERE lang_key = ?", [$value['0'], $key['0']]);

	// czyszczenie katalogu cache z plików
	$dir = 'cache/';
	foreach(glob($dir.'*.*') as $v){
	    unlink($v);
	}
	
	Session::flash('update', 'Zaktualizowano wpis.');
	Redirect::to('admin-translate-edit.php?lang='.$lang.'');
	
}


include_once 'top-admin.php';
echo '<div class=" stickyfooter">';
include_once 'menu-admin.php';
?>

	<div class="stickyfooter-content my-font">	
		<div class="container admin-content">
		
			<div class="row">
				<div class="col-12">
					<p class="font-md-400">Tłumaczenia - edycja języka</p>
					<p><?php echo 'Brakuje '. $lang_percent_pl .' tłumaczeń. Brakujce pola maja czerwone tło.' ?></p>
				</div>
			</div>
			<hr>
			<?php 
				if (Session::exists('update')) {
						echo '<div class="success-message mb-20">' . Session::flash('update') . '</div>';
					}
			?>

			
				<form method="get" action="">
					<div class="row">
						<div class="col-7 pr-10">
							<input class="form-control form-control-sm" type="text" id="search" name="search" value="<?php echo Input::get('search') ?>">
							<input class="form-control form-control-sm" type="hidden" id="lang" name="lang" value="<?php echo Input::get('lang') ?>">
						</div>
						<div class="col-4 pl-0">
							
							<button type="submit" class="btn btn-green btn-sm">Szukaj (po słowie strona np. "contact" lub słowie tłumaczenia)</button>
						</div>
					</div>
				</form>
			

			<hr>
			<div class="row">
				
				<div class="col-md-12">
					
						<div class="language-box-edit">
							
							<?php
								foreach ($all_data as $row) {
									
									echo '
									<form action="" method="post">
										<div class="form-group '. (empty($row[$lang])? 'tlumaczenia_red':'') .'">
										    <label>Strona: '.$row['page'].' --- Klucz: <b>'.$row['lang_key'].'</b>'.(($lang !== 'pl')? '<br>PL: '.$row['pl'] :'').'</label>
										    <textarea class="form-control" id="'.$row['lang_key'].'" name="name['.$row['lang_key'].']" rows="1">'.$row[$lang].'</textarea>
										</div>
										<input class="btn btn-green btn-sm mb-20" name="sent" type="submit" value="Zapisz">
									</form>
									';
								}
							?>

						</div>

				</div>
			</div>
		
		</div>
	</div><!-- /sticky-footer  -->
    	<!-- /content -->

<?php include_once 'footer-admin.php'; ?>

	</div><!-- /stickyfooter (or first-bg) -->
  </body>
</html>
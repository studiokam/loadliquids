<?php 
require_once 'core/init.php';
$title = 'Zaplecze | loadliquids.com';



$db = new Database();
$admin_settings = $db->getRow("SELECT * FROM admin_settings");


// aktualizacja cen
if (isset($_POST['pln30'])) {

	$validate = new Validate();
	$validation = $validate->check($_POST, array (
		'pln30' => array(
			'required' => true,
			'numeric' => true
		),
		'pln180' => array(
			'required' => true,
			'numeric' => true
		),
		'pln365' => array(
			'required' => true,
			'numeric' => true
		),
		'euro30' => array(
			'required' => true,
			'numeric' => true
		),
		'euro180' => array(
			'required' => true,
			'numeric' => true
		),
		'euro365' => array(
			'required' => true,
			'numeric' => true
		)
	));


	if ($validation->passed()) {

		
		try {
			$updateRow = $db->updateRow("UPDATE admin_settings SET price_pln_30 = ?, price_pln_180 = ?, price_pln_365 = ?,  price_euro_30 = ?,  price_euro_180 = ?,  price_euro_365 = ?  WHERE id = ?", [Input::get('pln30'), Input::get('pln180'), Input::get('pln365'), Input::get('euro30'), Input::get('euro180'), Input::get('euro365'),  "1"]);

			Session::flash('account', 'Your details have been updated.');
			Redirect::to('admin-settings.php');
			
		} catch (Exception $e) {
			die($e->getMessage());
		}
	} else {
		$error = array();
		foreach ($validation->errors() as $bit) {
			$error[] = $bit;
		}
	}
}

// aktualizacja ustawień
if (isset($_POST['orders_active'])) {

		
		try {
			$updateRow = $db->updateRow("UPDATE admin_settings SET orders_active = ?, free_premium = ?  WHERE id = ?", [Input::get('orders_active'), Input::get('free_premium'), "1"]);

			Session::flash('account', 'Your details have been updated.');
			Redirect::to('admin-settings.php');
			
		} catch (Exception $e) {
			die($e->getMessage());
		}
}

// aktualizacja ustawień grafiki
if (isset($_POST['bg_image'])) {

		
		try {
			$updateRow = $db->updateRow("UPDATE admin_settings SET bg_image = ? WHERE id = ?", [Input::get('bg_image'), "1"]);

			Session::flash('account', 'Your details have been updated.');
			Redirect::to('admin-settings.php');
			
		} catch (Exception $e) {
			die($e->getMessage());
		}
}

// aktualizacja ustawień języka
if (isset($_POST['lang'])) {

		
		try {
			$updateRow = $db->updateRow("UPDATE admin_settings SET lang = ? WHERE id = ?", [Input::get('lang'), "1"]);

			// wyczyszczenie katalogu cache z plików
			$dir = 'cache/';
			foreach(glob($dir.'*.*') as $v){
			    unlink($v);
			}

			Session::flash('account', 'Your details have been updated.');
			Redirect::to('admin-settings.php');
			
		} catch (Exception $e) {
			die($e->getMessage());
		}
}

include_once 'top-admin.php';
echo '<div class=" stickyfooter">';
include_once 'menu-admin.php';
?>

	<div class="stickyfooter-content my-font">	
		<div class="container admin-content">
			<?php 

				if (Session::exists('account')) {
					echo '<div class="success-message mb-20">' . Session::flash('account') . '</div>';
				}
				if (Session::exists('cache-deleted')) {
					echo '<div class="success-message mb-20">' . Session::flash('cache-deleted') . '</div>';
				}

				if (isset($error)) {
					echo '<div class="warning-message mb-20">';
					foreach ($error as $errors) {
						echo $errors, '<br>';
					}
					echo '</div>';
				}
			
			?>
			
			<div class="row">
				

				<div class="col-12 col-md-6 mb-50">
					<form action="" method="post">
						<p class="font-md-400">Ustawienia</p>
						<div class="form-group ">
							<label for="orders_active">Nowo dodawane zlecenia mają być zatwierdzane czy automatycznie dodawane jako aktywne?</label>
					        <select class="form-control form-control" id="orders_active" name="orders_active" >
					        	
						    	<option value="1" <?php echo (Input::get('orders_active') ? Input::get('orders_active') : ($admin_settings['orders_active'] == '1') ? 'selected' : '' )?>>Automatycznie aktywne</option>
						        <option value="0" <?php echo (Input::get('orders_active') ? Input::get('orders_active') : ($admin_settings['orders_active'] == '0') ? 'selected' : '' )?>>Po zatwierdzeniu przez administratora</option>
						    </select>
					    </div>
					    <div class="form-group mt-30">
					    	<label for="free_premium">Czy ma być dostępny darmowy okres po rejestracji?</label>
					        <select class="form-control form-control" id="free_premium" name="free_premium" >
						    	<?php

					        		$options = [
					        			'0' => 'Nie',
					        			'30' => 'Tak - 30 dni',
					        			'180' => 'Tak - 180 dni',
					        			'365' => 'Tak - 365 dni'
					        		];

					        		Drop::getList($options, 'free_premium', $admin_settings['free_premium']);
					        	?>
						    </select>
					    </div>
					    <button type="submit" class="btn btn-green btn-block btn-sm mt-30">Zapisz</button>
					</form>
					<div class="row">
						<div class="col-12 col-md-6 mb-50">
							<form action="" method="post">
								
							    <div class="form-group mt-50">
							    	<label for="free_premium">Grafika tła</label>
							        <select class="form-control form-control" id="bg_image" name="bg_image" >

							        	<?php

							        		$options = [
							        			'1' => '1',
							        			'2' => '2',
							        			'3' => '3',
							        			'4' => '4',
							        			'5' => '5'
							        		];

							        		Drop::getList($options, 'bg_image', $admin_settings['bg_image']);


							        	?>
								    </select>
							    </div>
							    <button type="submit" class="btn btn-green btn-block btn-sm mt-30">Zapisz</button>
							</form>

							
						</div>

						<div class="col-12 col-md-6 mb-50">
							

							<form action="" method="post">
								
							    <div class="form-group mt-50">
							    	<label for="free_premium">Język strony</label>
							        <select class="form-control form-control" id="lang" name="lang" >

							        	<?php

							        		$options = [
							        			'pl' => 'Polski',
							        			'en' => 'Angielski'
							        		];

							        		Drop::getList($options, 'lang', $admin_settings['lang']);


							        	?>
								    </select>
							    </div>
							    <button type="submit" class="btn btn-green btn-block btn-sm mt-30">Zapisz</button>
							</form>
						</div>
					</div>
					<hr class="mt-0">
					<div class="row">
						<div class="col-12">
							<label for ="free_premium">Cache (jeśli będzie gdzieś bałd języka i zmiana tłumaczenia nie zadziała)</label>
							<p><a href="admin-clear-cache.php" class="btn btn-sm btn-green">Wyczyść Cache</a></p>
						</div>
					</div>


				</div>

				
				<div class="col-12 offset-md-1 col-md-5 mb-50">
					<form action="" method="post">	
						<p class="font-md-400">Ceny PLN</p>
					    
					    <div class="form-group row ">
						    <div class="col-8">
						      <input type="text" class="form-control" id="pln30" name="pln30" value="<?php echo (Input::get('pln30')) ? Input::get('pln30') : $admin_settings['price_pln_30'] ?>">
						    </div>
						    <label for="pln30" class="col-4 col-form-label text-left">30 dni</label>
						</div>
						<div class="form-group row">
						    <div class="col-8">
						      <input type="text" class="form-control" id="pln180" name="pln180" value="<?php echo (Input::get('pln180')) ? Input::get('pln180') : $admin_settings['price_pln_180'] ?>">
						    </div>
						    <label for="pln180" class="col-4 col-form-label text-left">180 dni</label>
						</div>
						<div class="form-group row">
						    <div class="col-8">
						      <input type="text" class="form-control" id="pln365" name="pln365" value="<?php echo (Input::get('pln365')) ? Input::get('pln365') : $admin_settings['price_pln_365'] ?>">
						    </div>
						    <label for="pln365" class="col-4 col-form-label text-left">365 dni</label>
						</div>

						<p class="font-md-400 mt-50">Ceny EUR</p>
					    
					    <div class="form-group row">
						    <div class="col-8">
						      <input type="text" class="form-control" id="euro30" name="euro30" value="<?php echo (Input::get('euro30')) ? Input::get('euro30') : $admin_settings['price_euro_30'] ?>">
						    </div>
						    <label for="euro30" class="col-4 col-form-label text-left">30 dni</label>
						</div>
						<div class="form-group row">
						    <div class="col-8">
						      <input type="text" class="form-control" id="euro180" name="euro180" value="<?php echo (Input::get('euro180')) ? Input::get('euro180') : $admin_settings['price_euro_180'] ?>">
						    </div>
						    <label for="euro180" class="col-4 col-form-label text-left">180 dni</label>
						</div>
						<div class="form-group row">
						    <div class="col-8">
						      <input type="text" class="form-control" id="euro365" name="euro365" value="<?php echo (Input::get('euro365')) ? Input::get('euro365') : $admin_settings['price_euro_365'] ?>">
						    </div>
						    <label for="euro365" class="col-4 col-form-label text-left">365 dni</label>
						</div>
						<button type="submit" class="btn btn-green btn-sm">Zapisz</button>

					</form>
				</div>
			

			</div>
		
		</div>
	</div><!-- /sticky-footer  -->
    	<!-- /content -->

<?php include_once 'footer-admin.php'; ?>

	</div><!-- /stickyfooter (or first-bg) -->
  </body>
</html>
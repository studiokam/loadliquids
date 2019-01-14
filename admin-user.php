<?php 
require_once 'core/init.php';
$title = 'Zaplecze | loadliquids.com';


// sprawdzenie czy w linku get podane jest id
if (Input::get('id')) {
	$user_id = Input::get('id');
} else {
	Session::flash('admin-user', 'Błąd w adresie. Nie ma podanego id usera. skontaktuj się z administratorem.');
	Redirect::to('admin-user-list.php');
}

$db = new Database();
$user = $db->getRow("SELECT * FROM users WHERE id = $user_id");
$company = $db->getRow("SELECT * FROM company WHERE user_id = $user_id");

// sprawdzenie czy jest już tabela comapny dla danego usera
if (!$company) {
	$addRowToTableCompany = $db->insertRow("INSERT INTO company (user_id) VALUE(?)", [$user_id]);
}

$premium = new Premium();

if (Input::get('username')) {
	$validate = new Validate();
	$validation = $validate->check($_POST, array(
		'username' => array(
			'reqired' => true,
			'min' => 2,
			'max' => 20,
			'az09' => true
			),
		'email' => array(
			'required' => true,
			'min' => 2,
			'max' => 50
			)
		));


	if ($validation->passed()) {

		try {

			$updateRow = $db->updateRow("UPDATE users SET 
				 
                username = ?, 
                email = ?, 
                email2 = ?, 
                phone = ?,  
                phone2 = ?

				WHERE id = ?", 
				[
					Input::get('username'),
					Input::get('email'),
					Input::get('email2'),   
					Input::get('phone'),   
					Input::get('phone2'),
					$user_id
				]);

			Session::flash('edit_ok', 'Poprawnie edytowano zlecenie.');
			Redirect::to('admin-user.php?id='.$user_id.'');


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

// dodanie dni premium
if (Input::get('premium_add_days')) {
	$validate = new Validate();
	$validation = $validate->check($_POST, array(
		'premium_add_days' => array(
			'reqired' => true,
			'max' => 3,
			'numeric' => true
			)
		));


	if ($validation->passed()) {

        try {

            $insertRow = $db->insertRow("INSERT INTO premium(
            	user_id, 
            	date_before_add, 
            	days_add, 
            	who_add, 
            	gratis, 
            	note
            ) VALUE(?, ?, ?, ?, ?, ?)", [
            	$user_id, 
            	$user['premium_date'], 
            	Input::get('premium_add_days'), 
            	'admin', 
            	Input::get('gratis'),
            	Input::get('premium_note')
            ]);

            // sprawdzenie czy data przed dodaniem jest mniejsza niz NOW() - (jeśli jest mniejsza to zerowanie daty, inaczej doda np. 30 dni do daty przeszłej i finalnie wyjdzie 20 dni)
            if ($user['premium_date'] > date('Y-m-d H:i:s')) {
            	$check_premium = $user['premium_date'];
            } else {
            	$check_premium = date('Y-m-d H:i:s');
            }

            $updateRow = $db->updateRow("UPDATE users SET 
				 
                premium_date = ?

				WHERE id = ?", 
				[
					$premium->addDayswithdate($check_premium, Input::get('premium_add_days')),
					$user_id
				]);

            // dodanie do zamówień (purchase)
            
            $insertRow2 = $db->insertRow("INSERT INTO purchase(
            	user_id,  
            	buy_path, 
            	price, 
            	currency, 
            	days,
            	pdf
            ) VALUE(?, ?, ?, ?, ?, ?)", [
            	$user_id, 
            	'admin', 
            	Input::get('price'), 
            	Input::get('currency'), 
            	Input::get('premium_add_days'),
            	'0'
            ]);

            Session::flash('edit_ok', 'Poprawnie edytowano dane firmy.');
			Redirect::to('admin-user.php?id='.$user_id.'');

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

// edycja firmy (dane firmy) 
if (Input::get('comp_name')) {
	$validate = new Validate();
	$validation = $validate->check($_POST, array(
		'comp_name' => array(
			'reqired' => true,
			'min' => 2
			)
		));


	if ($validation->passed()) {

		try {

            $updateRow = $db->updateRow("UPDATE company SET 
            	comp_name = ?, 
            	comp_address = ?,
            	comp_city = ?,
            	comp_post = ?,
            	comp_number = ?
            	WHERE user_id = ?", [
            		Input::get('comp_name'), 
            		Input::get('comp_address'), 
            		Input::get('comp_city'), 
            		Input::get('comp_post'), 
            		Input::get('comp_number'), 
            		$user_id
            	]);
            if (Input::get('company_verif')) {
            		$company_verif = (Input::get('company_verif') == 'yes') ? '1' : '0';
             		$updateRowVerif = $db->updateRow("UPDATE users SET company_verif = ? WHERE id = ?", [$company_verif, $user_id]);
             	} 	

            Session::flash('edit_ok', 'Poprawnie edytowano dane firmy.');
			Redirect::to('admin-user.php?id='.$user_id.'');

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

// edycja notatki (do konta) 
if (Input::get('note')) {
	$validate = new Validate();
	$validation = $validate->check($_POST, array(
		'note' => array(
			'min' => 2
			)
		));


	if ($validation->passed()) {

		try {

            $updateRow = $db->updateRow("UPDATE users SET 
            	note = ?
            	WHERE id = ?", [
            		Input::get('note'), 
            		$user_id
            	]);	

            Session::flash('edit_ok', 'Poprawnie edytowano notatkę.');
			Redirect::to('admin-user.php?id='.$user_id.'');

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

// wykasowanie pliku pdf
if (Input::get('file_id') && Input::get('delete')) {
	
	try {

		// delete wpisu w db
		$delete_file = $db->getRow("SELECT * FROM company_files WHERE id = ".Input::get('file_id')."");
		$db->deleteRow("DELETE FROM company_files WHERE id = ?", [Input::get('file_id')]);

		// delete pliku
		$DelFilePath = 'upload/'.$user_id.'/' . $delete_file['file_name'];
 
		# delete file if exists
		if (file_exists($DelFilePath)) { unlink ($DelFilePath); }

		Redirect::to('admin-user.php?id='.$user_id.'');
		$success_upload = 'File deleted successfully.';
		
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
			if (Session::exists('edit_ok')) {
				echo '<div class="success-message mb-20">' . Session::flash('edit_ok') . '</div>';
			} 
			if (isset($error)) {
				echo '<div class="warning-message mb-30"> Błędy w formularzu: <br>';
				foreach ($error as $errors) {
					echo $errors, '<br>';
				}
				echo '</div>';
			}
					
			?>
			
			<div class="row">
				<div class="col-12">
					<p class="font-md-400">Użytkownik <b><?php echo $user['username'].'</b> #'.$user['id'] ?></p>
				
					<a href="admin-purchase-list.php?user_id=<?php echo $user_id ?>" class="btn btn-green btn-sm mb-1">Zamówienia</a>
					<a href="admin-ads-list.php?user_id=<?php echo $user_id ?>" class="btn btn-green btn-sm mb-1">Zlecenia</a>
					<a href="" class="btn btn-green btn-sm mb-1">Faktury</a>
					<a href="admin-user-delete.php?id=<?php echo $user_id ?>" class="btn btn-danger btn-sm mb-1">Usuń</a>
				</div>
			</div>
			
			<div class="row">
				<div class="col-12 col-md-6 col-xl-5 mb-50">
					
					<form action="" method="post" class="mb-50 mt-30">
						<div class="form-group">
							<input type="text" class="form-control form-control-sm form-control-sm" id="username" name="username" value="<?php echo (Input::get('username') ? Input::get('username') : $user['username'] )?>" required>
						</div>
						<div class="form-group">
							<input type="email" class="form-control form-control-sm" id="email" name="email" value="<?php echo (Input::get('email') ? Input::get('email') : $user['email'] )?> " required>
						</div>
						<div class="form-group">
							<input type="email" class="form-control form-control-sm" id="email2" name="email2" placeholder="alternatywny adres e-mail" value="<?php echo (Input::get('email2') ? Input::get('email2'):$user['email2'])?>">
						</div>
						<div class="form-group">
							<input type="text" class="form-control form-control-sm" id="phone" name="phone" value="<?php echo (Input::get('phone'))? Input::get('phone'):$user['phone'] ?>" placeholder="telefon">
						</div>
						<div class="form-group">
							<input type="text" class="form-control form-control-sm" id="phone2" name="phone2" value="<?php echo (Input::get('phone2')) ? Input::get('phone2') : $user['phone2'] ?>" placeholder="drugi telefon">
						</div>

						<button type="submit" class="btn btn-green btn-sm mb-1" >Zapisz</button>
					</form>	

					<p class="font-md-400">Dodaj konto PREMIUM</p>
					<form action="" method="post" class="mb-50">
					  <div class="form-group">
					    <input type="text" class="form-control form-control-sm" id="premium_add_days" name="premium_add_days" placeholder="podaj ilość dni" required>
					  </div>
					  <div class="form-group">
					    <select class="form-control form-control-sm form-control" id="gratis" name="gratis" >
					    	<option value="0" <?php echo (Input::get('gratis') ? Input::get('gratis') : 'selected')?>>Normalne (płatne, doliczane do statystyk)</option>
					        <option value="1" <?php echo (Input::get('gratis') ? Input::get('gratis') : '' )?>>Gratisowe (nie będzie doliczane do statystyk zakupowych)</option>
					    </select>
					  </div>
					  <div class="row">
					  	<div class="col-12 mb-10">Cena potrzebna jest do statystyk, faktury itp. Jeśli jest to gratis podaj 0.</div>
					  	<div class="col-6">
					  		
					  		<div class="form-group">
							    <input type="text" class="form-control form-control-sm" id="price" name="price" placeholder="cena" required>
							  </div>
					  	</div>
					  	<div class="col-6">
					  		<div class="form-group">
							    <select class="form-control form-control-sm form-control" id="currency" name="currency" >
							    	<option value="1" <?php echo (Input::get('currency') ? Input::get('currency') : 'selected')?>>EURO</option>
							        <option value="2" <?php echo (Input::get('currency') ? Input::get('currency') : '' )?>>PLN</option>
							    </select>
							  </div>
					  	</div>
					  </div>
					  <div class="form-group">
					    <input type="text" class="form-control form-control-sm" id="premium_note" name="premium_note" placeholder="notatka">
					  </div>
					  <button type="submit" class="btn btn-green btn-sm mb-1">Dodaj</button>
					</form>	

					<p class="font-md-400">Dane firmy</p>
					<form action="" method="post">
					  <div class="form-group">
					    <input type="text" class="form-control form-control-sm" id="comp_name" name="comp_name" placeholder="nazwa firmy" value="<?php echo (Input::get('comp_name')) ? Input::get('comp_name'): $company['comp_name']?>" required>
					  </div>
					  <div class="form-group">
					    <input type="text" class="form-control form-control-sm" id="comp_address" name="comp_address" placeholder="adres" value="<?php echo (Input::get('comp_address')) ? Input::get('comp_address'): $company['comp_address']?>">
					  </div>
					  <div class="form-group">
					    <input type="text" class="form-control form-control-sm" id="comp_city" name="comp_city" placeholder="miejscowość" value="<?php echo (Input::get('comp_city')) ? Input::get('comp_city'): $company['comp_city']?>">
					  </div>
					  <div class="form-group">
					    <input type="text" class="form-control form-control-sm" id="comp_post" name="comp_post" placeholder="kod pocztowy" value="<?php echo (Input::get('comp_post')) ? Input::get('comp_post'): $company['comp_post']?>">
					  </div>
					  <div class="form-group">
					    <input type="text" class="form-control form-control-sm" id="comp_number" name="comp_number" placeholder="NIP" value="<?php echo (Input::get('comp_number')) ? Input::get('comp_number'): $company['comp_number']?>">
					  </div>
					  <div class="form-group">
					    <select class="form-control form-control-sm form-control" id="company_verif" name="company_verif" >
					    	<option value="no" <?php echo (Input::get('company_verif') ? Input::get('company_verif') : ($user['company_verif'] == '0') ? 'selected' : '' )?>>Firma niezatwierdzona</option>
					        <option value="yes" <?php echo (Input::get('company_verif') ? Input::get('company_verif') : ($user['company_verif'] == '1') ? 'selected' : '' )?>>Firma zatwierdzona</option>
					    </select>
					  </div>
					  <button type="submit" class="btn btn-green btn-sm mb-1" >Zapisz</button>
					</form>					
					
				</div>
				<div class="col-12 offset-md-1 col-md-5 col-xl-5 mb-50">
					<p class="font-md-400">inf</p>
					<table class="table table-sm ">
					  <tbody>
				<?php 
				
					echo'
						<tr >
					      <td class="border-t0">Data rejestracji:</td>
					      <td class="border-t0 text-right">'.date('d.m.Y H:i:s', strtotime($user['register'])).'</td>
					    </tr>
					    <tr>
					      <td>Potwierdzenie adresu e-mail:</td>
					      <td class="text-right">'.($user['email_confirm'] !== '0000-00-00 00:00:00' ? date('d.m.Y H:i:s', strtotime($user['email_confirm'])):'Brak potwierdzenia').'</td>
					    </tr>
					    <tr>
					      <td>Weryfikacja firmy:</td>
					      <td class="text-right">'.($user['company_verif'] !== '0' ? 'Potwierdzona':'Brak potwierdzenia').'</td>
					    </tr>
					    <tr>
					      <td>Ostatnie logowanie:</td>
					      <td class="text-right">'.($user['last_login'] !== '0000-00-00 00:00:00' ? $user['last_login'] :'Jeszcze nie logowano się na konto').'</td>
					    </tr>
					    <tr>
					      <td>Premium:</td>
					      <td class="text-right">'.(($premium->isPremium($user['id'])) ? date('d.m.Y H:i:s', strtotime($user['premium_date'])):'Nie').'<br><b>'.(($premium->isPremium($user['id'])) ? $premium->timeleft(strtotime($user['premium_date']) - time()) :'').'</b></td>
					    </tr>
					';
				?>
					    
					  </tbody>
					</table>
					<p class="font-md-400 mt-30">logi</p>
					<table class="table table-sm ">
					  <tbody>
					    <tr >
					      <td class="border-t0">ostatnie 20</td>
					      <td class="border-t0 text-right"><a href="">zobacz wszytkie</a></td>
					    </tr>
					    <tr>
					      <td colspan="2">21.11.2017 15:39:02 - Poprawne logowanie </td>
					    </tr>
					    <tr>
					      <td colspan="2">21.11.2017 15:29:02 - Błąd logowania</td>
					    </tr>
					  </tbody>
					</table>

					<form action="" method="post">
						<p class="font-md-400 mt-30">notatki do konta</p>
						<div class="form-group">
						    <textarea class="form-control form-control-sm" id="note" name="note" rows="3" placeholder="notatka"><?php echo $user['note'] ?></textarea>
						</div>
						 <button type="submit" class="btn btn-green btn-sm mb-1" >Zapisz</button>
					</form>

					<?php
						// pobranie informacji o przesłanych plikach  
						$all_files = $db->getRows("SELECT * FROM company_files WHERE user_id = ?", [$user_id]);
						$all_files_count = $db->countRows();
					?>	

					<p class="font-md-400 mt-50">Przesłane pliki - <?php echo $all_files_count ?></p>
  								<hr>
  								<?php
  									echo ($all_files_count < 1) ? 'Brak plików' : '';
  									foreach ($all_files as $row) {
  										echo '
											<table class="table table-sm ">
											  <tbody>
											    <tr >
											      <td class="border-t0"><a href="upload/'.$user_id.'/'.$row['file_name'].'" target="_blank">'.substr($row['file_name'], 0 , 40) .'</a></td>
											      <td class="border-t0 text-right">
											      	<a href="admin-user.php?id='.$row['user_id'].'&file_id='.$row['id'].'&delete=1"><i class="ion-close-round" alt="Usuń"></i></a>
											      </td>
											    </tr>
											  </tbody>
											</table>
  										';
  									}
  								?>
  								<hr>
				</div>
			</div>
		
		</div>
	</div><!-- /sticky-footer  -->
    	<!-- /content -->

<?php include_once 'footer-admin.php'; ?>

	</div><!-- /stickyfooter (or first-bg) -->
  </body>
</html>
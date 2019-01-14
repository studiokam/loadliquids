<?php 
require_once 'core/init.php';
$title = 'Zaplecze | loadliquids.com';


// sprawdzenie czy w linku get podane jest id
if (Input::get('id')) {
	$id = Input::get('id');
} else {
	Session::flash('purchase_id', 'Błąd w adresie. Nie ma podanego id usera. skontaktuj się z administratorem.');
	Redirect::to('admin-purchase-list.php');
}


$db = new Database();

// wysyłanie pliku
if (isset($_FILES['file_upload'])) {
			
	$flag = true;

	// Check for errors
	if($_FILES['file_upload']['error'] > 0){
		$flag = false;
	    $error = 'An error ocurred when uploading.';
	}


	// Check filetype
	if($_FILES['file_upload']['type'] != 'application/pdf'){
	    $flag = false;
	    $error = 'Unsupported filetype uploaded.';
	}

	// Check filesize
	if($_FILES['file_upload']['size'] > 500000){
	    $flag = false;
	    $error = 'File uploaded exceeds maximum upload size.';
	}

	// Check if the file exists
	if(file_exists('invoices/' . $_FILES['file_upload']['name'])){
	    $flag = false;
	    $error = 'File with that name already exists.';
	}

	// Upload file
	if(!move_uploaded_file($_FILES['file_upload']['tmp_name'], 'invoices/' . $_FILES['file_upload']['name'])){
	    $flag = false;
	    $error = 'Error uploading file - check destination is writeable.';
	}

	if ($flag == true) {
		try {
			$db->updateRow("UPDATE purchase SET pdf = ? WHERE id = ?", [$_FILES['file_upload']['name'], $id ]);
			$success_upload = 'File uploaded successfully.';
			
		} catch (Exception $e) {
			die($e->getMessage());
		}
			
	} 

}	


$all_purchase = $db->getRow("SELECT * FROM purchase WHERE id = $id");
$company = $db->getRow("SELECT * FROM company WHERE user_id = ".$all_purchase['user_id']."");


// wykasowanie pliku pdf
if (Input::get('id') && Input::get('delete')) {
	
	try {

		// update wpisu w db
		$db->updateRow("UPDATE purchase SET pdf = ? WHERE id = ?", ['0', $id ]);

		// delete pliku
		$DelFilePath = 'invoices/' . $all_purchase['pdf'];
 
		# delete file if exists
		if (file_exists($DelFilePath)) { unlink ($DelFilePath); }

		Redirect::to('admin-purchase.php?id='.$id.'');
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
		
			<div class="row">
				<div class="col-12">
					<p class="font-md-400">Zamówienia</p>
				</div>
			</div>
			

			<div class="row">
				
				<div class="col-12 col-md-5 col-xl-5 mb-50">
					<p class="font-md-400">Szczegóły zamówienia nr. <?php echo $all_purchase['id'] ?></p>
					<table class="table table-sm ">
					  <tbody>
					    <tr >
					      <td class="border-t0">Data zamówienia</td>
					      <td class="border-t0 text-right"><?php echo date('d.m.Y H:i:s', strtotime($all_purchase['add_date'])) ?></td>
					    </tr>
					    <tr>
					      <td>Jak zamówiono</td>
					      <td class="text-right"><?php echo $all_purchase['buy_path'] ?></td>
					    </tr>
					    <tr>
					      <td>Kwota</td>
					      <td class="text-right"><?php echo $all_purchase['price'].' '.($all_purchase['currency'] === '1' ? 'EURO' : 'PLN') ?></td>
					    </tr>
					    <tr>
					      <td>Na ile</td>
					      <td class="text-right"><?php echo $all_purchase['days'] ?> dni</td>
					    </tr>
					  </tbody>
					</table>
					<table class="table table-sm ">
					  <tbody>
					    <tr >
					      <td class="border-t0">Firma</td>
					      <td class="border-t0 text-right"><b><?php echo $company['comp_name'] ?></b></td>
					    </tr>
					    <tr>
					      <td>Adres</td>
					      <td class="text-right"><b><?php echo $company['comp_address'] ?></b></td>
					    </tr>
					    <tr>
					      <td>Miejscowość</td>
					      <td class="text-right"><b><?php echo $company['comp_post'].' '.$company['comp_city'] ?></b></td>
					    </tr>
					    <tr>
					      <td>NIP</td>
					      <td class="text-right"><b><?php echo $company['comp_number'] ?></b></td>
					    </tr>
					  </tbody>
					</table>
					<a href="admin-user.php?id=<?php echo $all_purchase['user_id'] ?>" class="btn btn-green btn-sm">Zobacz usera</a>
				</div>
				<div class="col-12 offset-md-1 col-md-5 col-xl-5 mb-50">
					
					<p class="font-md-400">Faktury</p>
					<hr>
					<table class="table table-sm ">
					  <tbody>
					    <tr >
					      <td class="border-t0"><?php echo ($all_purchase['pdf'] === '0') ? 'Brak pliku' : $all_purchase['pdf'] ?></td>
					      <td class="border-t0 text-right">
					      	<a href="admin-purchase.php?id=<?php echo $id ?>&delete=1"><i class="ion-close-round" alt="Usuń"></i></a>
					      </td>
					    </tr>
					  </tbody>
					</table>
					<hr>
					<p class="record-date-sm">Wgrywane pliki muszą być typu .pdf. Pliki muszą mieć zawsze unikalną nazwę, nie może w nazwie być kropki (poza tą przed rozszerzeniem pliku .pdf). Na raz wgrany może być tylko jeden dokument do zamówienia, jeśli poprzednio dodany jest błędny nalezy go wykasować i dodać poprawny.</p>

					<?php 
					if(isset($error)) echo '<div class="warning-message mb-30">'.$error.'</div>';
					if(isset($success_upload)) echo '<div class="success-message mb-30">'.$success_upload.'</div>'; 
					?>


					<form method='post' method enctype='multipart/form-data' action=''>
					    <input type='file' name='file_upload'><br>
					    <?php echo ($all_purchase['pdf'] === '0') ? "<input type='submit' value='Wyślij'>" : "Wykasuj obecny plik jeśli chesz dodać inny." ?>
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
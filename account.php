<?php 
require_once 'core/init.php';
$title = 'Account settings | loadliquids.com';

// translate
$tr = Lang::set('account');
$tru = Lang::set('upload');

$user = new User();
$id = $user->data()->id;

$db = new Database();
$getRow = $db->getRow("SELECT * FROM company WHERE user_id = ?", ["$id"]);
$logi = $db->getRows("SELECT * FROM logs WHERE user_id = ?", ["$id"]);

// sprawdzenie czy jest zalogowany
if (!$user->isLoggedIn()) {
	Redirect::to('index.php');
}


// telefony
if (isset($_POST['phone-form'])) {

	if (Input::exists()) {
		
		$validate = new Validate();
		$validation = $validate->check($_POST, array (
			'phone' => array(
				'min' => 8,
				'phone09' => true
			),
			'phone2' => array(
				'min' => 8,
				'phone09' => true
			)
		));


		if ($validation->passed()) {

			try {
				$user->update(array(
					'phone' => Input::get('phone'),
					'phone2' => Input::get('phone2')
				));

				Session::flash('account', Lang::get($tr, 'message.update.ok'));
				Redirect::to('account.php');
				
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

}


// główny email
if (isset($_POST['email-form'])) {
	if (Input::exists()) {
			
		$validate = new Validate();
		$validation = $validate->check($_POST, array (
			'email' => array(
				'required' => true,
				'min' => 2,
				'max' => 50,
				'unique' => 'users'
			),
			'email_again' => array(
				'required' => true,
				'matches' => 'email'
			)
		));

		if ($validation->passed()) {
			
			try {
				$user->update(array(
					'email' => Input::get('email')
				));

				Session::flash('account', Lang::get($tr, 'message.update.ok'));
				Redirect::to('account.php');
				
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
}

// email dodatkowy
if (isset($_POST['email2-form'])) {
	if (Input::exists()) {
			
		$validate = new Validate();
		$validation = $validate->check($_POST, array (
			'email2' => array(
				'unique' => 'users'
			)
		));

		if ($validation->passed()) {
			
			try {
				$user->update(array(
					'email2' => Input::get('email2')
				));

				Session::flash('account', Lang::get($tr, 'message.update.ok'));
				Redirect::to('account.php');
				
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
}

// hasło password-form
if (isset($_POST['password-form'])) {
	if (Input::exists()) {
			
		$validate = new Validate();
		$validation = $validate->check($_POST, array (
			'current_password' => array(
				'required' => true,
				'match_in_db' => 'password'
			),
			'new_password' => array(
				'required' => true,
				'min' => 6,
			),
			'new_password_again' => array(
				'required' => true,
				'matches' => 'new_password'
			)
		));

		if ($validation->passed()) {
			
			//$salt = Hash::salt(32);
			$salt = Hash::unique();

			try {
				$user->update(array(
					'password' => Hash::make(Input::get('new_password'), $salt),
					'salt' => $salt,
				));

				Session::flash('account', Lang::get($tr, 'message.update.ok'));
				Redirect::to('account.php');
				
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
}

// wysyłanie pliku
if (isset($_FILES['file_upload'])) {
			
	$flag = true;

	// Check for errors
	if($_FILES['file_upload']['error'] > 0){
		$flag = false;
	    $error_upload = Lang::get($tru, 'error.upload.general');
	}


	// Check filetype
	$allowedExts = array("gif", "jpeg", "jpg", "png", "pdf");
	$temp = explode(".", $_FILES["file_upload"]["name"]);
	$extension = end($temp);

	if ((($_FILES["file_upload"]["type"] == "image/gif")
	|| ($_FILES["file_upload"]["type"] == "image/jpeg")
	|| ($_FILES["file_upload"]["type"] == "image/jpg")
	|| ($_FILES["file_upload"]["type"] == "image/pjpeg")
	|| ($_FILES["file_upload"]["type"] == "image/x-png")
	|| ($_FILES["file_upload"]["type"] == "image/png")
	|| ($_FILES["file_upload"]["type"] == "application/pdf") 
	&& in_array($extension, $allowedExts))) {
	    //
	} else {
	    $flag = false;
	    $error_upload = Lang::get($tru, 'error.upload.filetype');
	}

	// Check filesize
	if($_FILES['file_upload']['size'] > 5000000){
	    $flag = false;
	    $error_upload = Lang::get($tru, 'error.upload.filesize');
	}

	// Check if the file exists
	if(file_exists('upload/'.$id.'/' . $_FILES['file_upload']['name'])){
	    $flag = false;
	    $error_upload = Lang::get($tru, 'error.upload.exists');
	}

	// Upload file
	if(!move_uploaded_file($_FILES['file_upload']['tmp_name'], 'upload/'.$id.'/' . $_FILES['file_upload']['name'])){
	    $flag = false;
	    $error_upload = Lang::get($tru, 'error.upload.writeable');
	}

	if ($flag == true) {
		try {
			$db->updateRow("UPDATE purchase SET pdf = ? WHERE id = ?", [$_FILES['file_upload']['name'], $id ]);
			$db->insertRow("INSERT INTO company_files(user_id, file_name) VALUE(?, ?)", [$id, $_FILES['file_upload']['name']]);
			$success_upload = Lang::get($tru, 'upload.ok');
			
		} catch (Exception $e) {
			die($e->getMessage());
		}
			
	} 

}	

// wykasowanie pliku pdf
if (Input::get('id') && Input::get('delete')) {
	
	try {

		// delete wpisu w db
		$delete_file = $db->getRow("SELECT * FROM company_files WHERE id = ".Input::get('id')."");
		$db->deleteRow("DELETE FROM company_files WHERE id = ?", [Input::get('id')]);

		// delete pliku
		$DelFilePath = 'upload/'.$id.'/' . $delete_file['file_name'];
 
		# delete file if exists
		if (file_exists($DelFilePath)) { unlink ($DelFilePath); }

		Session::flash('account', Lang::get($tru, 'deleted.ok'));
		Redirect::to('account.php');

	} catch (Exception $e) {
		die($e->getMessage());
	}
}

include_once 'top.php';
echo '<div class=" stickyfooter">';
include_once 'menu.php';
?>


	
		<div class="stickyfooter-content my-font">
			<div class="container">

				<?php 

					if (Session::exists('account')) {
						echo '<div class="success-message mt-20">' . Session::flash('account') . '</div>';
					}

					if (isset($error)) {
						echo '<div class="warning-message mt-20">';
						foreach ($error as $errors) {
							echo $errors, '<br>';
						}
						echo '</div>';
					}

					if ($user->data()->company_verif !== '1') {
						echo '<div class="warning-message mt-20">'.Lang::get($tr, 'not.verified.message').'</div>';

					}

					if(isset($error_upload)) echo '<div class="warning-message mb-30 mt-10">'.$error_upload.'</div>';
					if(isset($success_upload)) echo '<div class="success-message mb-30 mt-10">'.$success_upload.'</div>'; 
				
				?>
				
				<div class="row account-box-toogle">
				<div class="col-lg-12">

					
					<ul class="nav nav-tabs" id="myTab" role="tablist">

					<li class="nav-item">
					    <a class="nav-link <?php echo ($user->data()->company_verif === '1') ? 'active' : '' ?> " id="profile-tab" data-toggle="tab" href="#profile" role="tab"><?php echo Lang::get($tr,'nav.settings'); ?></a>
					  </li>	
					  <li class="nav-item ">
					    <a class="nav-link <?php echo ($user->data()->company_verif === '0') ? 'active' : '' ?>" id="home-tab" data-toggle="tab" href="#home" role="tab"><?php echo Lang::get($tr,'nav.data'); ?></a>
					  </li>
					  
					  <!-- <li class="nav-item">
					    <a class="nav-link" id="info-tab" data-toggle="tab" href="#info" role="tab">Logi</a>
					  </li> -->
					</ul>
					<div class="tab-content" id="myTabContent">

					 <div class="tab-pane fade <?php echo ($user->data()->company_verif !== '1') ? '' : 'show active' ?>" id="profile" role="tabpanel">
							
							
						<div class="row">
    						<div class="col-12 col-sm-6 col-md-5 col-lg-4 account-form">
    							
    							<p class="font-md-400"><?php echo Lang::get($tr,'h1.email') .' <b> '. $user->data()->email ?></b></p>
  

								<form action="" method="post">
								  <div class="form-group">
								    <input type="email" class="form-control" id="email" name="email"  placeholder="<?php echo Lang::get($tr,'placeholder.email'); ?>" required>
								  </div>
								  <div class="form-group">
								    <input type="email" class="form-control" id="email_again" name="email_again"  placeholder="<?php echo Lang::get($tr,'placeholder.email2'); ?>" required>
								  </div>
								  <button type="submit" name="email-form" class="btn btn-green btn-sm btn-block"><?php echo Lang::get($tr,'btn.save'); ?></button>
								</form>

								<p class="font-md-400 mt-50"><?php echo Lang::get($tr,'h1.email2'); ?></p>

								<form action="" method="post">

								  <div class="form-group">
								    <input type="email" class="form-control" id="email2" name="email2" placeholder="<?php echo Lang::get($tr,'placeholder.additonal.email'); ?>" value="<?php echo escape($user->data()->email2); ?>">
								  </div>
								  
								  <button type="submit" name="email2-form" class="btn btn-green btn-sm btn-block"><?php echo Lang::get($tr,'btn.save'); ?></button>

								</form>

								

    						</div>
    						<div class="col-12 col-sm-6 col-md-5 col-lg-4 account-form">
    							
    							<p class="font-md-400"><?php echo Lang::get($tr,'h1.pass'); ?></p>
  

								<form action="" method="post">
								  <div class="form-group">
								    <input type="password" class="form-control" id="current_password" name="current_password" placeholder="<?php echo Lang::get($tr,'placeholder.pass.old'); ?>" required>
								  </div>
								  <div class="form-group">
								    <input type="password" class="form-control" id="new_password" name="new_password" placeholder="<?php echo Lang::get($tr,'placeholder.pass.new'); ?>" required>
								  </div>
								  <div class="form-group">
								    <input type="password" class="form-control" id="new_password_again" name="new_password_again" placeholder="<?php echo Lang::get($tr,'placeholder.pass.new.again'); ?>" required>
								  </div>
								  <button type="submit" name="password-form" class="btn btn-green btn-sm btn-block"><?php echo Lang::get($tr,'btn.save'); ?></button>

								</form>

    						</div>

    						<div class="col-12 col-sm-6 col-md-5 col-lg-4 account-form">
    							
    							<p class="font-md-400"><?php echo Lang::get($tr,'h1.phone'); ?></p>
  

								<form action="" method="post">

								  <div class="form-group">
								    <input type="text" class="form-control" id="phone" name="phone" placeholder="<?php echo Lang::get($tr,'placeholder.phone'); ?>" value="<?php echo escape($user->data()->phone); ?>">
								  </div>

								  <div class="form-group">
								    <input type="text" class="form-control" id="phone2" name="phone2"  placeholder="<?php echo Lang::get($tr,'placeholder.phone2'); ?>" value="<?php echo escape($user->data()->phone2); ?>">
								  </div>

								  <button type="submit" name="phone-form" class="btn btn-green btn-sm btn-block"><?php echo Lang::get($tr,'btn.save'); ?></button>

								</form>

								

    						</div>


    					</div>
						
						

					  </div>


					  <div class="tab-pane fade  <?php echo ($user->data()->company_verif !== '1') ? 'show active' : '' ?>" id="home" role="tabpanel" >
						
						<div class="row">
							<div class="col-12 col-sm-12 mt-15">
								
								<?php if ($user->data()->company_verif !== '1'): ?>

									<p class="mt-20  font-md-400"><?php echo Lang::get($tr,'status.no').'</p>
									<p>'.Lang::get($tr,'message.status.info.no').'</p>
									<p class="mb-30">'.Lang::get($tr,'required.doc').'<br>
									'.Lang::get($tr,'required.doc.list').'</p>'?>

								<?php else: ?>

									<p class="mt-20  font-md-400"><?php echo Lang::get($tr,'status.yes');?></p>

								<?php endif ?>
								
							</div>
						</div>
						<hr>

						

						<div class="row">
    						<div class="col-12 col-sm-6 col-md-5 col-lg-4 account-form">
    							
    							<?php echo
    							'<p class="font-md-400">'.Lang::get($tr,'company.data').'</p>
  

								<form>
								  <div class="form-group">
								    <input type="text" class="form-control" value="'.$getRow['comp_name'].'" placeholder="'.Lang::get($tr,'placeholder.name').'">
								  </div>
								  <div class="form-group">
								    <input type="text" class="form-control" value="'.$getRow['comp_address'].'" placeholder="'.Lang::get($tr,'placeholder.address').'">
								  </div>
								  <div class="form-group">
								    <input type="text" class="form-control" value="'.$getRow['comp_city'].'" placeholder="'.Lang::get($tr,'placeholder.place').'">
								  </div>
								  <div class="form-group">
								    <input type="text" class="form-control" value="'.$getRow['comp_post'].'" placeholder="'.Lang::get($tr,'placeholder.postal.code').'">
								  </div>
								  <div class="form-group">
								    <input type="text" class="form-control" value="'.$getRow['comp_number'].'" placeholder="'.Lang::get($tr,'placeholder.nip').'">
								  </div>
								</form>'?>

								

    						</div>
    						
    						<?php
    							// pobranie informacji o przesłanych plikach  
								$all_files = $db->getRows("SELECT * FROM company_files WHERE user_id = ?", [$id]);
								$all_files_count = $db->countRows();
    						?>
    						<div class="col-12 offset-md-1 col-md-5 col-xl-7 account-form">	
    							<p class="font-md-400"><?php echo Lang::get($tr,'h1.uploaded.files'), $all_files_count ?></p>
  								<hr>
  								<?php
  									echo ($all_files_count < 1) ? Lang::get($tr,'no.files') : '';
  									foreach ($all_files as $row) {
  										echo '
											<table class="table table-sm ">
											  <tbody>
											    <tr >
											      <td class="border-t0"><a href="upload/'.$id.'/'.$row['file_name'].'" target="_blank">'.substr($row['file_name'], 0 , 40) .'</a></td>
											      <td class="border-t0 text-right">
											      	<a href="account.php?id='.$row['id'].'&delete=1"><i class="ion-close-round" alt="Delete"></i></a>
											      </td>
											    </tr>
											  </tbody>
											</table>
  										';
  									}
  								?>
  								<hr>

								<form method='post' method enctype='multipart/form-data' action=''>
								    <input type='file' class="pl-0" name='file_upload'>
								    <?php echo ($all_files_count > 9) ? Lang::get($tr,'max.files') : "<input type='submit' class='btn btn-ssm btn-green float-right mt-10 mr-0' value='".Lang::get($tr,'btn.send')."'>" ?>
								</form>
								

    						</div>

    						
    					</div>
    					
    					
						
						

					  </div>
					 

					  <div class="tab-pane fade" id="info" role="tabpanel" aria-labelledby="info-tab">
							
							
						<div class="row">
    						<div class="col-12 col-sm-12 account-form">
    							
    							<p class="font-md-400">Logi serwisu</p>
  
								<?php
									foreach ($logi as $row) {
										echo $row['date'].' - '. $row['log'].'<br>';
									}
								?>

    						</div>
    					</div>
						
						

					  </div>
					</div>

				</div>
			</div> <!-- wyszukiwarka -->
			

			</div>
		</div><!-- /sticky-footer  -->
		

<?php include_once 'footer.php'; ?>

	</div><!-- /stickyfooter (or first-bg) -->
  </body>
</html>
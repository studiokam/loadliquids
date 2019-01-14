<?php 
require_once 'core/init.php';

	// translate 
	$tr = Lang::set('login');

	if (Input::exists()) {
		if (Token::check(Input::get('token'))) {
			
			$validate = new Validate();
			$validation = $validate->check($_POST, array(
				'email' => array('required' => true),
				'password' => array('required' => true)
			));

			if ($validation->passed()) {
				$user = new User();
				$remember = (Input::get('remember') === 'on') ? true : false;
				$login = $user->login(Input::get('email'), Input::get('password'), $remember);

				if (!$user->emailConfirm()) {
					
					if ($login) {
						$db = new Database();
						$update_last_login = $db->updateRow("UPDATE users SET last_login = ? WHERE email = ?", [date('Y-m-d H:i:s'), Input::get('email')]);
						Redirect::to('index.php');
					} else {
						$loginFailed = Lang::get($tr,'error.wrong.pass');
					}

				} else {
					$loginFailed = Lang::get($tr,'error.email.confim');
				}

			} else {
				$error = array();
				foreach ($validation->errors() as $bit) {
					$error[] = $bit;
				}
			}
		}
	}
?>
<!doctype html>
	<html lang="pl">
	  <head>
		<title>Giełda rejestracja</title>
	    <!-- Required meta tags -->
	    <meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

	    <!-- Bootstrap CSS -->
	    <link rel="stylesheet" href="css/bootstrap.css">
	    <link rel="stylesheet" href="css/style.css">
	    <link rel="stylesheet" href="css/mp.css">

	    <link href="https://fonts.googleapis.com/css?family=Istok+Web:200,400,700|PT+Sans+Narrow:400,700|PT+Sans:400,700" rel="stylesheet">

	    <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

	</head>
	<body>

	   	<div class="sign-up-100">
		<div class="sign-up ">

			<div class="container">

				<div class="row d-none d-sm-block sign-up-box-dark">
					<div class="sign-logo"><a href="index.php">load<b>liquids</b>.com</a></div>
				</div>
				<!-- content -->
				<div class="row sign-up-box ">
					<div class="col-12 d-sm-none sign-up-logo">
						<a href="index.php">load<b>liquids</b>.com</a>
					</div>
					<div class="col-12 sign-up-head mb-20">
						<?php echo Lang::get($tr,'login'); ?>
					</div>

					<div class="col-12 sign-in-form">

						<?php  
							if (isset($error)) {
								echo '<div class="warning">';
								foreach ($error as $errors) {
									echo $errors, '<br>';
								}
								echo '</div>';
							}
							if (isset($loginFailed)) {
								echo '<div class="warning-message mb-20">'. $loginFailed .'</div>';
							}
							if (Session::exists('new_pass_ok')) {
								echo '<div class="success-message mb-30">' . Session::flash('new_pass_ok') . '</div>';
							}
							if (Session::exists('registered')) {
								echo '<div class="success-message mb-30">' . Session::flash('registered') . '</div>';
							}
							
						?>

						 <form action="" method="post">

						  <div class="form-group">
						    <label for="email"><?php echo Lang::get($tr,'email'); ?></label>
						    <input type="email" class="form-control" name="email" id="email" value="<?php echo Input::get('email'); ?>" required>
						  </div>

						  <div class="form-group">
						    <label for="password"><?php echo Lang::get($tr,'password'); ?></label>
						    <input type="password" class="form-control" name="password"  id="password" required>
						  </div>


						  <div class="form-group">
							<label for="remember">
								<input type="checkbox" name="remember" id="remember"><?php echo Lang::get($tr,'remember'); ?>
							</label>
						</div>

						  

						  <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
						  <button type="submit" class="btn btn-green"><?php echo Lang::get($tr,'btn.login'); ?></button>
						  
						</form>

						
					</div>


				</div>
				<div class="row sign-up-in">
					<div class="col align-self-end pr-0">
					<a href="pass-reset.php"><?php echo Lang::get($tr,'remind'); ?></a>
					<a href="register.php"><?php echo Lang::get($tr,'register'); ?></a>
					</div>
				</div>
			</div>
			




	    	<!-- /content -->
		</div>
	</div>
</body>
</html>
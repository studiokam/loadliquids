<?php 
require_once 'core/init.php';

// translate
$tr = Lang::set('new-pass');

// sprawdzenei czy jest w linku sol
if (isset($_GET['sa'])) {

	// sprawdzenie czy id i sol nie sa puste
	if (!empty($_GET['id']) && !empty($_GET['sa'])) {
		$id = $_GET['id'];
		$salt = $_GET['sa'];
		$db = new Database();
		$users = $db->getRow("SELECT * FROM users WHERE id = ?", [$id]);
		$salt_in_db = $users['salt'];

		if ($salt_in_db === $salt) {

			// sprawdzenie czy jest wysłany formularz zmiany
			if (isset($_POST['password'])) {
			
				$validate = new Validate();
				$validation = $validate->check($_POST, array(
					'password' => array(
						'required' => true,
						'min' => 6,
						'az09' => true
					),
					'password_again' => array(
						'required' => true,
						'matches' => 'password'
					)
				));

				if ($validation->passed()) {
					$user = new User();

					//$salt = Hash::salt(32);
					$new_salt = Hash::unique();

					try {
						
						$db = new Database();
						$updateRow = $db->updateRow("UPDATE users SET salt = ?, password = ? WHERE id = ?", [$new_salt, Hash::make(Input::get('password'), $new_salt), $id]);


						Session::flash('new_pass_ok', Lang::get($tr,'flsh.message.new.pass.ok'));
						Redirect::to('login.php');
						
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

		} else {
			Redirect::to('index.php');
		}

	} else {
		Session::flash('link_error', Lang::get($tr,'flsh.message.link.error'));
		Redirect::to('index.php');
	}
	
} else {
	Session::flash('link_error', Lang::get($tr,'flsh.message.link.error'));
	Redirect::to('index.php');
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

	<link href="https://fonts.googleapis.com/css?family=Istok+Web:200,400,700|PT+Sans:400,700" rel="stylesheet">
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
						<?php echo Lang::get($tr,'h1.new.pass') ?>
					</div>



					<div class="col-12 sign-in-form">
						<?php  
						if (isset($error)) {
							echo '<div class="warning-message">';
							foreach ($error as $errors) {
								echo $errors, '<br>';
							}
							echo '</div>';
						}
						
						?>
						<form action="" method="post">
							<div class="form-group">
								<label for="password"><?php echo Lang::get($tr,'enter.password') ?></label>
								<input type="password" class="form-control" id="password" name="password" required>
							</div>
							<div class="form-group">
								<label for="password_again"><?php echo Lang::get($tr,'enter.password.again') ?></label>
								<input type="password" class="form-control" id="password_again" name="password_again" required>
							</div>
							<button class="btn btn-green mt-20 "><?php echo Lang::get($tr,'btn.save') ?></button>
						</form>
					</div>


				</div>
			</div>





			<!-- /content -->
		</div>
	</div>
</body>
</html>
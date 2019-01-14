<?php 
require_once 'core/init.php';



if (Input::exists()) {
	if (Token::check(Input::get('token'))) {
				
		if (Config::get('admin/login') === Input::get('login') && Config::get('admin/password') === Input::get('password')) {

			$_SESSION['admin'] = 'admin';
			Redirect::to('admin.php');

		} else {
			$loginFailed = 'Błędny login lub hasło.';
		}	
	}
}


 ?>

<!doctype html>
<html lang="pl">
  <head>
	<title>Giełda zaplecze</title>
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

			<?php 
				if (Session::exists('registered')) {
					echo '<div class="registered-flash">' . Session::flash('registered') . '</div>';
				}
			?>
			<!-- content -->
			<div class="row sign-up-box ">
				
				<div class="col-12 sign-up-head mb-20">
					Zaplecze
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
						
					?>

					 <form action="" method="post">

					  <div class="form-group">
					    <label for="login">Login</label>
					    <input type="text" class="form-control" name="login" id="login" value="<?php echo Input::get('login'); ?>" required>
					  </div>

					  <div class="form-group">
					    <label for="password">Password</label>
					    <input type="password" class="form-control" name="password"  id="password" required>
					  </div>

					  <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
					  <button type="submit" class="btn btn-green">Log in</button>
					  
					</form>

					
				</div>


			</div>
		</div>
		




    	<!-- /content -->
	</div>
</div>
</body>
</html>
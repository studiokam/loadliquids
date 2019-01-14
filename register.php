<?php 
require_once 'core/init.php';

// translate
$tr = Lang::set('register');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';



if (Input::exists()) {
	if (Token::check(Input::get('token'))) {
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'email' => array(
				'required' => true,
				'min' => 2,
				'max' => 50,
				'unique' => 'users'
			),
			'username' => array(
				'reqired' => true,
				'min' => 2,
				'max' => 20,
				'unique' => 'users',
				'az09' => true
			),
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
			$salt = Hash::unique();

			$premium = new Premium();
			$free_premium = $premium->FreePremium();
			$hash_register = Hash::unique();
			

			try {
				$lastid = $user->create(array(
					'username' => Input::get('username'),
					'password' => Hash::make(Input::get('password'), $salt),
					'salt' => $salt,
					'email' => Input::get('email'),
					'email_random' => $hash_register,
					'company_verif' => 0,
					'blocked' => 0,
					'premium_date' => date('Y-m-d H:i:s', strtotime("+ $free_premium days"))
				));

				
				$db = new Database();
				$inser = $db->insertRow("INSERT INTO premium(user_id, date_before_add, days_add, who_add, gratis, note) VALUE(?, ?, ?, ?, ?, ?)", [$lastid, 
					date('Y-m-d H:i:s'), 
					$free_premium, 
					'register', 
					'1', 
					'Gratis do rejestracji']);

				// tworzenie folderu usera
				if (!file_exists('upload/'.$lastid.'')) {
					mkdir('upload/'.$lastid.'', 0744, true);
				}

				// wiadomość
				$message = Lang::get($tr,'register.ok.send.info').'<a href="http://loadliquids.com?id='.$lastid.'&ha='.$hash_register.'">'.Lang::get($tr,'register.ok.send.info2');

				header('Content-type: text/html; charset=utf-8');
				date_default_timezone_set('Europe/Warsaw');

				$mail = new PHPMailer(true);
				
				$mail->isSMTP(); // Używamy SMTP
				$mail->Host = Config::get('mail/host'); // Adres serwera SMTP
				$mail->SMTPAuth = true; // Autoryzacja (do) SMTP
				$mail->Username = Config::get('mail/username'); // Nazwa użytkownika
				$mail->Password = Config::get('mail/password'); // Hasło
				$mail->SMTPSecure = 'ssl'; // Typ szyfrowania (TLS/SSL)
				$mail->Port = 465; // Port

				$mail->CharSet = "UTF-8";
				$mail->setLanguage('pl', '/phpmailer/language');

				$mail->setFrom('contact@loadliquids.com', 'loadliquids.com');
				$mail->addAddress(Input::get('email'), Input::get('username'));
				$mail->addReplyTo('contact@loadliquids.com', 'loadliquids.com');

				$mail->isHTML(true); // Format: HTML
				$mail->Subject = Lang::get($tr,'message.subject');
				$mail->Body = $message;
				$mail->AltBody = 'By wyświetlić wiadomość należy skorzystać z czytnika obsługującego wiadomości w formie HTML';

				$mail->send();
					

				Session::flash('registered', Lang::get($tr,'flsh.message.registred'));
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
}
?>
<!doctype html>
<html lang="pl">
<head>
	<title>Register | loadliquids.com</title>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="css/bootstrap.css">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/mp.css">

	<link href="https://fonts.googleapis.com/css?family=PT+Sans:400,700|Signika:300,400,600,700" rel="stylesheet">


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
					<div class="col-12 sign-up-head">
						<?php echo Lang::get($tr,'h1.free.start'); ?>
					</div>
					<div class="col-12 sign-up-head-sm">
						<?php echo Lang::get($tr,'h1.free.start.info'); ?>
					</div>



					<div class="col-12 sign-in-form">
						<?php  
						if (isset($error)) {
							echo '<div class="warning-message"> '.Lang::get($tr,'errors.in.form').' <br>';
							foreach ($error as $errors) {
								echo $errors, '<br>';
							}
							echo '</div>';
						}
						
						?>

						<form action="" method="post">
							<div class="form-group">
								<?php echo'
								<label for="email">'.Lang::get($tr,'email').'</label>
								<input type="email" class="form-control" name="email" id="email" aria-describedby="email" placeholder="'.Lang::get($tr,'placeholder.password.email').'" value="'.escape(Input::get('email')).'" required>
							</div>

							<div class="form-group">
								<label for="username">'.Lang::get($tr,'username').'</label>
								<input type="text" class="form-control" name="username" id="username" value="'.escape(Input::get('username')).'" required>
							</div>

							<div class="form-group">
								<label for="password">'.Lang::get($tr,'password').'</label>
								<input type="password" class="form-control" name="password" id="password" required>
							</div>

							<div class="form-group">
								<label for="password_again">'.Lang::get($tr,'password.again').'</label>
								<input type="password" class="form-control" name="password_again" id="password_again" required>
							</div>'?>

							<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
							<button type="submit" class="btn btn-green mt-10" ><?php echo Lang::get($tr,'btn.register') ?></button>

						</form>

					</div>


				</div>
				<div class="row sign-up-in">
					<div class="col align-self-end pr-0">
						<?php echo Lang::get($tr,'have.an.account').'<a href="login.php">'. Lang::get($tr,'sign.in').'</a>'?>
					</div>
				</div>
			</div>





			<!-- /content -->
		</div>
	</div>
</body>
</html>
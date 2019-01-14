<?php 
require_once 'core/init.php';

// translate
$tr = Lang::set('pass-reset');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$flag = false;

if (isset($_POST['email'])) {
	$email = $_POST['email'];
	$db = new Database();

	
	$users = $db->getRow("SELECT * FROM users WHERE email = ?", [$email]);
	if ($users) {

		// wiadomość
		$message = Lang::get($tr,'remind.ok.send.info').'<a href="http://loadliquids.com/new-pass.php?id='.$users['id'].'&sa='.$users['salt'].'">'.Lang::get($tr,'remind.ok.send.info2');

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


		Session::flash('remind_send', Lang::get($tr,'flsh.message.remind.send'));
		Redirect::to('pass-reset.php');

		} else {
			$flag = true;
			$error = Lang::get($tr,'error.no.email');
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
						<?php echo Lang::get($tr,'h1.pass-remind') ?>
					</div>



					<div class="col-12 sign-in-form">
						<?php echo ($flag) ? '<div class="warning-message mb-20">' . $error . '</div>' : '';
						if (Session::exists('remind_send')) {
							echo '<div class="success-message mb-20">' . Session::flash('remind_send') . '</div>';
						}
						?>
						<form action="" method="post">
							<div class="form-group">
								<label for="email"><?php echo Lang::get($tr,'enter.email') ?></label>
								<input type="email" class="form-control" id="email" name="email" placeholder="<?php echo Lang::get($tr,'placeholder.email').'" '.'value="'.(Input::get('email') ? escape(Input::get('email')) : '').'"' ?>" required>
							</div>
							<button class="btn btn-green mt-20 "><?php echo Lang::get($tr,'btn.remind') ?></button>
						</form>
					</div>


				</div>
				<div class="row sign-up-in">
					<div class="col align-self-end pr-0">
						<a href="login.php"><?php echo Lang::get($tr,'return.to.login') ?></a>
					</div>
				</div>
			</div>





			<!-- /content -->
		</div>
	</div>
</body>
</html>
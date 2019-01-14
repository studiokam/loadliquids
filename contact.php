<?php 
require_once 'core/init.php'; 
$title = 'Contact | Loadliquids.com';

// translate 
$tr = Lang::set('contact');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$user = new User();
if ($user->isLoggedIn()) {
	$username = $user->data()->username;
	$user_email = $user->data()->email;
} else {
	$username = false;
	$user_email = false;
}

// sprawdzenie formularza
if (isset($_POST['message'])) {

	try {

		// wiadomość
		$message = Input::get('message');

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
		$mail->addAddress('contact@loadliquids.com', 'loadliquids.com');
		$mail->addReplyTo(Input::get('email'), Input::get('nick'));

		$mail->isHTML(true); // Format: HTML
		$mail->Subject = Input::get('subject');
		$mail->Body = $message;
		$mail->AltBody = 'By wyświetlić wiadomość należy skorzystać z czytnika obsługującego wiadomości w formie HTML';

		$mail->send();
			

		Session::flash('send', Lang::get($tr,'message.send.ok'));
		Redirect::to('contact.php');
		
	} catch (Exception $e) {
		die($e->getMessage());
		Session::flash('send_fail', Lang::get($tr,'message.send.fail'));
		Redirect::to('contact.php');
	}
	
}

$bg = new PageBG();
include_once 'top.php';
echo '<div class="bg' . $bg->get() . ' stickyfooter">';
include_once 'menu.php';
include_once 'baner.php';

?>

<div class="stickyfooter-content my-font">	
	<div class="container">

		

		<div class="row contact">

			<div class="col-md-7 col-lg-6 mb-50">
				<?php
					if (Session::exists('send')) {
						echo '<div class="success-message mb-20">' . Session::flash('send') . '</div>';
					}
					if (Session::exists('send_fail')) {
						echo '<div class="warning-message mb-20">' . Session::flash('send_fail') . '</div>';
					}
				?>
				<p class="font-lg"><?php echo Lang::get($tr,'contact.form'); ?></p>

				<div class="col-12 sign-in-form p-0">
					<form action="" method="post">
						<div class="form-group">
							<label for="nick"><?php echo Lang::get($tr,'nick'); ?></label>
							<input type="text" class="form-control" id="nick" name="nick" <?php echo 'placeholder="'.Lang::get($tr,'placeholder.nick').'" value="'. ($username ? $username : (Input::get('nick') ? escape(Input::get('nick')) : '')) ?>">
						</div>

						<div class="form-group">
							<label for="subject"><?php echo Lang::get($tr,'subject'); ?></label>
							<input type="text" class="form-control" id="subject" name="subject" <?php echo 'placeholder="'.Lang::get($tr,'placeholder.subject').'" value="'.(Input::get('subject') ? escape(Input::get('subject')) : '') ?>" required>
						</div>
						<div class="form-group">
							<label for="email"><?php echo Lang::get($tr,'email'); ?></label>
							<input type="email" class="form-control" id="email" name="email" <?php echo 'placeholder="'.Lang::get($tr,'placeholder.email').'" value="'. ($user_email ? $user_email : (Input::get('email') ? escape(Input::get('email')) : '')) ?>" required>
						</div>


						<div class="form-group">
							<label for="message"><?php echo Lang::get($tr,'message'); ?></label>
							<textarea class="form-control" id="message" name="message" rows="4" required><?php echo (Input::get('message') ? escape(Input::get('message')) : '') ?></textarea>
						</div>
						<button type="submit" class="btn btn-green mt-20"><?php echo Lang::get($tr,'send'); ?></button>
					</form>
				</div>


			</div>
			<div class="offset-md-1 col-md-4  offset-lg-1 col-lg-5 contact-right">
				<p class="font-md">loadliqids.com</p>
				<p class="">
					LOAD LIQUIDS COMPANY<br>
					Register in England<br>
					VAT EU:<br><br>
					tel. +48 791 517 424<br>
					e-mail: office@loadliquids.com
				</p>




			</div>
		</div>

	</div>
</div><!-- /sticky-footer  -->	

<?php include_once 'footer.php'; ?>

</div><!-- /first-bg -->
</body>
</html>
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


// usuniecie usera
if (Input::get('delete') === 'true') {	
	$db->deleteRowUser($user_id);
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
			
			<form action="" method="get">
				<div class="row">
					<div class="col-12">
						<div class="delete-user">
							<p class="font-md-400">Uwaga! Czy potwierdzasz usunięcie usera <b><?php echo $user['username'].'</b>?<br>' ?> Potwierdzenie powoduje wykasowanie wszystkich jego zamówień, zleceń, danych, logów - wszytkiego. Nie można tego cofnąć.</p>
						</div>
						
					
						<a href="admin-user.php?id=<?php echo $user['id'] ?>" class="btn btn-green btn-sm mb-1">Wróć</a>
						<a href="admin-user-delete.php?id=<?php echo $user_id ?>&delete=true" class="btn btn-danger btn-sm mb-1">Usuń tego usera</a>
					</div>
				</div>
			</form>
			
			
		
		</div>
	</div><!-- /sticky-footer  -->
    	<!-- /content -->

<?php include_once 'footer-admin.php'; ?>

	</div><!-- /stickyfooter (or first-bg) -->
  </body>
</html>
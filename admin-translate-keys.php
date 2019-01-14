<?php 
require_once 'core/init.php';
$title = 'Zaplecze | loadliquids.com';


$db = new Database();

$keys = $db->getRows("SELECT id, page, lang_key FROM lang ORDER BY page");


if (isset($_POST['key'])) {
	$page = $_POST['page'];
	$key = $_POST['key'];

	$new = $db->insertRow("INSERT INTO lang (page, lang_key) VALUE(?, ?)", [$page, $key]);
	header('Location:admin-translate-keys.php');
}

include_once 'top-admin.php';
echo '<div class=" stickyfooter">';
include_once 'menu-admin.php';
?>

	<div class="stickyfooter-content my-font">	
		<div class="container admin-content">
		
			<div class="row">
				<div class="col-12">
					<p class="font-md-400">Ostrożnie ze zmianami tych kluczy</p>
				</div>
			</div>
			<hr>
			<div class="row">
				
				<div class="col-md-6">
					
					<?php 
						foreach ($keys as $row) {
							echo $row['page'] . ' / ' . $row['lang_key'].'<br>';
						}
					?>

				</div>
				<div class="col-md-6">
					
					

					<div class="language-box-edit">
						<p class="font-md-400">Dodaj nowy</p>
						<form action="" method="post">
							<div class="form-group">
							    <label for="page">Podaj stronę klucza</label>
							    <input type="text" class="form-control" id="page" name="page" required>
							
							    <label class="mt-20" for="key">Klucz</label>
							    <input type="text" class="form-control" id="key" name="key" required>
							</div>	
							<input type="submit" class="btn btn-green btn-sm" value="Zapisz">	
						</form>
					</div>

				</div>
			</div>
		
		</div>
	</div><!-- /sticky-footer  -->
    	<!-- /content -->

<?php include_once 'footer-admin.php'; ?>

	</div><!-- /stickyfooter (or first-bg) -->
  </body>
</html>
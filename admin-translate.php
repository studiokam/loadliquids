<?php 
require_once 'core/init.php';
$title = 'Zaplecze | loadliquids.com';

$db = new Database();

// policzenie wszytkich kluczy jezyka
$lang_keys = $db->getRows("SELECT lang_key FROM lang");
$count_lang_keys = $db->countRows();

// obliczenie % dla j. polskiego
$lang_pl = $db->getRows("SELECT pl FROM lang WHERE pl <> '' ");
$count_pl_all = $db->countRows();
$lang_percent_pl =  $count_pl_all / $count_lang_keys * 100; 

// obliczenie % dla j. angielskiego
$lang_en = $db->getRows("SELECT en FROM lang WHERE en <> '' ");
$count_en_all = $db->countRows();
$lang_percent_en =  $count_en_all / $count_lang_keys * 100; 



include_once 'top-admin.php';
echo '<div class=" stickyfooter">';
include_once 'menu-admin.php';
?>

	<div class="stickyfooter-content my-font">	
		<div class="container admin-content">
		
			<div class="row">
				<div class="col-12">
					<p class="font-md-400">Tłumaczenia</p>
				</div>
			</div>
			<div class="row">
				<div class="col-12 col-xl-12 mb-20">
					
					    <p>Aby możliwe było poprawne działanie tłumaczeń przed aktywowaniem nowego języka niezbędne będzie wypełnienie wcześnije wszystkich pól.</p>
					  
					
				</div>
				<div class="col-12 col-xl-12 mb-20">
					<hr>
					    <a href="admin-translate-keys.php" class="btn btn-green btn-sm">Klucze</a>
					<hr>
					
				</div>
				<div class="col-md-4">
					<p class="font-md-400">Polski</p>
					<div class="language-box">
						<table class="table table-sm ">
						  <tbody>
						    <tr>
						      <td class="border-t0">Stan tłumaczeń</td>
						      <td class="border-t0 text-right"><b><?php echo floor($lang_percent_pl).'%'; ?></b></td>
						    </tr>
						  </tbody>
						</table>
						<a href="admin-translate-edit.php?lang=pl" class="btn btn-green btn-sm">Edytuj</a>
					</div>
				</div>
				<div class="col-md-4">
					<p class="font-md-400">Angielski</p>
					<div class="language-box">
						<table class="table table-sm ">
						  <tbody>
						    <tr>
						      <td class="border-t0">Stan tłumaczeń</td>
						      <td class="border-t0 text-right"><b><?php echo floor($lang_percent_en).'%'; ?></b></td>
						    </tr>
						  </tbody>
						</table>
						<a href="admin-translate-edit.php?lang=en" class="btn btn-green btn-sm">Edytuj</a>
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
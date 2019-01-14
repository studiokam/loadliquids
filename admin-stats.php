<?php 
require_once 'core/init.php';
$title = 'Zaplecze | loadliquids.com';


$db = new Database();
$stats = new Stats();
$stats2 = new Stats();

include_once 'top-admin.php';
echo '<div class=" stickyfooter">';
include_once 'menu-admin.php';
?>

	<div class="stickyfooter-content my-font">	
		<div class="container admin-content">
		
			<div class="row">
				<div class="col-12">
					<p class="font-md-400">Statystyki</p>
				</div>
			</div>
			<div class="row">
				<div class="col-12 col-sm-6 col-xl-10 mb-50">
					<p>
					
				<?php
					echo'
						<b>Rejestracje</b><br>
						Dziś: '.$stats->Today('users', 'register').'<br>
						Miesiąc: '.$stats->Montch('users', 'register', '1').'<br>
						Zawsze: '.$stats->All('users').'<br><br>

						Liczba kont PREMIUM: <b>'.$stats->AllPremium().'</b><br><br>
						 
						Liczba wszystkich subskrybcji kończących się w ciągu:<br>
						1 dnia: '.$stats2->AllPremiumBetween('0', '0').'<br>
						1 tygodnia: '.$stats2->AllPremiumBetween('1', '7').'<br>
						1 miesiąca: '.$stats2->AllPremiumBetween('8', '30').'<br>
						3 miesięcy: '.$stats2->AllPremiumBetween('31', '90').'<br>
						6 miesięcy: '.$stats2->AllPremiumBetween('91', '181').'<br>
						1 rok: '.$stats2->AllPremiumBetween('181', '365').'<br>
						później: '.$stats2->AllPremiumBetween('365', '668').'<br><br>
					';
				?>
						 
						
						 
						 
						

						<b>Liczba wszystkich zakupionych konto PREMIUM: 3234</b><br><br>
 
						Bieżący rok: 34 (3975.00 PLN)<br>
						Miesiąc 2017-11: 34 (3975.00 PLN)<br>
					</p>
				</div>
			</div>
		
		</div>
	</div><!-- /sticky-footer  -->
    	<!-- /content -->

<?php include_once 'footer-admin.php'; ?>

	</div><!-- /stickyfooter (or first-bg) -->
  </body>
</html>
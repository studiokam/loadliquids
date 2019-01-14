<?php 
require_once 'core/init.php';
$title = 'loadliquids.com';



$bg = new PageBG();
include_once 'top.php';
echo '<div class="bg' . $bg->get() . ' stickyfooter">';

include_once 'menu.php';
include_once 'baner.php';

?>

<div class="stickyfooter-content my-font">	
	<div class="container">

		<div class="row pt-50">
			<div class="col-12 col-lg-4">
				<div class="home-box-search mt-30">
					<h5>Dla przewoznika</h5>
					
					<p class="pt-20">Zdobywaj nowe zlecenia
					Przez giełdę ładunków i bezpośrednio</p>
						<ul>
							<li>dostosowane do Twojej floty</li>
							<li>ładunki powrotne z całej Europy</li>
							<li>od wiarygodnych załadowców z Europy Zachodniej</li>
							<li>Współpracuj ze sprawdzonymi kontrahentami</li>
							<li>Dzięki weryfikacji firm obniżamy Twoje ryzyko</li>
						</ul> 
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Fuga nisi, ipsam repellendus tempora laborum. Quas itaque suscipit.</p>
					<a href="register.php" class="btn btn-green btn-block mt-50">Zarejestruj się</a>
					<a href="login.php" class="btn btn-green btn-block mt-50">Zaloguj się</a>

				</div>
			</div>
			<div class="col-12 col-lg-4">
				<div class="home-box-search mt-30">
					<h5>Dla spedytora</h5>
					
					<p class="pt-20">Zdobywaj nowe zlecenia
						Przez giełdę ładunków i bezpośrednio.</p>
						<ul>
							<li>oferty od wiarygodnych załadowców z całej Europy</li>
							<li>szansa na zlecenia stałe i dobre stawki</li>
							<li>Współpraca ze sprawdzonymi załadowcami</li>
							<li>Dzięki weryfikacji firm obniżamy Twoje ryzyko.</li>
						</ul>
						
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Fuga nisi, ipsam repellendus tempora laborum. Quas itaque debitis laudantium expedita, suscipit.</p>
						<a href="register.php" class="btn btn-green btn-block mt-50">Zarejestruj się</a>
						<a href="login.php" class="btn btn-green btn-block mt-50">Zaloguj się</a>

				</div>
			</div>
			<div class="col-12 col-lg-4">
				<div class="home-box-search mt-30">
					<h5>Fabryki</h5>
					
					<p class="pt-20">Rozpocznij bezpośrednią współpracę z przewoźnikami
						Dostęp do giełdy wolnych pojazdów w całej Europie
						<ul>
							<li>wszystkie typy cystern, nowoczesne floty</li>
							<li>zweryfikowane firmy transportowe</li>
							<li>konkurencyjne firmy transportowe z Europy Wschodniej</li>
							<li>certyfikowani i specjalistyczni przewoźnicy (ISO, ADR, HACCP)</li>
							<li>Sprawdzamy każdego przewoźnika</li>
							<li>Minimalizujemy ryzyko po Twojej stronie.</li>
							<li>kontrolujemy dokumenty i licencje firm</li>
							<li>weryfikujemy historię i powiązania przewoźników</li>
						</ul>

						</p>
						<a href="register.php" class="btn btn-green btn-block mt-50">Zarejestruj się</a>
						<a href="login.php" class="btn btn-green btn-block mt-50">Zaloguj się</a>

				</div>
			</div>
		</div>
		
		

	</div>
</div><!-- /sticky-footer  -->
<!-- /content -->

<?php 
include_once 'footer.php'; 
?>

<!-- Extra JavaScript/CSS added manually in "Settings" tab -->
<!-- Include jQuery -->
<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>


</div><!-- /stickyfooter (or first-bg) -->
</body>
</html>
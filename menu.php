<?php
$user = new User();

// translate 
$trm = Lang::set('main');

$premium = new Premium();
if ($user->isLoggedIn()) {
?>	
	<!-- baner -->

		<!-- logged -->
		<div class="container-nav my-font">
			
			<div class="container">
				
			
				<div class="row">
		    		<div class="col">
		    			
					
						<nav class="navbar navbar-expand-lg navbar-dark bg-dark pl-0 pr-0">
						  <a class="navbar-brand mr-30" href="index.php">load<b>liquids</b>.com</a>
						  <button class="navbar-toggler pr-0" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
						    <span class="navbar-toggler-icon"></span>
						  </button>
						  <div class="collapse navbar-collapse" id="navbarNav">
						  	
						    <ul class="navbar-nav  mr-auto">
						      <li class="nav-item">
						        <a class="nav-link" href="howitworks.php"><?php echo Lang::get($trm, 'how.it.works') ?></a>
						      </li>
						      <li class="nav-item">
						        <a class="nav-link" href="contact.php"><?php echo Lang::get($trm, 'contact') ?></a>
						      </li>
						      <li class="nav-item specjal">
						        <a class="nav-link" href="index.php"><?php echo Lang::get($trm, 'search') ?></a>
						      </li>
						      <li class="nav-item specjal-green">
						        <a class="nav-link" href="add-in.php"><?php echo Lang::get($trm, 'add.load') ?></a>
						      </li>
						      <li class="nav-item specjal-green">
						        <a class="nav-link" href="add-in-car.php"><?php echo Lang::get($trm, 'add.car') ?></a>
						      </li>

						    </ul>



						    
							<div class="dropdown mr-0">
								<ul class="navbar-nav  mr-auto">
						      <li class="nav-item">
						        <a class="nav-link" href="account.php"><?php echo Lang::get($trm, 'account') ?></a>
						      </li>
						      <li class="nav-item">
						        <a class="nav-link" href="userorders.php"><?php echo Lang::get($trm, 'orders') ?></a>
						      </li>
						      <li class="nav-item">
						        <a class="nav-link" href="invoice.php"><?php echo Lang::get($trm, 'invoice') ?></a>
						      </li>

						    </ul>
							</div>
							

						  </div>

						 
						</nav>

		    		</div><!-- /nav -->
		    		
		    		
		    	</div>


			</div>
		</div>


		<div class="container-nav-in my-font">
			
			<div class="container">
				
		    	<div class="row">
		    		<div class="col-8">		    		
						<span class="navbar-text mr-auto"><?php echo Lang::get($trm, 'logged.as') ?><b><?php echo escape($user->data()->username); ?></b><span class="d-none d-sm-inline"> / <?php echo escape($premium->accountType($user->data()->id)); ?></span></span>
		    		</div>
		    		
		    		<div class="col-4 logout pr-0">
						<a class="nav-link float-right" href="logout.php"><?php echo Lang::get($trm, 'logout') ?></a>
		    		</div>
		    	</div>

			</div>

		</div><!-- /container-nav-in -->
		<!-- /logged -->
<?php  
} else {
?>
		

		<!-- not logged -->
		<div class="container-nav my-font">
			
			<div class="container">
				
				<div class="row">
		    		<div class="col">
		    			
					
						<nav class="navbar navbar-expand-lg navbar-dark bg-dark pl-0">
						  <a class="navbar-brand mr-30" href="index.php">load<b>liquids</b>.com</a>
						  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
						    <span class="navbar-toggler-icon"></span>
						  </button>
						  <div class="collapse navbar-collapse" id="navbarNav">
						  	
						    <ul class="navbar-nav  mr-auto">
						      <!-- <li class="nav-item">
						        <a class="nav-link" href="">O nas</a>
						      </li> -->
						      <li class="nav-item">
						        <a class="nav-link" href="howitworks.php"><?php echo Lang::get($trm, 'how.it.works') ?></a>
						      </li>
						      <li class="nav-item">
						        <a class="nav-link" href="contact.php"><?php echo Lang::get($trm, 'contact') ?></a>
						      </li>
						      <li class="nav-item specjal">
						        <a class="nav-link" href="index.php"><?php echo Lang::get($trm, 'search') ?></a>
						      </li>
						      <li class="nav-item specjal">
						        <a class="nav-link" href="login.php"><?php echo Lang::get($trm, 'add.order') ?></a>
						      </li>

						    </ul>

							<div class="dropdown mr-0">
								<ul class="navbar-nav  mr-auto">
						      <li class="nav-item">
						        <a class="nav-link" href="register.php"><?php echo Lang::get($trm, 'register') ?></a>
						      </li>
						      <li class="nav-item">
						        <a class="nav-link" href="login.php"><?php echo Lang::get($trm, 'login') ?></a>
						      </li>

						    </ul>
							</div>

						  </div>

						 
						</nav>

		    		</div><!-- /nav -->
		    	</div>
		    	
			</div>
		</div>
		<!-- /not logged -->

<?php } ?>
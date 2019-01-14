<?php 
require_once 'core/init.php';
$title = 'Zaplecze | loadliquids.com';


$orderby = new OrderBy();
$orders = new Orders();
$comapny = new Company();


// sortowanie na stronie
if (!Input::get('sort')) {
	$sort = $orderby->sort('dateup');
} else {
	$sort = $orderby->sort(Input::get('sort'));
}

// sprawdzenie czy w linku get podane jest id
if (Input::get('user_id')) {
	$user_id = Input::get('user_id');
	$sort = 'WHERE user_id = '.$user_id.' ORDER BY id DESC ';
} 

// wyszukiwarka
if (Input::get('search')) {
	$sort = $orderby->searchInOrders(Input::get('search'));
}

$db = new Database();
$all_orders = $db->getRows("SELECT * FROM orders $sort");

include_once 'top-admin.php';
echo '<div class=" stickyfooter">';
include_once 'menu-admin.php';
?>

	<div class="stickyfooter-content my-font">	
		<div class="container admin-content">
		
		<?php 
				if (Session::exists('order_deleted')) {
					echo '<div class="success-message mb-20">' . Session::flash('order_deleted') . '</div>';
				}
				if (Session::exists('empty_id_error')) {
					echo '<div class="warning-message mb-20">' . Session::flash('empty_id_error') . '</div>';
				}
				if (isset($error)) {
					echo '<div class="warning-message mb-30"> Błędy w formularzu: <br>';
					foreach ($error as $errors) {
						echo $errors, '<br>';
					}
					echo '</div>';
				}
				
		?>
			<div class="row">
				<div class="col-12">
					<p class="font-md-400">Zlecenia</p>
				</div>
			</div>
			<div class="row">
				<div class="col-12 col-xl-10 mb-50">
					
					<form action="" method="get">	
						<div class="search">
							<div class="row">
								<div class="col-8 col-sm-8 col-md-4 pr-10">
									<input class="form-control form-control-sm" type="text" id="search" name="search" value="<?php echo Input::get('search') ?>">
								</div>
								<div class="col-4 col-sm-4 col-md-4 pl-0">
									<button type="submit" class="btn btn-green btn-sm">Szukaj po numarze zlecenia</button>
								</div>
							

								<div class="col-12 col-sm-12 col-md-4 mt-10">
									<div class="sort">
									Sortuj: <a class=" dropdown-toggle" data-toggle="dropdown" href="#" role="button"><?php echo $orderby->title() ?></a>
									    <div class="dropdown-menu">
									      <a class="dropdown-item" href="admin-ads-list.php?sort=date">Od najnowszego</a>
									      <a class="dropdown-item" href="admin-ads-list.php?sort=dateup">Od najstarszego</a>
									    </div>
									</div>
								</div>
							</div>
						
						</div>
					</form>
					  
					<table class="table table-bordered mt-20">
					  <thead>
					    <tr>
					      <th class="d-none d-sm-table-cell text-center" scope="col">#</th>
					      <th class="d-none d-md-table-cell" scope="col">Numer</th>
					      <th scope="col">User</th>
					      <th class="d-none d-md-table-cell" scope="col">Data dodania</th>
					      <th scope="col">Załadunek</th>
					      <th scope="col">Rozładunek</th>
					      <th class="d-none d-lg-table-cell" scope="col">Tonaż</th>
					      <th class="d-none d-lg-table-cell text-center" scope="col">TYP</th>
					      <th class="text-center" scope="col"></th>
					    </tr>
					  </thead>
					  <tbody>
	    <?php
			$i = 1;
			  	foreach ($all_orders as $row) {
				  		
					$load_orders =  $orders->xxx($row['order_type'].'_orders', $row['car_or_load_id']);

				  	echo'
						<tr>
					      <td>'. $i++.'</td>
					      <td>'. $row['id'] .'</td>
					      <td class="d-none d-md-table-cell">'. $comapny->userName($row['user_id']) .'</td>
					      <td class="d-none d-md-table-cell">'. $load_orders['add_date'] .'</td>
					      <td class="d-none d-lg-table-cell"><img class="mr-10" src=img/flags/'.$load_orders['in_country_id'].'.png alt="'.$orders->shortcut($load_orders['in_country_id']).'"> '. $load_orders['in_city'] .'</td>
					      <td class="d-none d-lg-table-cell"><img class="mr-10" src=img/flags/'.$load_orders['out_country_id'].'.png alt="'.$orders->shortcut($load_orders['out_country_id']).'"> '. $load_orders['out_city'] .'</td>
					      <td class="d-none d-md-table-cell">'. $load_orders['tonnage'] .'</td>
					      <td class="d-none d-lg-table-cell">'. $row['order_type'] .'</td>
					      <td class="text-center">
					      	<a href="admin-ads-'. $row['order_type'] .'.php?id='. $row['id'] .'"><i class="ion-edit mr-10" alt="Edytuj"></i></a>
					      </td>
					    </tr>
				  	';
				}
			?>

					  </tbody>
					</table>
				</div>
			</div>
		
		</div>
	</div><!-- /sticky-footer  -->
    	<!-- /content -->

<?php include_once 'footer-admin.php'; ?>

	</div><!-- /stickyfooter (or first-bg) -->
  </body>
</html>
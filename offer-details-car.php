<?php 
require_once 'core/init.php';
$title = 'Loadliquids.com';

// translate
$tr = Lang::set('offer-details-car');

$user = new User();
$premium = new Premium();
$orders = new Orders();
$id = $user->data()->id;


// sprawdzenie czy w linku get podane jest id
if (Input::get('id')) {
	$order_id = Input::get('id');
} else {
	Session::flash('order_details_error', Lang::get($tr,'flash.error.link'));
	Redirect::to('index.php');
}


$db = new Database();
$global_order = $db->getRow("SELECT * FROM orders WHERE id = $order_id");
$car_orders = $db->getRow("SELECT * FROM car_orders WHERE id = ".$global_order['car_or_load_id']."");

// pobranie danych wystawiającego ogłoszenie (widać je jesli user ma premium)
$order_owner = $db->getRow("SELECT * FROM users WHERE id = ".$car_orders['user_id']."");


$countries = $db->getRows("SELECT * FROM countries");

include_once 'top.php';
echo '<div class="first-bg stickyfooter">';
include_once 'menu.php';
include_once 'baner.php';

?>


		
		<div class="stickyfooter-content my-font">	
		<div class="container">
		<div class="record-wrapper">

			<div class="row offer-details-head">
				<div class="col-lg-12"><?php echo Lang::get($tr,'h1.order.details').'
					<span class="font-numbers">'.$order_id ?></span>
				</div>
			</div>

			

		<?php
			echo '
			
			<div class="offer-details-table">
				<table class="table table-bordered">
					<tbody>
					    <tr>
					      <th scope="row">'.Lang::get($tr,'loading.place').'</th>
					      <td><img class="flag" src=img/flags/'.$car_orders['in_country_id'].'.png alt="'.$orders->shortcut($car_orders['in_country_id']).'"> '.$orders->country($car_orders['in_country_id']).' / <b>'.$car_orders['in_city'] .'</b> <span class="offer-details-data">'.$car_orders['in_post'].'</span> / <span class="offer-details-data">'.date('d.m.Y', strtotime($car_orders['in_date'])).'</span></td>
					    </tr>
					    <tr>
					      <th scope="row">'.Lang::get($tr,'loading.place').'</th>
					      <td><img class="flag" src=img/flags/'.$car_orders['out_country_id'].'.png alt="'.$orders->shortcut($car_orders['out_country_id']).'"> '. $orders->country($car_orders['out_country_id']).' / <b>'.$car_orders['out_city'] .'</b> <span class="offer-details-data">'.$car_orders['out_post'].'</span> / <span class="offer-details-data">'.date('d.m.Y', strtotime($car_orders['out_date'])).'</span></td>
					    </tr>
					    <tr>
						    <th scope="row">'.Lang::get($tr,'added').'</th>
						    <td><span class="offer-details-data">'. date('d.m.Y H:i', strtotime($car_orders['add_date'])) .'</span>
					  		</td>
					    </tr>
					    <tr>
						    <th scope="row">'.Lang::get($tr,'validity').'</th>
						    <td><span class="offer-details-data">'. date('d.m.Y H:i', strtotime($car_orders['display_to_date'])) .'</span>
					  		</td>
					    </tr>
					    <tr>
						    <th scope="row">'.Lang::get($tr,'tonnage').'</th>
						    <td><span class="offer-details-data">'. $car_orders['tonnage'] .'</span>
					  		</td>
					    </tr>
					    <tr>
						    <th scope="row">'.Lang::get($tr,'car.type').'</th>
						    <td>Cysterna</td>
					    </tr>
					    <tr>
						    <th scope="row">'.Lang::get($tr,'additional').'</th>
						    <td>'.$orders->additional('car_orders', $car_orders['id']).'</td>
					    </tr>
					    <tr>
						    <th scope="row">'.Lang::get($tr,'note').'</th>
						    <td>'. $car_orders['note'] .'</td>
					    </tr>

				';
		
	    		if ($user->isLoggedIn()) { //is logged in
	    			if ($premium->isPremium($id)) { // premium user
	    				echo'
						<tr>
						    <th scope="row">'.Lang::get($tr,'price').'</th>
						    <td>'.$car_orders['price'].' '.$car_orders['currency_id'].'</td>
					    </tr>
					    <tr>
						    <th scope="row">'.Lang::get($tr,'contact').'</th>
						    <td>
						    	<span class="line-height3"><b>'.$order_owner['username'].'</b> <br>e-mail: <b><a href="mailto:'.$order_owner['email'].'">'.$order_owner['email'].'</a> / <a href="mailto:'.$order_owner['email2'].'">'.$order_owner['email2'].'</a></b>  <br>Phone: '.$order_owner['phone'].' / ' .$order_owner['phone2'].'</span>
						    </td>
					    </tr>';
	    			} else { // no premium user
	    				echo'
						<tr>
						    <th scope="row">'.Lang::get($tr,'price').'</th>
						    <td class="danger_bold"><a href="premium.php">'.Lang::get($tr,'premium.price.info').'</a></td>
					    </tr>
					    <tr>
						    <th scope="row">'.Lang::get($tr,'contact').'</th>
						    <td class="danger_bold"><a href="premium.php">'.Lang::get($tr,'premium.contact.info').'</a></td>
					    </tr>';
	    			}
					
					echo'    
					</tbody>
				</table>
			</div>
					';
				} else { // not logged in
					echo'
						<tr>
						    <th scope="row">'.Lang::get($tr,'price').'</th>
						    <td><a href="login.php" class="btn btn-green btn-sm ">'.Lang::get($tr,'btn.login').'</a></td>
					    </tr>
					    <tr>
						    <th scope="row">'.Lang::get($tr,'contact').'</th>
						    <td>
						    	<a href="login.php" class="btn btn-green btn-sm ">'.Lang::get($tr,'btn.login').'</a>
						    </td>
					    </tr>
				    </tbody>
			</table>
		</div>
		<div class="row">
			<div class="col">
				'.Lang::get($tr,'login.to.get.access').'
			</div>
		</div>
					';
				}
	    	?>
				    	

					
				


		</div><!-- /record-wrapper -->


	</div>
	</div><!-- /sticky-footer  -->
    	<!-- /content -->

<?php include_once 'footer.php'; ?>

	</div><!-- /stickyfooter (or first-bg) -->
  </body>
</html>
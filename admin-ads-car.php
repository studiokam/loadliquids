<?php 
require_once 'core/init.php';
$title = 'Zaplecze | loadliquids.com';


$user = new User();
$orders = new Orders();
$db = new Database();


// sprawdzenie czy w linku get podane jest id
if (Input::get('id')) {
	$order_id = Input::get('id');
	$empty_test = $db->getRows("SELECT * FROM orders WHERE id = $order_id");
	
	if (!$db->countRows() > 0 ) {
		Session::flash('empty_id_error', 'Przepraszamy nie ma takiego numeru zlecenia. Nie można wyświetlić jego detali.');
		Redirect::to('admin-ads-list.php');
	} 

} else {
	Session::flash('user-order-error', 'Przepraszamy wystąpil błąd. Nie można w tej chwili sprawdzić detali zlecenia.');
	Redirect::to('admin-ads-car.php');
}

$user_id = $user->data()->id;

$orderID = $db->getRow("SELECT * FROM orders WHERE id = $order_id");
$car_orders = $db->getRow("SELECT * FROM car_orders WHERE id = ".$orderID['car_or_load_id']."");


// zamkniecie zlecenia
if (Input::get('id') && Input::get('type')) {
	try {
		
		$updateRow = $db->updateRow("UPDATE car_orders SET order_status = ?  WHERE id = ?", ["0", $orderID['car_or_load_id']]);

		Session::flash('order_closed', 'Zlecenie zostało zamknięte.');
		Redirect::to('admin-ads-car.php?id='.$orderID['id'].'');
		
	} catch (Exception $e) {
		die($e->getMessage());
	}
	
}

// usunięcie zlecenia
if (Input::get('delete') && Input::get('delete') == '1') {
	
	try {
		// usuniecie z tabeli orders
		$delete_from_orders = $db->deleteRow("DELETE FROM orders WHERE id = ?", [Input::get('id')]);
		// usuniecie z tabelu car_orders
		$delete_from_car_orders = $db->deleteRow("DELETE FROM car_orders WHERE id = ?", [$orderID['car_or_load_id']]);

		Session::flash('order_deleted', 'Usunięto zlecenie.');
		Redirect::to('admin-ads-list.php');
		
	} catch (Exception $e) {
		die($e->getMessage());
	}
}

include_once 'top-admin.php';
echo '<div class=" stickyfooter">';
include_once 'menu-admin.php';
?>


	
		<div class="stickyfooter-content my-font">
			<div class="container">

				<?php 
				if (Session::exists('order_closed')) {
					echo '<div class="success-message mt-20">' . Session::flash('order_closed') . '</div>';
				} 
						if (isset($error)) {
							echo '<div class="warning-message mt-30"> Błędy w formularzu: <br>';
							foreach ($error as $errors) {
								echo $errors, '<br>';
							}
							echo '</div>';
						}
						
				?>
				
				<div class="row invoice">
					<div class="col">

						<p class="font-md-400">Szczegóły zlecenia nr.: <b><?php echo $orderID['id'];?></b></p>

						<div class="row">

							<div class="col-md-6">
								
								<table class="table table-bordered">
								  
								  <tbody>
								    
								    <tr>
								      <td>Załadunek</td>
								      <td>
								      	<?php 
								      	echo 
								      		$car_orders['in_post'] .' '.$car_orders['in_city'].' / '.
								      		$orders->country($car_orders['in_country_id']). '<br>'.
								      		date('d.m.Y',strtotime($car_orders['in_date'])). '<br> ' 
								      	;?>
								      	
								      </td>
								    </tr>
								    <tr>
								      <td>Rozładunek</td>
								      <td>
								      	<?php 
								      	echo 

								      		$car_orders['out_post'] .' '.$car_orders['out_city'].' / '.
								      		$orders->country($car_orders['out_country_id']). '<br>'.
								      		date('d.m.Y',strtotime($car_orders['out_date'])). '<br> '

								      		
								      	;?>
								      </td>
								    </tr>
								    <tr>
								      <td>Car details</td>
								      <td><?php echo $car_orders['car_details'] ;?></td>
								    </tr>
								    <tr>
								      <td>Notatka</td>
								      <td><?php echo $car_orders['note'] ;?></td>
								    </tr>
								  </tbody>
								</table>

							</div>

							<div class="col-md-6">

								<table class="table table-bordered">
								  
								  <tbody>
								    <tr>
								      <td>Numer</td>
								      <td><?php echo $orderID['id']; ?></td>
								    </tr>
								    <tr>
								      <td>Dodano</td>
								      <td><?php echo date('d.m.Y H:i:s', strtotime($car_orders['add_date'])); ?></td>
								    </tr>
								    <tr>
								      <td>Koniec</td>
								      <td><?php echo date('d.m.Y',strtotime($car_orders['display_to_date'])).' '.$car_orders['display_to_hours']; ?></td>
								    </tr>
								    <tr>
								      <td>Status</td>
								      <td><?php echo $orders->orderStatus('car_orders', $car_orders['id']); ?></td>
								    </tr>
								    <tr>
								      <td>Typ zabudowy</td>
								      <td><?php echo $orders->carType($car_orders['car_type_id']); ?></td>
								    </tr>
								    <tr>
								      <td>Cena</td>
								      <td><?php echo $car_orders['price'] . ' ' .$car_orders['currency_id'] ; ?></td>
								    </tr>
								    <tr>
								      <td>Wymagania dodatkowe</td>
								      <td><?php echo $orders->additional('car_orders', $car_orders['id']); ?></td>
								    </tr>
								  </tbody>
								</table>
								
							</div>
							
						</div>
						
						<?php 
							if ($car_orders['order_status'] === '0')  {
								echo '<p class="font-md-400">Zamówienie jest zamknięte.</p>
								<a href="admin-ads-car.php?delete=1&id='.$orderID['id'].'" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteOrder">Usuń</a>';
							} else {
								echo '<a href="admin-ads-car.php?id='.$orderID['id'].'&type='.$car_orders['order_type'].'" class="btn btn-green btn-sm" data-toggle="modal" data-target="#closeOrder">Zamknij zlecenie</a>
								<a href="admin-ads-car.php?delete=1&id='.$orderID['id'].'" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteOrder">Usuń</a>';
							}
						?>
						

						<!-- Modal -->
						<div class="modal fade" id="closeOrder" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
						  <div class="modal-dialog" role="document">
						    <div class="modal-content">
						      <div class="modal-header">
						        <h5 class="modal-title" id="exampleModalLongTitle">Zamknięcie zlecenia</h5>
						        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						          <span aria-hidden="true">&times;</span>
						        </button>
						      </div>
						      <div class="modal-body">
						        Czy potwierdzasz zamknięcie zlecenia? Zamknietych zleceń nie można edytować, ponawiać czy włączać. 
						      </div>
						      <div class="modal-footer">
						        <button type="submit" class="btn btn-secondary btn-sm" data-dismiss="modal">No</button>
						        <?php  echo '<a href="admin-ads-car.php?id='.$orderID['id'].'&type='.$car_orders['order_type'].'" class="btn btn-danger btn-sm">Close Order</a>'?>
						      </div>
						    </div>
						  </div>
						</div>

						<!-- Modal -->
						<div class="modal fade" id="deleteOrder" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
						  <div class="modal-dialog" role="document">
						    <div class="modal-content">
						      <div class="modal-header">
						        <h6 class="modal-title" id="exampleModalLabel">Usuń trwale zlecenie</h6>
						        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						          <span aria-hidden="true">&times;</span>
						        </button>
						      </div>
						      <div class="modal-body">
						        <p>Ta opcja całkowicie <b>kasuje</b> zlecenie z systemu nie pozostawiając po nim nigdzie śladów. Rozważ wcześniej <b>Zamknięcie</b> zlecenia.</p>
						      </div>
						      <div class="modal-footer">
						        <button type="submit" class="btn btn-secondary btn-sm" data-dismiss="modal">No</button>
						        <a href="admin-ads-car.php?delete=1&id=<?php echo $orderID['id'] ?>" class="btn btn-danger btn-sm">Usuń</a>
						      </div>
						    </div>
						  </div>
						</div>
						
						
						

					</div>
				</div>

			</div>
		</div><!-- /sticky-footer  -->
		


<?php include_once 'footer-admin.php'; ?>

	</div><!-- /stickyfooter (or first-bg) -->
  </body>
</html>
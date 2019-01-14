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
	Redirect::to('admin-ads-load.php');
}

$user_id = $user->data()->id;


$orderID = $db->getRow("SELECT * FROM orders WHERE id = $order_id");
$load_orders = $db->getRow("SELECT * FROM load_orders WHERE id = ".$orderID['car_or_load_id']."");


// zamkniecie zlecenia
if (Input::get('id') && Input::get('type')) {
	try {
		
		$updateRow = $db->updateRow("UPDATE load_orders SET order_status = ?, last_edit_date = ?  WHERE id = ?", ["0", date("Y-m-d H:i:s"), $orderID['car_or_load_id']]);

		Session::flash('order_closed', 'Zlecenie zostało zamknięte.');
		Redirect::to('admin-ads-load.php?id='.$orderID['id'].'');
		
	} catch (Exception $e) {
		die($e->getMessage());
	}
	
}

// usunięcie zlecenia
if (Input::get('delete') && Input::get('delete') == '1') {
	
	try {
		// usuniecie z tabeli orders
		$delete_from_orders = $db->deleteRow("DELETE FROM orders WHERE id = ?", [Input::get('id')]);
		// usuniecie z tabelu load_orders
		$delete_from_load_orders = $db->deleteRow("DELETE FROM load_orders WHERE id = ?", [$orderID['car_or_load_id']]);

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

						<p class="font-md-400">Szczegóły zlecenia nr. <b><?php echo $orderID['id']; ?></b> - zlecenie na przewóz: <?php echo $load_orders['load_is']; ?></p>

						<div class="row">

							<div class="col-md-6">
								
								<table class="table table-bordered">
								  
								  <tbody>
								    
								    <tr>
								      <td>Załadunek</td>
								      <td>
								      	<?php 
								      	echo 
								      		$load_orders['in_city'].' / '.
								      		$orders->country($load_orders['in_country_id']). '<br><br>'.
								      		str_replace('/', '.', $load_orders['in_date']). '<br> w godz. ' .
								      		$load_orders['in_hours']. '<br><br>' .
								      		$load_orders['in_company'].'<br>'.
								      		$load_orders['in_address'].'<br>'.
								      		$load_orders['in_post'].' '.$load_orders['in_city'].'<br><br>'.
								      		'tel. '.$load_orders['in_phone'].'<br>'.
								      		'os. do kontaktu: '.$load_orders['in_contact_person'].'<br>'.
								      		'e-mail: '.$load_orders['in_email'].'<br>'
								      	;?>
								      	
								      </td>
								    </tr>
								    <tr>
								      <td>Rozładunek</td>
								      <td>
								      	<?php 
								      	echo 
								      		$load_orders['out_city'].' / '.
								      		$orders->country($load_orders['out_country_id']). '<br><br>'.
								      		str_replace('/', '.', $load_orders['out_date']). '<br> w godz. ' .
								      		$load_orders['out_hours']. '<br><br>' .
								      		$load_orders['out_company'].'<br>'.
								      		$load_orders['out_address'].'<br>'.
								      		$load_orders['out_post'].' '.$load_orders['out_city'].'<br><br>'.
								      		'tel. '.$load_orders['out_phone'].'<br>'.
								      		'os. do kontaktu: '.$load_orders['out_contact_person'].'<br>'.
								      		'e-mail: '.$load_orders['out_email'].'<br>'
								      	;?>
								      </td>
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
								      <td><?php echo date('d.m.Y H:i:s', strtotime($load_orders['add_date'])); ?></td>
								    </tr>
								    <tr>
								      <td>Koniec</td>
								      <td><?php echo str_replace('/', '.', $load_orders['display_to_date']).' '.$load_orders['display_to_hours']; ?></td>
								    </tr>
								    <tr>
								      <td>Status</td>
								      <td><?php echo $orders->orderStatus('load_orders', $load_orders['id']); ?></td>
								    </tr>
								    <tr>
								      <td>Typ zabudowy</td>
								      <td><?php echo $orders->carType($load_orders['car_type_id']); ?></td>
								    </tr>
								    <tr>
								      <td>Ładunek</td>
								      <td><?php echo $load_orders['load_is']; ?></td>
								    </tr>
								    <tr>
								      <td>Cena</td>
								      <td><?php echo $load_orders['price'] . ' ' .$load_orders['currency_id'] ; ?></td>
								    </tr>
								    <tr>
								      <td>Wymagania dodatkowe</td>
								      <td><?php echo $orders->additional('load_orders', $load_orders['id']); ?></td>
								    </tr>
								    <tr>
								      <td>Notatka</td>
								      <td><?php echo $load_orders['note']; ?></td>
								    </tr>
								  </tbody>
								</table>
								
							</div>
							
						</div>

						
						<?php 
							if ($load_orders['order_status'] === '0')  {
								echo '<p class="font-md-400">Zamówienie jest zamknięte.</p>
								<a href="admin-ads-load.php?delete=1&id='.$orderID['id'].'" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteOrder">Usuń</a>';
							} else {
								echo '<a href="admin-ads-load.php?id='.$orderID['id'].'&type='.$load_orders['order_type'].'" class="btn btn-green btn-sm" data-toggle="modal" data-target="#closeOrder">Zamknij zlecenie</a>
								<a href="admin-ads-load.php?delete=1&id='.$orderID['id'].'" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteOrder">Usuń</a>';
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
						        <?php  echo '<a href="admin-ads-load.php?id='.$orderID['id'].'&type='.$load_orders['order_type'].'" class="btn btn-danger btn-sm">Close Order</a>'?>
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
						        <a href="admin-ads-load.php?delete=1&id=<?php echo $orderID['id'] ?>" class="btn btn-danger btn-sm">Usuń</a>
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
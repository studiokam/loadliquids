<?php 
require_once 'core/init.php';
$title = 'Loadliquids.com';

// translate
$tr = Lang::set('loadorder-details');

$user = new User();
$orders = new Orders();


if (!$user->isLoggedIn()) {
	Redirect::to('index.php');
}

// sprawdzenie czy w linku get podane jest id
if (Input::get('id')) {
	$order_id = Input::get('id');
} else {
	Session::flash('user-order-error', Lang::get($tr,'no.id.error'));
	Redirect::to('userorders.php');
}

$user_id = $user->data()->id;

$db = new Database();
$orderID = $db->getRow("SELECT * FROM orders WHERE id = $order_id");
$load_orders = $db->getRow("SELECT * FROM load_orders WHERE id = ".$orderID['car_or_load_id']."");

// sprawdzenie czy user jest właścicielem zamówienia jakie chce przegladać
if ($orderID['user_id'] !== $user_id) {
	echo Lang::get($tr,'not.owner.error');
	die();
}


// zamkniecie zlecenia
if (Input::get('id') && Input::get('type')) {
	try {
		
		$updateRow = $db->updateRow("UPDATE load_orders SET order_status = ?, last_edit_date = ?  WHERE id = ?", ["0", date("Y-m-d H:i:s"), $orderID['car_or_load_id']]);

		Session::flash('order-closed', Lang::get($tr,'message.order.closed'));
		Redirect::to('userorders.php');
		
	} catch (Exception $e) {
		die($e->getMessage());
	}
	
}

include_once 'top.php';
echo '<div class=" stickyfooter">';
include_once 'menu.php';
?>



<div class="stickyfooter-content my-font">
	<div class="container">

		<div class="row invoice">
			<div class="col">

				<p class="font-md-400"><?php echo Lang::get($tr,'h1.part1').'<b>'. $orderID['id'].'</b> - '. Lang::get($tr,'h1.part2') . $load_orders['load_is']; ?></p>

				<div class="row">

					<div class="col-md-6">

						<table class="table table-bordered">

							<tbody>

								<tr>
									<td><?php echo Lang::get($tr,'loading').'</td>
									<td>'.
									$load_orders['in_city'].' / '.
									$orders->country($load_orders['in_country_id']). '<br><br>'.
									str_replace('/', '.', $load_orders['in_date']). '<br> '.Lang::get($tr,'in.hours') .
									$load_orders['in_hours']. '<br><br>' .
									$load_orders['in_company'].'<br>'.
									$load_orders['in_address'].'<br>'.
									$load_orders['in_post'].' '.$load_orders['in_city'].'<br><br>'.
									Lang::get($tr,'phone').$load_orders['in_phone'].'<br>'.
									Lang::get($tr,'person').$load_orders['in_contact_person'].'<br>'.
									Lang::get($tr,'email').$load_orders['in_email'].'<br>
									

								</td>
							</tr>
							<tr>
								<td>'. Lang::get($tr,'unloading').'</td>
								<td>'.
									$load_orders['out_city'].' / '.
									$orders->country($load_orders['out_country_id']). '<br><br>'.
									str_replace('/', '.', $load_orders['out_date']). '<br> '.Lang::get($tr,'in.hours') .
									$load_orders['out_hours']. '<br><br>' .
									$load_orders['out_company'].'<br>'.
									$load_orders['out_address'].'<br>'.
									$load_orders['out_post'].' '.$load_orders['out_city'].'<br><br>'.
									Lang::get($tr,'phone').$load_orders['out_phone'].'<br>'.
									Lang::get($tr,'person').$load_orders['out_contact_person'].'<br>'.
									Lang::get($tr,'email').$load_orders['out_email'].'<br>'
									;?>
								</td>
							</tr>
						</tbody>
					</table>

				</div>

				<div class="col-md-6">

					<table class="table table-bordered">

						<tbody>
							<?php echo'
							<tr>
							<td>'.Lang::get($tr,'number').'</td>
							<td> '.$orderID['id'].'</td>
							</tr>
							<tr>
							<td>'.Lang::get($tr,'add').'</td>
							<td>'.date('d.m.Y H:i:s', strtotime($load_orders['add_date'])).'</td>
							</tr>
							<tr>
							<td>'.Lang::get($tr,'end').'</td>
							<td>'.str_replace('/', '.', $load_orders['display_to_date']).' '.$load_orders['display_to_hours'].'</td>
							</tr>
							<tr>
							<td>'.Lang::get($tr,'status').'</td>
							<td>'.$orders->orderStatus('load_orders', $load_orders['id']).'</td>
							</tr>
							<tr>
							<td>'.Lang::get($tr,'car.type').'</td>
							<td>'.$orders->carType($load_orders['car_type_id']).'</td>
							</tr>
							<tr>
							<td>'.Lang::get($tr,'load').'</td>
							<td>'.$load_orders['load_is'].'</td>
							</tr>
							<tr>
							<td>'.Lang::get($tr,'price').'</td>
							<td>'.$load_orders['price'] . ' ' .$load_orders['currency_id'] .'</td>
							</tr>
							<tr>
							<td>'.Lang::get($tr,'additional').'</td>
							<td>'.$orders->additional('load_orders', $load_orders['id']).'</td>
							</tr>
							<tr>
							<td>'.Lang::get($tr,'note').'</td>
							<td>'.$load_orders['note'].'</td>
						</tr>'?>
					</tbody>
				</table>

			</div>

		</div>

		<?php 
		if ($load_orders['order_status'] === '0')  {
			echo '<p class="font-md-400">'.Lang::get($tr,'order.closed').'</p>';
		} else {
			echo '<a href="loadorder-details.php?id='.$orderID['id'].'&type='.$load_orders['order_type'].'" class="btn btn-danger" data-toggle="modal" data-target="#closeOrder">'.Lang::get($tr,'close').'</a>
			<a href="edit_order.php?id='.$orderID['id'].'" class="btn btn-green">'.Lang::get($tr,'edit').'</a>';
		}
		?>


		<!-- Modal -->
		<div class="modal fade" id="closeOrder" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLongTitle"><?php echo Lang::get($tr,'closing.order'); ?></h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<?php echo Lang::get($tr,'closing.order.message'); ?>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-secondary" data-dismiss="modal"><?php echo Lang::get($tr,'closing.order.no'); ?></button>
						<?php  echo '<a href="loadorder-details.php?id='.$orderID['id'].'&type='.$load_orders['order_type'].'" class="btn btn-danger">'.Lang::get($tr,'closing.order.confirm').'</a>'?>
					</div>
				</div>
			</div>
		</div>




	</div>
</div>

</div>
</div><!-- /sticky-footer  -->



<?php include_once 'footer.php'; ?>

</div><!-- /stickyfooter (or first-bg) -->
</body>
</html>
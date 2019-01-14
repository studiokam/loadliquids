<?php 
require_once 'core/init.php';
$title = 'Loadliquids.com';

// translate
$tr = Lang::set('userorders');

$user = new User();
$orders = new Orders();


if (!$user->isLoggedIn()) {
	Redirect::to('index.php');
}

$user_id = $user->data()->id;

$db = new Database();
$load_orders = $db->getRows("SELECT * FROM load_orders WHERE user_id = $user_id ORDER BY id desc");
$car_orders = $db->getRows("SELECT * FROM car_orders WHERE user_id = $user_id ORDER BY id desc");

include_once 'top.php';
echo '<div class=" stickyfooter">';
include_once 'menu.php';
;?>


	
		<div class="stickyfooter-content my-font">
			<div class="container">
			
				<div class="row account-box-toogle">
					<div class="col">

						<?php  
							if (Session::exists('user-order-error')) {
								echo '<div class="warning-message mt-20">' . Session::flash('user-order-error') . '</div>';
							}
							if (Session::exists('order-closed')) {
								echo '<div class="success-message mt-20">' . Session::flash('order-closed') . '</div>';
							}
							
						?>

						<p class="mt-20 font-md-400"><?php echo Lang::get($tr,'h1.your.ads'); ?></p>
						
						<ul class="nav nav-tabs" id="myTab" role="tablist">
						  <li class="nav-item">
						    <a class="nav-link active" id="load-tab" data-toggle="tab" href="#load" role="tab"><?php echo Lang::get($tr,'loads'); ?></a>
						  </li>
						  <li class="nav-item">
						    <a class="nav-link" id="cars-tab" data-toggle="tab" href="#cars" role="tab"><?php echo Lang::get($tr,'cars'); ?></a>
						  </li>
						</ul>

						<div class="tab-content" id="myTabContent">

							<div class="tab-pane fade show active" id="load" role="tabpanel">
			
								<div class="row orders">
									<div class="col">

																
										<table class="table table-bordered">
											<thead>
												<tr>
													<th class="d-none d-lg-table-cell" scope="col"><?php echo Lang::get($tr,'number'); ?></th>
													<th scope="col"><?php echo Lang::get($tr,'added'); ?></th>
													<th class="d-none d-lg-table-cell" scope="col"><?php echo Lang::get($tr,'end'); ?></th>
													<th class="d-none d-lg-table-cell" scope="col"><?php echo Lang::get($tr,'status'); ?></th>
													<th scope="col"><?php echo Lang::get($tr,'place.start'); ?></th>
													<th class="d-none d-sm-table-cell" scope="col"><?php echo Lang::get($tr,'place.end'); ?></th>
													<th class="d-none d-md-table-cell" scope="col"><?php echo Lang::get($tr,'load'); ?></th>
													<th class="d-none d-lg-table-cell" scope="col"><?php echo Lang::get($tr,'price'); ?></th>
													<th scope="col"></th>
												</tr>
											</thead>
											<tbody>
											<?php   
												foreach ($load_orders as $row) { echo '
												<tr>
													<th class="d-none d-lg-table-cell">'.$orders->orderId($row['id'], 'load').'</td>
													<th class="d-none d-lg-table-cell">'.date('d.m.Y', strtotime($row['add_date'])).'</td>
													<td>'.str_replace('/', '.', $row['display_to_date']).'</td>
													<td class="d-none d-lg-table-cell">'.$orders->orderStatus('load_orders', $row['id']).'</td>
													<td>'.$row['in_city'].' / '.$orders->country($row['in_country_id']).'</td>
													<td class="d-none d-sm-table-cell">'.$row['out_city'].' / '.$orders->country($row['out_country_id']).'</td>
													<td class="d-none d-md-table-cell">'.$row['load_is'].'</td>
													<td class="d-none d-lg-table-cell">'.$row['price'].' '.$row['currency_id'].'</td>
													<td class="text-center"><a href="loadorder-details.php?id='.$orders->orderId($row['id'], 'load').'">'.Lang::get($tr,'more').'</td>
												</tr>';
												}
											?>
											</tbody>
										</table>

									</div>
								</div> <!-- /row load orders -->
							
						    </div><!-- /tab-panel load -->

						    <div class="tab-pane fade" id="cars" role="tabpanel">
			
								<div class="row orders">
									<div class="col">
																
										<table class="table table-bordered">
											<thead>
												<tr>
													<th class="d-none d-lg-table-cell" scope="col"><?php echo Lang::get($tr,'number'); ?></th>
													<th scope="col"><?php echo Lang::get($tr,'added'); ?></th>
													<th class="d-none d-lg-table-cell" scope="col"><?php echo Lang::get($tr,'end'); ?></th>
													<th class="d-none d-lg-table-cell" scope="col"><?php echo Lang::get($tr,'status'); ?></th>
													<th scope="col"><?php echo Lang::get($tr,'place.start'); ?></th>
													<th class="d-none d-sm-table-cell" scope="col"><?php echo Lang::get($tr,'place.end'); ?></th>
													<th class="d-none d-md-table-cell" scope="col"><?php echo Lang::get($tr,'car.details'); ?></th>
													<th class="d-none d-lg-table-cell" scope="col"><?php echo Lang::get($tr,'price'); ?></th>
													<th scope="col"></th>
												</tr>
											</thead>
											<tbody>
											<?php   
												foreach ($car_orders as $row) { echo '
												<tr>
													<th class="d-none d-lg-table-cell">'.$orders->orderId($row['id'], 'car').'</td>
													<th class="d-none d-lg-table-cell">'.date('d.m.Y', strtotime($row['add_date'])).'</td>
													<td>'.str_replace('/', '.', $row['display_to_date']).'</td>
													<td class="d-none d-lg-table-cell">'.$orders->orderStatus('car_orders', $row['id']).'</td>
													<td>'.$row['in_city'].' / '.$orders->country($row['in_country_id']).'</td>
													<td class="d-none d-sm-table-cell">'.$row['out_city'].' / '.$orders->country($row['out_country_id']).'</td>
													<td class="d-none d-md-table-cell">'.$row['car_details'].'</td>
													<td class="d-none d-lg-table-cell">'.$row['price'].' '.$row['currency_id'].'</td>
													<td class="text-center"><a href="car-order-details.php?id='.$orders->orderId($row['id'], 'car').'">'.Lang::get($tr,'more').'</td>
												</tr>';
												}
											?>
											</tbody>
										</table>

									</div>
								</div> <!-- /row load orders -->
							
						    </div><!-- /tab-panel load -->
						</div><!-- /tab-content -->

					</div>
				</div> <!-- /row account-box-toogle -->

			</div>
		</div><!-- /sticky-footer  -->
		


<?php include_once 'footer.php'; ?>

	</div><!-- /stickyfooter (or first-bg) -->
  </body>
</html>
<?php 
require_once 'core/init.php';
$title = 'loadliquids.com';

// translate
$tr = Lang::set('index');

if (Session::exists('home')) {
	echo Session::flash('home');
}

$db = new Database();
$orders = new Orders();
$orderby = new OrderBy();
$countries = $db->getRows("SELECT * FROM countries");

// sprawdzenie potwierdzenia adresu email
if (isset($_GET['ha'])) {
	$reg_id = $_GET['id'];
	$reg_hash = $_GET['ha'];

	$hash_check = $db->getRow("SELECT * FROM users WHERE id = $reg_id");

	if($db->countRows() > 0) {
		if ($hash_check['email_random'] === $reg_hash) {
			$updateRow = $db->updateRow("UPDATE users SET email_random = ?, email_confirm = ? WHERE id = ?", ["0", date("Y-m-d H:i:s"), $reg_id]);
			Session::flash('email_confirm', Lang::get($tr,'flash.message.email.confirm'));
			Redirect::to('index.php');
		}
	}
}

// wyszukiwarka ładunków
if (isset($_GET['in_country_id'])) {

	if (Input::get('in_country_id')) {
		$sql[] = 'in_country_id = '.Input::get('in_country_id');
	}
	if (Input::get('out_country_id')) {
		$sql[] = 'out_country_id = '.Input::get('out_country_id');
	}

	if (Input::get('in_post')) {
		$sql[] = " `in_post` = '".escape(Input::get('in_post'))."'";
	}
	if (Input::get('out_post')) {
		$sql[] = " `out_post` = '".escape(Input::get('out_post'))."'";
	}

	if (Input::get('in_date')) {
		$sql[] = " `add_date` > '".date('Y-m-d', strtotime(Input::get('in_date')))."'";
	}
	if (Input::get('out_date')) {
		$sql[] = " `add_date` < '".date('Y-m-d', strtotime(Input::get('out_date')))."'";
	}

	$order_type = 'load';
	$query = "SELECT * FROM load_orders WHERE display_to_date > NOW()";

	if (!empty($sql)) {
		$query .= ' AND ' . implode(' AND ', $sql);
	}

	$search = $db->getRows("$query");
	$count_search = $db->countRows();

} elseif (isset($_GET['car_in_country_id'])) {

	if (Input::get('car_in_country_id')) {
		$sql[] = 'in_country_id = '.Input::get('car_in_country_id');
	}
	if (Input::get('car_out_country_id')) {
		$sql[] = 'out_country_id = '.Input::get('car_out_country_id');
	}

	if (Input::get('car_in_post')) {
		$sql[] = " `in_post` = '".escape(Input::get('car_in_post'))."'";
	}
	if (Input::get('car_out_post')) {
		$sql[] = " `out_post` = '".escape(Input::get('car_out_post'))."'";
	}

	if (Input::get('car_in_date')) {
		$sql[] = " `add_date` > '".date('Y-m-d', strtotime(Input::get('car_in_date')))."'";
	}
	if (Input::get('car_out_date')) {
		$sql[] = " `add_date` < '".date('Y-m-d', strtotime(Input::get('car_out_date')))."'";
	}

	$order_type = 'car';
	$query = "SELECT * FROM car_orders WHERE display_to_date > NOW()";

	if (!empty($sql)) {
		$query .= ' AND ' . implode(' AND ', $sql);
	}

	$search = $db->getRows("$query");
	$count_search = $db->countRows();

} else {
	// jeśli nie ma wyszukiwania
	$order_type = 'load';
	$query = "SELECT * FROM car_orders WHERE display_to_date > NOW()";
	$search = $db->getRows("SELECT * FROM load_orders WHERE display_to_date > NOW()");
	$count_search = $db->countRows();
}

// sortowanie na stronie
if (!Input::get('sort')) {
	$sort = $orderby->sort('dateup');
	$sort_args = 'dateup';
} else {
	$sort = $orderby->sort(Input::get('sort'));
	$sort_args = 'sort='.Input::get('sort');
}

//DO NOT limit this query with LIMIT keyword, or...things will break!
$pagin_query = $query;

//these variables are passed via URL
$limit = ( isset( $_GET['limit'] ) ) ? $_GET['limit'] : 10; //rows per page
$page = ( isset( $_GET['page'] ) ) ? $_GET['page'] : 1; //starting page
$links = 3;


$paginator = new Paginator( $pagin_query ); //__constructor is called
$results = $paginator->getData( $limit, $page, $search_args, $sort_args ); // podanie wartości do generwanych linków ($search_args i $sort_args)


include_once 'top.php';
echo '<div class="first-bg stickyfooter">';

include_once 'menu.php';
include_once 'baner.php';

?>

<div class="stickyfooter-content my-font">	
	<div class="container">
		<?php
		if (Session::exists('email_confirm')) {
			echo '<div class="success-message mb-30">' . Session::flash('email_confirm') . '</div>';
		}
		if (Session::exists('link_error')) {
			echo '<div class="warning-message mb-30">' . Session::flash('link_error') . '</div>';
		}
		?>
		<div class="home-box-search">
			<!-- content -->
			<div class="row home-box-toogle">
				<div class="col-lg-12">
					
					<ul class="nav nav-tabs" id="myTab" role="tablist">
						<li class="nav-item">
							<a class="nav-link <?php echo (!Input::get('car_in_country_id') ? 'active' : '') ?>" id="home-tab" data-toggle="tab" href="#home" role="tab" ><?php echo Lang::get($tr,'h1.tab.free.load');?></a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php echo (Input::get('car_in_country_id') ? 'active' : '') ?>" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" ><?php echo Lang::get($tr,'h1.tab.free.cars');?></a>
						</li>
					</ul>
					<div class="tab-content" id="myTabContent">
						<div class="tab-pane fade <?php echo (!Input::get('car_in_country_id') ? 'show active' : '') ?>" id="home" role="tabpanel" aria-labelledby="home-tab">

							<p class="mt-50 font-md"><?php echo Lang::get($tr,'h1.search.free.load');?></p>
							<hr>

							<form action="" method="GET">
								<div class="row pt-30">


									<div class="col-md-3 col-sm-6">



										<div class="form-group">
											<label for="in_country_id"><?php echo Lang::get($tr,'label.loading.place');?></label>
											<select class="form-control form-control-sm" id="in_country_id" name="in_country_id">
												<?php 
												echo '<option value="" selected >'.Lang::get($tr,'state').'</option>';
												foreach ($countries as  $row) {
													if (Input::get('in_country_id') === $row['id']) {
														echo '<option selected value="'.$row['id'].'">'.$row['country'].'</option>';
													} else {
														echo '<option value="'.$row['id'].'">'.$row['country'].'</option>';
													}
												}

												?>
											</select>
										</div>
										<div class="form-group">
											<input type="text" class="form-control form-control-sm" id="in_post" name="in_post"  placeholder="<?php echo Lang::get($tr,'placeholder.enter.post').'" value="'.escape(Input::get('in_post')); ?>">
										</div>

									</div>
									<div class="col-md-3 col-sm-6">


										<div class="form-group">
											<label for="out_country_id"><?php echo Lang::get($tr,'label.unloading.place');?></label>
											<select class="form-control form-control-sm" id="out_country_id" name="out_country_id">
												<?php 
												echo '<option value="" selected >'.Lang::get($tr,'state').'</option>';

												foreach ($countries as  $row) {
													if (Input::get('out_country_id') === $row['id']) {
														echo '<option selected value="'.$row['id'].'">'.$row['country'].'</option>';
													} else {
														echo '<option value="'.$row['id'].'">'.$row['country'].'</option>';
													}
												}
												?>
											</select>
										</div>
										<div class="form-group">
											<input type="text" class="form-control form-control-sm" id="out_post" name="out_post"  placeholder="<?php echo Lang::get($tr,'placeholder.enter.post').'" value="'.escape(Input::get('out_post')); ?>">
										</div>

									</div>


									<div class="col-md-3 col-sm-6 szukaj_publikacja">


										<div class="form-group">
											<label for="exampleFormControlSelect1"><?php echo Lang::get($tr,'label.date');?></label>
											<input class="form-control form-control-sm" id="in_date" name="in_date" placeholder="<?php echo Lang::get($tr,'placeholder.date.from').'(DD/MM/YYYY)" type="text" value="'.escape(Input::get('in_date')); ?>">
										</div>
										<div class="form-group">
											<input class="form-control form-control-sm" id="out_date" name="out_date" placeholder="<?php echo Lang::get($tr,'placeholder.date.to').'(DD/MM/YYYY)" type="text" value="'.escape(Input::get('out_date')); ?>">
										</div>


									</div>

									<div class="col-md-3 col-sm-6 btn-search-m">
										<input type="submit" class="btn btn-green btn-sm" value="<?php echo Lang::get($tr,'btn.search');?>">


									</div>


								</div> <!-- row wolne ladunki -->
							</form>	


						</div>
						<div class="tab-pane fade <?php echo (Input::get('car_in_country_id') ? 'show active' : '') ?>" id="profile" role="tabpanel" aria-labelledby="profile-tab">
							
							
							<p class="mt-50 font-md"><?php echo Lang::get($tr,'h1.search.free.cars');?></p>
							<hr>

							<form action="" method="GET">
								<div class="row pt-30">


									<div class="col-md-3 col-sm-6">



										<div class="form-group">
											<label for="car_in_country_id"><?php echo Lang::get($tr,'label.loading.place');?></label>
											<select class="form-control form-control-sm" id="car_in_country_id" name="car_in_country_id">
												<?php 
												echo '<option value="" selected >'.Lang::get($tr,'state').'</option>';
												foreach ($countries as  $row) {
													if (Input::get('car_in_country_id') === $row['id']) {
														echo '<option selected value="'.$row['id'].'">'.$row['country'].'</option>';
													} else {
														echo '<option value="'.$row['id'].'">'.$row['country'].'</option>';
													}
												}

												?>
											</select>
										</div>
										<div class="form-group">
											<input type="text" class="form-control form-control-sm" id="car_in_post" name="car_in_post"  placeholder="<?php echo Lang::get($tr,'placeholder.enter.post').'" value="'.escape(Input::get('car_in_post')); ?>">
										</div>

									</div>
									<div class="col-md-3 col-sm-6">


										<div class="form-group">
											<label for="out_country_id"><?php echo Lang::get($tr,'label.unloading.place');?></label>
											<select class="form-control form-control-sm" id="car_out_country_id" name="car_out_country_id">
												<?php 
												echo '<option value="" selected >'.Lang::get($tr,'state').'</option>';

												foreach ($countries as  $row) {
													if (Input::get('car_out_country_id') === $row['id']) {
														echo '<option selected value="'.$row['id'].'">'.$row['country'].'</option>';
													} else {
														echo '<option value="'.$row['id'].'">'.$row['country'].'</option>';
													}
												}
												?>
											</select>
										</div>
										<div class="form-group">
											<input type="text" class="form-control form-control-sm" id="car_out_post" name="car_out_post"  placeholder="<?php echo Lang::get($tr,'placeholder.enter.post').'" value="'.escape(Input::get('car_out_post')); ?>">
										</div>

									</div>


									<div class="col-md-3 col-sm-6 szukaj_publikacja">


										<div class="form-group">
											<label for="exampleFormControlSelect1"><?php echo Lang::get($tr,'label.date');?></label>
											<input class="form-control form-control-sm" id="car_in_date" name="car_in_date" placeholder="<?php echo Lang::get($tr,'placeholder.date.from').'(DD/MM/YYYY)" type="text" value="'.escape(Input::get('car_in_date')); ?>">
										</div>
										<div class="form-group">
											<input class="form-control form-control-sm" id="car_out_date" name="car_out_date" placeholder="<?php echo Lang::get($tr,'placeholder.date.to').'(DD/MM/YYYY)" type="text" value="'.escape(Input::get('car_out_date')); ?>">
										</div>


									</div>

									<div class="col-md-3 col-sm-6 btn-search-m">
										<input type="submit" class="btn btn-green btn-sm" value="<?php echo Lang::get($tr,'btn.search');?>">


									</div>


								</div> <!-- row wolne ladunki -->
							</form>	





						</div>
					</div>

				</div>
			</div> <!-- wyszukiwarka -->

		</div>


		<?php 
		if (Session::exists('order_details_error')) {
			echo '<div class="warning-message mt-30">' . Session::flash('order_details_error') . '</div>';
		}

		if (isset($_GET['in_country_id'])) {
			$found_type = Lang::get($tr,'found.type.loads');
		} elseif (isset($_GET['car_in_country_id'])) {
			$found_type = Lang::get($tr,'found.type.cars');
		} else {
			$found_type = Lang::get($tr,'found.type.loads');
		}
		?>

		<div class="record-wrapper">
			<div class="row">
				<div class="col-lg-12">
					<p class="mt-50 font-md"><?php echo Lang::get($tr,'found') .'<b>'. $found_type .'</b>'. Lang::get($tr,'found.secound.part') ?><span class="font-numbers">(<?php echo $count_search ?>)</span>:</p>
					<hr>
				</div>
			</div> <!-- /box_found -->

			<div class="row record-head">
				<div class="col-5 col-md-3 col-lg-3">
					<?php echo Lang::get($tr,'record.loading');?>
				</div>
				<div class="col-5 col-md-3 col-lg-3">
					<p><?php echo Lang::get($tr,'record.unloading');?></p>
				</div>
				<div class="d-none d-md-block col-md-2 col-lg-2">
					<p><?php echo Lang::get($tr,'record.added');?></p>
				</div>
				<div class="d-none d-md-block col-md-2 col-lg-2">
					<p><?php echo Lang::get($tr,'record.validity');?></p>
				</div>
				<div class="d-none d-lg-block col-lg-1">
					<p><?php echo Lang::get($tr,'record.tonnage');?></p>
				</div>
				<div class="col-2 col-sm-2 col-md-2 col-lg-1"></div>
			</div><!-- /record-head -->

			
			<?php
			foreach ($search as $row) {	

				echo '
				
				<div class="record_box">
				<a href="offer-details-'.$order_type.'.php?id='.$orders->orderId($row['id'], $order_type).'">
				<div class="row record record-bg-second">
				<div class="col-5 col-md-3 col-lg-3">
				<img class="flag" src=img/flags/'.$row['in_country_id'].'.png alt="'.$orders->shortcut($row['in_country_id']).'"> 
				<div>'.$orders->country($row['in_country_id']).'</div>
				<div class="record-city"><b>'.$row['in_city'].'</b></div>
				<div class="record-date-sm">'.date('d.m.Y', strtotime($row['in_date'])).'</div>
				<div class="record-date-sm">'.$row['in_post'].'</div>
				</div>
				<div class="col-5 col-md-3 col-lg-3">
				<img class="flag" src=img/flags/'.$row['out_country_id'].'.png alt="'.$orders->shortcut($row['in_country_id']).'">
				<div>'.$orders->country($row['out_country_id']).'</div>
				<div class="record-city"><b>'.$row['out_city'].'</b></div>
				<div class="record-date-sm">'.date('d.m.Y', strtotime($row['out_date'])).'</div>
				<div class="record-date-sm">'.$row['out_post'].'</div>
				</div>
				<div class="d-none d-md-block col-md-2 col-lg-2">
				<div class="record-date">'.date('d.m.Y', strtotime($row['add_date'])).'</div>
				<div class="record-date-sm">'.date('m:i', strtotime($row['add_date'])).'</div>
				</div>
				<div class="d-none d-md-block col-md-2 col-lg-2">
				<div class="record-date">'.date('d.m.Y', strtotime($row['display_to_date'])).'</div>
				<div class="record-date-sm">'.$row['display_to_hours'].'</div>
				</div>
				<div class="d-none d-lg-block col-lg-1">
				<div class="record-date">'.$row['tonnage'].'</div>
				</div>
				<div class="col-2 col-sm-2 col-md-2 col-lg-1">
				<i class="ion-android-done-all float-right mt-10"></i>
				</div>
				</div>
				</a>
				</div><!-- /record(first) -->

				';
			}
			?>


		</div><!-- /record-wrapper -->



		<form action="" method="get">
			<div class="row">
				<div class="col-md-12 col-xl-10">
					<?php echo $paginator->createLinks( $links, 'pagination justify-content-end' ); ?>
					<div class="admin-user-pagination-nr">
						Wyświetlaj na stronie: <a class=" dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><?php echo (isset($_GET['limit']))? $_GET['limit'] : $limit ?></a>
						<div class="dropdown-menu">
							<?php 
								$paginator->createLimitLinks('admin-ads-list.php?limit=', 'sort', 'search', $dane = array('5', '10', '50', '100')); 
							?>
						</div>
					</div>
				</div>
			</div>
		</form>


	</div>
</div><!-- /sticky-footer  -->
<!-- /content -->

<?php 
include_once 'footer.php'; 
?>

<!-- Extra JavaScript/CSS added manually in "Settings" tab -->
<!-- Include jQuery -->
<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>

<!-- Include Date Range Picker -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>

<script>
	$(document).ready(function(){
		var date_input=$('input[name="in_date"]'); //our date input has the name "date"
		var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
		date_input.datepicker({
			format: 'dd-mm-yyyy',
			container: container,
			orientation: "bottom",
			todayHighlight: true,
			autoclose: true,
		})
	})
</script>

<script>
	$(document).ready(function(){
		var date_input=$('input[name="out_date"]'); //our date input has the name "date"
		var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
		date_input.datepicker({
			format: 'dd-mm-yyyy',
			container: container,
			orientation: "bottom",
			todayHighlight: true,
			autoclose: true,
		})
	})
</script>

</div><!-- /stickyfooter (or first-bg) -->
</body>
</html>
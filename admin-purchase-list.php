<?php 
require_once 'core/init.php';
$title = 'Zaplecze | loadliquids.com';


$orderby = new OrderBy();
$orders = new Orders();
$comapny = new Company();


// sortowanie na stronie
if (!Input::get('sort')) {
	$sort = $orderby->sort('dateup');
	$sort_args = 'dateup';
} else {
	$sort = $orderby->sort(Input::get('sort'));
	$sort_args = 'sort='.Input::get('sort');
}

// wyszukiwarka
if (!Input::get('search')) {
	$search = '';
	$search_args = '';
} else {
	$search = $orderby->searchInOrders(Input::get('search'));
	$search_args = 'search='.Input::get('search');
}

// sprawdzenie czy w linku get podane jest id
if (Input::get('user_id')) {
	$user_id = Input::get('user_id');
	$sort = 'WHERE user_id = '.$user_id.' ORDER BY id DESC ';
} 


//DO NOT limit this query with LIMIT keyword, or...things will break!
$pagin_query = "SELECT * FROM purchase $search $sort";

//these variables are passed via URL
$limit = ( isset( $_GET['limit'] ) ) ? $_GET['limit'] : 10; //rows per page
$page = ( isset( $_GET['page'] ) ) ? $_GET['page'] : 1; //starting page
$links = 3;


$paginator = new Paginator( $pagin_query ); //__constructor is called
$results = $paginator->getData( $limit, $page, $search_args, $sort_args ); // podanie wartości do generwanych linków ($search_args i $sort_args)


if ($page > 1) {
	$i = $limit * $page - $limit + 1;
} else {
	$i = 1;
}


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
		if (Session::exists('purchase_id')) {
			echo '<div class="warning-message mb-20">' . Session::flash('purchase_id') . '</div>';
		}

		?>
		<div class="row">
			<div class="col-12">
				<p class="font-md-400">Zamówienia</p>
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
								<button type="submit" class="btn btn-green btn-sm">Szukaj po numarze zamówienia</button>
							</div>
							

							<div class="col-12 col-sm-12 col-md-4 mt-10">
								<div class="sort">
									Sortuj: <a class=" dropdown-toggle" data-toggle="dropdown" href="#" role="button"><?php echo $orderby->title() ?></a>
									<div class="dropdown-menu">
										<?php 
										$paginator->createSortLinks('limit', 'page', 'search', $dane = array(
											'admin-purchase-list.php?sort=dateup' => 'Od najnowszego',
											'admin-purchase-list.php?sort=date' => 'Od najstarszego'
										)); 
										?>
									</div>
								</div>
							</div>
						</div>
						
					</div>
				</form>

				<table class="table table-bordered mt-20">
					<thead>
						<tr>
							<th class="text-center" scope="col">#</th>
							<th class="d-none d-md-table-cell" scope="col">Numer</th>
							<th scope="col">User</th>
							<th class="d-none d-md-table-cell" scope="col">Data</th>
							<th class="d-none d-md-table-cell" scope="col">Dodał</th>
							<th class="d-none d-lg-table-cell" scope="col">Kwota</th>
							<th class="d-none d-lg-table-cell" scope="col">Na ile</th>
							<th class="text-center" scope="col">Pdf</th>
							<th class="text-center" scope="col"></th>
						</tr>
					</thead>
					<tbody>
						
						<?php 
						
						for ($p = 0; $p < count($results->data); $p++): ?> 

						<?php 
						//store in $row variable for easier reading
						$row = $results->data[$p]; 

						

							echo'
							<tr>
							<td class="text-center">'. $i++.'</td>
							<td class="d-none d-md-table-cell">'. $row['id'] .'</td>
							<td>'. $comapny->userName($row['user_id']) .'</td>
							<td class="d-none d-md-table-cell">'. date('d.m.Y H:i:s', strtotime($row['add_date'])) .'</td>
							<td class="d-none d-md-table-cell">'. $row['buy_path'] .'</td>
							<td class="d-none d-lg-table-cell">'. $row['price'] .' '. ($row['currency'] === '1' ? 'euro' : 'pln') .'</td>
							<td class="d-none d-lg-table-cell">'. $row['days'] .' dni</td>
							<td class="text-center">'. ($row['pdf'] !== '0' ? '<a href=""><img src="img/pdf.png" width="13px"  alt="Pobierz"></a>' : '-') .'</td>
							<td class="text-center">
							<a href="admin-purchase.php?id='. $row['id'] .'"><i class="ion-edit" alt="Edytuj"></i></a>
							</td>
							</tr>
							';
						?>
						<?php endfor; ?>
						
					</tbody>
				</table>
			</div>
		</div>

		<form action="" method="get">
			<div class="row">
				<div class="col-md-12 col-xl-10">
					<?php echo $paginator->createLinks( $links, 'pagination justify-content-end' ); ?>
					<div class="admin-user-pagination-nr">
						Wyświetlaj na stronie: <a class=" dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><?php echo (isset($_GET['limit']))? $_GET['limit'] : $limit ?></a>
						<div class="dropdown-menu">
							<?php 
								$paginator->createLimitLinks('admin-purchase-list.php?limit=', 'sort', 'search', $dane = array('5', '10', '50', '100')); 
							?>
						</div>
					</div>
				</div>
			</div>
		</form>
		
	</div>
</div><!-- /sticky-footer  -->
<!-- /content -->

<?php include_once 'footer-admin.php'; ?>

</div><!-- /stickyfooter (or first-bg) -->
</body>
</html>
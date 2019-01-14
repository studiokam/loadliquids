<?php 
require_once 'core/init.php';
$title = 'Zaplecze | loadliquids.com';


$orderby = new OrderBy();

// sortowanie na stronie
if (!Input::get('sort')) {
	$sort = $orderby->sort('rdup');
	$sort_args = 'rdup';
} else {
	$sort = $orderby->sort(Input::get('sort'));
	$sort_args = 'sort='.Input::get('sort');
}

// wyszukiwarka
if (!Input::get('search')) {
	$search = '';
	$search_args = '';
} else {
	$search = $orderby->search(Input::get('search'));
	$search_args = 'search='.Input::get('search');
}


$premium = new Premium();

//DO NOT limit this query with LIMIT keyword, or...things will break!
$pagin_query = "SELECT * FROM users $search $sort";


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
		if (Session::exists('admin-user')) {
			echo '<div class="warning-message mb-30">' . Session::flash('admin-user') . '</div>';
		}
		if (Session::exists('deleted')) {
			echo '<div class="success-message mb-20">' . Session::flash('deleted') . '</div>';
		} 
		?>
		
		<div class="row">
			<div class="col-12">
				<p class="font-md-400">Użytkownicy <?php echo $paginator->total(); ?> </p>
			</div>
		</div>
		<div class="row">
			<div class="col-12 col-xl-10 mb-20">

				<form action="" method="get">	
					<div class="search">
						<div class="row">
							<div class="col-8 col-sm-8 col-md-4 pr-10">
								<input class="form-control form-control-sm" type="text" id="search" name="search" value="<?php echo Input::get('search') ?>">
							</div>
							<div class="col-4 col-sm-4 col-md-4 pl-0">
								<button type="submit" class="btn btn-green btn-sm">Szukaj</button>
							</div>


							<div class="col-12 col-sm-12 col-md-4 mt-10">
								<div class="sort">
									Sortuj: <a class=" dropdown-toggle" data-toggle="dropdown" href="#" role="button"><?php echo $orderby->title() ?></a>
									<div class="dropdown-menu">
										<?php 
											$paginator->createSortLinks('limit', 'page', 'search', $dane = array(
												'admin-user-list.php?sort=az' => 'Alfabetycznie A-Z',
												'admin-user-list.php?sort=za' => 'Alfabetycznie Z-A',
												'admin-user-list.php?sort=rdup' => 'Rejestracja od najnowszej',
												'admin-user-list.php?sort=rd' => 'Rejestracja od najstarszej',
												'admin-user-list.php?sort=eaz' => 'Email A-Z',
												'admin-user-list.php?sort=eza' => 'Email Z-A'
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
							<th scope="col">#</th>
							<th scope="col">Firma</th>
							<th class="d-none d-md-table-cell" scope="col">Rejestracja</th>
							<th class="d-none d-md-table-cell" scope="col">Premium</th>
							<th class="d-none d-lg-table-cell" scope="col">Zweryfikowany</th>
							<th class="d-none d-md-table-cell" scope="col">E-mail</th>
							<th class="d-none d-lg-table-cell" scope="col">Telefon</th>
							<th scope="col">Opcje</th>
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
							<td>'. $i++.'</td>
							<td>'. $row['username'] .'</td>
							<td class="d-none d-md-table-cell">'. $row['register'] .'</td>
							<td class="d-none d-md-table-cell">'. (($premium->isPremium($row['id'])) ? 'Tak':'Nie') .'</td>
							<td class="d-none d-lg-table-cell">'. ($row['company_verif'] == '1' ? 'Tak' : 'Nie') .'</td>
							<td class="d-none d-md-table-cell">'. $row['email'] .'</td>
							<td class="d-none d-lg-table-cell">'. $row['phone'] .'</td>
							<td class="text-center">
							<a href="admin-user.php?id='. $row['id'] .'"><i class="ion-edit mr-10" alt="Edytuj"></i></a>
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
								$paginator->createLimitLinks('admin-user-list.php?limit=', 'sort', 'search', $dane = array('5', '10', '50', '100')); 
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
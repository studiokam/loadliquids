<?php 
require_once 'core/init.php';
$title = 'Invoice | loadliquids.com';

// translate
$tr = Lang::set('invoice');

$user = new User();

if (!$user->isLoggedIn()) {
	Redirect::to('index.php');
}

$user_id = $user->data()->id;

$db = new Database();
$all_purchase = $db->getRows("SELECT * FROM purchase WHERE user_id = $user_id ORDER BY id DESC");

include_once 'top.php';
echo '<div class=" stickyfooter">';
include_once 'menu.php';

?>


	
		<div class="stickyfooter-content my-font">
			<div class="container">
				
				<div class="row invoice">
					<div class="col">

						<p class="font-md-400"><?php echo Lang::get($tr,'h1.invoice'); ?></p>
						<p><?php echo Lang::get($tr,'subtitle.invoice'); ?></p>
						
						<table class="table table-bordered table-responsive-sm ">
							<thead>
							<tr>
							  <th scope="col">#</th>
							  <th scope="col"><?php echo Lang::get($tr,'number'); ?></th>
							  <th class="d-none d-sm-table-cell" scope="col"><?php echo Lang::get($tr,'title'); ?></th>
							  <th class="d-none d-sm-table-cell" scope="col"><?php echo Lang::get($tr,'price'); ?></th>
							  <th scope="col"><?php echo Lang::get($tr,'date'); ?></th>
							  <th scope="col"></th>
							</tr>
							</thead>
							<tbody>
				<?php
				$i = 1;
					foreach ($all_purchase as $row) {
						
						echo '
							<tr>
							  <th scope="row">'.$i++.'</th>
							  <td>'.($row['pdf'] !== '0' ? $row['pdf'] : Lang::get($tr,'no.doc.yet') ).'</td>
							  <td class="d-none d-sm-table-cell">'.Lang::get($tr,'no.doc.yet').' - '.$row['days'].' '.Lang::get($tr,'days').'</td>
							  <td class="d-none d-sm-table-cell">'.$row['price'].' '.($row['currency'] !== '1' ? 'PLN' : 'EURO').'</td>
							  <td>'.date('d.m.Y H:i:s', strtotime($row['add_date'])).'</td>
							  <td class="text-center">'.(($row['pdf'] !== '0') ? '<a href="invoices/'.$row['pdf'].'"><img src="img/pdf.png" width="20px"  alt="download"></a>' : '-').'</td>
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
		


<?php include_once 'footer.php'; ?>

	</div><!-- /stickyfooter (or first-bg) -->
  </body>
</html>
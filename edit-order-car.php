<?php 
require_once 'core/init.php';
$title = 'Loadliquids.com';
$meta = '<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
	<script src="js/chung-timepicker.js" type="text/javascript" charset="utf-8"></script>';

// translate
$tr = Lang::set('edit-order-car');

$user = new User();
$orders = new Orders();
$user_id = $user->data()->id;

$company = new Company();

if (!$user->isLoggedIn()) {
	Redirect::to('index.php');
}

// sprawdzenie czy w linku get podane jest id
if (Input::get('id')) {
	$order_id = Input::get('id');
} else {
	Session::flash('user-order-error', Lang::get($tr,'flash.message.error.id'));
	Redirect::to('userorders.php');
}


$db = new Database();
$orderID = $db->getRow("SELECT * FROM orders WHERE id = $order_id");
$edit_orders = $db->getRow("SELECT * FROM car_orders WHERE id = ".$orderID['car_or_load_id']."");

// sprawdzenie czy user jest właścicielem zamówienia jakie chce przegladać
if ($orderID['user_id'] !== $user_id) {
	echo "To nie jest Twoje zamówienie";
	die();
}

$countries = $db->getRows("SELECT * FROM countries");
$car_type = $db->getRows("SELECT * FROM car_types");


if (Input::exists()) {
	if (Token::check(Input::get('token'))) {

		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'in_country_id' => array(
				'required' => true,
				'max' => 3
			),
			'in_date' => array(
				'required' => true
			),
			'in_city' => array(
				'required' => true,
				'min' => 2,
				'max' => 50,
			),
			'in_post' => array(
				'required' => true,
				'min' => 2,
				'max' => 20,
			),

			'out_country_id' => array(
				'required' => true,
				'max' => 3
			),
			'out_date' => array(
				'required' => true
			),
			'out_city' => array(
				'required' => true,
				'min' => 2,
				'max' => 50,
			),
			'out_post' => array(
				'required' => true,
				'min' => 2,
				'max' => 20,
			),
			'display_to_date' => array(
				'required' => true
			),
			'display_to_hours' => array(
				'required' => true,
				'hour' => true
			),
			'price' => array(
				'required' => true
			),
			'currency_id' => array(
				'required' => true
			),
			'car_type_id' => array(
				'required' => true,
				'max' => 3
			),
			'tonnage' => array(
				'required' => true
			),
			
		));

		if ($validation->passed()) {


			try {

				$updateRow = $db->updateRow("UPDATE car_orders SET 


					in_country_id = ?, 
					in_date = ?, 
					in_city = ?, 
					in_post = ?,  

					out_country_id = ?, 
					out_date = ?, 
					out_city = ?, 
					out_post = ?,

					display_to_date = ?, 
					display_to_hours = ?, 
					price = ?, 
					currency_id = ?, 
					car_type_id = ?, 
					car_details =?,
					tonnage = ?, 
					compressor = ?, 
					pump = ?, 
					adr = ?, 
					gps = ?, 
					ready_to_ride = ?, 
					note = ?
					WHERE id = ?", 
					[
						
						Input::get('in_country_id'), 
						date('Y.m.d', strtotime(Input::get('in_date'))),
						Input::get('in_city'),
						Input::get('in_post'),

						Input::get('out_country_id'),
						date('Y.m.d', strtotime(Input::get('out_date'))),
						Input::get('out_city'),
						Input::get('out_post'),

						date('Y.m.d', strtotime(Input::get('display_to_date'))).' '.Input::get('display_to_hours').':00',
						Input::get('display_to_hours'),  
						Input::get('price'),
						Input::get('currency_id'),
						Input::get('car_type_id'),
						Input::get('car_details'),
						Input::get('tonnage'),
						Input::get('compressor'),   
						Input::get('pump'),   
						Input::get('adr'),   
						Input::get('gps'),   
						Input::get('ready_to_ride'),   
						Input::get('note'),
						$orderID['car_or_load_id']
					]);

				

				Session::flash('edit_ok', Lang::get($tr,'flash.message.order.edited'));
				Redirect::to('edit-order-car.php?id='.$order_id.'');


			} catch (Exception $e) {
				die($e->getMessage());
			}

		} else {
			
			$error = array();
			foreach ($validation->errors() as $bit) {
				$error[] = $bit;
			}
			
		}
	}
}




include_once 'top.php';
echo '<div class=" stickyfooter">';
include_once 'menu.php';

?>


<div class="stickyfooter-content my-font">
	<div class="container">

		<?php 
		if (Session::exists('add-in-car')) {
			echo '<div class="success-message mt-20">' . Session::flash('add-in-car') . '</div>';
		} 
		if (isset($error)) {
			echo '<div class="warning-message mt-30"> '.Lang::get($tr,'errors.in.form').' <br>';
			foreach ($error as $errors) {
				echo $errors, '<br>';
			}
			echo '</div>';
		}

		?>

		<div class="row account-box-toogle">
			<div class="col-lg-12">

				<p class="mt-20  font-md-400"><?php echo Lang::get($tr,'h1.title'). $order_id; ?></p>


				<form action="" method="post">		

					<div class="row account-form">
						<div class="col-12 col-sm-6 col-md-4">

							<p class="font-md-400"><?php echo Lang::get($tr,'loading'); ?></p>



							<div class="form-group">
								<select class="form-control form-control" id="in_country_id" name="in_country_id" required>
									<?php 

									if(Input::get('in_country_id')) { 
										echo '<option value="'.Input::get('in_country_id').'" selected >'.$orders->country(Input::get('in_country_id')).'</option>';
									} else {
										echo '<option value="'.$edit_orders['in_country_id'].'" selected >'.$orders->country($edit_orders['in_country_id']).'</option>';
									}
									foreach ($countries as  $row) {
										echo '<option value="'.$row['id'].'">'.$row['country'].'</option>';												
									}

									?>
								</select>
							</div>

							<?php echo'
							<div class="form-group">
								<input class="form-control" id="in_date" name="in_date" placeholder="* '.Lang::get($tr,'date') .' (DD.MM.YYYY)" type="text" value="'.((Input::get('in_date')) ? escape(Input::get('in_date')) : date('d.m.Y', strtotime($edit_orders['in_date']))).'" required/>
							</div>
							<div class="form-group">
								<input type="text" class="form-control" id="in_city" name="in_city"  placeholder="* '.Lang::get($tr,'city') .'" value="'.((Input::get('in_city')) ? escape(Input::get('in_city')) : $edit_orders['in_city']).'" required>
							</div>

							<div class="form-group">
								<input type="text" class="form-control" id="in_post" name="in_post"  placeholder="* '.Lang::get($tr,'postal.code') .'" value="'.((Input::get('in_post')) ? escape(Input::get('in_post')) : $edit_orders['in_post']).'" required>
							</div>
							'?>


							<p class="font-md-400 mt-30"><?php echo Lang::get($tr,'unloading'); ?></p>



							<div class="form-group">
								<select class="form-control form-control" id="out_country_id" name="out_country_id" required>
									<?php 

									if(Input::get('out_country_id')) { 
										echo '<option value="'.Input::get('out_country_id').'" selected >'.$orders->country(Input::get('out_country_id')).'</option>';
									} else {
										echo '<option value="'.$edit_orders['out_country_id'].'" selected >'.$orders->country($edit_orders['out_country_id']).'</option>';
									}
									foreach ($countries as  $row) {
										echo '<option value="'.$row['id'].'">'.$row['country'].'</option>';												
									}

									?>
								</select>
							</div>

							<?php echo'
							<div class="form-group">
								<input class="form-control" id="out_date" name="out_date" placeholder="* '.Lang::get($tr,'date') .' (DD.MM.YYYY)" type="text" value="'.((Input::get('out_date')) ? escape(Input::get('out_date')) : date('d.m.Y', strtotime($edit_orders['out_date']))).'" required/>
							</div>


							<div class="form-group">
								<input type="text" class="form-control" id="out_city" name="out_city"  placeholder="* '.Lang::get($tr,'city') .'" value="'.((Input::get('out_city')) ? escape(Input::get('out_city')) : $edit_orders['out_city']).'" required>
							</div>
							<div class="form-group">
								<input type="text" class="form-control" id="out_post" name="out_post"  placeholder="* '.Lang::get($tr,'postal.code') .'" value="'.((Input::get('out_post')) ? escape(Input::get('out_post')) : $edit_orders['out_post']).'" required>
							</div>
							'?>



						</div>



						<div class="col-12 col-sm-6 col-md-4">
							<p class="font-md-400 add-col-options-xs"><?php echo Lang::get($tr,'options'); ?></p>
							<div class="form-group">
								<input class="form-control" id="display_to_date" name="display_to_date" placeholder="* Wyświetlaj do: daty (format DD.MM.YYYY)" type="text" value="<?php if(Input::get('display_to_date')) {echo escape(Input::get('display_to_date'));} else {echo date('d.m.Y', strtotime($edit_orders['display_to_date']));}?>" required/>
							</div>

							<div class="form-group">
								<input type="text" class="form-control" id="display_to_hours" name="display_to_hours" placeholder="* Wyświetlaj do: godziny (format 00:00)" value="<?php if(Input::get('display_to_hours')) { echo escape(Input::get('display_to_hours')); } else {echo $edit_orders['display_to_hours'];} ?>" required>
								<script type="text/javascript">
									$('#display_to_hours').chungTimePicker({
										viewType: 1
									});
								</script>
							</div>

							<div class="form-group">
								<input type="text" class="form-control" id="price" name="price" placeholder="* <?php echo Lang::get($tr,'price'); ?>" value="<?php if(Input::get('price')) { echo escape(Input::get('price')); } else {echo $edit_orders['price'];} ?>" required>
							</div>

							<div class="form-group">
								<input type="text" class="form-control" id="currency_id" name="currency_id" placeholder="* <?php echo Lang::get($tr,'currency'); ?>" value="<?php if(Input::get('currency_id')) { echo escape(Input::get('currency_id')); } else {echo $edit_orders['currency_id'];} ?>" required>
							</div>

							<div class="form-group">
								<select class="form-control form-control" id="car_type_id" name="car_type_id" required>
									<?php 

									if(Input::get('car_type_id')) { 
										echo '<option value="'.Input::get('car_type_id').'" selected >'.$orders->carType(Input::get('car_type_id')).'</option>';
									} else {
										echo '<option value="'.$edit_orders['car_type_id'].'" selected >'.$orders->carType($edit_orders['car_type_id']).'</option>';
									}
									foreach ($car_type as  $row) {
										echo '<option value="'.$row['id'].'">'.$row['car_type'].'</option>';												
									}
									?>
								</select>
							</div>

							<div class="form-group">
								<input type="text" class="form-control" id="tonnage" name="tonnage" placeholder="* <?php echo Lang::get($tr,'tonnage'); ?>" value="<?php if(Input::get('tonnage')) { echo escape(Input::get('tonnage')); } else {echo $edit_orders['tonnage'];} ?>" required>
							</div>

							<div class="form-check">
								<label class="form-check-label">
									<input class="form-check-input" type="checkbox" name="compressor" id="compressor" value="1" <?php if(Input::get('compressor')) { echo "checked";} elseif ($edit_orders['compressor'] === '1') {echo "checked";} ?>>
									<?php echo Lang::get($tr,'compressor'); ?>
								</label>
							</div>

							<div class="form-check">
								<label class="form-check-label">
									<input class="form-check-input" type="checkbox" name="pump" id="pump" value="1" <?php if(Input::get('pump')) { echo "checked";} elseif ($edit_orders['pump'] === '1') {echo "checked";} ?>>
									<?php echo Lang::get($tr,'pump'); ?>
								</label>
							</div>

							<div class="form-check">
								<label class="form-check-label">
									<input class="form-check-input" type="checkbox" name="adr" id="adr" value="1" <?php if(Input::get('adr')) { echo "checked";} elseif ($edit_orders['adr'] === '1') {echo "checked";} ?>>
									<?php echo Lang::get($tr,'adr'); ?>
								</label>
							</div>
							<div class="form-check">
								<label class="form-check-label">
									<input class="form-check-input" type="checkbox" name="gps" id="gps" value="1" <?php if(Input::get('gps')) { echo "checked";} elseif ($edit_orders['gps'] === '1') {echo "checked";} ?>>
									<?php echo Lang::get($tr,'gps'); ?>
								</label>
							</div>
							<div class="form-check">
								<label class="form-check-label">
									<input class="form-check-input" type="checkbox" name="ready_to_ride" id="ready_to_ride" value="1" <?php if(Input::get('ready_to_ride')) { echo "checked";} elseif ($edit_orders['ready_to_ride'] === '1') {echo "checked";} ?>>
									<?php echo Lang::get($tr,'ready.to.ride'); ?>
								</label>
							</div>

						</div>

						<div class="col-12 col-sm-12 col-md-4">
							<p class="font-md-400 add-col-options-xs add-col-options"><?php echo Lang::get($tr,'car.details'); ?></p>

							<div class="form-group">
								<label for="car_details"><?php echo Lang::get($tr,'car.details.info'); ?></label>

								<input type="text" class="form-control" id="car_details" name="car_details" placeholder="<?php echo Lang::get($tr,'placeholder.car.details'); ?>" value="<?php if(Input::get('car_details')) { echo escape(Input::get('car_details')); } else {echo $edit_orders['car_details'];} ?>">
							</div>

							<p class="font-md-400 add-col-options-xs add-col-options"><?php echo Lang::get($tr,'note'); ?></p>

							<div class="form-group">
								<textarea class="form-control" id="note" name="note" rows="3" placeholder="Notatka"><?php if(Input::get('note')) { echo escape(Input::get('note')); } else {echo $edit_orders['note'];} ?></textarea>
							</div>


						</div>

					</div>

					<div class="row">
						<div class="col-12 mt-10">
							<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
							<button type="submit" class="btn btn-green mt-10" ><?php echo Lang::get($tr,'btn.update.order') ?></button>
						</div>
					</div>




				</form>

			</div>
		</div> <!-- wyszukiwarka -->


	</div>
</div><!-- /sticky-footer  -->




<?php include_once 'footer.php'; ?>

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
			format: 'dd.mm.yyyy',
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
			//format: 'dd/mm/yyyy',
			format: 'dd.mm.yyyy',
			container: container,
			orientation: "bottom",
			todayHighlight: true,
			autoclose: true,
		})
	})
</script>

<script>
	$(document).ready(function(){
		var date_input=$('input[name="display_to_date"]'); //our date input has the name "date"
		var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
		date_input.datepicker({
			format: 'dd.mm.yyyy',
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
<?php
require_once 'core/init.php';
$title = 'Loadliquids.com';
$meta = '<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
	<script src="js/chung-timepicker.js" type="text/javascript" charset="utf-8"></script>';
// translate
$tr = Lang::set('add-in');

$user = new User();
$user_id = $user->data()->id;

$company = new Company();

if (!$user->isLoggedIn()) {
	Redirect::to('index.php');
}

// sprawdzenie czy firma jest zweryfikowana przez admina, jeśli nie to nie moze dodać zlecenia
if (!$company->isVerif($user_id)) { 
	Session::flash('company_verif', Lang::get($tr,'flash.message.company.verify'));
	Redirect::to('account.php');

}


$db = new Database();
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
			'in_hours' => array(
				'required' => true,
			),
			'in_company' => array(
				'required' => true,
				'min' => 2,
				'max' => 50
			),
			'in_address' => array(
				'required' => true,
				'min' => 2,
				'max' => 50,
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
			'in_phone' => array(
				'min' => 6,
				'max' => 20,
				'phone09' => true
			),

			'out_country_id' => array(
				'required' => true,
				'max' => 3
			),
			'out_date' => array(
				'required' => true
			),
			'out_hours' => array(
				'required' => true,
			),
			'out_company' => array(
				'required' => true,
				'min' => 2,
				'max' => 50
			),
			'out_address' => array(
				'required' => true,
				'min' => 2,
				'max' => 50,
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
			'out_phone' => array(
				'min' => 6,
				'max' => 20,
				'phone09' => true
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
			'load_is' => array(
				'required' => true
			)

			
			
			
		));

		if ($validation->passed()) {


			try {
				

				$insertRowLoadOrders = $db->insertRowLoadOrders(
					[
						$user_id,   //  user id
						date("Y-m-d H:i:s"),   //  last_edit_date (now)
						"1",   //  active ( jeśli dana firma wymaga zatwierdzenia zlecenia przez admina - sprawdza czy firma ma flagę, jesli nie to domyślnie jest aktywne od razu)

						Input::get('in_country_id'),  
						date('Y.m.d', strtotime(Input::get('in_date'))),
						Input::get('in_hours'),
						Input::get('in_company'),
						Input::get('in_address'),
						Input::get('in_city'),
						Input::get('in_post'),
						Input::get('in_phone'),
						Input::get('in_email'),
						Input::get('in_contact_person'),

						Input::get('out_country_id'),
						date('Y.m.d', strtotime(Input::get('out_date'))),  
						Input::get('out_hours'),
						Input::get('out_company'), 
						Input::get('out_address'), 
						Input::get('out_city'),
						Input::get('out_post'),
						Input::get('out_phone'),
						Input::get('out_email'),
						Input::get('out_contact_person'),

						date('Y.m.d', strtotime(Input::get('display_to_date'))).' '.Input::get('display_to_hours').':00',
						Input::get('display_to_hours'),  
						Input::get('price'),
						Input::get('currency_id'),
						Input::get('car_type_id'),
						Input::get('tonnage'),
						Input::get('load_is'),
						Input::get('compressor'),   
						Input::get('pump'),   
						Input::get('adr'),   
						Input::get('gps'),   
						Input::get('ready_to_ride'),   
						Input::get('note')
					]
				);

				if ($insertRowLoadOrders) {

					$insertRowWithLastId = $db->insertRowWithLastId("INSERT INTO orders(
						car_or_load_id, 
						order_type, 
						user_id) 
						VALUE(?, ?, ?)", [
							$db->lastInsertedId(), 
							"load", 
							$user_id
						]);

					Session::flash('add-in', Lang::get($tr,'flash.message.order.added') . $insertRowWithLastId);
					Redirect::to('add-in.php');
					
				}

				
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
echo '<div class =" stickyfooter">';
include_once 'menu.php';


?>


<div class="stickyfooter-content my-font">
	<div class="container">

		<?php 
		if (Session::exists('add-in')) {
			echo '<div class="success-message mt-20">' . Session::flash('add-in') . '</div>';
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

				<p class="mt-20  font-md"><?php echo Lang::get($tr,'h1.title'); ?></p>

				<form action="" method="post">
					<div class="row account-form">
						<div class="col-12 col-sm-6 col-md-4">

							<p class="font-md-400"><?php echo Lang::get($tr,'loading'); ?></p>



							<div class="form-group">
								<select class="form-control form-control" id="in_country_id" name="in_country_id" required>
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

						<?php	echo'
							<div class="form-group">
								<input class="form-control" id="in_date" name="in_date" placeholder="* '.Lang::get($tr,'date') .'(DD/MM/YYYY)" type="text" value="'.escape(Input::get('in_date')).'" required/>
							</div>
							<div class="form-group">
								<input type="text" class="form-control" id="in_hours" name="in_hours" placeholder="* '.Lang::get($tr,'hours') .' 10:00-12:00" value="'.escape(Input::get('in_hours')).'" required>
							</div>
							<div class="form-group">
								<input type="text" class="form-control" id="in_company" name="in_company" placeholder="* '.Lang::get($tr,'company') .'" value="'.escape(Input::get('in_company')).'" required>
							</div>
							<div class="form-group">
								<input type="text" class="form-control" id="in_address" name="in_address"  placeholder="* '.Lang::get($tr,'address') .'" value="'.escape(Input::get('in_address')).'" required>
							</div>
							<div class="form-group">
								<input type="text" class="form-control" id="in_city" name="in_city"  placeholder="* '.Lang::get($tr,'city') .'" value="'.escape(Input::get('in_city')).'" required>
							</div>
							<div class="form-group">
								<input type="text" class="form-control" id="in_post" name="in_post"  placeholder="* '.Lang::get($tr,'postal.code') .'" value="'.escape(Input::get('in_post')).'" required>
							</div>
							<div class="form-group">
								<input type="text" class="form-control" id="in_phone" name="in_phone"  placeholder="'.Lang::get($tr,'phone') .'" value="'.escape(Input::get('in_phone')).'">
							</div>
							<div class="form-group">
								<input type="email" class="form-control" id="in_email" name="in_email"  placeholder="'.Lang::get($tr,'email').'" value="'.escape(Input::get('in_email')).'">
							</div>
							<div class="form-group">
								<input type="text" class="form-control" id="in_contact_person" name="in_contact_person"  placeholder="'.Lang::get($tr,'person') .'" value="'.escape(Input::get('in_contact_person')).'">
							</div>

						</div>'?>

						<div class="col-12 col-sm-6 col-md-4">

							<p class="font-md-400"><?php echo Lang::get($tr,'unloading'); ?></p>



							<?php 
									echo '<div class="form-group">
								<select class="form-control form-control" id="out_country_id" name="out_country_id" placeholder="'.Lang::get($tr,'state').'" required>
									<option value="" selected >'.Lang::get($tr,'state').'</option>';

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

						<?php	echo'
							<div class="form-group">
								<input class="form-control" id="out_date" name="out_date" placeholder="* '.Lang::get($tr,'date').' (DD/MM/YYYY)" type="text" value="'.escape(Input::get('out_date')).'" required/>
							</div>

							<div class="form-group">
								<input type="text" class="form-control" id="out_hours" name="out_hours"  placeholder="* '.Lang::get($tr,'hours').' ex. 10:00-12:00" value="'.escape(Input::get('out_hours')).'" required>
							</div>
							<div class="form-group">
								<input type="text" class="form-control" id="out_company" name="out_company"  placeholder="* '.Lang::get($tr,'company').'" value="'.escape(Input::get('out_company')).'" required>
							</div>
							<div class="form-group">
								<input type="text" class="form-control" id="out_address" name="out_address"  placeholder="* '.Lang::get($tr,'address').'" value="'.escape(Input::get('out_address')).'" required>
							</div>
							<div class="form-group">
								<input type="text" class="form-control" id="out_city" name="out_city"  placeholder="* '.Lang::get($tr,'city').'" value="'.escape(Input::get('out_city')).'" required>
							</div>
							<div class="form-group">
								<input type="text" class="form-control" id="out_post" name="out_post"  placeholder="* '.Lang::get($tr,'postal.code').'" value="'.escape(Input::get('out_post')).'" required>
							</div>
							<div class="form-group">
								<input type="text" class="form-control" id="out_phone" name="out_phone"  placeholder="'.Lang::get($tr,'phone').'" value="'.escape(Input::get('out_phone')).'">
							</div>
							<div class="form-group">
								<input type="email" class="form-control" id="out_email" name="out_email"  placeholder="'.Lang::get($tr,'email').'" value="'.escape(Input::get('out_email')).'">
							</div>
							<div class="form-group">
								<input type="text" class="form-control" id="out_contact_person" name="out_contact_person"  placeholder="'.Lang::get($tr,'person').'" value="'.escape(Input::get('out_contact_person')).'">
							</div>
						</div>'?>

						<div class="col-12 col-sm-12 col-md-4">
							<div class="row">
								<div class="col-sm-6 col-md-12">
									<p class="font-md-400"><?php echo Lang::get($tr,'options'); ?></p>
									
									<?php echo '
									<div class="form-group">
										<input class="form-control" id="display_to_date" name="display_to_date" placeholder="* '.Lang::get($tr,'display.to.date').' (DD/MM/YYYY)" type="text" value="'.escape(Input::get('display_to_date')) .'" required/>
									</div>
									
									<div class="form-group">
										<input type="text" class="form-control" id="display_to_hours" name="display_to_hours" placeholder="* '.Lang::get($tr,'display.to.hours').'(00:00)" value="'.escape(Input::get('display_to_hours')).'" required>'?>
										<script type="text/javascript">
											$('#display_to_hours').chungTimePicker({
												viewType: 1
											});
										</script>
									</div>

									<?php echo'
									<div class="row">
										<div class="col-6">
											<div class="form-group">
												<input type="text" class="form-control" id="price" name="price" placeholder="* '.Lang::get($tr,'price').'" value="'.escape(Input::get('price')).'" required>
											</div>
										</div>
										<div class="col-6">
											<div class="form-group">
												<input type="text" class="form-control" id="currency_id" name="currency_id" placeholder="* '.Lang::get($tr,'currency').'" value="'.((Input::get('currency_id')) ? Input::get('currency_id') : 'EURO').'" required>
											</div>
										</div>
									</div>
								</div>'?>

								<div class="col-sm-6 col-md-12 add-col-options">
									<div class="form-group">
										<select class="form-control form-control" id="car_type_id" name="car_type_id" required>
											<?php 
											echo '<option value="" selected >'.Lang::get($tr,'car.type').'</option>';
											foreach ($car_type as  $row) {
												if (Input::get('car_type_id') === $row['id']) {
													echo '<option selected value="'.$row['id'].'">'.$row['car_type'].'</option>';
												} else {
													echo '<option value="'.$row['id'].'">'.$row['car_type'].'</option>';
												}
											}
											?>
										</select>
									</div>


									<?php echo'
									<div class="form-group">
										<input type="text" class="form-control" id="tonnage" name="tonnage" placeholder="* '.Lang::get($tr,'tonnage').'" value="'.escape(Input::get('tonnage')).'" required>
									</div>
									<div class="form-group">
										<input type="text" class="form-control" id="load_is" name="load_is" placeholder="* '.Lang::get($tr,'what.load.is').'" value="'.escape(Input::get('load_is')).'" required>
									</div>

									<div class="form-check">
										<label class="form-check-label">
											<input class="form-check-input" type="checkbox" name="compressor" id="compressor" value="1"'. ((Input::get('compressor'))? "checked" : '').'>
											'.Lang::get($tr,'compressor').'
										</label>
									</div>
									<div class="form-check">
										<label class="form-check-label">
											<input class="form-check-input" type="checkbox" name="pump" id="pump" value="1"'. ((Input::get('pump'))? "checked" : '').'>
											'.Lang::get($tr,'pump').'
										</label>
									</div>
									<div class="form-check">
										<label class="form-check-label">
											<input class="form-check-input" type="checkbox" name="adr" id="adr" value="1"'. ((Input::get('adr'))? "checked" : '').'>
											'.Lang::get($tr,'adr').'
										</label>
									</div>
									<div class="form-check">
										<label class="form-check-label">
											<input class="form-check-input" type="checkbox" name="gps" id="gps" value="1"'. ((Input::get('gps'))? "checked" : '').'>
											'.Lang::get($tr,'gps').'
										</label>
									</div>
									<div class="form-check">
										<label class="form-check-label">
											<input class="form-check-input" type="checkbox" name="ready_to_ride" id="ready_to_ride" value="1"'. ((Input::get('ready_to_ride'))? "checked" : '').'>
											'.Lang::get($tr,'ready.to.ride').'
										</label>
									</div>
								</div>
							</div>'?>







						</div>

					</div>

					<div class="row">
						<div class="col-12">

							<div class="form-group">
								<div class="form-group">
									<textarea class="form-control" id="note" name="note" rows="3" placeholder="<?php echo Lang::get($tr,'note') ?>"><?php echo escape(Input::get('note')); ?></textarea>
								</div>
							</div>

							<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
							<button type="submit" class="btn btn-green mt-10" ><?php echo Lang::get($tr,'btn.add.order') ?></button>
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
			//format: 'dd/mm/yyyy',
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
		var date_input=$('input[name="display_to_date"]'); //our date input has the name "date"
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
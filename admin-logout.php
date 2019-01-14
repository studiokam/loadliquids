<?php

require_once 'core/init.php';

if (isset($_SESSION['admin'])) {
	unset($_SESSION['admin']);
	Redirect::to('admin-login.php');
}

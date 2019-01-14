<?php
  if (!isset($_SESSION['admin'])) {
    Redirect::to('admin-login.php');
  }
?>
<!doctype html>
  <html lang="pl">
    <head>

  	<title><?php echo $title ?></title>
      <!-- Required meta tags -->
      <meta charset="utf-8">
  	  <meta name="viewport" content="width=device-width, initial-scale=1.0">

      <!-- Bootstrap CSS -->
      <link rel="stylesheet" href="css/bootstrap.css">
      <link rel="stylesheet" href="css/style.css">
      <link rel="stylesheet" href="css/mp.css">

      <link href="https://fonts.googleapis.com/css?family=Istok+Web:200,400,700|PT+Sans:400,700" rel="stylesheet">

      <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    </head>
    <body>
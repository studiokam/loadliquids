<?php 
class Mail 
{
	// treść wiadomości po rejestracji
	public static function register($id, $hash_register) 
	{
		//treść wiadomości wysyłanej do klienta
		return '<!DOCTYPE HTML>
					<html>
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<style type="text/css">
					div, span, p, a, font, img, strong, u, i, table, tbody, tr, th, td {
						margin: 0;
						padding: 0;
						border: 0;
						background: transparent;
					}
					body,td,th {
						font-family: Calibri;
						font-size: 15px;
					}
					body {
						margin-left: 0px;
						margin-top: 0px;
						margin-right: 0px;
						margin-bottom: 0px;
					}
					.center {
						margin: 0px;
						padding-top: 50px;
						padding-right: 0px;
						padding-bottom: 0px;
						padding-left: 100px;
					}
					</style>
					</head>
					    <body>
					    <div class="center">
					      <p>Witamy</p>
					      <p>&nbsp;</p>
					      <p>Rejestracja wymaga potwierdzenia adresu e-mail. <a href="http://loadliquids.com?id='.$id.'&ha='.$hash_register.'">Kliknij aby potwierdzić email.</a> <b>. <br></p>
					      
					    </div>
						<body>
					</body>
					</html>';
	}
}

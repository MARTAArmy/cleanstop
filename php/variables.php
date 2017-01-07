<?php
$username = "database-username";
$password = 'database-password';
$database = "database-name";

$stripeSecretKey = 'your-stripe-secret-key';

function send500()
{
	header('HTTP/1.1 500 Internal Server Error');
	exit(0);
}

function send500WithMessage($msg)
{
	echo $msg;
	send500();
}
?>

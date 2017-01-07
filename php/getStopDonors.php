<?php

require("variables.php");

$conn = mysqli_connect('localhost', $username, $password, $database);
if (mysqli_connect_errno())
{
	send500();
}

$stopcode = mysqli_real_escape_string($conn, $_GET["stopcode"]);

$result = mysqli_query($conn, "SELECT name, amount FROM donations where stopcode = '$stopcode'");

if (!$result)
{
	send500();
}

$donors = array();
	
while ($row = mysqli_fetch_array($result, MYSQLI_NUM))
{
	$name = $row[0];
	$amount = $row[1];		

	array_push($donors, array('name' => $name, 'amount' => $amount));
}

echo json_encode($donors);
?>
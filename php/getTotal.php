<?php
require("php/variables.php");

$conn = mysqli_connect('localhost', $username, $password, $database);
if (mysqli_connect_errno())
{
	echo "Please check later.";
}
else
{
	$result = mysqli_query($conn, "SELECT SUM(amount) from donations");

	if (!$result)
	{
		echo "Please check later.";
	}
	else
	{
		$row = mysqli_fetch_array($result, MYSQLI_NUM);
		echo $row[0];
	}
}
?>
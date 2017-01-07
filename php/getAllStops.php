<?php

require("variables.php");

$conn = mysqli_connect('localhost', $username, $password, $database);
if (mysqli_connect_errno())
{
	send500();
}

$result = mysqli_query(
	$conn, 
	"SELECT s.stopcode, s.name, s.lat, s.lng, s.ridership, SUM(d.amount) FROM busstops s 
	 LEFT JOIN donations d ON s.stopcode = d.stopcode GROUP BY s.stopcode");

if (!$result)
{
	send500();
}

$allStops = array();

while ($row = mysqli_fetch_array($result, MYSQLI_NUM))
{
	$stopcode = $row[0];
	$name = $row[1];
	$lat = $row[2];
	$lng = $row[3];
	$ridership = $row[4];
	$totalAmountReceived = $row[5];

	if (is_null($totalAmountReceived))
	{
		$totalAmountReceived = 0;
	}

	array_push($allStops, array(
		'stopcode' => $stopcode, 
		'name' => $name,
		'lat' => $lat,
		'lng' => $lng,
		'ridership' =>$ridership,
		'totalAmountReceived' => $totalAmountReceived
	));
}

echo json_encode($allStops);
?>
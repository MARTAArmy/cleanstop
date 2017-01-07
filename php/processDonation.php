<?php

require("stripe-php-4.0.0/init.php");
require("variables.php");

send500WithMessage('Sorry, donations have now closed!');

$conn = mysqli_connect('localhost', $username, $password, $database);
if (mysqli_connect_errno())
{
    send500WithMessage('Failed to process this donation! Please try again later.');
}

$token = $_POST['token'];

$token_escaped = mysqli_real_escape_string($conn, $token);

$name = mysqli_real_escape_string($conn, $_POST['name']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$stopcode = mysqli_real_escape_string($conn, $_POST['stopcode']);
$amount = mysqli_real_escape_string($conn, $_POST['amount']);
$comments = mysqli_real_escape_string($conn, $_POST['comments']);
$stopcode = mysqli_real_escape_string($conn, $_POST["stopcode"]);

$sql1 = "SELECT SUM(amount) from donations WHERE stopcode='$stopcode'";
$result1 = mysqli_query($conn, $sql1);
if (!$result1)
{
    send500WithMessage('Failed to process this donation! Please try again later.');
}

$row = mysqli_fetch_array($result1, MYSQLI_NUM);
$totalSoFar = $row[0];
if ($totalSoFar > 20000) // $200
{
    send500WithMessage('This stop has already reached its fundraising target! Please choose a different stop.');
}

$sql1 = "INSERT INTO donations (token, name, email, comments, amount, stopcode, status) 
         values('$token_escaped','$name','$email', '$comments', $amount, '$stopcode', 'pending')";
$result1 = mysqli_query($conn, $sql1);

if (!$result1)
{
    send500WithMessage('Failed to process this donation! Please try again later.');
}

\Stripe\Stripe::setApiKey($stripeSecretKey);

// Create a charge: this will charge the user's card
try 
{
    $charge = \Stripe\Charge::create(array(
        "amount" => $amount, // Amount in cents
        "currency" => "usd",
        "source" => $token,
        "description" => "Marta Army - Operation CleanStop Stop#$stopcode - Thanks"
    ));
}
catch (\Stripe\Error\InvalidRequest $e) 
{
    handleStripeError($conn, 'inv_req', $token_escaped);
} 
catch (\Stripe\Error\Authentication $e) 
{
    handleStripeError($conn, 'api_auth_err', $token_escaped);
} 
catch (\Stripe\Error\ApiConnection $e) 
{
    handleStripeError($conn, 'nw_err', $token_escaped);
} 
catch (\Stripe\Error\Base $e) 
{
    handleStripeError($conn, 'stripe_exception: '. $e->getMessage(), $token_escaped);
} 
catch (Exception $e) 
{
    handleStripeError($conn, 'generic_exception: ' . $e->getMessage(), $token_escaped);
}


$update2 = mysqli_query($conn, "UPDATE donations set status='success' where token = '$token_escaped'");
if (!$update2)
{
    send500WithMessage('Failed to process this donation! Please contact themartaarmy@gmail.com if you see a charge on your card, and mention code 003');
}

mysqli_close($conn);


function handleStripeError($conn, $status, $token_escaped)
{
    $status_escaped = mysqli_real_escape_string($conn, $status);

    $update1 = mysqli_query($conn, "UPDATE donations set status='$status_escaped' where token = '$token_escaped'");
    if (!$update1)
    {
        send500WithMessage('Failed to process this donation! Please contact themartaarmy@gmail.com if you see a charge on your card, and mention code 001');
        return;
    }
    send500WithMessage('Failed to process this donation! Please contact themartaarmy@gmail.com if you see a charge on your card, and mention code 002');
}

?>
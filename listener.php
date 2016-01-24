<?php

////////////////////////////////////////////////
// STEP 1: CATCH POSTED VARIABLES AND VALIDATE
////////////////////////////////////////////////


//CASE 1: no posted variables coming in
if ($_SERVER['REQUEST_METHOD'] != "POST") {
	die ("No Post Variables");
}

//CASE 2: posted variables are coming in
//Initialize the $req variable and add the CMD key value pair to validate
$req = 'cmd=_notify-validate';

// Read the post from PayPal
foreach ($_POST as $key => $value) {

	//URLENCODE returns an encoded string
	//STRIPSLASHES returns a string with backslashes stripped off
    $value = urlencode(stripslashes($value));
    $req .= "&" . $key . "=" . $value;
}

////////////////////////////////////////////////////////
// STEP 2: VALIDATE BY SENDING BACK TO PAYPAL'S SERVER
////////////////////////////////////////////////////////

// We will use CURL instead of PHP for this for a more universally operable script (fsockopen has issues on some environments)

//$url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
$url = "https://www.sandbox.paypal.com/cgi-bin/webscr";

//initialize curl strings
$curl_result= '';
$curl_err= '';

//initialize a new session and return a cURL handle on success, false on errors
$ch = curl_init();

//CURL_SETOPT(ch, option, value) set an option for cURL transfer
//ch: a cURL handle returned by curl_init()
//option: the cURL option ot be set
//value: the value to be set for option

curl_setopt($ch, CURLOPT_URL, $url);

//curlopt_returntransfer set to TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

//curlopt_post set to TRUE to do a regular http post
curl_setopt($ch, CURLOPT_POST, 1);

//curlopt_postfields: the full data to post in a http post operation
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);

curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded", "Content-Length: " . strlen($req)));

//curlopt_header: true to include the header in the output
curl_setopt($ch, CURLOPT_HEADER , 0);

//idk what these are
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

//execute the given url session, will return the result on success, false on failure
$curl_result = @curl_exec($ch);

//return a string that contains the last error for the current session
$curl_err = curl_error($ch);

//close a cURL session
curl_close($ch);

$req = str_replace("&", "\n", $req);  // Make it a nice list in case we want to email it to ourselves for reporting

// Check that the result verifies
//strpos(haystack, needle) returns first numeric position of first occurence, or else false
if (strpos($curl_result, "VERIFIED") !== false) {
    $req .= "\n\nPaypal Verified OK";
} 
//if not verified, someones messing with you
else {
	$req .= "\n\nData NOT verified from Paypal!";
	mail("homerootcreations@gmail.com", "IPN interaction not verified", "$req", "From: homerootcreations@gmail.com" );
	//no further action if it is not validated
	exit();

}

//////////////////////////////////////////////////
// STEP 3: CHECK INFO AGAINST YOUR OWN RECORDS
//////////////////////////////////////////////////

/* CHECK THESE 4 THINGS BEFORE PROCESSING THE TRANSACTION, HANDLE THEM AS YOU WISH
1. Make sure that business email returned is your business email
2. Make sure that the transaction’s payment status is “completed”
3. Make sure there are no duplicate txn_id
4. Make sure the payment amount matches what you charge for items. (Defeat Price-Jacking) 
5. Check currency code.*/
 
//CHECK 1
$receiver_email = $_POST['receiver_email'];

if ($receiver_email != "homerootcreations@gmail.com") {
	//someone is messing with you
	$message = "Investigate why and how receiver email is wrong. Email being used is: " . $_POST['receiver_email'] . "\n\n\n$req";
    mail("homerootcreations@gmail.com", "Receiver Email is incorrect", $message, "From: homerootcreations@gmail.com" );
    exit(); // exit script
}
//CHECK 2
if ($_POST['payment_status'] != "Completed") {
	// Handle how you think you should if a payment is not complete yet, a few scenarios can cause a transaction to be incomplete
	//don't fulfill order until it is completed
}

require_once 'php/connection.php';

//CHECK 3
$this_txn = $_POST['txn_id'];
$duplicate_query = "SELECT id FROM transactions WHERE txn_id = '" . $this_txn . "' LIMIT 1";

$duplicate_result = mysql_query($duplicate_query);
$duplicate_num_rows = mysql_num_rows($duplicate_result);

//if there is at least 1 other order with the same transaction id, someones messing with you
if ($duplicate_num_rows > 0) {

    $message = "Duplicate transaction ID occured so we killed the IPN script. \n\n\n$req";
    mail("homerootcreations@gmail.com", "Duplicate txn_id in the IPN system", $message, "From: homerootcreations@gmail.com" );
    exit(); // exit script
} 

//CHECK 4
//paypal should return custom value exactly as we inputted it
$product_id_string = $_POST['custom'];
$product_id_string = rtrim($product_id_string, ","); // remove last comma

// Explode the string, make it an array, then query all the prices out, add them up, and make sure they match the payment_gross amount
$id_str_array = explode(",", $product_id_string); // Uses Comma(,) as delimiter(break point)

$fullAmount = 0; //initialize full amount of cart
foreach ($id_str_array as $key => $value) {
    
	$id_quantity_pair = explode("-", $value); // Uses Hyphen(-) as delimiter to separate product ID from its quantity

	$product_id = $id_quantity_pair[0]; // Get the product ID
	$product_quantity = $id_quantity_pair[1]; // Get the quantity

	$price_check_query = "SELECT PRICE FROM products WHERE ID = '" . $product_id . "' LIMIT 1";
	$price_check_result = mysql_query($price_check_query);
    while($row = mysql_fetch_array($price_check_query)){

		$product_price = $row["price"];
	}

	$product_price = $product_price * $product_quantity;
	$fullAmount = $fullAmount + $product_price;
}

$fullAmount = number_format($fullAmount, 2);
$grossAmount = $_POST['mc_gross']; 
if ($fullAmount != $grossAmount) {
        $message = "Possible Price Jack: " . $_POST['payment_gross'] . " != $fullAmount \n\n\n$req";
        mail("homerootcreations@gmail.com", "Price Jack or Bad Programming", $message, "From: homerootcreations@gmail.com" );

        exit(); // exit script
} 

//////////////////////////////////////////////////////
//   STEP 4: INSERT INTO DATABASE
//////////////////////////////////////////////////////

//TODO: FINISH ASSIGNING POST VARIABLES TO LOCAL ONES

$txn_id = $_POST['txn_id'];
$payer_email = $_POST['payer_email'];
$custom = $_POST['custom'];

// Place the transaction into the database
$sql = mysql_query("INSERT INTO transactions (product_id_array, payer_email, first_name, last_name, payment_date, mc_gross, payment_currency, txn_id, receiver_email, payment_type, payment_status, txn_type, payer_status, address_street, address_city, address_state, address_zip, address_country, address_status, notify_version, verify_sign, payer_id, mc_currency, mc_fee) 
   VALUES('$custom','$payer_email','$first_name','$last_name','$payment_date','$mc_gross','$payment_currency','$txn_id','$receiver_email','$payment_type','$payment_status','$txn_type','$payer_status','$address_street','$address_city','$address_state','$address_zip','$address_country','$address_status','$notify_version','$verify_sign','$payer_id','$mc_currency','$mc_fee')") or die ("unable to execute the query");

mysql_close();
// Mail yourself the details
mail("homerootcreations@gmail.com", "NORMAL IPN RESULT YAY MONEY!", $req, "From: homerootcreations@gmail.com");
?>
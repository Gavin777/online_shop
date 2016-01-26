<?php

$title = 'Thank you!';
$extra_style = "";
include "header.php";

var_dump($_POST);
echo "<br>hello<br>";
var_dump($_GET);

//http://www.geekality.net/2010/10/19/php-tutorial-paypal-payment-data-transfers-pdt/
//using Paypal payment standard
if (isset($_GET['tx'])) {

	//necessary for validating
	$id_string = "WpQS5uYZlia_2RAhTKvQD26Hy8M-6Kvp4nbBrFy10YKfnJ7CiP8dsXeaeX4";

	$tx = $_GET['tx'];


	//TESTING
	echo $id_string  . "<br>";
	echo $tx . "<br>";


	//VERIFY BY SENDING BACK TO PAYPAL
	$url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
	$pp_hostname = "www.sandbox.paypal.com";
	$curl_result= '';
	$curl_err= '';

	//build the variables required to send back to paypal
	$req = "cmd=_notify-synch";
	$req .= "&tx=" . $tx;
	$req .= "&at=" . $id_string;


	//TESTING
	echo $req . "<br>";


	//begin posting variables
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

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);

	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: " . $pp_hostname));


	//execute the given url session, will return the result on success, false on failure
	$curl_result = curl_exec($ch);

	//return a string that contains the last error for the current session
	$curl_err = curl_error($ch);

	//last http status
	$status = curl_getinfo($curl_request, CURLINFO_HTTP_CODE);

	//close a cURL session
	curl_close($ch);

	if (($status == 200) && (strpos($curl_result, 'SUCCESS') === 0)) {


		//TESTING
		echo $curl_result . "<br>";


		//parse the data
		$lines = explode("/n", $curl_result);

		//initialize the array
		$key_array = array();

		//gather the components of post; starts at 1 because we want to skip SUCCESS
		for ($i = 1; $i < count($lines); $i++) {
			list($key, $value) = explode("=", $lines[$i]);
			$key_array[urldecode($key)] = urldecode($value);

		}//everything is now packaged in the $key_array

		//grab user data from array
		$firstname = $keyarray['first_name'];
   		$lastname = $keyarray['last_name'];
   		$itemname = $keyarray['item_name'];
   		$amount = $keyarray['payment_gross'];
   		$payer_email = $keyarray['payer_email'];

   		echo "<b>Payment Details</b><br>";
   		echo "Name: " . $firstname . $lastname;
   		echo "Item: " . $itemname;
    	echo "Amount: " . $amount;
    	echo "A receipt of your payment has been emailed to " . $payer_email;

	}

	else if (strpos ($curl_result, "FAIL") === 0) {
        // log for manual investigation
        echo "EPIC FAILURE";
        echo $curl_result;
        echo $curl_err;
    }

    else {
    	echo "something went wrong";
    	echo $curl_result;
    	echo $curl_err;
    }

}

else {
	echo "yo idk man";
}

include "footer.php";
?>

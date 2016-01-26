<?php

$title = 'Thank you!';
$extra_style = "";
include "header.php";


//http://www.geekality.net/2010/10/19/php-tutorial-paypal-payment-data-transfers-pdt/
//using Paypal payment standard
if (isset($_GET['tx'])) {

	//necessary for validating
	$id_string = "WpQS5uYZlia_2RAhTKvQD26Hy8M-6Kvp4nbBrFy10YKfnJ7CiP8dsXeaeX4";

	$tx = $_GET['tx'];

	//VERIFY BY SENDING BACK TO PAYPAL
	$url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
	$pp_hostname = "www.sandbox.paypal.com";
	$curl_result= '';
	$curl_err= '';

	//build the variables required to send back to paypal
	$req = "cmd=_notify-synch";
	$req .= "&tx=" . $tx;
	$req .= "&at=" . $id_string;

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

	//close a cURL session
	curl_close($ch);

	if (strpos($curl_result, 'SUCCESS') === 0) {

		//turn each name-value pair into an array
		$lines = explode(' ', $curl_result);

		//initialize the array
		$keyarray = array();

		//number of lines for the for loop
		$num = 0;
		$num = count($lines);

		//gather the components of post; starts at 1 because we want to skip SUCCESS
		for ($i = 1; $i < $num; $i++) {
			list($key, $value) = array_pad(explode("=", $lines["" . $i]), 2, null);
			$keyarray[urldecode($key)] = urldecode($value);

		}//everything is now packaged in the $key_array


		//grab user data from array
		$firstname = $keyarray['first_name'];
   		$lastname = $keyarray['last_name'];
   		$amount = $keyarray['payment_gross'];
   		$payer_email = $keyarray['payer_email'];
   		$address_street = $keyarray['address_street'];
   		$address_zip = $keyarray['address_zip'];
   		$address_city = $keyarray['address_city'];
   		$address_state = $keyarray['address_state'];
   		$payment_status = $keyarray['payment_status'];

   		echo "<b>Payment Details</b><br>";
   		echo "Name: " . $firstname . " " . $lastname . "<br>";
    	echo "Amount: " . $amount . "<br>";
    	echo "A receipt of your payment has been emailed to :" . $payer_email . "<br>";
    	echo "Your package will be shipped to: " . $address_street . "<br>" . $address_city . " " . $address_state . ", " . $address_zip;

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

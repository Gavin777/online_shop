

<?php

//http://www.geekality.net/2010/10/19/php-tutorial-paypal-payment-data-transfers-pdt/
//using Paypal payment standard
if isset($_GET['tx']) {

	$tx = $_GET['tx'];

	//VERIFY BY SENDING BACK TO PAYPAL
	$url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
	$curl_result= '';
	$curl_err= '';
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

}

?>

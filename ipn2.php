<?php
if (isset($_POST)) {

	//APPEND POSTED INFORMATION WITH $REQ
	$req = 'cmd=_notify-validate';

	foreach ($_POST as $key => $value) {

   		$value = urlencode(stripslashes($value));
   		$req .= "&" . $key . "=" . $value;
	}

	//SEND BACK TO PAYPAL TO VALIDATE
	$url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
	$curl_result= '';
	$curl_err= '';

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);

	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

	$curl_result = curl_exec($ch);
	$curl_err = curl_error($ch);

	curl_close($ch);

	//CHECK THAT PAYPAL VERIFIED THE INFORMATION
	if (strpos($curl_result, "VERIFIED") == 0) {

	}

	else if (strpos($curl_result, "INVALID") == 0) {
		//someone is trying to mess with you
	}

	else {
		//i have no idea what happened
	}
}
?>
<?php
$title = 'Sandbox';
$extra_style = '';
include "header.php";

$string = "SUCCESS mc_gross=6.00 protection_eligibility=Eligible address_status=confirmed item_number1= payer_id=J33CMDD6N6JKU tax=0.00 address_street=1+Main+St payment_date=18%3A36%3A15+Jan+25%2C+2016+PST payment_status=Completed charset=windows-1252 address_zip=95131 mc_shipping=0.00 mc_handling=0.00 first_name=test mc_fee=0.47 address_country_code=US address_name=test+buyer custom=7-1%2C payer_status=verified business=homerootcreations-facilitator%40gmail.com address_country=United+States num_cart_items=1 mc_handling1=0.00 address_city=San+Jose payer_email=homerootcreations-buyer%40gmail.com mc_shipping1=0.00 tax1=0.00 txn_id=8BJ00164G17661634 payment_type=instant last_name=buyer address_state=CA item_name1=Charcoal receiver_email=homerootcreations-facilitator%40gmail.com payment_fee=0.47 quantity1=1 receiver_id=8VWSLT6BR2YEY txn_type=cart mc_gross_1=6.00 mc_currency=USD residence_country=US transaction_subject=7-1%2C payment_gross=6.00 ";



$lines = explode(' ', $string);

		$keyarray = array();
		$num = 0;
		$num = count($lines);

		//gather the components of post; starts at 1 because we want to skip SUCCESS
		for ($i = 1; $i < $num; $i++) {
			list($key, $value) = array_pad(explode("=", $lines["" . $i]), 2, null);
			$keyarray[urldecode($key)] = urldecode($value);

		}//everything is now packaged in the $key_array


		//TESTING
		//TODO: nothing in keyarray

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
?>




<?php
include "footer.php";
?>
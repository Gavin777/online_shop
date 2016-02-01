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

		//STEP 1: CHECK BUSINESS EMAIL
		$homeroot_email = 'homerootcreations-facilitator@gmail.com';

		if ($homeroot_email != $_POST['business']) {
			//email being used is not ours
			echo "this isn't my email";
			echo '<br>';
			echo $_POST['business'];
			exit();
		}


		//possible values for payment_status
		$payment_cancelled_reversal = 'Cancelled_Reversal'; //
		$payment_completed = 'Completed';//
		$payment_denied = 'Denied'; //
		$payment_expired = 'Expired'; //
		$payment_failed = 'Failed'; //
		$payment_pending= 'Pending';
		$payment_refunded = 'Refunded'; //
		$payment_reversed = 'Reversed';//
		$payment_processed = 'Processed'; 
		$payment_voided = 'Voided'; //


		//STEP 2: CHECK PAYMENT STATUS IS COMPLETED
		require_once 'php/connection.php';

		//ignore: expired, failed, denied
		//CASE: refunded, reversed, or cancel reversed
		if ($payment_refunded == $_POST['payment_status'] || $payment_reversed == $_POST['payment_status'] || $payment_cancelled_reversal == $_POST['payment_status']) {

			echo 'bad payment';
			echo '<br>';
			echo $_POST['payment_status'];

			//parent_txn_id contains the txn_id of original transaction
			$id_search = $_POST['parent_txn_id'];
			$new_txn_id = $_POST['txn_id'];

			//double check that there is exactly one parent_txn_id logged into txn_id
			$double_check = 'SELECT * FROM transactions WHERE txn_id = "' . $_POST['parent_txn_id'] . '"';
			$check_result = mysqli_query($link, $double_check);
			$check_rows = mysqli_num_rows($check_result);

			if ($check_rows != 1) {

				//someone is messing with you
				echo 'duplicate txn';
				echo '<br>';
				echo $_POST['parent_txn_id'];
				exit();
			}

			else if ($check_rows == 1) {

				//check that total matches
				$expected_total = 0.00;
				$purchase_details = rtrim($_POST['custom'], ",");
				$purchase_array = explode(",", $purchase_details);
				
				

				//add up the prices
				foreach($purchase_array as $item) {

					$item_details = explode("-", $item);
					$price_query = 'SELECT PRICE FROM products WHERE ID = "' . $item_details[0] . '"';
					$price_result = mysqli_query($link, $price_query);
					$price = mysqli_fetch_array($price_result);
					$expected_total += $price['PRICE'] * $item_details[1];

				}

				//if the prices do not match
				if ($expected_total != $_POST['mc_gross']) {

					//someone is messing with you
					echo 'wrong price';
					echo $expected_total . '<br>';
					echo $_POST['mc_gross'];
					exit();
					
				}

				//if the prices do match
				elseif ($expected_total == $_POST['mc_gross']) {

				//update txn_id with new txn_id
				$query = 'UPDATE transactions SET txn_id = "' . $new_txn_id . '", payment_status = "' . $_POST['payment_status'] . '" WHERE txn_id = "' . $id_search .'"';
				echo $query;
				$update_result = mysqli_query($link, $query);
				exit();
				}
			}



		}


		//if payment is completed
		elseif ($payment_completed == $_POST['payment_status']) {

			//STEP 3: CHECK TXN_ID IS ORIGINAL
			echo 'payment complete';

			$txn_id = $_POST['txn_id'];
			$txn_check = 'SELECT * FROM transactions WHERE txn_id = "' . $txn_id . '"';
			$check_result = mysqli_query($link, $txn_check);
			$txn_duplicate = mysqli_num_rows($check_result);

			if ($txn_duplicate > 0) {

				//someone is messing with you
				echo 'duplicate txn';
				exit();
			}

			//STEP 4: CHECK PRICES

			$expected_total = 0.00;
			$purchase_details = rtrim($_POST['custom'], ",");
			$purchase_array = explode(",", $purchase_details);
			
			//add up the prices
			foreach($purchase_array as $item) {

				$item_details = explode("-", $item);
				$price_query = 'SELECT PRICE FROM products WHERE ID = "' . $item_details[0] . '"';
				$price_result = mysqli_query($link, $price_query);
				$price = mysqli_fetch_array($price_result);
				$expected_total += $price['PRICE'] * $item_details[1];

			}

			//if the prices do not match
			if ($expected_total != $_POST['mc_gross']) {

				//someone is messing with you
				echo 'wrong price';
				echo $expected_total . '<br>';
				echo $_POST['mc_gross'];
				exit();
			}

			//STEP 5: ADD TO DATABASE
			$payment_type = $_POST['payment_type'];
			$payment_date = $_POST['payment_date'];
			$payment_status = $_POST['payment_status'];
			$address_status = $_POST['address_status'];
			$payer_status = $_POST['payer_status'];
			$first_name = $_POST['first_name'];
			$last_name = $_POST['last_name'];
			$payer_email = $_POST['payer_email'];
			$payer_id = $_POST['payer_id'];
			$address_name = $_POST['address_name'];
			$address_country = $_POST['address_country'];
			$address_country_code = $_POST['address_country_code'];
			$address_zip = $_POST['address_zip'];
			$address_state = $_POST['receiver_email'];
			$address_city = $_POST['receiver_email'];
			$address_street = $_POST['receiver_email'];
			$business = $_POST['receiver_email'];
			$receiver_id = $_POST['receiver_email'];
			$residence_country = $_POST['receiver_email'];
			$shipping = $_POST['receiver_email'];
			$tax = $_POST['receiver_email'];
			$mc_currency = $_POST['receiver_email'];
			$mc_fee = $_POST['mc_fee'];
			$mc_gross = $_POST['mc_gross'];
			$txn_type = $_POST['txn_type'];
			$txn_id = $_POST['txn_id'];
			$notify_version = $_POST['notify_version'];
			$custom = $_POST['custom'];
			$verify_sign = $_POST['verify_sign'];

			$new_transaction = 'INSERT INTO transactions (
				ID,
				payment_type,
				payment_date,
				payment_status,
				address_status,
				payer_status,
				first_name,
				last_name,
				payer_email,
				payer_id,
				address_name,
				address_country,
				address_country_code,
				address_zip,
				address_state,
				address_city,
				address_street,
				business,
				receiver_id,
				residence_country,
				shipping,
				tax,
				mc_currency,
				mc_fee,
				mc_gross,
				txn_type,
				txn_id,
				notify_version,
				custom,
				verify_sign
				) 
			VALUES (
				NULL, "' . 
				$payment_type . '", "' .
				$payment_date . '", "' .
				$payment_status . '", "' .
				$address_status . '", "' .
				$payer_status . '", "' .
				$first_name . '", "' .
				$last_name . '", "' .
				$payer_email . '", "' .
				$payer_id . '", "' .
				$address_name . '", "' .
				$address_country . '", "' .
				$address_country_code . '", "' .
				$address_zip . '", "' .
				$address_state . '", "' .
				$address_city . '", "' .
				$address_street . '", "' .
				$business . '", "' .
				$receiver_id . '", "' .
				$residence_country . '", "' .
				$shipping . '", "' .
				$tax . '", "' .
				$mc_currency . '", "' .
				$mc_fee . '", "' .
				$mc_gross . '", "' .
				$txn_type . '", "' .
				$txn_id . '", "' .
				$notify_version . '", "' .
				$custom . '", "' .
				$verify_sign .
				'")';

			$transaction_input = mysqli_query($link, $new_transaction);
			echo 'we gucci';

			foreach ($purchase_array as $item) {

				$item_quantity = explode('-', $item);

				$get_quantity = 'SELECT QUANTITY FROM PRODUCTS WHERE ID = "' . $item_quantity[0] . '"';
				$get_quantity = mysqli_query($link, $get_quantity);
				$quantity_array = mysqli_fetch_array($get_quantity);

				//new quantity
				$update = $quantity_array[0] - $item_quantity[1];
				$update_quantity = 'UPDATE products SET QUANTITY = "' . $update . '" WHERE ID = "' . $item_quantity[0] . '"';
				$update_query = mysqli_query($update_quantity);

			}

		}
	}

	else if (strpos($curl_result, "INVALID") == 0) {
		echo 'someone is trying to mess with you';
	}

	else {
		echo 'i have no idea what happened';
	}


}

?>
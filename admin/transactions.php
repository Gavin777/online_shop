<?php
session_start();
$title = "Transactions";
$extra_style = '<link rel="stylesheet" href="../style/adminstyle.css">';

include "adminheader.php";
include "check.php";
include "../php/connection.php";

//display id, txn number, name, date, price, payment status, address status, payer status
echo '<div class="admin_title">Transactions</div>';
echo '<table class="transaction" border = "1">
	<tr>
		<th>ID</th>
		<th>Transaction</th>
		<th>Name</th>
		<th>Date</th>
		<th>Price</th>
		<th>Payment Status</th>
		<th>Address Status</th>
		<th>Payment Status</th>
		<th>Details</th>
	</tr>
';

//execute query
$display_query = 'SELECT * FROM transactions';
$display_result = mysqli_query($link, $display_query);

while ($customer = mysqli_fetch_array($display_result)) {
	echo '
		<tr>
			<td>' . $customer['ID'] . '</td>
			<td>' . $customer['txn_id'] . '</td>
			<td>' . $customer['first_name'] . ' ' . $customer['last_name'] . '</td>
			<td>' . substr($customer['payment_date'], 0, 15) . '</td>
			<td>' . $customer['mc_gross'] . '</td>
			<td>' . $customer['payment_status'] . '</td>
			<td>' . $customer['address_status'] . '</td>
			<td>' . $customer['payer_status'] . '</td>
			<td><a href="transactions.php?ordernum=' . $customer['ID'] . '"><input type="button" id="order' . $customer['ID'] . '" value="View"></a>
		</tr>
	';
}

echo '</table>';
mysqli_close($link);

////////////////////////////////////////////
/////   Get Order Details //////////////////
////////////////////////////////////////////

if (isset($_GET['ordernum'])) {

	include "../php/connection.php";

	$order_id = $_GET['ordernum'];
	$order_query = 'SELECT * FROM transactions WHERE ID = "' . $order_id . '" LIMIT 1';
	$order_result = mysqli_query($link, $order_query);
	$order_array = mysqli_fetch_array($order_result);

	//translate custom variable into order details
	$prod_ordered = rtrim($order_array['custom'], ",");
	$prod_array = explode(",", $prod_ordered);
	$prod_details = "";

	foreach ($prod_array as $prod) {
		$each_prod = explode("-", $prod);

		$each_prod_query = 'SELECT * FROM products WHERE ID = "' . $each_prod[0] . '" LIMIT 1';
		$each_prod_result = mysqli_query($link, $each_prod_query);
		$each_prod_array = mysqli_fetch_array($each_prod_result);

		$prod_details .= $each_prod_array['NAME'] . ' (' . $each_prod_array['TYPE'] . '): ' . $each_prod[1] . '<br>';


	}


	echo '<a href ="transactions.php"><div class="black"></div></a>';
	echo '
		<table class="transaction_details">
		<tr>
			<td>
				Transaction:
			</td>
			<td>
				' . $order_array['txn_id'] . '
			</td>
		</tr>
		<tr>
			<td>
				Name:
			</td>
			<td>
				' . $order_array['first_name'] . ' ' . $order_array['last_name'] . '
			</td>
		</tr>
		<tr>
			<td>
				Email:
			</td>
			<td>
				' . $order_array['payer_email'] . '
			</td>
		</tr>
		<tr>
			<td>
				Total:
			</td>
			<td>
				' . $order_array['mc_gross'] . '
			</td>
		</tr>
		<tr>
			<td>
				Order:
			</td>
			<td>
				' . $prod_details . '
			</td>
		</tr>
		</table>
	';




}
include '../footer.php';
?>
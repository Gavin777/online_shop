

<?php

include "php/connection.php";

$string = '8-1,10-1,7-1,';
$edited_string = rtrim($string, ',');

$purchase_array = explode(',', $edited_string);

foreach ($purchase_array as $item) {

	$item_quantity = explode('-', $item);

	$get_quantity = 'SELECT QUANTITY FROM PRODUCTS WHERE ID = "' . $item_quantity[0] . '"';
	echo $get_quantity;

	$get_quantity = mysqli_query($link, $get_quantity);
	$quantity_array = mysqli_fetch_array($get_quantity);
	var_dump($quantity_array);

	//new quantity

	$update = $quantity_array[0] - $item_quantity[1];
	echo $update;

	$update_quantity = 'UPDATE products SET QUANTITY = "' . $update . '" WHERE ID = "' . $item_quantity[0] . '"';
	echo $update_quantity;

	$update_query = mysqli_query($link, $update_quantity);

	//to check our work
	$check = 'SELECT * FROM products';
	$check_sql = mysqli_query($link, $check);
	$check_array = mysqli_fetch_array($check_sql);

	var_dump($check_array);


}
?>
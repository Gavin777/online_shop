<?php
session_start();

//header stuff
$title = 'Shopping Cart';
$extra_style = '<link rel="stylesheet" href="style/product-style.css">';
include_once("header.php");
setlocale(LC_MONETARY, 'en_US');
$subtotal = 0.00;
$shipping = number_format(0, 2);
$total_display = 0;


/////////////////////////////////
//     EMPTY CART FUNCTION
/////////////////////////////////

if (isset($_GET['clear']) && $_GET['clear'] =='clear') {
	unset($_SESSION['cart_array']);
	header('Location: cart.php');
	exit();
}


///////////////////////////////////////////
//ADD ITEM X WITH QUANTITY Y TO THE CART
///////////////////////////////////////////

if (isset($_POST['id'])) {

	//grab values from POST
	$id = $_POST['id'];
	$quantity = $_POST['quantity'];

	//CASE 1: FIRST ITEM IN THE CART
	if (!isset($_SESSION['cart_array']) || count($_SESSION['cart_array']) < 1) {
		$_SESSION['cart_array'] = 
		array(
			array(
				"item_id" => $id,
				"quantity" => $quantity
			)
		);
	}

	//CASE 2: ITEM EXISTS IN CART, JUST NEED TO UPDATE QTY
	else {

		$found = false;
		$j = 0;
		foreach ($_SESSION['cart_array'] as $cart_item) {
			
			while (list($key, $value) = each($cart_item)) {
				if ($key == 'item_id' && $value == $id) {
					$found = true;
					$_SESSION['cart_array'][$j]['quantity'] += $quantity;
				}
			}
			$j++;

		}

		//CASE 2: ITEM EXISTS IN CART, JUST NEED TO UPDATE QTY
		if ($found == false) {

			array_push($_SESSION['cart_array'], array('item_id' => $id, 'quantity' => $quantity));
		}
	} //end else statement

	//refresh the page so that if the user refreshes the cart, a duplicate will not be added
	header('Location: cart.php');
	exit();

} //end if statement to add item to cart



/////////////////////////////////
//      DELETE FUNCTION:
/////////////////////////////////
if (isset($_POST['index_to_remove']) && $_POST['index_to_remove'] !='') {
	$remove_index = $_POST['index_to_remove'];

	if (count($_SESSION['cart_array']) <= 1) {
		unset($_SESSION['cart_array']);
	}
	else {
		unset($_SESSION['cart_array'][$remove_index]);
		sort($_SESSION['cart_array']);
	}
}


////////////////////////////////
//      EDIT FUNCTION:
////////////////////////////////
if (isset($_POST['index_to_edit']) && $_POST['index_to_edit'] != '') {
	$edit_index = $_POST['index_to_edit'];
	$edit_qty = $_POST['quantity' . $edit_index];

	$_SESSION['cart_array'][$edit_index]['quantity'] = $edit_qty;
}



///////////////////////////////
//   OUTPUT SHOPPING CART:
///////////////////////////////

//CASE 1: SHOPPING CART IS EMPTY
if (!isset($_SESSION['cart_array']) || count($_SESSION['cart_array']) < 1) {

	$cart_total=0;
	$pp_checkout='';
	$quantity_total = 0;

	echo '<div class="cart_page">';
	echo '<div class="cart_container">';
	//cart banner
	echo "<div class='cart_banner'>Your Cart</div>";
	echo '<div id="empty">Your shopping cart is empty. Let us fix that!</div></div>';


	//CHECKOUT SUMMARY PANEL
	echo '<div class="checkout">';
	echo '<img class="checkout_logo" src="images/whitelogo.png">';
	echo '<div class="summary">Total Quantity: ' . $quantity_total . '</div>';

	echo "<div class='summary'>Subtotal: $" . $subtotal. '</div>';
	echo '<div class="summary">Shipping and Handling: $' . $shipping . '</div>';
	echo '<div class="summary">Total: $' . $total_display . '</div>';


	echo '<div class="pp_button">' . $pp_checkout . '</div>';
	echo '</div></div>';



}

//CASE 2: SHOPPING CART IS NOT EMPTY, LOOP THROUGH EACH ENTRY
else {

	$cart_total = '';  //initialize cart total variable
	$pp_checkout = ''; //initialize checkout button variable
	$prod_id_array = ''; //to pass to paypal
	$i = 0; //initialize index for items in the cart
	$quantity_total = 0; //count total number of items in the cart
	$item_price = 0; //price per item

	//BEGIN CONSTRUCTING THE CHECKOUT BUTTON
	$pp_checkout .= '
		<form method = "post" action="https://www.sandbox.paypal.com/cgi-bin/webscr">
			<input type = "hidden" name = "cmd" value = "_cart">
			<input type = "hidden" name = "upload" value = "1">
			<input type = "hidden" name = "business" value = "homerootcreations-facilitator@gmail.com">';

	//begin outputting the cart
	//HTML STRUCTURE: cart_page contains cart_container and checkout
	echo "<p><a href='shop.php'>Shop</a></p>";
	echo '<div class="cart_page">';

	
	echo '<div class="cart_container">';

	//cart banner
	echo "<div class='cart_banner'>Your Cart</div>";

	//loop through each item in the array to output necessary information
	foreach ($_SESSION['cart_array'] as $each_product) {

		$i++;

		while (list($key, $value) = each($each_product)) {

			//GRAB EACH ITEM ID FROM THE ARRAY
			if ($key == 'item_id') {

				//query the item based on ID
				include 'php/connection.php';
				$query = 'SELECT * FROM products WHERE ID = ' . $value . ' LIMIT 1';
				$result = mysqli_query($link, $query);
				$result_array = mysqli_fetch_array($result);

				//create the picture filename
				$pic_link = $value . '.jpg';


				//PRODUCT CONTAINER: one row per item
				echo '<div class="cart_prod_container">';

				//IMAGE
				echo '<div class="cart_image_container"><a href="product.php?id=' . $value . '"><img class="cart_image" src="inventory_images/' . $pic_link . '"></a></div>';
				
				//CART INFO: second row
				echo '<div class="cart_info">';

				//PRODUCT NAME
				echo '<a href="product.php?id=' . $value . '"><div class="cart_item_name">' . $result_array['NAME'] . '</div></a>';

				//PRODUCT TYPE
				echo '<div class="cart_item_type"><em>' . $result_array['TYPE'] . '</em></div>';

				
				
			}

			elseif ($key == 'quantity') {

				//use the same query the item based on ID, obtain quantity
				$prod_id = $_SESSION['cart_array'][$i -1]['item_id'];
				include 'php/connection.php';
				$query = 'SELECT * FROM products WHERE ID = ' . $prod_id . ' LIMIT 1';
				$result = mysqli_query($link, $query);
				$result_array = mysqli_fetch_array($result);

				//PRICE
				$item_price = number_format($result_array['PRICE'] * $value, 2);
				echo '<div class="cart_item_price">$' . $item_price . '</div>';

				//QUANTITY: create a drop down selection, with desired quantity preselected
				//update quantity_total variable
				$quantity_total += $value;
				echo '<form action="cart.php" method="post">';
				echo '<label for="quantity' . ($i-1) . '">Quantity: </label>';
				echo '<select name="quantity' . ($i-1) . '" class="quantity qty_dropdown">';
				for ($k = 1; $k <= $result_array['QUANTITY']; $k++) {
					if ($k == $value) {
						$selected = 'SELECTED = "selected"';
					}
					else {
						$selected = '';
					}
					echo '<option value = "' . $k . '" ' . $selected . ' >' . $k . '</option>';
				}		
				echo '</select>';

				//UPDATE QUANTITY BUTTON
				echo '
					<input type="hidden" value="' . ($i-1) . '" name="index_to_edit">
					<input class="delete submit" type="submit" value="Update" name="update_qty_' . $prod_id . '">
					</form>';


				//DELETE BUTTON
				echo '<span>
				<form action="cart.php" method="post">
					<input class="delete delete_x" type="image" value="Delete" src = "images/x.png" name="delete_button_' . $prod_id . '">
					<input type="hidden" value="' . ($i-1) . '" name="index_to_remove">
				</form></span>';




				echo '</div></div>'; //for cart_info and each product's row in the cart



				//CALCULATING SUBTOTAL:add to total amount
				$cart_total += $item_price;

				//LOOP THROUGH EACH ENTRY TO CONSTUCT PP BUTTON
				//index i should start at 1
				$pp_checkout .= '
					<input type = "hidden" name = "item_name_' . $i . '"value = "' . $result_array["NAME"] . '">
					<input type = "hidden" name = "quantity_' . $i . '" value = "' . $value . '">
					<input type = "hidden" name = "amount_' . $i . '" value = "' . $result_array["PRICE"] . '">';

				//product id- quantity pair, separated by a comma. to be used by IPN
				$prod_id_array .= $prod_id . "-" . $value . ",";

			}

		}

	}
	echo "<div class='clear'><a href='cart.php?clear=clear'>Empty</a></div>";
	echo '</div>';

	//FINISH PAYPAL CHECKOUT BUTTON
	$pp_checkout .= '
			<input type = "hidden" name = "custom" value = "' . $prod_id_array . '">
			<input type = "hidden" name = "return" value = "https://gentle-beyond-64568.herokuapp.com/success.php">
			<input type = "hidden" name = "cancel_return" value = "https://gentle-beyond-64568.herokuapp.com/cancel.php">
			<input type = "hidden" name = "notify_url" value = "https://gentle-beyond-64568.herokuapp.com/ipn2.php">
			<input type="hidden" name="charset" value="utf-8" /> 
			<input type = "hidden" name = "rm" value = "2">
			<input type = "hidden" name = "cbt" value = "Return to Homeroot Creations.">
			<input type = "hidden" name = "lc" value = "US">
			<input type = "hidden" name = "currency_code" value = "USD">


			<input type = "image" name = "submit" value = "Paypal" src = "https://www.paypalobjects.com/webstatic/en_US/btn/btn_checkout_pp_142x27.png" alt = "Make payments with PayPal.">
		</form>';

	mysqli_close($link);

	$subtotal = number_format($cart_total, 2);

	$total = $cart_total + $shipping;
	$total_display = number_format($total, 2);

	$_SESSION['subtotal'] = $subtotal;

	//CHECKOUT SUMMARY PANEL
	echo '<div class="checkout">';
	echo '<img class="checkout_logo" src="images/whitelogo.png">';
	echo '<div class="summary">Total Quantity: ' . $quantity_total . '</div>';

	echo "<div class='summary'>Subtotal: $" . $subtotal. '</div>';
	echo '<div class="summary">Shipping and Handling: $' . $shipping . '</div>';
	echo '<div class="summary">Total: $' . $total_display . '</div>';


	echo '<div class="pp_button">' . $pp_checkout . '</div>';
	echo '</div></div>';

}

include_once('footer.php');
?>
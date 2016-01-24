<?php

if (isset($_GET['id'])) {
	$id = $_GET['id'];
	include_once('php/connection.php');
	$query = 'SELECT * FROM products WHERE ID = ' . $id;
	$prod_results = mysqli_query($link, $query);
	$prod_array = mysqli_fetch_array($prod_results);
	mysqli_close($link);

	$title = $prod_array['NAME'];
	$extra_style = '<link rel="stylesheet" href="style/product-style.css">';
	include_once('header.php');

}
else {
	header('Location: shop.php');
	exit();
}
?>
<div class="prod_detail_container">
	<div class="detail_container"><img class="prod_detail" src="inventory_images/<?php echo $id . '.jpg'; ?>"></div>
	<span class="prod_detail_info">
		<div class="name"><?php echo $prod_array['NAME']; ?></div>
		<em><?php echo $prod_array['TYPE']; ?></em>
		<div class="price">$<?php echo $prod_array['PRICE']; ?></div>

		<div class="description"><?php echo $prod_array['DESCRIPTION']; ?></div>

		

		

<?php 
$quantity = $prod_array['QUANTITY'];
if ($quantity == 0) {
	echo '<p>Out of Stock!</p>';
	echo '</span></div>';

}
else {

	echo '<form id="cart" name="cart" method="post" action="cart.php">';
		echo '<select name="quantity" class="quantity">';
			for ($i = 1; $i <= $quantity; $i++) {
				echo '<option value="' . $i . '">' . $i . '</option>';
			}
		echo '</select>';
		echo '<input type="hidden" name="id" id="id" value="' . $id . '">';
		echo '<input type="submit" name="submit" class="submit" value="Add to Cart">';
	echo '</form></span></div>';

}

include_once('footer.php');
?>
<?php

session_start();
//if any type is specified with GET, run a certain query, else, print top six with a banner
$title = 'Online Shop';
$extra_style = '<link rel="stylesheet" href="style/product-style.css">';
include_once('header.php');
include_once("php/connection.php");

//CASE 1: NO GET VARIABLE POSTED, DISPLAY BANNER AND TOP SIX PRODUCTS
if (!isset($_GET['type'])) {

	$type_query = 'SELECT * FROM products ORDER BY added DESC LIMIT 8';
}

//CASE 2: ALL PRODUCTS
else if ($_GET['type'] == 'all') {
	$type_query = "SELECT * FROM products";

}

//CASE 3: GET VARIABLE POSTED, USE REQUIRED QUERY
else if (isset($_GET['type'])) {

	$type = $_GET['type'];
	$type = str_replace("_", " ", $type);
	$type_query = "SELECT * FROM products WHERE TYPE = '" . $type . "' ORDER BY added DESC";

}

$type_result = mysqli_query($link, $type_query);
$num_rows = mysqli_num_rows($type_result);

//begin outputting the page
echo '<div class="page">';
echo '<img class="logo" src="images/logo.jpg">';
echo '<div id="icon-wrapper">
		<nav id="prod-icon-wrapper">
			<a href="shop.php?type=Handcrafted_Soap"><img class="prod-icon" id = "bar" src="images/soap.png"></a>
			<a href="shop.php?type=salt"><img class="prod-icon" id = "salt" src="images/salt.png"></a>
			<a href="shop.php?type=Body_Scrub"><img class="prod-icon" id = "bubble" src="images/scrub.png"></a>
			<a href="shop.php?type=Body_Butter"><img class="prod-icon" id = "squeeze" src="images/butter.png"></a>
			<a href="shop.php?type=Candle"><img class="prod-icon" id = "candle" src="images/candle.png"></a>
	 	</nav>
	</div>';
echo '</div>';
echo '<div class="prod_container">';
echo '<div class="type_list">
<ul class="type_ul">
<li class="type_item" id="creations">Our Creations: </li>
<div class="line"></div>
<li class="type_item"><a href = "shop.php?type=all">All</a></li>
<li class="type_item"><a href = "shop.php?type=Handcrafted_Soap">Handcrafted Soaps</a></li>
<li class="type_item"><a href = "shop.php?type=Body_Butter">Home Whipped Body Butter</a></li><li>
<li class="type_item"><a href = "shop.php?type=candle">Scented Candles</a></li>
<li class="type_item"><a href = "shop.php?type=scrub">Body Scrub</a></li>
<li class="type_item"><a href = "shop.php?type=salt">Bath Salt</a></li>
</ul>
</div>';
echo '<div class="item_list">';
if ($num_rows > 0) {
	while ($new = mysqli_fetch_array($type_result)) {
		$id = $new['ID'];

		echo '<div class="prod_square">';
		
		//create the filename for the picture
		$pic_id = $id . '.jpg';
		echo '<a href="product.php?id=' . $id . '"><div class="prod_image_container"><img class="prod_thumb" src="inventory_images/' . $pic_id . '"></div>';
		echo '<div class="prod_info"><div class="prod_name">' . $new['NAME'] . '</div>';
		echo '<div class="prod_type">' . $new['TYPE'] . '</div>';
		echo '<div class="prod_price">$' . $new['PRICE'] . '</div>';

		echo '</div>';
		echo '</div></a>';
	}
	echo '</div>';
}

else {
	echo '<div class="excuse">Sorry, we are still creating these right now...</div>';
}
echo '</div></div>'; //end prod_container and item_list


?>
<br><br>

<?php
include_once('footer.php');
?>
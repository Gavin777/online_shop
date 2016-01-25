<?php 
session_start();
$title = 'Homeroot Creations';
$style = 'style/style.css';
$extra_style = '';
include 'header.php';

?>
<div class="rapper">
<img class='logo' src='images/logo.jpg'>

<div class="row_wrapper">
	<a href="about.php"><img class="row_item" src="images/house.png"></a>
	<a href="shop.php"><img class="row_item" src="images/cart.png"></a>
	<a href="ingredients.php"><img class="row_item" src="images/ingredients.png"></a>
</div>
</div>
<?php
include 'footer.php';
?>
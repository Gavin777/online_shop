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
	<img class="row_item" src="images/house.png">
	<img class="row_item" src="images/cart.png">
	<img class="row_item" src="images/ingredients.png">
</div>
</div>
<?php
include 'footer.php';
?>
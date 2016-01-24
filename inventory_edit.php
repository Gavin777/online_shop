<?php
session_start();
include 'check.php';

$title = 'Edit Product';
$extra_style = '';
include 'header.php';

//Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

//initialize variables

$id ='';
$name ='';
$price='';
$desc='';
$type='';
$quantity='';
$edit = date("Y-m-d"); 

//Prefill form with existing data

if (isset($_GET['prod_id'])) {
		$edit_prod = $_GET['prod_id'];
		include 'php/connection.php';
		$edit_query = 'SELECT * FROM products WHERE ID = "' . $edit_prod . '"';
		$edit_results = mysqli_query($link, $edit_query);
		$edit_array = mysqli_fetch_array($edit_results);
		mysqli_close($link);

		$id = $edit_array['ID'];
		$name = $edit_array['NAME'];
		$price = $edit_array['PRICE'];
		$desc = $edit_array['DESCRIPTION'];
		$type = $edit_array['TYPE'];
		$quantity = $edit_array['QUANTITY'];

	}

if (isset($_POST['submit'])) {

	include 'php/connection.php';
	$update_query = 'UPDATE products SET NAME = "' . $_POST['prod_name'] . '", PRICE = "' . $_POST['prod_price'] . '", DESCRIPTION = "' . $_POST['prod_desc'] . '", TYPE = "' . $_POST['prod_type'] . '", QUANTITY = "' . $_POST['prod_quantity'] . '", edit = "' . $edit . '" WHERE ID = ' . $_POST['prod_id'];
	$update_result = mysqli_query($link, $update_query);
	mysqli_close($link);

	if ($_FILES['prod_image']['tmp_name'] != '') {
		//move a file to the image folder only if the file is selected
		$new_name = $_POST['prod_id'] . '.jpg';
		move_uploaded_file($_FILES['prod_image']['tmp_name'], 'inventory_images/' . $new_name);

	}
	header('Location: inventory.php');
	exit();
}
?>

<form action = '<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>' enctype = 'multipart/form-data' name = 'edit_item' id = 'edit_item' method = 'post'>

		<input type='hidden' name='prod_id' id='prod_id' value='<?php echo $id; ?>'>

		<label for='prod_name'>Product Name: </label>
		<input type='text' name='prod_name' id='prod_name' value='<?php echo $name; ?>'>

		<label for='prod_price'>Product Price: </label>
		<input type='number' name='prod_price' id='prod_price' value='<?php echo $price; ?>'>

		<label for='prod_desc'>Product Description: </label>
		<textarea name='prod_desc' id='prod_desc'><?php echo $desc; ?></textarea>

		<label for='prod_type'>Product Type: </label>
		<select name = 'prod_type' id='prod_type'>
			<option value='<?php echo $type; ?>'><?php echo $type; ?></option>
			<option value='soap'>Handmade Soap</option>
			<option value='butter'>Body Butter</option>
			<option value='candle'>Candles</option>
			<option value='scrub'>Body Scrub</option>
			<option value='salt'>Bath Salt</option>
			<option value='gift'>Gift Boxes</option>
		</select>

		<label for='prod_quantity'>Product Quantity: </label>
		<input type='number' name='prod_quantity' id='prod_quantity' value='<?php echo $quantity; ?>'>

		<label for='prod_image'>Product Images: </label>
		<input type='file' name='prod_image' id='prod_image'>

		<input type='submit' value='Submit' name='submit'>
		<a href="inventory.php"><input type="button" value="Cancel"></a>
	</form>
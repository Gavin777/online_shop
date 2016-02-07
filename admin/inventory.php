<?php
session_start();

//Administrative Stuff
$title = 'Inventory';
$feedback = '';
$extra_style = '<link rel="stylesheet" href="../style/adminstyle.css">';
include 'adminheader.php';

//check that session manager is actually logged on
include 'check.php';

//Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

/////////////////////////////////////////////////////////
////////////////     Delete Product    //////////////////
/////////////////////////////////////////////////////////

if (isset($_GET['delete_id'])) {
	echo '<p>Do you really want to delete product with ID of ' . $_GET['delete_id'] . '? <a href="inventory.php?yes_delete=' . $_GET['delete_id'] . '">Yes</a> | <a href="inventory.php">No</a></p>';
	exit();
}

if (isset($_GET['yes_delete'])) {
	//remove item from system and delete its picture
	//delete from DB
	$pending_delete = $_GET['yes_delete'];
	include '../php/connection.php';
	$delete_query = 'DELETE FROM products WHERE id = "' . $pending_delete . '" LIMIT 1';
	$delete_result = mysqli_query($link, $delete_query) or die(mysqli_error($link));
	mysql_close($link);

	//unlink image from server
	$image_pending = ('inventory_images/' . $pending_delete . '.jpg');
	if (file_exists($image_pending)) {
		unlink($image_pending);
	}

	//reload the page
	header('Location: inventory.php');
	exit();
}

//////////////////////////////////////////////////////
///////////// NEW ITEM   /////////////////////////////
//////////////////////////////////////////////////////

if (isset($_GET['newitem'])) {

	echo '
	<a href ="inventory.php"><div class="black"></div></a>
	<p>' . $feedback . '</p>
	<div class="item_form">
		<a href="inventory.php"><img class="button x_item" src="../images/x.png"></a>
		<form action = "' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" enctype = "multipart/form-data" name = "new_item" id = "new_item" method = "post">
			<label for="prod_name">Product Name: </label>
			<input type="text" name="prod_name" id="prod_name" class="prod regular">

			<label for="prod_price">Product Price: </label>
			<input type="number" name="prod_price" id="prod_price" class="prod regular">

			<label for="prod_desc">Product Description: </label>
			<textarea name="prod_desc" id="prod_desc" class="prod prod_text"></textarea>

			<label for="prod_type">Product Type: </label>
			<select name = "prod_type" id="prod_type" class="prod regular">
				<option value="Handcrafted_Soap">Handcrafted Soap</option>
				<option value="Body_Butter">Body Butter</option>
				<option value="Soy_Candle">Candles</option>
				<option value="Body_Scrub">Body Scrub</option>
				<option value="Bath_Salt">Bath Salt</option>
				<option value="Gift_Box">Gift Boxes</option>
			</select>

			<label for="prod_quantity">Product Quantity: </label>
			<input type="number" name="prod_quantity" id="prod_quantity" class="prod regular">

			<label for="prod_image">Product Images: </label>
			<input type="file" name="prod_image" id="prod_image" class="prod regular">

			<input type="submit" value="submit" name="submit" class="prod regular">
			<a href="inventory.php"><input type="button" value="Cancel" id="prod_cancel" class="prod_edit"></a>
		</form>
	</div>';

}

//Parse the form data and add inventory item to the system
if (isset($_POST['submit'])) {

	include '../php/connection.php';

	//Upload POST values onto DB
	$name = mysqli_real_escape_string($link, $_POST['prod_name']);
	$price = mysqli_real_escape_string($link, $_POST['prod_price']);
	$descrip = mysqli_real_escape_string($link, $_POST['prod_desc']);
	$type = mysqli_real_escape_string($link, $_POST['prod_type']);
	$type = str_replace("_", " ", $type);
	$quantity = mysqli_real_escape_string($link, $_POST['prod_quantity']);
	$today = date("Y-m-d"); 

	//check if product name is identical match to another product

	$duplicate = 'SELECT * FROM products WHERE NAME = "' . $name . '" AND TYPE = "' . $type . '"';
	$dup_result = mysqli_query($link, $duplicate);
	$dup_num_rows = mysqli_num_rows($dup_result);
	mysqli_close($link);

	if ($dup_num_rows == 0) {
		//if there are no duplicates, add input into db
		include '../php/connection.php';
		$insert_query = 'INSERT INTO products(ID, NAME, PRICE, DESCRIPTION, TYPE, QUANTITY, added, edit) VALUES(NULL, "' . $name . '", "' . $price . '", "' . $descrip . '", "' . $type . '", "' . $quantity . '", "' . $today . '", "' . $today . '")';
		$insert_result = mysqli_query($link, $insert_query) or die(mysqli_error($link));
		
		//grabs the ID of the newly added product
		$pid = mysqli_insert_id($link);
		mysqli_close($link);

		$new_name = $pid . '.jpg';

		move_uploaded_file($_FILES['prod_image']['tmp_name'], '../inventory_images/' . $new_name);

		//refresh the page after you add a new item
		header('Location: inventory.php');
		
	}
	else {
		$feedback = 'The product, <em>' . $name . '</em>, already has an entry of the same type, <em>' . $type . '</em>. Please Try again.';
	}

}

//////////////////////////////////////////////////////////
///////////////  EDIT PRODUCT ////////////////////////////
//////////////////////////////////////////////////////////

if (isset($_GET['prod_id'])) {
	$edit_prod = $_GET['prod_id'];
	include '../php/connection.php';
	$edit_query = 'SELECT * FROM products WHERE ID = "' . $edit_prod . '"';
	$edit_results = mysqli_query($link, $edit_query);
	$edit_array = mysqli_fetch_array($edit_results);
	mysqli_close($link);

	//assign to local variables
	$edit_id = $edit_array['ID'];
	$edit_name = $edit_array['NAME'];
	$edit_price = $edit_array['PRICE'];
	$edit_desc = $edit_array['DESCRIPTION'];
	$edit_type = $edit_array['TYPE'];
	$edit_quantity = $edit_array['QUANTITY'];
	$edit_date = date("Y-m-d"); 

	echo "
	<a href ='inventory.php'><div class='black'></div></a>
	<form action = '" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' class='item_form' enctype = 'multipart/form-data' name = 'edit_item' id = 'edit_item' method = 'post'>
		<a href='inventory.php'><img class='button x_item' src='../images/x.png'></a>
		<input type='hidden' name='prod_id' id='prod_id' value='" . $edit_id . "'>

		<label for='prod_name'>Product Name: </label>
		<input type='text' name='prod_name' id='prod_name' class='prod regular' value='" . $edit_name . "'>

		<label for='prod_price'>Product Price: </label>
		<input type='number' name='prod_price' id='prod_price' class='prod regular' value='" . $edit_price . "'>

		<label for='prod_desc'>Product Description: </label>
		<textarea name='prod_desc' id='prod_desc' class='prod prod_text'>" . $edit_desc . "</textarea>

		<label for='prod_type'>Product Type: </label>
		<select name = 'prod_type' id='prod_type' class='prod regular'>
			<option value='" . $edit_type . "'>" . $edit_type . "</option>
			<option value='Handcrafted_Soap'>Handcrafted Soap</option>
			<option value='Body_Butter'>Body Butter</option>
			<option value='Soy_Candle'>Candles</option>
			<option value='Body_Scrub'>Body Scrub</option>
			<option value='Bath_Salt'>Bath Salt</option>
			<option value='Gift_Box'>Gift Boxes</option>
		</select>

		<label for='prod_quantity'>Product Quantity: </label>
		<input type='number' name='prod_quantity' id='prod_quantity' class='prod regular' value='" . $edit_quantity . "'>

		<label for='prod_image'>Product Images: </label>
		<input type='file' name='prod_image' id='prod_image'>

		<input type='submit' value='Submit' name='edit_submit' id='prod regular' class='prod_edit'>
		<a href='inventory.php'><input type='button' value='Cancel' id='prod_cancel' class='prod regular'></a>
	</form>";

}
//update product 
if (isset($_POST['edit_submit'])) {

	include '../php/connection.php';
	$_POST['prod_type'] = str_replace("_", " ", $_POST['prod_type']);



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

/////////////////////////////////////////////////////////
//////////////////////  DISPLAY TABLE ///////////////////
/////////////////////////////////////////////////////////

//execute query
include '../php/connection.php';
$product_query = 'SELECT * FROM products';
$product_results = mysqli_query($link, $product_query);
$product_num_rows = mysqli_num_rows($product_results);
mysqli_close($link);

//display table if products exist
if($product_num_rows > 0) {
	//display a table if exists data
	echo '<div class="admin_title">Quick Look Inventory List</div>';
	echo '<table id="quick_table" border="1">';
		echo '<tr>';
			echo '<th>ID</th>';
			echo '<th>Name</th>';
			echo '<th>Type</th>';
			echo '<th>Quantity</th>';
			echo '<th>Edit</th>';
			echo '<th>Delete</th>';
		echo '</tr>';


		while($object = mysqli_fetch_array($product_results)) {
			$id = $object['ID'];
			echo '<tr>';
				echo '<td>' . $object['ID'] . '</td>';
				echo '<td>' . $object['NAME'] . '</td>';
				echo '<td>' . $object['TYPE'] . '</td>';
				echo '<td>' . $object['QUANTITY'] . '</td>';
				echo '<td>' . '<a href="inventory.php?prod_id=' . $id . '"><input type="button" id= "edit' . $object['ID'] . '" name = "edit' . $object['ID'] . '" value = "Edit">' . '</td></a>';
				echo '<td>' . '<a href="inventory.php?delete_id=' . $id . '"><input type="button" id= "delete' . $object['ID'] . '" name = "delete' . $object['ID'] . '" value = "Delete">' . '</td></a>';
		}
	echo '</table>';
}

//error message if not
else {
	$feedback = 'You have no products listed yet';
}

echo '<a href="inventory.php?newitem=add"><div id="new_item_button">Add New Item</div></a>';

//display new item form if user clicks the button



?>


<?php
include '../footer.php';
?>
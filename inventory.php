<?php
session_start();

//Administrative Stuff
$title = 'Inventory';
$feedback = '';
$extra_style = '';
include 'header.php';

//check that session manager is actually logged on
include 'check.php';

//Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

//Welcome the user
echo 'Welcome, ' . $_SESSION['manager'] . '!'; 

//Delete Product
if (isset($_GET['delete_id'])) {
	echo '<p>Do you really want to delete product with ID of ' . $_GET['delete_id'] . '? <a href="inventory.php?yes_delete=' . $_GET['delete_id'] . '">Yes</a> | <a href="inventory.php">No</a></p>';
	exit();
}

if (isset($_GET['yes_delete'])) {
	//remove item from system and delete its picture
	//delete from DB
	$pending_delete = $_GET['yes_delete'];
	include 'php/connection.php';
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

//Parse the form data and add inventory item to the system
if (isset($_POST['submit'])) {

	//Upload POST values onto DB
	$name = mysqli_real_escape_string($link, $_POST['prod_name']);
	$price = mysqli_real_escape_string($link, $_POST['prod_price']);
	$descrip = mysqli_real_escape_string($link, $_POST['prod_desc']);
	$type = mysqli_real_escape_string($link, $_POST['prod_type']);
	$quantity = mysqli_real_escape_string($link, $_POST['prod_quantity']);
	$today = date("Y-m-d"); 

	//check if product name is identical match to another product
	include 'php/connection.php';
	$duplicate = 'SELECT * FROM products WHERE NAME = "' . $name . '" AND TYPE = "' . $type . '"';
	$dup_result = mysqli_query($link, $duplicate);
	$dup_num_rows = mysqli_num_rows($dup_result);
	mysqli_close($link);

	if ($dup_num_rows == 0) {
		//if there are no duplicates, add input into db
		include 'php/connection.php';
		$insert_query = 'INSERT INTO products(ID, NAME, PRICE, DESCRIPTION, TYPE, QUANTITY, added, edit) VALUES(NULL, "' . $name . '", "' . $price . '", "' . $descrip . '", "' . $type . '", "' . $quantity . '", "' . $today . '", "' . $today . '")';
		$insert_result = mysqli_query($link, $insert_query) or die(mysqli_error($link));
		
		//grabs the ID of the newly added product
		$pid = mysql_insert_id($link);
		mysqli_close($link);

		$new_name = $pid . '.jpg';

		move_uploaded_file($_FILES['prod_image']['tmp_name'], 'inventory_images/' . $new_name);

		//refresh the page after you add a new item
		header('Location: inventory.php');
		
	}
	else {
		$feedback = 'The product, <em>' . $name . '</em>, already has an entry of the same type, <em>' . $type . '</em>. Please Try again.';
	}

}

//product list and quantity in table
include 'php/connection.php';
$product_query = 'SELECT * FROM products';
$product_results = mysqli_query($link, $product_query);
$product_num_rows = mysqli_num_rows($product_results);
mysqli_close($link);

if($product_num_rows > 0) {
	//display a table if exists data
	echo '<h3 class="quick">Quick Look Inventory List</h3>';
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
				echo '<td>' . '<a href="inventory_edit.php?prod_id=' . $id . '"><input type="button" id= "edit' . $object['ID'] . '" name = "edit' . $object['ID'] . '" value = "Edit">' . '</td></a>';
				echo '<td>' . '<a href="inventory.php?delete_id=' . $id . '"><input type="button" id= "delete' . $object['ID'] . '" name = "delete' . $object['ID'] . '" value = "Delete">' . '</td></a>';
		}
	echo '</table>';
}
else {
	$feedback = 'You have no products listed yet';
}
?>

<div id='new_item'>Add New Item</div>
<p><?php echo $feedback; ?></p>
<div id='item_form'>
	<img class='button x' src='images/x.png'>
	<form action = '<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>' enctype = 'multipart/form-data' name = 'new_item' id = 'new_item' method = 'post'>
		<label for='prod_name'>Product Name: </label>
		<input type='text' name='prod_name' id='prod_name'>

		<label for='prod_price'>Product Price: </label>
		<input type='number' name='prod_price' id='prod_price'>

		<label for='prod_desc'>Product Description: </label>
		<textarea name='prod_desc' id='prod_desc'></textarea>

		<label for='prod_type'>Product Type: </label>
		<select name = 'prod_type' id='prod_type'>
			<option value='soap'>Handmade Soap</option>
			<option value='butter'>Body Butter</option>
			<option value='candle'>Candles</option>
			<option value='scrub'>Body Scrub</option>
			<option value='salt'>Bath Salt</option>
			<option value='gift'>Gift Boxes</option>
		</select>

		<label for='prod_quantity'>Product Quantity: </label>
		<input type='number' name='prod_quantity' id='prod_quantity'>

		<label for='prod_image'>Product Images: </label>
		<input type='file' name='prod_image' id='prod_image'>

		<input type='submit' value='submit' name='submit'>
	</form>
</div>
<?php
include 'footer.php';
?>
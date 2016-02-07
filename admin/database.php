<?php
//authentication
session_start();
include 'check.php';
include '../php/connection.php';
//administrative stuff
$title = 'Database';
$extra_style = '<link rel="stylesheet" href="../style/adminstyle.css">';
include 'adminheader.php';

//Delete Product
if (isset($_GET['delete_id'])) {
	echo '<p>Do you really want to delete product with ID of ' . $_GET['delete_id'] . '? <a href="database.php?yes_delete=' . $_GET['delete_id'] . '">Yes</a> | <a href="database.php">No</a></p>';
	exit();
}

//confirm delete
if (isset($_GET['yes_delete'])) {
	//remove item from system and delete its picture
	//delete from DB
	$pending_delete = $_GET['yes_delete'];
	$delete_query = 'DELETE FROM orderforms WHERE id = "' . $pending_delete . '" LIMIT 1';
	$delete_result = mysqli_query($link, $delete_query) or die(mysqli_error($link));

	//unlink image from server
	//$image_pending = ('images/$pending_delete.jpg');
	//if (file_exists($image_pending)) {
	//	unlink($image_pending);
	//}

	//reload the page
	header('Location: database.php');
	exit();
}

//feedback table
echo '<div class="admin_title">Feedback</div>';

$query = 'SELECT * FROM orderforms';
$display = mysqli_query($link, $query);
mysqli_close($link);

echo 
	'<table border="1">
		<tr>
			<th>Name</th>
			<th>Email</th>
			<th>Date</th>
			<th>Replied?</th>
			<th>View</th>
			<th>Delete?</th>
		</tr>';

while ($object = mysqli_fetch_array($display)) {

	echo '<tr>';
		echo '<td>' . $object['name'] . '</td>';
		echo '<td>' . $object['email'] . '</td>';
		echo '<td>' . $object['sent'] . '</td>';
		echo '<td>' . $object['replied'] . '</td>';
		echo '<td>' . '<a href="database.php?message_details=' . $object['ID'] . '"><input type="button" id= "details' . $object['ID'] . '" name = "details' . $object['ID'] . '" value = "Details">' . '</td></a>';
		echo '<td>' . '<a href="database.php?delete_id=' . $object['ID'] . '"><input type="button" id= "delete' . $object['ID'] . '" name = "delete' . $object['ID'] . '" value = "Delete">' . '</td></a>';
	echo '</tr>';

}

echo '</table>';

//to view message
if (isset($_GET['message_details'])) {

	$detail_num = $_GET['message_details'];
	$detail_query = 'SELECT * FROM orderforms WHERE ID = "' . $detail_num . '"';

	include'../php/connection.php';
	$detail_result = mysqli_query($link, $detail_query);

	$detail_array = mysqli_fetch_array($detail_result);
	mysqli_close($link);

	echo "<a href='database.php'><div class='black'></div></a>";
	echo "<div class='module'>";
	echo '<table class="module-table">
		<tr>
			<td>
				ID: 
			</td>
			<td>' . 
				$detail_array['ID'] . 
			'</td>
		</tr>
		<tr>
			<td>
				Name: 
			</td>
			<td>' . 
				$detail_array['name'] . 
			'</td>
		</tr>
		<tr>
			<td>
				Email: 
			</td>
			<td>' . 
				$detail_array['email'] . 
			'</td>
		</tr>
		<tr>
			<td>
				Message: 
			</td>
			<td>' . 
				$detail_array['feedback'] . 
			'</td>
		</tr>
		<tr>
			<td>
				Date: 
			</td>
			<td>' . 
				$detail_array['sent'] . 
			'</td>
		</tr>
		<tr>
			<td>
				Replied?
			</td>
			<td>' . 
				$detail_array['replied'] . 
			'</td>
		</tr>
	</table>'; 
	echo '</div>';
	
}


include '../footer.php';

?>

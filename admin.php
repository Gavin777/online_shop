<?php
session_start();

$title = 'Administration';
$extra_style = '';
include 'adminheader.php';

//check that manager session value exists
include 'check.php';
echo 'Welcome, ' . $_SESSION['manager'] . '!'; 
?>
<div id='admin'>
	<h3 id='adminh3'>Admin Panel</h3>
	<p><a href='database.php'>View Questions and Comments</a></p>
	<p><a href='inventory.php'>Manage Inventory</a></p>
	<p><a href='logout.php'>Logout</a></p>
</div>

<?php
include 'footer.php';
?>
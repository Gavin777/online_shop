<?php
session_start();

$title = 'Administration';
$extra_style = '<link rel="stylesheet" href="../style/adminstyle.css">';
include 'adminheader.php';

//check that manager session value exists
include 'check.php';
echo '<div class="welcome">Welcome, ' . $_SESSION['manager'] . '!</div>'; 
?>
<div id='admin'>
	<h3 id='adminh3'>Admin Panel</h3>
	<p><a href='database.php' class="admin_control">View Questions and Comments</a></p>
	<p><a href='inventory.php' class="admin_control">Manage Inventory</a></p>
	<p><a href ='transactions.php' class="admin_control">Transactions</a></p>
	<p><a href='logout.php' class="admin_control">Logout</a></p>

</div>

<?php
include '../footer.php';
?>
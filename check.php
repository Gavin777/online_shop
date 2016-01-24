<?php
//check that managr session value exists
if (!isset($_SESSION['manager'])) {
	header('Location: login.php');
	exit();
}

//check that manager session value is actually in DB
$user = preg_replace('#[^A-Za-z0-9]#i', '', $_SESSION['manager']);
$pass = preg_replace('#[^A-Za-z0-9]#i', '', $_SESSION['password']);

include 'php/connection.php';
$query = 'SELECT * FROM login WHERE username = ' . '\'' . $user . '\'' . ' AND pass = ' . '\'' . $pass . '\'';
$result = mysqli_query($link, $query);
$numrows = mysqli_num_rows($result);
mysqli_close($link);


if ($numrows == 0) {
	header('Location: login.php');
	exit();
}

?>
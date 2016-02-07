<?php
session_start();

//feedback is in case the user/pass is wrong
$feedback = '';

//administrative stuff
$title = 'Login';
$extra_style = '';
include 'header.php';


//if a session manager value already exists, go to admin page
//admin page will check if the values actually match
if (isset($_SESSION['manager'])) {
	header('Location: admin/admin.php');
	exit();
}

//checks that input matches data
if (isset($_POST['submit']) && isset($_POST['login']) && isset($_POST['password'])) {

	//sanitize input
	$user = preg_replace('#[^A-Za-z0-9]#i', '', $_POST['login']);
	$pass = preg_replace('#[^A-Za-z0-9]#i', '', $_POST['password']);

	include 'php/connection.php';
	$query = 'SELECT * FROM login WHERE username = ' . '\'' . $user . '\'' . ' AND pass = ' . '\'' . $pass . '\'';

	$result = mysqli_query($link, $query);
	$numrows = mysqli_num_rows($result);
	mysqli_close($link);
	
	if ($numrows == 0) {
		$feedback = "Get Outta Here!";
	}

	if ($numrows == 1) {
		$_SESSION['manager'] = $user;
		$_SESSION['password'] = $pass;
		header("Location: admin/admin.php");
		exit();
	}
}	

?>

<form action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>' id='login_form' method='post'>
	<div class='error'><?php echo $feedback; ?></div>
	<ul id = "login_list">
		<li class = "login_item">
			<div id='login'>Login:</div>
			<input type='text' name='login' id='login' />
		</li>
		<li class = "login_item">
			<div id='password'>Password: </div>
			<input type='password' name='password' id='password' />
		</li>
		<li class = "login_item">
			<input type='submit' id="login_submit" value='submit' name='submit'/>
		</li>
	</ul>
</form>

<?php
include 'footer.php';
?>
<?php
$title = "Order";
$extra_style = '';
include 'header.php';

$name_error = '';
$email_error = '';
$confirm_email_error = '';
$message_error = '';

$name = '';
$email = '';
$email2 = '';
$topic = '';
$message = '';
$today = date("Y-m-d"); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	//NAME
	if (empty($_POST["name"])) {
		$name_error = "Missing";
	}
	else {
		$name_error = '';
		$name = $_POST["name"];
	}

	//EMAIL
	$at = '@';
	$num_at = substr_count($_POST["email"], $at);

	if (empty($_POST["email"])) {
		$email_error = "Missing";
	}
	elseif ($num_at !== 1) {
		$email_error = "Please enter a valid email";
	}
	elseif ($_POST["email"] != $_POST["email2"]) {
		$email_error = "Emails do not match";
	}
	else {
		$name_error = '';
		$email = $_POST["email"];
	}

	//CONFIRM EMAIL
	if (empty($_POST["email2"])) {
		$confirm_email_error = "Missing";
	}
	elseif ($email == $_POST["email2"]) {
		$email2 = $_POST["email2"];
		}
		
	//MESSAGE
	if (empty($_POST["message"])) {
		$message_error = "Missing";
	}

	else {
		$message_error = '';
		$message = $_POST["message"];
	}

	if ($name_error == '' and $email_error == '' and $confirm_email_error =='' and $message_error == '') {
		include 'php/connection.php';
		$insert_query = "INSERT INTO orderforms(ID, name, email, products, placed, completed) VALUES(NULL, '$name', '$email', '$message', '$today', 'n')";
		$result = mysql_query($link, $insert_query);
		mysqli_close($link);

		header("Location: thankyou.php");
		
	}
}
echo '<a href="index.php">Home</a>';
?>

<form action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>' method="post">
	<ul>
		<li>
			<label for="name">Name:</label>
			<input type="text" name="name" id="name" value = '<?php echo htmlspecialchars($name); ?>' />
			<span class= 'error'><?php echo $name_error; ?></span>
		</li>
		<li>
			<label for="email">Email:</label>
			<input type="text" name="email" id="email" value = '<?php echo htmlspecialchars($email); ?>' />
			<span class= 'error'><?php echo $email_error; ?></span>
		</li>
		<li>
			<label for="email2">Confirm Email:</label>
			<input type="text" name="email2" id="email2" value = '<?php echo htmlspecialchars($email2); ?>'/>
			<span class= 'error'><?php echo $confirm_email_error; ?></span>
		</li>
		<li>
			<label for="topic">Topic: </label>
			<input type='radio' name='topic' value='other'> Other
			<input type='radio' name='topic' value='questions'> Questions
			<input type='radio' name='topic' value='comments'> Comments
		</li>
		<li>
			<label for="message">What's up?</label>
			<textarea id="message" name="message" cols="42" rows="9"><?php echo htmlspecialchars($message); ?></textarea>
			<span class= 'error'><?php echo $message_error; ?></span>
		</li>
		<li>
			<input type="submit" value="Submit!" />
		</li>
	</ul>
</form>

<?php
include 'footer.php';

?>
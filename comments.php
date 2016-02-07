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
		$confirm_email_error = "Emails do not match";
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
?>

<div class="landing">
	<div class="landing_title">Contact</div>
	<div class="landing_msg">
		How is your day? How do you like our soaps? If you have any questions, comments, or just want to chat, we'd love to hear from you! Just drop us a line!
	</div>
</div>




<form action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>' method="post" class="comment_form">

	<label for="name" class="comment">Name:</label>
	<input type="text" name="name" id="name" class="comments reg" value = '<?php echo htmlspecialchars($name); ?>' />
	<div class= 'error_msg'><?php echo $name_error; ?></div>

	<label for="email" class="comment">Email:</label>
	<input type="email" name="email" id="email" class="comments reg" value = '<?php echo htmlspecialchars($email); ?>' />
	<div class= 'error_msg'><?php echo $email_error; ?></div>

	<label for="email2" class="comment">Confirm Email:</label>
	<input type="email" name="email2" id="email2" class="comments reg" value = '<?php echo htmlspecialchars($email2); ?>'/>
	<div class= 'error_msg'><?php echo $confirm_email_error; ?></div>

	<label for="message" class="comment">What's up?</label>
	<textarea id="message" name="message" cols="42" class="comments" rows="9"><?php echo htmlspecialchars($message); ?></textarea>
	<div class= 'error_msg'><?php echo $message_error; ?></div>

	<input type="submit" value="Submit!" class="comments reg"/>

</form>


<?php
include 'footer.php';

?>
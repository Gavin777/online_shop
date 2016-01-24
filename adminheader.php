<html>
	<head>
		<title><?php echo $title; ?></title>
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />

		<!--CSS STYLES-->
		<link rel="stylesheet" href="style/style.css">
		<?php echo $extra_style; ?>
		<link href='https://fonts.googleapis.com/css?family=Roboto:300' rel='stylesheet' type='text/css'>

		<!--SCRIPT-->
		<script type="text/javascript"src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script type="text/javascript" scr="JS/jquery.waypoints.js"></script>
		<script type="text/javascript" src="JS/script.js"></script>
		<script type="text/javascript" src="JS/smoothscroll.js"></script>
	</head>
	<body>
		<!--navigation-->
		<div class="navigation">
			<div class="menu-div">
				<img class="menu" src="images/menu.png">

				<div id="white"></div>
				<div class="shopping-container">
					<a href="database.php"><div class="tool total">Feedback</div></a>
					<a href='inventory.php'><div class='tool shop'>Inventory</div></a>
					<a href='logout.php'><div class='tool cart'>Logout</div></a>
				</div>

			</div>
			<nav class="menu-container">
				<div class="x-div">
		 			<img class="x" src="images/x.png">
	 			</div>
				<ul>
					<a href="index.php">
						<li class="sidebar-item" id="sidebar-home">Home</li>
					</a>
					<a href="about.php">
						<li class="sidebar-item">About</li>
					</a>
					<a href="ingredients.php">
						<li class="sidebar-item">Ingredients</li>
					</a>
					<a href="shop.php">
						<li class="sidebar-item">Store</li>
					</a>
					<a href="comments.php">
						<li class="sidebar-item">Hello</li>
					</a>
				</ul>
			</nav>

		</div>
		
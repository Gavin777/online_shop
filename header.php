<html>
	<head>
		<title><?php echo $title; ?></title>
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />

		<!--CSS STYLES-->
		<link rel="stylesheet" href="style/style.css">
		<?php echo $extra_style; ?>
		<link href='https://fonts.googleapis.com/css?family=Roboto:300' rel='stylesheet' type='text/css'>

		<!--SCRIPT-->
		<script type="text/javascript"src="JS/jquery-1.11.1.js"></script>
		<script type="text/javascript" scr="JS/jquery.waypoints.js"></script>
		<script type="text/javascript" src="JS/smoothscroll.js"></script>
		<script type="text/javascript" src="JS/script.js"></script>
	</head>
	<body>
		<!--navigation-->
		<div class="navigation">
			<div class="menu-div">
				<img class="menu" src="images/menu.png">

				<div id="white"></div>
				<div class="shopping-container">
					<a href='shop.php'><div class='tool shop'>Shop</div></a>
					<a href='cart.php'><div class='tool cart'>Cart</div></a>
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
					<a href="comments.php">
						<li class="sidebar-item">Hello</li>
					</a>
				</ul>
				<div class="credits">Design by Jonathan Chiu. Copyright Homeroot Creations 2016.</div>
			</nav>


		</div>
		
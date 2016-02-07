<html>
	<head>
		<title><?php echo $title; ?></title>
		<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />

		<!--CSS STYLES-->
		<link rel="stylesheet" href="../style/style.css">
		<?php echo $extra_style; ?>
		<link href='https://fonts.googleapis.com/css?family=Roboto:300' rel='stylesheet' type='text/css'>

		<!--SCRIPT-->
		<script type="text/javascript"src="../JS/jquery-1.11.1.js"></script>
		<script type="text/javascript" scr="../JS/jquery.waypoints.js"></script>
		<script type="text/javascript" src="../JS/smoothscroll.js"></script>
		<script type="text/javascript" src="../JS/script.js"></script>
	</head>
	<body>
		<!--navigation-->
		<div class="navigation">
			<div class="menu-div">
				<img class="menu" src="../images/menu.png">
				<a href ="logout.php"><img class = "power" src="../images/power.png"></a>
			</div>
			<nav class="menu-container">
				<div class="x-div">
		 			<img class="x" src="../images/x.png">
	 			</div>
				<ul>
					<a href="../index.php">
						<li class="sidebar-item" id="sidebar-home">Home</li>
					</a>
					<a href="admin.php">
						<li class="sidebar-item" id="sidebar-home">Admin</li>
					</a>
					<a href="database.php">
						<li class="sidebar-item">Feedback</li>
					</a>
					<a href="inventory.php">
						<li class="sidebar-item">Inventory</li>
					</a>
					<a href="transactions.php">
						<li class="sidebar-item">Transactions</li>
					</a>
				</ul>
			</nav>

		</div>
		
<?php 
session_start();
require('connection.php');
 ?>
<html>
<head>
	<title>The Wall - Login</title>
	<link rel="stylesheet" type="text/css" href="login.css">
</head>
<body>
<div id="outerouter">
	<h1>The Wall</h1>
	<div id="container">
		<p class='error'>
		<?php 
			if(isset($_SESSION['login_error']))
			{
				echo $_SESSION['login_error'];
				unset ($_SESSION['login_error']);
			}
		 ?>
		 </p>
		<h2>Please log in to view The Wall</h2>
		<form action="process.php" method="post">
			<input type="hidden" name="action_login" value="login">
			<div id="input1">
			Email:<input type="text" name="email" placeholder="Enter Email">
			</div>
			<div id="input2">
			Password:<input type="password" name="password" placeholder="Enter Password">
			</div>
			<input type="submit" value="Login">
		</form>
		<p>Not a member? <a href="process.php?register=1">Register Here</a></p>
		<p>View as guest? <a href="process.php?guest=1">Click Here!</a></p>
	</div>
</div>
</body>
</html>

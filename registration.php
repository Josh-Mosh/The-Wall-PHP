<?php 
session_start();
require('connection.php');
 ?>
<html>
<head>
	<title>Registration - The Wall</title>
	<link rel="stylesheet" type="text/css" href="registration.css">
</head>
<body>
	<div id="container">
		<?php 
			if(isset($_SESSION['error']))
			{
				foreach($_SESSION['error'] as $message)
				{
					echo "<p class='error'>{$message}</p>";
				}
			}
			elseif(isset($_SESSION['success']))
				{
				echo $_SESSION['success'];
				}
		 ?>
		<h2>Please register to continue</h2>
		<form action='process.php' method='post'>
			<input type="hidden" name="action" value="register">
			First Name: <input type="text" name="first_name" placeholder="First Name">
			Last Name: <input type="text" name="last_name" placeholder="Last Name">
			Email: <input type="text" name="email" placeholder="Email">
			Password: <input type="password" name="password" placeholder="Password">
			Confirm Password:<input type="password" name="confirm_password" placeholder="Confirm Password">
			<input type="submit" value="Register">
		</form>

		<p>Already a member? <a href='process.php?login_home=1'>Login Here</a></p>
	</div>
</body>
</html>
<?php 
$_SESSION=array();
 ?>
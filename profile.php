<?php 
session_start();
require('connection.php');
?>

<html>
<head>
	<title>Profile - The Wall</title>
	<link rel="stylesheet" type="text/css" href="the_wall.css">
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.js"></script>
</head>
<body>
	<div id="header">
		<h1>CodingDojo Wall</h1>
		<div id="header_right">
		<?php 
			if(isset($_SESSION['user_id']))
			{
			$name = "SELECT id, first_name, last_name
					 FROM users 
					 WHERE id=".$_SESSION['user_id'];
			$row = fetch_record($name);
			echo "<h4>Hello {$row['first_name']} {$row['last_name']}</h4>";
			echo "<a href='process.php?logout=1'>Logout</a>";
			}
			else
			{
				echo "<h4>Hello Guest</h4>
					  <a href='process.php?login_home=1'> Login </a> |
					  <a href='process.php?register=1'> Register </a>";
			}
		 ?>
		</div>
	</div>
	<div id="container">
		<h2>Post a message</h2>
		
		<div id='errors'>
			<?php 
				if(isset($_SESSION['error']['empty']))
				{
					echo $_SESSION['error']['empty'];
					unset ($_SESSION['error']['empty']);
				}
				elseif(isset($_SESSION['error']['member']))
				{
					echo $_SESSION['error']['member'];
					unset ($_SESSION['error']['member']);
				}
				elseif(isset($_SESSION['error']['comment']))
				{
					?>
					<script>
						$(document).ready(function(){
						alert('Sorry, comment can not be blank');
						});
					</script>
					<?php
					// echo "<p class='error'>{$_SESSION['error']['comment']}</p>";
					unset ($_SESSION['error']['comment']);
				}
				elseif (isset($_SESSION['error']['c_member']))
				{
					?>
					<script>
						$(document).ready(function(){
						alert('Sorry, you must be logged in to comment');
						});
					</script>
					<?php
					// echo "<p class='error'>{$_SESSION['error']['c_member']}</p>";
					unset($_SESSION['error']['c_member']);
				}
			 ?>
		</div>

		<form action="process.php" method="post">
			<input type="hidden" name="action_post" value="wall_post">
			<textarea class="message_textarea" name="message" placeholder="Post your message here"></textarea>
			<input id="post_message" type="submit" value="Post your message">
		</form>

		<?php 
			$query2 = "SELECT messages.id, messages.message, messages.users_id, users.first_name, users.last_name, messages.created_at
					   FROM messages LEFT JOIN users ON users.id=messages.users_id
					   ORDER BY messages.created_at DESC";

			$messages = fetch_all($query2);

			foreach($messages as $message)
			{

				date_default_timezone_set('America/Los_Angeles');
				$posted_time = strtotime($message['created_at'], $now=time());
				$message_date = date('m/d/y', $posted_time);
				$message_time = date('g:i a', strtotime($message['created_at'], $now=time()));

				echo "<div class='message'><h3>  {$message['first_name']} {$message['last_name']}  </h3>
				<p class='message_format'> {$message['message']}</p>
				<i>Posted on: </i><h6> {$message_date} </h6><i> at: </i><h6> {$message_time} </h6></div>";

				$minutes_passed = (time() - $posted_time) / 60;

				if($minutes_passed < 30)
				{
					if($row['id'] == $message['users_id'])
					{
					echo "<form action='process.php' method='post'>
							<input type='hidden' name='action_delete' value='delete_post'>
							<input type='hidden' name='msg_id' value='{$message['id']}'>
							<input class='delete_button' type='submit' value='Delete Your Message'>
						</form>";
					}
				}

				$query_comment = "SELECT comments.comment, comments.messages_id, messages.id, users.first_name, users.last_name, comments.created_at
								  FROM comments LEFT JOIN users ON users.id=comments.users_id
								  LEFT JOIN messages ON messages.id=comments.messages_id
								  WHERE comments.messages_id = messages.id";

				$comments = fetch_all($query_comment);

				foreach ($comments as $comment)
				{
					if ($comment['messages_id'] == $message['id'])
					{
					$comment_date = date('m/d/y', strtotime($comment['created_at'], $now=time()));
					echo "<div class='comment'><h5>  {$comment['first_name']} {$comment['last_name']}  </h5>
						 <p class='comment_format'> {$comment['comment']}</p>
						 <i> on: </i><h6> {$comment_date} </h6></div>";
					}
				}

				echo "<div><form class='comment_form' action='process.php' method='post'>
						<input type='hidden' name='action_comment' value='comment'>
						<input type='hidden' name='messageid' value='{$message['id']}'>
						<textarea class='comment_box' name='comment' placeholder='Post your comment here'></textarea>
						<input class='post_comment' type='submit' value='Comment'>
				  	 </form></div>";
			}
		?>
	</div>
</body>
</html>
<?php
session_start();
require('connection.php');

/////// REGISTRATION //////////

function register($post)
{
	///////// validations ///////////
	foreach ($post as $name => $value) 
	{
		if(empty($value))
		{
			$_SESSION['error'][$name] = "sorry, " . $name . " cannot be blank";
		}
		else
		{
			switch ($name) 
			{
				case 'first_name':
				case 'last_name':
					if(is_numeric($post['first_name']) || is_numeric($post['last_name']))
					{
						$_SESSION['error'][$name] = "Name cannot contain numbers";
					}
				break;
				case 'email':
					if(!filter_var($value, FILTER_VALIDATE_EMAIL))
					{
						$_SESSION['error'][$name] = $value . " is not a valid email";
					}
				break;
				case 'password':
					$password = $value;
				break;
				case 'confirm_password':
					if($password != $value)
					{
						$_SESSION['error'][$name] = 'Passwords do not match <br>';
					}
				break;
			}
		}
	}

	///////// success //////////
	if(!isset($_SESSION['error']))
	{
		$_SESSION['success'] = "You have successfully registered!";
			///////// encrypt password //////////
		$salt = bin2hex(openssl_random_pseudo_bytes(22)); 
		$hash = crypt($post['password'], $salt);

		$first_name = mysqli_real_escape_string($post['first_name']);
		$last_name = mysqli_real_escape_string($post['last_name']);
		$email = mysqli_real_escape_string($post['email']);
		$query = "INSERT INTO users (first_name, last_name, email, password, created_at, updated_at)
				  VALUES ('{$first_name}', '{$last_name}', '{$email}', '{$hash}', NOW(), NOW())";
		///// insert into database /////
		run_mysql_query($query);
	}

header('location: registration.php');
exit;
}

//////// LOGIN ////////////

function login($post)
{
	if(empty($post['email']) || empty($post['password']))
	{
		$_SESSION['login_error'] = "Email or Password cannot be blank.";
	}
	else
	{
		$query = "SELECT id, password
				  FROM users
				  WHERE email= '{$post['email']}'";
		$row = fetch_record($query);
		if(empty($row))
		{
			$_SESSION['login_error'] = "Could not find email address in database";
		}
		else
		{
			if(crypt($post['password'], $row['password']) != $row['password'])
			{
				$_SESSION['login_error'] = 'Incorrect Password';
			}
			else
			{
				$_SESSION['user_id'] = $row['id'];
				header('location: profile.php');
				exit;
			}
		}
	}
	header('location: index.php');
	exit;
}

/////////// LOGOUT ///////////

function logout($get)
{
	$_SESSION = array();
	session_destroy();
	header('location: index.php');
	exit;
}

////////// post message to wall function //////////

function post_to_wall($post)
{	
	if(empty($post['message']))
	{
		$_SESSION['error']['empty'] = "Sorry, message can not be blank";

		header('location: profile.php');
		exit;
	}
	elseif(!isset($_SESSION['user_id']))
	{
		$_SESSION['error']['member'] = "You must be logged in to post a message";
		header('location: profile.php');
		exit;
	}
	else
	{
		$msg = $post['message'];

		$query = "INSERT INTO messages (message, created_at, updated_at, users_id)
				  VALUES ('{$msg}', NOW(), NOW(), {$_SESSION['user_id']})";
		$row = run_mysql_query($query);
		
		header('location: profile.php');
	exit;
	}
}

///////// post comment function ////////////

function comment($post)
{
	if(empty($post['comment']))
	{
		$_SESSION['error']['comment'] = "Sorry, comment can not be blank";
		header('location: profile.php');
		exit;
	}
	elseif (!isset($_SESSION['user_id']))
	{
		$_SESSION['error']['c_member'] = "You must be logged in to post a comment";
		header('location: profile.php');
		exit;
	}
	else
	{
		$query = "INSERT INTO comments (comment, created_at, updated_at, messages_id, users_id)
				  VALUES ('{$post['comment']}', NOW(), NOW(), {$post['messageid']}, {$_SESSION['user_id']})";
		$row = run_mysql_query($query);

		header('location: profile.php');
		exit;
	}
}

/////// delete message //////////

function delete($post)
{
	$query = "DELETE FROM messages
			  WHERE id={$post['msg_id']}";
	$row = run_mysql_query($query);

	header('location: profile.php');
	exit;
}

////// registration link on login home page //////////

if(isset($_GET['register']))
{
	header('location: registration.php');
	exit;
}

////// guest link on login page ///////////

if(isset($_GET['guest']))
{
	header('location: profile.php');
	exit;
}

////// login link on registration page ///////////

if(isset($_GET['login_home']))
{
	header('location: index.php');
	exit;
}

///////// CALLING FUNCTIONS ////////////

//////// register function ////////////
if(isset($_POST['action']) && $_POST['action'] == 'register')
{
	register($_POST);
}

///////// login function ////////////
if(isset($_POST['action_login']) && $_POST['action_login'] == 'login')
{
	login($_POST);
}

//////// logout function /////////////
if(isset($_GET['logout']))
{
	logout($_GET);
}

//////// post to wall function ////////
if(isset($_POST['action_post']) && $_POST['action_post'] == 'wall_post')
{
	post_to_wall($_POST);
}

//////// comment function /////////7//
if(isset($_POST['action_comment']) && $_POST['action_comment'] == 'comment')
{
	comment($_POST);
}

//////// delete function //////////////
if(isset($_POST['action_delete']) && $_POST['action_delete'] == 'delete_post')
{
	delete($_POST);
}

 ?>
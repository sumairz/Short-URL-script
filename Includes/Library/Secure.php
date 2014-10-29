<?php

class encryptpass
{

	//function to encrypt the password
	function qtel_password_protection($pass_string)
	{

		$pass_string = md5($pass_string);
		return $pass_string;
	}

}

class login
{


	// User Login
	function loginUser($user,$pass)
	{
		//session_start(); // Registering the Login Session
		$errorText = '';
		$validUser = false;
		//require_once("Includes/Config/Config.php");
		global $AdminUser;
		global $AdminPswd;
		global $BackendUsers;
		// Check user existance	
		foreach($BackendUsers as $loginUser)
		{
			if ($loginUser['username'] == $user) 
			{
				// User exists, check password
				/*$encrytpwdcheck = new encryptpass();
				$userpasscheck = $encrytpwdcheck->qtel_password_protection($pass);*/
				if (trim($loginUser['password']) == $pass)
				{
					$validUser= true;
					$_SESSION['userName'] = $loginUser['name'];
					$_SESSION['type'] = $loginUser['type'];
					$_SESSION['adminId'] = $loginUser['id'];
				}
				
			}
		}

		if ($validUser != true) $errorText = "OMG! This is an Invalid Username or Password!";
		
		if ($validUser == true) $_SESSION['validUser'] = true;
		else $_SESSION['validUser'] = false;
		
		return $errorText;	
	}



	// User logout
	function logoutUser()
	{
		// To Get rid of the Logout errors
		session_start(); // Registering the Login Session
		unset($_SESSION['validUser']);
		unset($_SESSION['userName']);
	}



	// Check for Duplicate Usernmae
	function checkUser()
	{	//session_start(); // Registering the Login Session
		if ((!isset($_SESSION['validUser'])) || ($_SESSION['validUser'] != true))
		{
			echo "<script>window.location = 'Login.php';</script>";
			//header('Location: Login.php'); // After Logging out it will be redirected to Login.php
			exit(); // To avoid Headers laredy sent problem
			
		}
		
	}
}

?>
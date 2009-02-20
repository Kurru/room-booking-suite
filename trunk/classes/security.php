<?php
include_once("classes/database.php");
class security
{
	function logged_in ()
	{
		$level = security::get_level('');
		if ($level > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	} // end of function 'logged_in'
	
	function get_level($check)
	{
		if (!empty($_SESSION['SESSIONusername']) AND !empty($_SESSION['SESSIONlevel']) AND !empty($_SESSION['SESSIONpassword']))
		{
			$random = rand(0,9);
			if ($random == 1 AND $check != 1)
			{
				// if the random number is 1, and check is currently not set to check, then set to check
				$check = 1;
			}
			if($check == 1)
			{
				$username = $_SESSION['SESSIONusername'];
				$userlevel = $_SESSION['SESSIONlevel'];
				$passwordResults = database::checkUserPassword($username,$userlevel);
				
				if (count($passwordResults) == 1)
				{	
					$entry = $passwordResults[0];
					
					if ($_SESSION['SESSIONpassword'] == $entry['password'])
					{
						return $_SESSION['SESSIONlevel'];
					}
					else 
					{
						security::sign_out();
						return 0;
					}
				}
				else
				{
					// no user found with the username and security level
					security::sign_out();
					return 0;
				}
			}
			else
			{
				// not to check, just output the current level as stored in the session data
				return $_SESSION['SESSIONlevel'];	
			}
		}
		else
		{
			// username or password or level is not set, so not logged in.
			return 0;
		}
	}	// end function getLevel()
	
	function sign_in($username,$password)
	{
		$entry = database::sign_on_Request($username,$password);
		if (count($entry) == 1)
		{
			// then there is one user with a valid username and password
			// so now add username, password and level to the session variables
			$_SESSION['SESSIONusername'] = $username;
			$_SESSION['SESSIONpassword'] = $password;
			$_SESSION['SESSIONlevel'] = $entry[0]['level'];
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function generatePassword($originalPassword)
	{
		//echo md5($originalPassword);
		return md5($originalPassword);
	}
	function sign_out()
	{
		session_unset();
		session_destroy();
		unset($_SESSION);
	
	}
	
}	// end class security
?>
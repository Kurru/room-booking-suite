<center>
<?php

$section = raw_param('section');
$confirmation = raw_param('confirmation');
if (empty($section))
{
	echo "<h1>Account Options</h1>\n";
	
	echo "<a href=\"?page=Account&section=changepassword\">Change Password</a><br />\n";
}
elseif ($section == 'changepassword') // change password
{
	if ($confirmation != true) // display forms
	{
		echo "<h1>Change Password</h1>\n";
		echo "Enter your new password below.\n<br /><br />\n";
		echo "<table>\n";
		echo "<form action=\"?page=Account&section=changepassword&confirmation=1\" method='post'>\n";
		echo "\t<tr><td class='alright'>New Password : </td><td><input type='password' name='password1' /></td></tr>\n";
		echo "\t<tr><td class='alright'>And Again : </td><td><input type='password' name='password2' /></td></tr>\n";
		echo "\t<tr><td>&nbsp;</td></tr>\n";
		echo "\t<tr><td colspan='2'><center><input type='submit' value='Change Password' /></center></td></tr>\n";
		echo "</form>\n";
		echo "</table>\n";
	}
	else // save new password and update logon details
	{
		if (security::get_level('1') >= 1) // user logged in
		{
			if (raw_param_post('password1') == raw_param_post('password2')) // entered passwords are the same. so save
			{
				$user_level = security::get_level('');
				$user = $_SESSION['SESSIONusername'];
				$password = security::generatePassword(raw_param_post('password1'));
				
				$query = "UPDATE bookingusers SET password='$password' WHERE username='$user' AND level='$user_level'";
				database::executeQuery($query);
				$_SESSION['SESSIONpassword'] = $password;
				echo "Password saved successfully.<br />\n";
				echo "<a href=\"?page=Account&p=pword\">Return to Account Settings</a>\n";
			}
			else
			{
				echo "Entered Passwords are not the same. <a href=\"javascript:history.back();\">Go Back</a> and edit.\n";
			}
		}
		else
		{
			echo "You are not logged in.\n";
		}
	}
}
elseif ($g['p'] == '')
{
}
elseif ($g['p'] == '')
{
}
elseif ($g['p'] == '')
{	
}
elseif ($g['p'] == '')
{
}
else
{
}
?>
</center>

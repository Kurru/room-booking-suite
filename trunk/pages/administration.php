<center>
<?php
$section = $_GET['section'];
$confirmation = $_GET['confirmation'];

if (security::get_level('1') >= 2) // does user have enough permissions to view these options
{
	if (empty($section)) // list links for main admin page
	{
		echo "<h1>Administration</h1>\n";
		echo "Use the links below to interact with the system.\n<br /><br />\n";
		echo "<table>\n";

		echo "\t<tr>\n";
		echo "\t\t<td class='alleft alignTop' colspan='3'>\n";
		echo "\t\t\t<fieldset>\n";
		echo "\t\t\t<legend>System</legend>\n";
		echo "\t\t\t<ul>\n";
		echo "\t\t\t<li><a href='?page=Administration&section=BookingsTable'>View The Overall Booking's Table</a></li>\n";
		echo "\t\t\t</ul>\n";
		echo "\t\t\t</fieldset>\n";
		echo "\t\t</td>\n";
		echo "\t</tr>\n";

		echo "\t<tr><td colspan='2' class='alcenter'></td></tr>\n";
		echo "\t<tr><td>&nbsp;</td></tr>\n";
		echo "\t<tr>\n";
		echo "\t\t<td class='alleft alignTop'>\n";
		echo "\t\t\t<fieldset>\n";
		echo "\t\t\t<legend>User Settings</legend>\n";
		echo "\t\t\t<ul>\n";
		echo "\t\t\t<li><a href=\"?page=Administration&section=aduser\">Add New User</a></li>\n";
		echo "\t\t\t<li><a href=\"?page=Administration&section=edituserlevel\">Edit User's Level</a></li>\n";
		echo "\t\t\t<li><a href='?page=Administration&section=edituserpass'>Edit User's Password</a></li>\n";
		echo "\t\t\t<li><a href='?page=Administration&section=edituserbookinglimit'>Edit User's Booking Limit</a></li>\n";
		echo "\t\t\t<li><a href='?page=Administration&section=editusername'>Edit User's Username</a></li>\n";
		echo "\t\t\t<li><a href=\"?page=Administration&section=deleteUser\">Delete Existing User</a></li>\n";
		echo "\t\t\t</ul>\n";
		echo "\t\t\t</fieldset>\n";
		echo "\t\t</td>\n";
		echo "\t\t<td class='alleft alignTop'>\n";
		echo "\t\t\t<fieldset>\n";
		echo "\t\t\t<legend>System Settings</legend>\n";
		echo "\t\t\t<ul>\n";
		echo "\t\t\t<li><a href='?page=Administration&section=editsettings'>Settings</a></li>\n";
		echo "\t\t\t<li><a href='?page=Administration&section=editareas'>Areas/Rooms</a></li>\n";
		echo "\t\t\t<li><a href='?page=Administration&section=editperiods'>Periods</a></li>\n";
		echo "\t\t\t<li><a href='?page=Administration&section=editsubjects'>Subjects</a></li>\n";
		echo "\t\t\t<li><a href='?page=Administration&section=editclasses'>Classes</a></li>\n";
		echo "\t\t\t<li><a href='?page=Administration&section=editreasons'>Booking Reasons</a></li>\n";
		echo "\t\t\t<br /><br />\n";
		echo "\t\t\t<li><a href=\"?page=Administration&section=resetyear\">Delete All Bookings &amp; Notes</a></li>\n";
		echo "\t\t\t</ul>\n";
		echo "\t\t\t</fieldset>\n";
		echo "\t\t</td>\n";
		echo "\t\t<td class='alleft alignTop'>\n";
		echo "\t\t\t<fieldset>\n";
		echo "\t\t\t<legend>Data Export/Import</legend>\n";
		echo "\t\t\t<ul>\n";
		echo "\t\t\t<li><a href='?page=Administration&section=bookingcsv'>Export Booking Data as CSV</a></li>\n";
		echo "\t\t\t<li><a href='?page=Administration&section=backupsystem'>Backup System Data</a></li>\n";
		echo "\t\t\t<li><a href='?page=Administration&section=restoresystem'>Restore System Data</a></li>\n";
		echo "\t\t\t</ul>\n";
		echo "\t\t\t</fieldset>\n";
		echo "\t\t</td>\n";
		echo "\t</tr>\n";
		echo "</table>\n";
	}
	elseif ($section == 'aduser') // add user page
	{
		echo "<h1>Add New User</h1>\n";
		if ($confirmation != true) // show form
		{
			echo "Use the below form to add a user to the system.\n<br /><br />\n";
			echo "<form action=\"?page=Administration&section=aduser&confirmation=1\" method='post'>\n";
			echo "<table>\n";
			echo "\t<tr><td class='alright'>Username : </td><td><input type='text' name='user' /></td></tr>\n";
			echo "\t<tr><td class='alright'>Password : </td><td><input type='text' name='pass' /></td></tr>\n";
			echo "\t<tr><td class='alright'>Level : </td><td><select name='level' onChange='if(this.value==2) {alert(\"This option should be used only for allowing full access to the system!!\")}'><option value='1'>Teacher</option><option value='2'>Administator</option></select></td></tr>\n";
			echo "\t<tr><td colspan='2'><center><input type='reset' value='Clear' /><input type='submit' value='Add User' /></center></td></tr>\n";
			echo "</table>\n";
			echo "</form>\n";
		}
		else // add user
		{
			$user = addslashes(raw_param_post('user'));
			$level = addslashes(raw_param_post('level'));
			$pass = raw_param_post('pass');
			$pass2 = security::generatePassword($pass);
			$query = "SELECT * FROM bookingusers WHERE username='$user'";
			$result = database::executeQuery($query);
	
			if (count($result) == 0) // username does not exist. insert it
			{
				$query = "INSERT INTO bookingusers (username,password,level) VALUES ('$user','$pass2','$level')";
				database::executeQuery($query);
				echo "User has been added to the system. With password '$pass'.\n<br /><br />\n";
				echo "<a href=\"?page=Administration\">Return to Administration page</a>\n";
			}
			else // username exists.
			{
				echo "Username is already used.\n<br />\n<a href='javascript:history.back()'>Go Back</a> and <b>try another username</b>.\n";
			}
		}
	}
	elseif ($section == 'edituserlevel')
	{
			echo "<h1>Edit User Level</h1>";
		if ($confirmation != true) // display form
		{
			$query = "SELECT * FROM bookingusers ORDER BY username";
			$users = database::executeQuery($query);
?>
<script type="application/javascript">

function checkSubmit()
{
	var form = document.forms['editForm'];
	var username = form.elements['username'].value;
	var level = form.elements['level'].value;
	
	if (username == 'null' || level == '0')
	{
	}
	else
	{
		form.submit();
	}

}


</script>
<?PHP
			echo "Select from the menu below, which user's level you want to change.\n";
			echo "<br /><br />\n";
			echo "<form action=\"?page=Administration&section=edituserlevel&confirmation=1\" id='editForm' method='post'>\n";
			echo "<table>\n";
			echo "\t<tr>\n\t\t<td>Username :</td>\n\t\t<td>\n\t\t\t<select name='username' id='username'>\n";
			echo "\t\t\t\t<option value='null'>---Select User---</option>\n";
			foreach ($users as $index => $user)
			{
				echo "\t\t\t\t<option value='".$user['username']."'>".$user['username']."</option>\n";
			};
			echo "\t\t\t</select>\n";
			echo "\t\t</td>\n\t</tr>\n";
			echo "\t<tr>\n\t\t<td>\n";
			echo "\t\t\tLevel: \n";
			echo "\t\t</td>\n";
			echo "\t\t<td>\n\t\t\t<select name='level' id='level' onChange='if(this.value==2) {alert(\"This option should be used only for allowing full access to the system!!\")}'>\n\t\t\t\t<option value='0'>&nbsp;</option>\n\t\t\t\t<option value='1'>Normal User</option>\n\t\t\t\t<option value='2'>Administator</option>\n\t\t\t</select>\n\t\t</td>\n\t</tr>\n";
			echo "\t<tr>\n\t\t<td colspan='2'>\n";
			echo "\t\t\t<center><input type='button' value='Save Edit' onclick='checkSubmit();'/></center>\n";
			echo "\t\t</td>\n\t</tr>\n</table>\n";
			echo "</form>\n";
		}
		else // remove user
		{
			$user = raw_param_post('username');
			$level = raw_param_post('level');
			if (($level == 1 || $level == 2) && ($user != 'null' && $user != ''))
			{
				$query = "UPDATE bookingusers SET level='$level' WHERE username='$user' LIMIT 1";
				$result = database::executeQuery($query);
				if ($level == 2)
				{
					$levelT = "an Administator";
				}
				elseif ($level == 1)
				{
					$levelT = "a Normal User";
				}
				echo "User '".$user."' now has privilages of ".$levelT."\n";
			}
			else
			{
				echo "Not valid data.\n";
			}
			echo "<br /><br />\n";
			echo "<a href='?page=Administration'>Return To Admin</a>\n";
		}
	
	}
	elseif ($section == 'editusername')
	{
		echo "<h2>Edit Username</h2>\n";
		if ($confirmation != true)
		{	// display text
			$query = "SELECT * FROM bookingusers ORDER BY username";
			$users = database::executeQuery($query);
			
			?>
<script type="text/javascript">
<!--
var DataList = new Array();
<?php
foreach ($users as $index => $user)
{
	echo "DataList[''+'".$user['username']."'] = '".$user['username']."';\n";
}
?>
-->
</script>
			<?php
			echo "<form id=\"selectAndEdit\" name=\"selectAndEdit\" method=\"post\" action=\"?page=Administration&section=editusername&confirmation=1\">\n";
			echo "<table>\n\t<tr>\n\t\t<td colspan=\"2\">\n";
			
			echo "\t\t\tSelect the user to edit: \n\t\t\t<select name=\"editList\" id=\"editList\">\n";
			echo "\t\t\t\t<option value=\"NULL\">--Select Username--</option>\n";
			foreach($users as $index => $user)
			{
				echo "\t\t\t\t<option value=\"".$user['username']."\">".$user['username']."</option>\n";
			}
			echo "\t\t\t</select>\n";
			echo "\t\t</td>\n\t</tr>\n\t<tr>\n";
			echo "\t\t<td><input type=\"hidden\" name=\"dataid\" id=\"dataid\" value=\"NULL\" /><input type='hidden' id='secret' value='NULL' />Current Username: </td>\n";
			echo "\t\t<td><input name=\"oldDATA\" disabled=\"disabled\" id=\"oldDATA\" type=\"text\" /></td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n";
			echo "\t\t<td>New Username:</td>\n";
			echo "\t\t<td><input name=\"newDATA\" id=\"newDATA\" type=\"text\" /></td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n\t\t<td colspan=\"2\" class=\"alcenter\">\n";
			echo "\t\t<input type=\"submit\" id=\"button\" value=\"Save After Every Edit\"/>\n";
			echo "\t\t</td>\n";
			echo "</table>\n";
			echo "</form>\n";
		}
		else
		{
			$newUserName = trim(raw_param_post('newDATA'));
			$oldUserName = trim(raw_param_post('oldDATA'));
			$username = raw_param_post('dataid');
			
			
			if (empty($newUserName) && $username == 'NULL')
			{
				// if new name is blank and the id is 'NULL' then do nothing
				echo "Nothing changed so nothing saved.\n";
			}
			elseif (!empty($newUserName) && $username != 'NULL')
			{
				$query = "SELECT * FROM bookingusers WHERE username='$newUserName'";
				$result = database::executeQuery($query);
				if (count($result)==0)
				{
					// update the current username
					$query = "UPDATE bookingusers SET username='$newUserName' WHERE username='$username'";
					database::executeQuery($query);
					$query = "UPDATE bookingsingle SET username='$newUserName' WHERE username='$username'";
					database::executeQuery($query);
					$query = "UPDATE bookingpermanent SET username='$newUserName' WHERE username='$username'";
					database::executeQuery($query);
					echo "The user has been renamed.\n";
				}
				else
				{
					echo "There exists a user by this name already, therefore this username can not be changed to the new value.\n";
				}
			}
			else
			{
				echo "Nothing changed so nothing saved.\n";
			}
			echo "<br />\n";
			echo "<a href='?page=Administration&section=editusername'>Continue Editing Users</a>.\n";
		}
	}
	elseif ($section == 'resetyear') // check password // check level then delete all bookings after confirmation screen
	{
		echo "<h1>Delete All Bookings &amp; Notes</h1>\n";
		if ($confirmation != true) // logon
		{
			echo "Enter your password below to confirm your identity.\n<br />\n";
			echo "<form action=\"?page=Administration&section=resetyear&confirmation=1\" method=\"post\">\n";
			echo "\tPassword : <input type='password' name='pass' />\n";
			echo "\t<br />\n";
			echo "\t<input type='submit' value='Send Password' />\n";
			echo "</form>\n";
		}
		elseif ($_GET['confirmation2']  == true)
		{
			$query = "DELETE FROM bookingpermanent";
			database::executeQuery($query);
			$query = "DELETE FROM bookingsingle";
			database::executeQuery($query);
			$query = "DELETE FROM bookingnotes";
			database::executeQuery($query);
			echo "All bookings and notes have been wiped from the system.\n<br /><br />\n";
			echo "<a href='?'>Return to the front page</a>\n";
		}
		else // form  sent. check password
		{
			$pass = md5(raw_param_post('pass'));
			if ($_SESSION['SESSIONpassword'] == $pass)
			{
				echo "Password correct.\n<br /><br />\n";
				echo "Are you sure you wish to clear all the bookings and calender notes?\n<br />\n";
				echo "Think carefully as this is unreversable.\n";
				echo "<br /><br /><br />\n";
				echo "<b>To clear all bookings, <a href='?page=Administration&section=resetyear&confirmation=1&confirmation2=1'>Click Here</a></b>\n";
				echo "<br /><br /><br />\n";
				echo "To cancel, <a href='?page=Administration'>Click Here</a>.\n";
			}
			else
			{
				echo "<b>Password Check Failed</b>\n";
				echo "<br /><a href='javascript:history.back();'>Go Back</a>\n";
			}
		}
	}
	elseif ($section == 'edituserpass') // change a user's password
	{
		echo "<h1>Edit User's Password</h1>";
		if ($confirmation != true) // display form
		{
			// get teacher list
			$query = "SELECT * FROM bookingusers ORDER BY username";
			$result = database::executeQuery($query);
			echo "Use the form below to change a user's password.\n";
			echo "<br /><br />\n";
			echo "<table>\n";
			echo "<form action='?page=Administration&section=edituserpass&confirmation=1' method='post'>\n";
			echo "\t<tr>\n\t\t<td class='alright'>";
			echo "Select username : ";
			echo "</td>\n\t\t<td>\n";
			echo "\t\t\t<select name='user'>\n";
			echo "\t\t\t\t<option>----Select Username----</option>\n";
			foreach ($result as $key => $teacher)
			{
				echo "\t\t\t\t<option value=\"".$teacher['username']."\">".$teacher['username']."</option>\n";
			};
			echo "\t\t\t</select>\n";
			echo "\t\t</td>\n\t</tr>\n";
			echo "\t<tr>\n\t\t<td class='alright'>";
			echo "New Password : ";
			echo "</td>\n\t\t<td>";
			echo "<input type='password' name='newpass' size='24' />";
			echo "</td>\n\t</tr>\n";
			echo "\t<tr>\n\t\t<td colspan='2' class='alcenter'>";
			echo "<input type='submit' value='Change Password' />";
			echo "</td>\n\t</tr>\n";
			echo "</form>\n";
			echo "</table>\n";
		}
		else // save form
		{
			$user = raw_param_post('user');
			$pass = md5(raw_param_post('newpass'));
			$query = "UPDATE bookingusers SET password='$pass' WHERE username='$user'";
			$result = database::executeQuery($query);
			echo $user."'s password has been changed.\n";
			echo "<br /><br />\n";
			echo "<a href='?page=Administration'>Return To Administration</a>\n";
		}
	}
	elseif ($section == 'edituserbookinglimit') // change a user's password
	{
		echo "<h1>Edit User's Booking Limit Offset</h1>";
		if ($confirmation != true) // display form
		{
			// get teacher list
			$query = "SELECT * FROM bookingusers ORDER BY username";
			$result = database::executeQuery($query);
			?>
<script type="text/javascript">
<!--
var offset = new Array();
var teacher = new Array();
<?PHP
			foreach ($result as $teacher)
			{
				echo "teacher[teacher.length] = '".$teacher['username']."';\n";
				echo "offset[offset.length] = '".$teacher['maxBookingOffset']."';\n";
			}
			?>
function setTeacherOffset(username)
{
	var offsetText = document.getElementById('bookingOffset');
	var i=0;
	for (i=0;i<teacher.length;i++)
	{
		if (teacher[i] == username)
		{
			break;
		}
	}
		
	if (username == '----Select Username----' || username=='')
	{
		offsetText.value = '';
	}
	else
	{
		offsetText.value = offset[i];
	}
}
-->
</script>
<?PHP
			echo "Use the form below to change a user's booking limit offset.\n";
			echo "<br /><br />\n";
			echo "<table>\n";
			echo "<form action='?page=Administration&section=edituserbookinglimit&confirmation=1' method='post'>\n";
			echo "\t<tr>\n\t\t<td class='alright'>";
			echo "Current Basic Value : </td>\n";
			echo "\t\t<td class='b'>".settings::get_numberOfBookingsPerYear();
			echo "</td>\n\t</tr>\n";
			echo "\t<tr>\n\t\t<td class='alright'>";
			echo "Select username : ";
			echo "</td>\n\t\t<td>\n";
			echo "\t\t\t<select name='user' onchange='setTeacherOffset(this.value);'>\n";
			echo "\t\t\t\t<option>----Select Username----</option>\n";
			foreach ($result as $key => $teacher)
			{
				echo "\t\t\t\t<option value=\"".$teacher['username']."\">".$teacher['username']."</option>\n";
			};
			echo "\t\t\t</select>\n";
			echo "\t\t</td>\n\t</tr>\n";
			echo "\t<tr>\n\t\t<td class='alright'>";
			echo "Booking Offset : ";
			echo "</td>\n\t\t<td>";
			echo "<input type='text' name='bookingOffset' id='bookingOffset' size='24' />";
			echo "</td>\n\t</tr>\n";
			echo "\t<tr>\n\t\t<td class='alcenter i' colspan='2'>";
			echo "Booking allowance offset can be positive or negative.<br />";
			echo "To calculate the maximum number of bookings for this user, add the 'Current Basic Value' and 'booking offset' value.";
			echo "</td>\n\t</tr>\n";
			echo "\t<tr>\n\t\t<td colspan='2' class='alcenter'>";
			echo "<input type='submit' value='Save Current Offset' />";
			echo "</td>\n\t</tr>\n";
			echo "</form>\n";
			echo "</table>\n";
		}
		else // save form
		{
			$user = raw_param_post('user');
			$maxBookingOffset = raw_param_post('bookingOffset');
			if (empty($maxBookingOffset))
			{
				$maxBookingOffset = 0;
			}
			$query = "UPDATE bookingusers SET maxBookingOffset='$maxBookingOffset' WHERE username='$user'";
			$result = database::executeQuery($query);
			echo $user."'s booking limit has been changed to ".$maxBookingOffset.".\n";
			echo "<br /><br />\n";
			echo "<a href='?page=Administration'>Return To Administration</a>\n";
		}
	}
	elseif ($section == 'deleteUser')
	{
		echo "<h1>Delete User</h1>";
		if ($confirmation != true) // display form
		{
			echo "Select from the menu below, what user you wish to remove from the system.\n";
			echo "<br /><br />\n";
			echo "To undo, you will have to add the user into the system again.\n";
			echo "<br /><br />\n";
			$query = "SELECT * FROM bookingusers ORDER BY username";
			$result = database::executeQuery($query);
			echo "<form action=\"?page=Administration&section=deleteUser&confirmation=1\" method='post'>\n";
			echo "\tUsername : <select name='deluser'>\n";
			echo "\t\t<option value=''>---Select User---</option>\n";
			foreach ($result as $index => $teacher)
			{
				echo "\t\t<option value='".$teacher['username']."'>".$teacher['username']."</option>\n";
			};
			echo "\t</select>\n";
			echo "\t<br /><br /><br />\n";
			echo "\t<b>If you are sure</b> you wish to delete this user, press 'Delete User' below.\n";
			echo "\t<br /><br /><br />\n";
			echo "\t<input type='submit' value='Delete User' />\n";
			echo "</form>\n";
		}
		else // remove user
		{
			$user = raw_param_post('deluser');
			$query = "DELETE FROM bookingusers WHERE username='$user' LIMIT 1";
			$result = database::executeQuery($query);
			echo "User has been deleted from the system.\n";
			echo "<br /><br />\n";
			echo "Username : <b>".$user."</b>\n";
			echo "<br /><br />\n";
			echo "<a href='?page=Administration'>Return To Admin</a>\n";
		}
	}
	elseif ($section == 'BookingsTable')
	{
		$year1 = raw_param('year');
		$currentYear = date("Y");
		$currentMonth = date("n");

		if($currentMonth >= 8)
		{
			$current = array($currentYear,$currentYear+1);
		}
		else
		{
			$current = array($currentYear-1,$currentYear);
		}
		if (ereg('^[0-9]{8}$',$year1))
		{
			$displayedYear = array(substr($year1,0,4),substr($year1,4,4));
		}
		else
		{
			$displayedYear = $current;
		}


		// calculate the values for the previous year and the next year
		$previousYear = array($displayedYear[0]-1,$displayedYear[1]-1);
		$nextYear = array($displayedYear[0]+1,$displayedYear[1]+1);
		
		// calculate the very first year for the system based on the lowest value for the schoolYear tab
		$query = "SELECT min(schoolYear) FROM bookingsingle";
		$result1 = database::executeQuery($query);
		$query = "SELECT min(schoolYear) FROM bookingpermanent";
		$result2 = database::executeQuery($query);
		if ($result1[0]['min(schoolYear)'] == '' && $result2[0]['min(schoolYear)'] == '')
		{
			$firstYear = $current;
		}
		elseif ($result1[0]['min(schoolYear)'] == '')
		{
			$firstYear = $result2[0]['min(schoolYear)'];
		}
		elseif ($result2[0]['min(schoolYear)'] == '')
		{
			$firstYear = $result1[0]['min(schoolYear)'];
		}
		else
		{
			$firstYear = min($result1[0]['min(schoolYear)'],$result2[0]['min(schoolYear)']);
		}
		$temp = $firstYear;
		$firstYear = array(substr($temp,0,4),substr($temp,4,4));

		$displayedYearText = "".$displayedYear[0].$displayedYear[1];
		
		// assemble all the bookings data into a multi-dimensional array
		$query = "SELECT * FROM bookingsingle WHERE schoolYear='$displayedYearText'";
		$result = database::executeQuery($query);
		$data = array();
		foreach ($result as $index => $booking)
		{
			if (!isset($data[$booking['username']][$booking['class']]['single']))
			{
				$data[$booking['username']][$booking['class']]['single'] = 1;
			}
			else 
			{
				$data[$booking['username']][$booking['class']]['single'] ++;
			}
		};
		$query = "SELECT * FROM bookingpermanent WHERE schoolYear='$displayedYearText'";
		$result = database::executeQuery($query);
		foreach ($result as $index => $booking)
		{
			if (!isset($data[$booking['username']][$booking['class']]['weekly']))
			{
				$data[$booking['username']][$booking['class']]['weekly'] = 1;
			}
			else 
			{
				$data[$booking['username']][$booking['class']]['weekly'] ++;
			}
		};

		// display the data
		echo "<h1>Total Bookings Table</h1>\n";
		echo "Below is a table detailing the number of bookings that have been made by each teacher and for what classes over the School Year <span class='b'>{$displayedYear[0]}/{$displayedYear[1]}</span>.\n<br /><br />\n";

		// display the links for previous year and next year
		echo "<span style='float:left;'>\n";
		if ($firstYear[0] <= $previousYear[0])
		{
			$text = "".$previousYear[0].$previousYear[1];
			echo "\t<a href='?page=Administration&section=BookingsTable&year=".$text."'>".$previousYear[0]."-".$previousYear[1]."</a>\n";
		}
		else
		{
			echo $previousYear[0]."-".$previousYear[1];
		}
		echo "</span>\n";
		echo "<span style='float:right;'>\n";

		if ($current[0] >= $nextYear[0])
		{
			$text = "".$nextYear[0].$nextYear[1];
			echo "\t<a href='?page=Administration&section=BookingsTable&year=".$text."'>".$nextYear[0]."-".$nextYear[1]."</a>\n";
		}
		else
		{
			echo $nextYear[0]."-".$nextYear[1];
		}
		echo "</span>\n";
		echo "<br /><br />\n";
		echo "<table width='100%' class='centertable bookings'>\n";
		
		if (count($data) > 0)
		{
			echo "\t<tr>\n\t\t<td rowspan='2' class='attention'>Username</td>\n\t\t<td rowspan='2' class='attention'>Class</td>\n\t\t<td colspan='2' class='attention'>Booking Type</td>\n\t\t<td rowspan='2' class='attention'>Total</td>\n\t</tr>\n";
			echo "\t<tr>\n\t\t<td class='bold'>Single</td>\n\t\t<td class='bold'>Weekly</td>\n\t</tr>\n";
			echo "\t<tr>\n\t\t<td colspan='5'>&nbsp;</td>\n\t</tr>\n";
			ksort($data);
			foreach ($data as $username => $value)
			{
				$totalweekly = 0;
				$totalsingle = 0;
				$overalltotal = 0;
				$counter = 1;
				foreach ($value as $class => $value2)
				{
					if (empty($value2['single']))
					{
						if (!empty($value2['weekly']))
						{
							$counter++;
						}
					}
					else
					{
						$counter++;
					}
					if (!empty($value2['weekly']))
					{
						$totalweekly = $totalweekly + $value2['weekly'];
					}
					if (!empty($value2['single']))
					{
						$totalsingle = $totalsingle + $value2['single'];
					}
				};
				$overalltotal = $totalweekly + $totalsingle;
				echo "\t<tr>\n\t\t<td rowspan='$counter' class='bold'>$username</td>\n\t\t<td class='bold'>TOTAL</td>\n\t\t<td class='bold'>$totalsingle</td>\n\t\t<td class='bold'>$totalweekly</td>\n\t\t<td class='bold'>$overalltotal</td>\n\t</tr>\n";
				ksort($value);
				foreach ($value as $class => $value2)
				{
					$single = $value2['single'];
					$weekly = $value2['weekly'];
					if ($single == '') {$single = 0;}
					if ($weekly == '') {$weekly = 0;}
					$total = $single + $weekly;
					echo "\t<tr>\n\t\t<td>$class</td>\n\t\t<td>$single</td>\n\t\t<td>$weekly</td>\n\t\t<td>$total</td>\n\t</tr>\n";
				};
				
				
				echo "\t<tr>\n\t\t<td colspan='5'>&nbsp;</td>\n\t</tr>\n";
	
	//			echo "<tr><td>$type</td><td>$single</td><td>$weekly</td><td>$total</td></tr>";
			};
		}
		else
		{
			echo "\t<tr>\n\t\t<td><i>No Bookings in system.</i></td>\n\t</tr>\n";
		}
		echo "</table>\n";
	}
	elseif ($section == 'editclasses')
	{
		echo "<h2>Edit Classes</h2>\n";
		if ($confirmation != true)
		{	// display text
			$query = "SELECT * FROM bookingclasses ORDER BY class";
			$classes = database::executeQuery($query);
			
			?>
			
<script type="text/javascript">
<!--
var DataList = new Array();
<?php
foreach ($classes as $index => $class)
{
	echo "DataList[''+'".$class['id']."'] = '".$class['class']."';\n";
}
?>
-->
</script>
			<?php
			echo "<form id=\"selectAndEdit\" name=\"selectAndEdit\" method=\"post\" action=\"?page=Administration&section=editclasses&confirmation=1\">\n";
			echo "<table>\n\t<tr>\n\t\t<td colspan=\"2\">\n";
			
			echo "\t\t\tSelect the class to edit: \n\t\t\t<select name=\"editList\" id=\"editList\">\n";
			echo "\t\t\t\t<option value=\"NULL\">NEW</option>\n";
			foreach($classes as $index => $class)
			{
				echo "\t\t\t\t<option value=\"".$class['id']."\">".$class['class']."</option>\n";
			}
			echo "\t\t\t</select>\n";
			echo "\t\t</td>\n\t</tr>\n\t<tr>\n";
			echo "\t\t<td><input type=\"hidden\" name=\"dataid\" id=\"dataid\" value=\"NULL\" />Current Name: </td>\n";
			echo "\t\t<td><input name=\"oldDATA\" disabled=\"disabled\" id=\"oldDATA\" type=\"text\" /></td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n";
			echo "\t\t<td>New Name:</td>\n";
			echo "\t\t<td><input name=\"newDATA\" id=\"newDATA\" type=\"text\" /></td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n\t\t<td colspan=\"2\">\n";
			echo "\t\t<center><input type=\"submit\" id=\"button\" value=\"Save After Every Edit\"/></center>\n";
			echo "\t\t</td>\n";
			echo "\t<tr>\n\t\t<td colspan=\"2\">\n";
			echo "\t\t<center><i>To delete, clear the 'new name' and save.</i></center>\n";
			echo "\t\t</td>\n";
			echo "\t</tr>\n";
			echo "</table>\n";
			echo "</form>\n";
		}
		else
		{
			$newClassName = raw_param_post('newDATA');
			$oldClassName = raw_param_post('oldDATA');
			$idClass = raw_param_post('dataid');
			
			
			if (empty($newClassName) && $idClass == 'NULL')
			{
				// if new name is blank and the id is 'NULL' then do nothing
				echo "Nothing changed so nothing saved.\n";
			}
			elseif (!empty($newClassName) && $idClass == 'NULL')
			{
				// there is a new class typed so save it
				$query = "INSERT INTO bookingclasses (class) VALUES ('$newClassName')";
				database::executeQuery($query);
				echo "Class has been added to the system.\n";
			}
			elseif (!empty($newClassName) && $idClass != 'NULL')
			{
				// update the current class
				$query = "UPDATE bookingclasses SET class='$newClassName' WHERE id='$idClass'";
				database::executeQuery($query);
				echo "The class has been renamed.\n";
			}
			elseif (empty($newClassName) && $idClass != 'NULL')
			{
				$query = "DELETE FROM bookingclasses WHERE id='$idClass' LIMIT 1";
				database::executeQuery($query);
				echo "The class has been removed from the system.<br /><br />\n User's will now be unable to make any more bookings for this class.\n";
			}
			
			echo "<br />\n";
			echo "<a href='?page=Administration&section=editclasses'>Continue Editing Classes</a>.\n";
		}
	}	
	elseif ($section == 'editsubjects')
	{
		echo "<h2>Edit Subjects</h2>\n";
		if ($confirmation != true)
		{	// display text
			$query = "SELECT * FROM bookingsubjects ORDER BY subject";
			$subjects = database::executeQuery($query);
			
			?>
			
<script type="text/javascript">
<!--
var DataList = new Array();
<?php
foreach ($subjects as $index => $subject)
{
	echo "DataList[''+'".$subject['id']."'] = '".$subject['subject']."';\n";
}
?>
-->
</script>
			<?php
			echo "<form id=\"selectAndEdit\" name=\"selectAndEdit\" method=\"post\" action=\"?page=Administration&section=editsubjects&confirmation=1\">\n";
			echo "<table>\n\t<tr>\n\t\t<td colspan=\"2\">\n";
			
			echo "\t\t\tSelect the subject to edit: \n\t\t\t<select name=\"editList\" id=\"editList\">\n";
			echo "\t\t\t\t<option value=\"NULL\">NEW</option>\n";
			foreach($subjects as $index => $subject)
			{
				echo "\t\t\t\t<option value=\"".$subject['id']."\">".$subject['subject']."</option>\n";
			}
			echo "\t\t\t</select>\n";
			echo "\t\t</td>\n\t</tr>\n\t<tr>\n";
			echo "\t\t<td><input type=\"hidden\" name=\"dataid\" id=\"dataid\" value=\"NULL\" />Current Subject Name: </td>\n";
			echo "\t\t<td><input name=\"oldDATA\" disabled=\"disabled\" id=\"oldDATA\" type=\"text\" /></td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n";
			echo "\t\t<td>New Subject Name:</td>\n";
			echo "\t\t<td><input name=\"newDATA\" id=\"newDATA\" type=\"text\" /></td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n\t\t<td colspan=\"2\">\n";
			echo "\t\t<center><input type=\"submit\" id=\"button\" value=\"Save After Every Edit\"/></center>\n";
			echo "\t\t</td>\n";
			echo "\t<tr>\n\t\t<td colspan=\"2\">\n";
			echo "\t\t<center><i>To delete, clear the 'new name' and save.</i></center>\n";
			echo "\t\t</td>\n";
			echo "\t</tr>\n";
			echo "</table>\n";
			echo "</form>\n";
		}
		else
		{
			$newSubjectName = trim(raw_param_post('newDATA'));
			$oldSubjectName = trim(raw_param_post('oldDATA'));
			$idSubject = raw_param_post('dataid');
			
			
			if (empty($newSubjectName) && $idSubject == 'NULL')
			{
				// if new name is blank and the id is 'NULL' then do nothing
				echo "Nothing changed so nothing saved.\n";
			}
			elseif (!empty($newSubjectName) && $idSubject == 'NULL')
			{
				// there is a new subject typed so save it
				$query = "INSERT INTO bookingsubjects (subject) VALUES ('$newSubjectName')";
				database::executeQuery($query);
				echo "Subject has been added to the system.\n";
			}
			elseif (!empty($newSubjectName) && $idSubject != 'NULL')
			{
				// update the current subject
				$query = "UPDATE bookingsubjects SET subject='$newSubjectName' WHERE id='$idSubject'";
				database::executeQuery($query);
				echo "The subject has been renamed.\n";
			}
			elseif (empty($newSubjectName) && $idSubject != 'NULL')
			{
				// delete the current subject as it is currently blanked out
				$query = "DELETE FROM bookingsubjects WHERE id='$idSubject' LIMIT 1";
				database::executeQuery($query);
				echo "The subject has been removed from the system.<br /><br />\n User's will now be unable to make any more bookings for this subject.\n";
			}
			
			echo "<br />\n";
			echo "<a href='?page=Administration&section=editsubjects'>Continue Editing Subjects</a>.\n";
		}
	}
	elseif ($section == 'editareas')
	{
		echo "<h2>Edit Areas/Rooms</h2>\n";
		if ($confirmation != true)
		{	// display text
			$query = "SELECT * FROM bookingareas ORDER BY name";
			$areas = database::executeQuery($query);
			
			?>
			
<script type="text/javascript">
<!--
var DataList = new Array();
<?php
foreach ($areas as $index => $area)
{
	echo "DataList[''+'".$area['id']."'] = '".$area['name']."';\n";
}
?>
-->
</script>
<?php
			echo "<form id=\"selectAndEdit\" name=\"selectAndEdit\" method=\"post\" action=\"?page=Administration&section=editareas&confirmation=1\">\n";
			echo "<table>\n\t<tr>\n\t\t<td colspan=\"2\">\n";
			
			echo "\t\t\tSelect the area to edit: \n\t\t\t<select name=\"editList\" id=\"editList\">\n";
			echo "\t\t\t\t<option value=\"NULL\">NEW</option>\n";
			foreach($areas as $index => $area)
			{
				echo "\t\t\t\t<option value=\"".$area['id']."\">".$area['name']."</option>\n";
			}
			echo "\t\t\t</select>\n";
			echo "\t\t</td>\n\t</tr>\n\t<tr>\n";
			echo "\t\t<td><input type=\"hidden\" name=\"dataid\" id=\"dataid\" value=\"NULL\" />Current Area Name: </td>\n";
			echo "\t\t<td><input name=\"oldDATA\" disabled=\"disabled\" id=\"oldDATA\" type=\"text\" /></td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n";
			echo "\t\t<td>New Area Name:</td>\n";
			echo "\t\t<td><input name=\"newDATA\" id=\"newDATA\" type=\"text\" /></td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n\t\t<td colspan=\"2\">\n";
			echo "\t\t<center><input type=\"submit\" id=\"submitButton\" value=\"Save After Every Edit\"/></center>\n";
			echo "\t\t</td>\n";
			echo "\t<tr>\n\t\t<td colspan=\"2\">\n";
			echo "\t\t<center><i>To delete, clear the 'new name' and save. <br />This will delete all the bookings for the room as well.</i></center>\n";
			echo "\t\t</td>\n";
			echo "\t</tr>\n";
			echo "</table>\n";
			echo "</form>\n";
		}
		else
		{
			$newAreaName = trim(raw_param_post('newDATA'));
			$oldAreaName = trim(raw_param_post('oldDATA'));
			$idArea = raw_param_post('dataid');
			
			
			if (empty($newAreaName) && $idArea == 'NULL')
			{
				// if new name is blank and the id is 'NULL' then do nothing
				echo "Nothing changed so nothing saved.\n";
			}
			elseif (!empty($newAreaName) && $idArea == 'NULL')
			{
				// there is a new area typed so save it
				$query = "INSERT INTO bookingareas (name) VALUES ('$newAreaName')";
				database::executeQuery($query);
				echo "Area '".$newAreaName."' has been added to the system.\n";
			}
			elseif (!empty($newAreaName) && $idArea != 'NULL')
			{
				// update the current area
				$query = "UPDATE bookingareas SET name='$newAreaName' WHERE id='$idArea'";
				database::executeQuery($query);
				echo "The area has been renamed to '".$newAreaName."'.\n";
			}
			elseif (empty($newAreaName) && $idArea != 'NULL')
			{
				// delete the current area as it is currently blanked out
				$query = "DELETE FROM bookingareas WHERE id='$idArea' LIMIT 1";
				database::executeQuery($query);
				$query = "DELETE FROM bookingsingle WHERE room='$idArea'";
				database::executeQuery($query);
				$query = "DELETE FROM bookingpermanent WHERE room='$idArea'";
				database::executeQuery($query);
				echo "The area has been removed from the system.\n<br /><br />\n User's will now be unable to make any more bookings in for this area.\n<br />\n";
				echo "All bookings for this room have also been deleted completely from the system.\n";
			}
			
			echo "<br />\n";
			echo "<a href='?page=Administration&section=editareas'>Continue Editing Areas</a>.\n";
		}
	}
	elseif ($section == 'editperiods')
	{
			echo "<h1>Edit Period Times</h1>";
		if ($confirmation != true) // display form
		{
			$query = "SELECT * FROM bookingusers ORDER BY username";
			$users = database::executeQuery($query);
			$query = "SELECT * FROM bookingperiods ORDER BY number";
			$period_raw = database::executeQuery($query);
			
			$periods = array();
			for ($i=1;$i<=settings::get_numberOfPeriods();$i++)
			{
				$periods[$i] = array();
			}
			foreach($period_raw as $index => $period)
			{
				$periods[$period['number']] = $period;
			}


			echo "Set in the grid below the period times in the form 9.10 and 10.20.\n";
			echo "<br /><br />\n";
			echo "<form action=\"?page=Administration&section=editperiods&confirmation=1\" id='editForm' method='post'>\n";
			echo "<table>\n";
			echo "\t<tr>\n";
			echo "\t\t<td class=\"alcenter attention\">Period Number</td>\n";
			echo "\t\t<td class=\"alcenter attention\">Start Time</td>\n";
			echo "\t\t<td class=\"alcenter attention\">End Time</td>\n";
			echo "\t</tr>\n";
			for ($i=1;$i<=settings::get_numberOfPeriods();$i++)
			{
				echo "\t<tr>\n";
				echo "\t\t<td class=\"alcenter\">\n";
				echo "\t\t\t{$i}\n";
				echo "\t\t</td>\n";
				echo "\t\t<td>\n";
				echo "\t\t\t<input type=\"text\" name=\"period{$i}_start\" id=\"period{$i}_start\" value=\"".$periods[$i]['starttime']."\" />\n";
				echo "\t\t</td>\n";
				echo "\t\t<td>\n";
				echo "\t\t\t<input type=\"text\" name=\"period{$i}_end\" id=\"period{$i}_end\" value=\"".$periods[$i]['endtime']."\" />\n";
				echo "\t\t</td>\n";
				echo "\t</tr>\n";
			}
			echo "\t<tr>\n";
			echo "\t\t<td colspan='3' class=\"alright\">\n";
			echo "\t\t\t<input id=\"submitButton\" type='submit' value='Save Periods' />\n";
			echo "\t\t</td>\n\t</tr>\n</table>\n";
			echo "</form>\n";
		}
		else // remove user
		{
			$periodData = array();
			// get all the raw data from the form
			for ($i=1;$i<=settings::get_numberOfPeriods();$i++)
			{
				$data = array();
				$data['period'] = $i;
				$data['starttime'] = raw_param_post("period".$i."_start");
				$data['endtime'] = raw_param_post("period".$i."_end");
				$periodData[$i] = $data;
			}
			
			// check that there exists a period row for each period already
			$query = "SELECT * FROM bookingperiods ORDER BY number";
			$result = database::executeQuery($query);
			$active = array();
			foreach($result as $row)
			{
				$active[$row['number']] = 1;
			}
			for($i=1;$i<=settings::get_numberOfPeriods();$i++)
			{
				if (empty($active[$i]))
				{
					// if there is no booking for this period then make a blank one so it can be 'updated'
					$query = "INSERT INTO bookingperiods (number) VALUES ('$i')";
					database::executeQuery($query);
				}
			}
			foreach ($periodData as $periodNumber => $period)
			{
				// update all the period data
				$number = $period['period'];
				$starttime = $period['starttime'];
				$endtime = $period['endtime'];
				$query = "UPDATE bookingperiods SET starttime='$starttime', endtime='$endtime' WHERE number='$number'";
				database::executeQuery($query);
			}
			
			echo "Periods have be updated to their new times.\n";
			echo "<br /><br />\n";
			echo "<a href='?page=Administration'>Return To Admin</a>\n";
		}
	
	}
	elseif ($section == 'editsettings')
	{
		echo "<h1>Edit Settings</h1>";
		if ($confirmation != true) // display form
		{
			echo "Alter the settings below for this system.\n";
			echo "<br /><br />\n";
			echo "<form action=\"?page=Administration&section=editsettings&confirmation=1\" id='editForm' method='post'>\n";
			echo "<table>\n";
			echo "\t<tr>\n";
			echo "\t\t<td>System Title :</td>\n";
			echo "\t\t<td>\n";
			echo "\t\t\t<input type=\"text\" class=\"checkText\" name=\"systemTitle\" value=\"".settings::get_SystemTitle()."\" />\n";
			echo "\t\t</td>\n";
			echo "\t\t<td class=\"i small\">The system title.</td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n";
			echo "\t\t<td>Administrator Name :</td>\n";
			echo "\t\t<td>\n";
			echo "\t\t\t<input type=\"text\" class=\"checkText\" name=\"adminName\" value=\"".settings::get_AdminName()."\" />\n";
			echo "\t\t</td>\n";
			echo "\t\t<td class=\"i small\">The system administrator's name.</td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n";
			echo "\t\t<td>Front Page Message :</td>\n";
			echo "\t\t<td>\n";
			echo "\t\t\t<input type=\"text\" name=\"mainPageMessage\" value=\"".settings::get_frontPageMessage()."\" />\n";
			echo "\t\t</td>\n";
			echo "\t\t<td class=\"i small\">The large, bold message displayed at sign in.</td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n";
			echo "\t\t<td>Number Of Booking Per User Per Year :</td>\n";
			echo "\t\t<td>\n";
			echo "\t\t\t<input type=\"text\" class=\"checkInteger\" name=\"numberOfBookingPerYear\" value=\"".settings::get_numberOfBookingsPerYear()."\" />\n";
			echo "\t\t</td>\n";
			echo "\t\t<td class=\"i small\">The basic number of bookings allowed per user per year.</td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n";
			echo "\t\t<td>Number Of Periods In Day :</td>\n";
			echo "\t\t<td>\n";
			echo "\t\t\t<input type=\"text\" class=\"checkInteger\" name=\"numberOfPeriods\" value=\"".settings::get_numberOfPeriods()."\" />\n";
			echo "\t\t</td>\n";
			echo "\t\t<td class=\"i small\">The number of periods in a day.</td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n";
			echo "\t\t<td>Booking Range Maximum :</td>\n";
			echo "\t\t<td>\n";
			echo "\t\t\t<input type=\"text\" class=\"checkInteger\" name=\"bookingRangeMax\" value=\"".settings::get_bookingRangeMax()."\" />\n";
			echo "\t\t</td>\n";
			echo "\t\t<td class=\"i small\">The maximum number of days ahead that are bookable.</td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n";
			echo "\t\t<td>Booking Range Minimum :</td>\n";
			echo "\t\t<td>\n";
			echo "\t\t\t<input type=\"text\" class=\"checkInteger\" name=\"bookingRangeMin\" value=\"".settings::get_bookingRangeMin()."\" />\n";
			echo "\t\t</td>\n";
			echo "\t\t<td class=\"i small\">The minimum number of days ahead that are bookable.</td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n";
			echo "\t\t<td>Booking Reason Enabled? :</td>\n";
			echo "\t\t<td>\n";
			echo "\t\t\t<select name=\"reasonEnabled\">\n";
			$possiblities = array("No","Yes");
			foreach ($possiblities as $index => $value)
			{
				$selected = "";
				if (settings::get_bookingReasonEnabled() == $index)
				{
					$selected = "selected=\"selected\"";
				}
				echo "\t\t\t\t<option value=\"$index\" $selected>$value</option>\n";
			}
			echo "\t\t\t</select>\n";
			echo "\t\t</td>\n";
			echo "\t\t<td class=\"i small\">Does the sytem prompt for a reason for the booking?</td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n";
			echo "\t\t<td colspan='3' class=\"alright\">\n";
			echo "\t\t\t<input type='submit' value='Save Changes' id='submitButton' />\n";
			echo "\t\t</td>\n";
			echo "\t</tr>\n";
			echo "</table>\n";
			echo "</form>\n";
		}
		else // remove user
		{
			$systemTitle = raw_param_post('systemTitle');
			$adminName = raw_param_post('adminName');
			$numberOfPeriods = raw_param_post('numberOfPeriods');
			$mainPageMessage = raw_param_post('mainPageMessage');
			$numberOfBookingPerYear = raw_param_post('numberOfBookingPerYear');
			$bookingRangeMax = raw_param_post('bookingRangeMax');
			$bookingRangeMin = raw_param_post('bookingRangeMin');
			$reasonEnabled = raw_param_post('reasonEnabled');
			
			$query = "UPDATE bookingsettings SET bookingRangeMax='$bookingRangeMax', bookingRangeMin='$bookingRangeMin', yearlyBookingCount='$numberOfBookingPerYear', mainPageMessage='$mainPageMessage', numberOfPeriods='$numberOfPeriods', SystemTitle='$systemTitle', AdminName='$adminName', bookingReason='$reasonEnabled' LIMIT 1";
			$result = database::executeQuery($query);

			echo "Settings Updated.\n";
		}
		echo "<br /><br />\n";
		echo "<a href='?page=Administration'>Return To Admin</a>\n";
	}
	elseif ($section == 'backupsystem')
	{
		echo "<h1>Backup System</h1>";
		echo "To create a backup file containing all the data for this system, click the following link.\n<br />";
		echo "Save the file somewhere safe.\n";
		echo "<br /><br />\n";
		echo "<a href='backupSystem.php'>Backup File</a>\n";
		echo "<br />";
		echo "<br /><br />\n";
		echo "<a href='?page=Administration'>Return To Admin</a>\n";

	}
	elseif ($section == 'restoresystem')
	{
		echo "<h1>Restore System</h1>\n";
		if ($confirmation != true) // display form
		{
			echo "Restore the system from a backup file.<br />\nCopy and paste the contents of a backup file into the box below.<br />\nThis will restore the system to the same state when the backup was made.\n";
			echo "<br /><br />\n";
			echo "<form action=\"?page=Administration&section=restoresystem&confirmation=1\" id='editForm' method='post'>\n";
			echo "<table>\n";
			echo "\t<tr>\n";
			echo "\t\t<td class='alright alignTop'>File Contents :</td>\n";
			echo "\t\t<td>\n";
			echo "\t\t\t<textarea cols='50' rows='15' name='sql' wrap='off'></textarea>\n";
			echo "\t\t</td>\n";
			echo "\t</tr>\n";
			echo "\t<tr>\n";
			echo "\t\t<td colspan='2' class=\"alright\">\n";
			echo "\t\t\t<input type='submit' value='Restore Backup' id='submitButton' />\n";
			echo "\t\t</td>\n";
			echo "\t</tr>\n";
			echo "</table>\n";
			echo "</form>\n";
		}
		else // run code
		{
			$backupData = raw_param_post('sql');
			$data1 = explode("\n",$backupData);
			$data2 = explode("\r",$backupData);
			if (is_array($data1))
			{
				$data = $data1;
			}
			elseif (is_array($data2))
			{
				$data = $data2;
			}
			else
			{
				$data = array();
			}
			
			foreach ($data as $query)
			{
				if (!empty($query) && !ereg('^--',$query) && $query != "\n" && $query != "\r" && !ereg('^\t',$query))
				{
					database::executeQuery($query);
				}
			}

			echo "Database restored.\n";
		}
		echo "<br /><br />\n";
		echo "<a href='?page=Administration'>Return To Admin</a>\n";
	}
	elseif ($section == 'bookingcsv')
	{
		echo "<h1>Create CSV file</h1>";
		echo "To create a CSV file containing all the individual booking data, click the following link or enter a specific school year below.<br /><span class='b'>Note:</span> Bookings that are repetitive [weekly] are not included in the following output.\n<br />";
		echo "This data can be used in Excel to perform analysis on the bookings.\n";
		echo "<br /><br />\n";
		echo "<a href='createCVS.php'>All data</a><br /><br />\n";
		$schoolYear = generateSchoolYear(date("m"),date("Y"));
		echo "<form id='year'><input type='text' name='schoolYear' id='schoolYear' value='$schoolYear' /><input type='button' value='Get CVS for Year' onclick=\"var schoolYear=document.getElementById('schoolYear');schoolYear = schoolYear.value;window.location='createCVS.php?schoolYear='+schoolYear;\" /></form>";
		echo "<br />";
		echo "<br /><br />\n";
		echo "<a href='?page=Administration'>Return To Admin</a>\n";

	}
	elseif ($section == 'editreasons')
	{
		echo "<h1>Edit Booking Reasons</h1>\n";
		if ($confirmation != true) // display form
		{
			$query = "SELECT * FROM bookingreason ORDER BY id";
			$reasons = database::executeQuery($query);
			$maxID = $reasons[count($reasons)-1]['id'];
			if ($maxID == "" || $maxID == NULL)
			{
				$maxID = 0;
			}
			echo "Edit the booking reasons list. To disable a reason, just uncheck the checkbox.<br />\nEnter the reason's text into the form below.\n";
			echo "<br /><br />\n";
			echo "<form action=\"?page=Administration&section=editreasons&confirmation=1\" id='editForm' method='post'>\n";
			echo "<input type='hidden' name='maxID' id='maxID' value='$maxID' />\n";
			echo "<table>\n";
			echo "\t<thead class='b h4'>\n";
			echo "\t<tr>\n";
			echo "\t\t<td class='alcenter'>Reason ID</td>\n";
			echo "\t\t<td class='alcenter'>Reason Text</td>\n";
			echo "\t\t<td class='alcenter'>Enabled?</td>\n";
			echo "\t</tr>\n";
			echo "\t</thead>\n";
			
			if (count($reasons) > 0)
			{
				foreach($reasons as $index => $reason)
				{
					$id = $reason['id'];
					$text = $reason['reasonText'];
					$enabled = $reason['enabled'];
					if ($enabled == true)
					{
						$enabledText = "checked=\"checked\"";
					}
					else
					{
						$enabledText = "";
					}
					
					echo "\t<tr>\n";
					echo "\t\t<td class='alcenter'>\n";
					echo "\t\t\t<input type='text' name='{$id}_id' value='{$id}' class='alcenter b' readonly='readonly' />\n";
					echo "\t\t</td>\n";
					echo "\t\t<td class='alcenter'>\n";
					echo "\t\t\t<input type='hidden' name='{$id}_backup' value='{$text}' id='{$id}_backup' />\n";
					echo "\t\t\t<input type='text' name='{$id}_text' value='{$text}' class='alcenter' />\n";
					echo "\t\t</td>\n";
					echo "\t\t<td class='alcenter'>\n";
					echo "\t\t\t<input type='checkbox' name='{$id}_enabled' value='1' class='alcenter' {$enabledText} />\n";
					echo "\t\t</td>\n";
					echo "\t</tr>\n";
				}
			}
			else
			{
				echo "\t<tr>\n";
				echo "\t\t<td colspan='3' class='alcenter'>\n";
				echo "\t\t\tNo reasons currently in the system.\n";
				echo "\t\t</td>\n";
				echo "\t</tr>\n";
			}
			for ($i=1;$i<=5;$i++)
			{
				$id = $maxID+$i;
				echo "\t<tr id='new_row_{$i}'>\n";
				echo "\t\t<td class='alcenter'>\n";
				echo "\t\t\t<input type='text' name='new_{$i}_id' id='new_{$i}_id' value='new' class='alcenter b' readonly=\"readonly\" />\n";
				echo "\t\t</td>\n";
				echo "\t\t<td class='alcenter'>\n";
				echo "\t\t\t<input type='text' name='new_{$i}_text' id='new_{$i}_text' value='' class='alcenter' />\n";
				echo "\t\t</td>\n";
				echo "\t\t<td class='alcenter'>\n";
				echo "\t\t\t<input type='checkbox' name='new_{$i}_enabled' id='new_{$i}_enabled' value='1' class='alcenter' checked=\"checked\" />\n";
				echo "\t\t</td>\n";
				echo "\t</tr>\n";
			}
			echo "\t<tr>\n";
			echo "\t\t<td colspan='3' class=\"alright\">\n";
			echo "\t\t\t<span id='errorSpace' class='errorMsg'></span>\n";
			echo "\t\t\t<input type='button' id='add_new' value='Add New' /><input type='submit' value='Save' id='submitButton' />\n";
			echo "\t\t</td>\n";
			echo "\t</tr>\n";
			echo "</table>\n";
			echo "</form>\n";
		}
		else // run code
		{
			$query = "SELECT * FROM bookingreason ORDER BY id";
			$reasons = database::executeQuery($query);
			
			if (count($reasons) > 0)
			{
				foreach ($reasons as $index => $reason)
				{
					// go through all the reasons and handle updates
					$currentID = $reason['id'];
					$newText = raw_param_post($currentID."_text");
					
					$newState = raw_param_post($currentID."_enabled");
					if (empty($newState))
					{
						$newState = 0;
					}
					else
					{
						$newState = 1;
					}
					
					$query = "UPDATE bookingreason SET reasonText='$newText', enabled='$newState' WHERE id='$currentID'";
					database::executeQuery($query);
				}
			}
			
			// handle the new forms
			for ($i=1; $i<=5;$i++)
			{
				$newText = raw_param_post("new_".$i."_text");
				$newState = raw_param_post("new_".$i."_enabled");
				if (empty($newState))
				{
					$newState = 0;
				}
				else
				{
					$newState = 1;
				}
				
				if (!empty($newText))
				{
					// add this into the database
					$query = "INSERT INTO bookingreason (reasonText,enabled) VALUES ('$newText','$newState')";
					database::executeQuery($query);
				}
			}
			
			echo "Reasons saved.\n";
		}
		echo "<br /><br />\n";
		echo "<a href='?page=Administration'>Return To Admin</a>\n";
	}
	elseif ($section == '')
	{
	}
	else
	{
		echo "You have opened an invalid page.\n";
	}
}
else
{
	echo "You are not permitted to view this page.\n";
}

?>
</center>

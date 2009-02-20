<?php
function my_stripos($haystack,$needle)
{
	$haystack = strtolower($haystack);
	$needle = strtolower($needle);
	return strpos($haystack,$needle);
}
class database
{
	function connect() 
	{
		// PUT YOUR OWN VALUES IN HERE
		
		$username = "username";
		$password = "password";
		$server = "localhost";
		$database = "bookingsuite";
		
		
		// DO NOT EDIT ANY FURTHER


		
		$connection = mysql_connect($server,$username,$password)
			or die("Couldn't connect to server");
		$db = mysql_select_db($database,$connection)
			or die("Couldn't select database");
		return $connection;
	}
		
	function disconnect($connection)
	{
		if ($connection)
		{
			// if connection is active, then disconnect
			mysql_close($connection);
		}
		else
		{
			echo "\n\n\n<!-- Connection not active, so can not be disconnected -->\n\n\n";
		}
	}
	
	function executeQuery($query)
	{
		$connection = database::connect();
		$result = mysql_query($query,$connection) OR die ("Couldn't execute query: $query<br />");
		$mainArray = array();
		if (my_stripos($query,"insert") === FALSE && my_stripos($query,"update") === FALSE && my_stripos($query,"delete") === FALSE && my_stripos($query,"create") === FALSE)
		{
			while($row = mysql_fetch_assoc($result))
			{
				$innerArray = array();
				foreach ($row as $key => $data)
				{
					$innerArray[$key] = $data;
				}
				$mainArray[] = $innerArray;
			}
		}
		database::disconnect($connection);
		return $mainArray;
	}
	
	function checkUserPassword($username,$level)
	{
		$query = "SELECT * FROM bookingusers WHERE username='$username' AND level='$level'";
		$result = database::executeQuery($query);
		return $result;
	}
	
	function sign_on_Request($username,$password)
	{
		$query = "SELECT * FROM bookingusers WHERE username='$username' AND password='$password' LIMIT 1";
		$result = database::executeQuery($query);
		return $result;
	}
	
	function getSettings()
	{
		$query = "SELECT * FROM bookingsettings LIMIT 1";
		$result = database::executeQuery($query);
		return $result[0];
	}
	
	function getDaySingleBookingsData($year,$month,$day)
	{
		$query = "SELECT * FROM bookingsingle WHERE day='$day' AND month='$month' AND year='$year'";
		return database::executeQuery($query);
	}
	
	function getRoomName($id)
	{
		$query = "SELECT * FROM bookingareas WHERE id='$id'";
		$result = database::executeQuery($query);
		return $result[0];
	}
	
	function getRoomNames()
	{
		$query = "SELECT * FROM bookingareas ORDER BY name";
		return database::executeQuery($query);
	}
	
	function getPeriodTimes()
	{
		$query = "SELECT * FROM bookingperiods ORDER BY number";
		$results = database::executeQuery($query);
		return $results;
	}
	
	function getNote($year,$month,$day)
	{
		$query = "SELECT * FROM bookingnotes WHERE year='$year' AND month='$month' AND day='$day'";
		$results = database::executeQuery($query);
		if (is_array($results) && count($results) > 0)
		{
			return $results[0];
		}
		else
		{
			return NULL;
		}
	}
}
?>
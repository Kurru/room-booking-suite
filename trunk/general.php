<?php
error_reporting(5); 
function printArray($array)
{
	foreach($array as $key => $value)
	{
		if (is_array($value))
		{
			printArray($value);
		}
		else
		{
			echo $key.' '.$value."<br />\n";
		}
	}
}

function page()
{
	return $_GET['page'];
}

function generateSchoolYear($month,$year)
{
	if ($month >= 8)
	{
		$schoolYear = "".$year.($year+1);
	}
	else
	{
		$schoolYear = "".($year-1).$year;
	}
	return $schoolYear;
}
function raw_param($name)
{
	return trim(ini_get('magic_quotes_gpc') ? stripslashes($_GET[$name]) : $_GET[$name]);
}

function raw_param_post($name)
{
	return trim(ini_get('magic_quotes_gpc') ? stripslashes($_POST[$name]) : $_POST[$name]);
}

function displayNavigationBar()
{
?>
	<div class="nav"><a href="?">Book</a> | <a href="?page=Bookings">Your Bookings</a> | <a href="?page=Account">Account Options</a> | <?php if (security::get_level('') >= 2) { echo '<a href="?page=Administration">Admin</a> | ';} ?><a href="signoff.php">Sign Off</a></div>
<?php
}

function join_2d_arrays($array1,$array2)
	{
	$array = array();
	$names = array();
	
	//printArray($array1);
	//printArray($array2);
	if (is_array($array1) && is_array($array2))
	{
		// both inputs are the same
		
		foreach ($array1 as $section => $subsections)
		{
			// walk through the 1st array joining the arrays together
			if (is_array($subsections))
			{
				if (is_array($array2[$section]))
				{
					// join all the data contained with the current section in the 2 arrays together
					$array[$section] = array_merge($array1[$section],$array2[$section]);
				}
				else
				{
					// there is no section in $array2 so just add the first array
					$array[$section] = $subsections;
				}
			}
			else
			{
				echo "No such subsection: ".$subsections.'<br />';			
			}
		}
	
		foreach ($array2 as $section => $subsections)
		{
			if (!array_key_exists($section, $array)) // if $type2 is not included so far then add it
			{
				$array[$section] = $subsections;
			}
		};
	} 
	
	return $array;
	};
?>
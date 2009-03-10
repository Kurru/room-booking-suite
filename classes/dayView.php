<?php
class dayView
{
	
	function displayTitle()
	{
		// the page title
		echo "Timetable Viewer";
	}
	
	function displayView()
	{
		// display the table for the links to be displayed in
		
		$year = raw_param('year');
		$month = raw_param('month');
		$day = raw_param('day');
		
		if (empty($year) || empty($month) || empty($day))
		{
			// if any of the required inputs are missing, then display a prompt to select month
			echo "<br /><i>Select a day from the left</i>";
		}
		else
		{
			// print the time to the top of the field as a reminder to the day
			echo "<center>";
			echo date("l, jS F Y",mktime(1,1,1,$month,$day,$year));
			echo "</center>";
			// all require parameters have been provided, display the day view
			if (booking::canBook())
			{
				dayView::PrintTable($year,$month,$day);
			}
			else
			{
				echo "<br /><br />\n";
				echo "<span class='alcenter'>";
				echo "You have reached your booking limit for this year.";
				echo "</span>";
			}
		}
		
	} // end displayView function
	
	function PrintTable($year,$month,$day)
	{
		// output the table headers first
		$roomNames = database::getRoomNames();
		$numberOfColumns = count($roomNames)+2;
		echo "\n<table width=\"100%\" class='dayView'>\n";
		echo "\t<tr>\n";
		echo "\t\t<td>Period</td>\n";
		echo "\t\t<td>Time</td>\n";
		
		foreach ($roomNames as $value)
		{
			echo "\t\t<td>".$value['name']."</td>\n";
		}
		echo "\t</tr>\n";
		// add the rows for each of the different bookable areas
		$linkdescription = dayView::assembleArray($year,$month,$day,$roomNames);
		dayView::printTimeTable($linkdescription,$year,$month,$day,$roomNames);

		notes::displayNote($year,$month,$day,$numberOfColumns);

		if (security::get_level('') >= 2)
		{
			// limit the 'Print Timetable' and 'Blank Book Room for Today' options to admin level users
			echo "\t<tr>\n"; // start blank row
			echo "\t\t<td colspan=\"$numberOfColumns\">&nbsp;</td>\n";
			echo "\t</tr>\n";
	
			echo "\t<tr>\n"; // start print timetable
			echo "\t\t<td colspan=\"2\">Print Timetable</td>\n";
			foreach ($roomNames as $value)
			{
				echo "\t\t<td>\n";
				echo "\t\t\t<a href=\"#\" onClick=\"window.open('printDayBookings.php?room=".$value['id']."&day=".$day."&month=".$month."&year=".$year."','printsheet".$value['id']."','resizable=1,scrollbars=1,menubar=1,location=0,toolbar=0')\">".$value['name']."</a>\n";
				echo "\t\t</td>\n";
			}
			echo "\t</tr>\n";
			
			echo "\t<tr>\n"; // start blank row
			echo "\t\t<td colspan=\"$numberOfColumns\">&nbsp;</td>\n";
			echo "\t</tr>\n";
	
			echo "\t<tr>\n"; // start blank book room
			echo "\t\t<td colspan=\"2\">Blank Book Room For Today</td>\n";
			foreach ($roomNames as $value)
			{
				echo "\t\t<td>\n";
				echo "\t\t\t<a href=\"#\" onClick=\"window.open('bookingView.php?type=blankBooking&room=".$value['id']."&day=".$day."&month=".$month."&year=".$year."','printsheet".$value['id']."','resizable=1,scrollbars=1,menubar=1,location=0,toolbar=0')\">".$value['name']."</a>\n";
				echo "\t\t</td>\n";
			}
			echo "\t</tr>\n";
		}
		echo "</table>\n";


	} // end printTable function
	
	function printTimeTable($data,$year,$month,$day,$roomNames)
	{
		// retrieve period times
		$periodTimes = database::getPeriodTimes();
		for ($i=0;$i<count($data);$i++)
		{
			$items = $data[$i];
			echo "\t<tr>\n";
			$j = $i + 1;
			$periodName = $periodTimes[$i]['name'];
			echo "\t\t<td>{$periodName}</td>\n";	//print the period number
			
			echo "\t\t<td>";
			echo $periodTimes[$i]['starttime'].' - '.$periodTimes[$i]['endtime'];	// print the period times
			echo "</td>\n";
			foreach ($items as $roomIndex => $elementArray)
			{
				$cells = $items[$k];
				echo "\t\t<td>";
				
				$value = $elementArray[0];
				
				//echo $roomIndex.' '.$value;
				if ($value == 2)
				{
					$linkText = $elementArray[1];//#EE00EE
					echo "<a style=\"color:#FF0099\" href=\"#\" onClick=\"window.open('bookingView.php?year=$year&month=$month&day=$day&room=$roomIndex&period=$j','booking','resizable=1,scrollbars=1,menubar=0,location=0,toolbar=0')\">{$linkText}</a>";
				}
				elseif($value == 1)
				{
					echo "Not Available";
				}
				elseif ($value === 0)
				{
					echo "<a href=\"#\" onClick=\"window.open('bookingView.php?year=$year&month=$month&day=$day&room=$roomIndex&period=$j','booking','resizable=1,scrollbars=1,menubar=0,location=0,toolbar=0')\">Available</a>";
				}
				else
				{
					echo "ERROR ERROR";
				}
				echo "</td>\n";
			}
		echo "\t</tr>\n";
		}
	}
	function assembleArray($year,$month,$day,$roomNames)
	{
		$dayTime = mkTime(23,59,59,$month,$day,$year); // get the timestamp of the current day
		$dayName = date("D",$dayTime);				// use the timestamp to find the name of the day
		
		$repetitiveBookings = repetitive::getDayBookings($dayName,generateSchoolYear($month,$year)); // get all the bookings that happen on this day
		$normalBookings = normal::getDayBookings($year,$month,$day);// get all the bookings that happen on this day
		
		$theArray = array();
		$defaultValue = array(0); // 0 if current day is within valid range of bookable times
		
		// get the values required for checking if withing valid booking times
		$currentTime = time();
		$startingTime = $currentTime + settings::get_bookingRangeMin()*60*60*24;
		$finalTime = $currentTime + settings::get_bookingRangeMax()*60*60*24;
		$displayedTime = $dayTime;
		
		// set a flag to check if is admin account or not
		$is_admin = false;
		if (security::get_level('') == 2)
		{
			$is_admin = true;
		}
		
		
		// set up the default values
		if ($displayedTime<=$finalTime && $displayedTime>=$startingTime)
		{
			// is within the area that can be booked for normal users
			$defaultValue = array(0); // default action, avaiable
		}
		else
		{
			$defaultValue = array(1); // default action, not availiable [too far in the future, or in the past]
		}
		if ($is_admin == true)
		{
			$defaultValue = array(0); // default action for admin accounts, is always avaible
		}
		
				
		for ($j=0;$j<settings::get_numberOfPeriods();$j++)
		{
			for ($i=0;$i<count($roomNames);$i++)
			{
				$theArray[$j][$roomNames[$i]['id']] = $defaultValue; // initialise all the different periods/rooms to the default
			}
		}
		
		
		
		foreach ($repetitiveBookings as $booking)
		{
			$username = $booking->username;
			$shortUsername = substr($username,0,2);
			$classname = $booking->classname;
			$linkText = strtoupper($shortUsername)." - ".$classname;
			$theArray[$booking->period-1][$booking->room] = array(2,$linkText);
		}
		foreach ($normalBookings as $booking)
		{
			$username = $booking->username;
			$shortUsername = substr($username,0,2);
			$classname = $booking->classname;
			$linkText = strtoupper($shortUsername)." - ".$classname;

			$theArray[$booking->period-1][$booking->room] = array(2,$linkText);
		}
		return $theArray;
	}
	
	function javascript()
	{
?>
<script language="javascript" type="text/javascript" src="javascript/diashow.js">
<!-- 
// -->
</script>
<script language="javascript" type="text/javascript">
<!-- 
function resetNoteScreen($year,$month,$day)
{
setTimeout('self.frames["notes"].location.href="notepage.php?year=$year&month=$month&day=$day";',1000);
setTimeout("self.location.reload(true);",1500);
}
// -->
</script>
<?php
	} // end javascript function
} // end dayView class defination
?>
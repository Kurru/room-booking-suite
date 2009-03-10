<center>
<?php
$username = $_SESSION['SESSIONusername'];
// calculate the current school year to be displayed
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
$query = "SELECT min(schoolYear) FROM bookingsingle WHERE username='$username'";
$result1 = database::executeQuery($query);
$query = "SELECT min(schoolYear) FROM bookingpermanent WHERE username='$username'";
$result2 = database::executeQuery($query);
if ($result1[0]['min(schoolYear)'] == '' && $result2[0]['min(schoolYear)'] == '')
{
	$firstYear = $current[0].$current[1];
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

echo "<h2>Your Bookings</h2>\n";
echo "Below are all bookings you have made for the School Year <span class='b'>{$displayedYear[0]}/{$displayedYear[1]}</span>.\n<br /><br />\n";



echo "<span style='float:left;'>\n";
if ($firstYear[0] <= $previousYear[0])
{
	$text = "".$previousYear[0].$previousYear[1];
	echo "\t<a href='?page=Bookings&year=".$text."'>".$previousYear[0]."-".$previousYear[1]."</a>\n";
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
	echo "\t<a href='?page=Bookings&year=".$text."'>".$nextYear[0]."-".$nextYear[1]."</a>\n";
}
else
{
	echo $nextYear[0]."-".$nextYear[1];
}
echo "</span>\n";
echo "<br /><br />\n";


echo "<table class=\"bookings\" width=\"100%\">\n";
echo "\t<thead><tr><td></td><td>Date</td><td>Period</td><td>Room</td><td>Subject</td><td>Class</td><td></td></tr></thead>\n";

$query = "SELECT * FROM bookingperiods ORDER BY number";
$periodData = database::executeQuery($query);

$query = "SELECT * FROM bookingsingle WHERE username='$username' AND schoolYear='$displayedYearText' ORDER BY day DESC,month DESC,year DESC";
$results = database::executeQuery($query);
$numberResults = count($results);
if ($numberResults > 0)
{
	foreach ($results as $index => $booking)
	{
		$date = date('D, jS M Y',mktime(1,1,1,$booking['month'],$booking['day'],$booking['year']));
		$room_id = $booking['room'];
		$room_data = database::getRoomName($room_id);
		$room_name = $room_data['name'];
		$period = $booking['period'];
		$periodName = $periodData[$period-1]['name'];
		
		$result = database::executeQuery("SELECT starttime FROM bookingperiods WHERE number='$period'");
		$time = explode(".",$result[0]['starttime']);
		$startTimeOfBooking = mktime($time[0],$time[1],0,$booking['month'],$booking['day'],$booking['year']);
		
		$urlstring = "day=".$booking['day']."&month=".$booking['month']."&year=".$booking['year']."&period=".$period."&room=".$room_id;
		
		$link = "<a href=\"#\" onClick=\"window.open('bookingView.php?{$urlstring}','booking','resizable=1,scrollbars=1,menubar=0,location=0,toolbar=0')\">View</a>";
		
		echo "\t<tr>\n";
		echo "\t\t<td>".$link."</td>\n";
		echo "\t\t<td>".$date."</td>\n";
		echo "\t\t<td>".$periodName."</td>\n";
		echo "\t\t<td>".$room_name."</td>\n";
		echo "\t\t<td>".$booking['subject']."</td>\n";
		echo "\t\t<td>".$booking['class']."</td>\n";
		echo "\t\t<td>";
		if(time() < $startTimeOfBooking || security::get_level('') >= 2)
		{
			echo "<a href=\"#\" onclick=\"window.open('bookingView.php?type=Unbook&{$urlstring}','booking','resizable=1,scrollbars=1,menubar=0,location=0,toolbar=0')\">Delete Booking</a> ";
		}
		echo "</td>\n";
		echo "\t</tr>\n";
	};
}
else
{
	echo "\t<tr>\n\t\t<td colspan='7'>No bookings.</td>\n\t</tr>\n";
}


echo "\t<tr>\n\t\t<td colspan='7'><h4>Yearly Bookings</h3></td>\n\t</tr>\n";
echo "\t<thead>\n";
echo "\t<tr>\n\t\t<td></td>\n\t\t<td>Day</td>\n\t\t<td>Period</td>\n\t\t<td>Room</td>\n\t\t<td>Subject</td>\n\t\t<td>Class</td>\n\t\t<td></td>\n\t\t</tr>\n";
echo "\t</thead>\n";
$query = "SELECT * FROM bookingpermanent WHERE username='$username' AND schoolYear='$displayedYearText' ORDER BY period,day";
$results = database::executeQuery($query);
$num_results = count($results);
if ($num_results > 0)
{
	foreach ($results as $index => $booking)
	{
		$room_id = $booking['room'];
		$room_data = database::getRoomName($room_id);
		$room_name = $room_data['name'];
		$period = $booking['period'];
		$periodName = $periodData[$period-1]['name'];
		
		
		$urlstring = "dayName=".$booking['day']."&schoolYear=".$displayedYearText."&period=".$period."&room=".$room_id;

		$dayName = $booking['day'];
		if ($dayName == "Tue")
			$dayName = "Tues";
		elseif ($dayName == "Wed")
			$dayName = "Wednes";
		elseif ($dayName == "Thu")
			$dayName = "Thurs";
		
		$dayName .= "day";
		
		echo "\t<tr>\n";
		echo "\t\t<td>".$dayName."</td>\n";
		echo "\t\t<td>".$dayName."</td>\n";
		echo "\t\t<td>".$periodName."</td>\n";
		echo "\t\t<td>".$room_name."</td>\n";
		echo "\t\t<td>".$booking['subject']."</td>\n";
		echo "\t\t<td>".$booking['class']."</td>\n";
		echo "\t\t<td>";
		if(security::get_level('') >= 2)
		{
			echo "<a href=\"#\" onClick=\"window.open('bookingView.php?type=Unbook&{$urlstring}','booking','resizable=1,scrollbars=1,menubar=0,location=0,toolbar=0')\">Delete Yearly Booking</a>";
		}
		echo "</td>\n";
		echo "\t</tr>\n";
	};
}
else
{
	echo "<tr><td colspan='7'>No permanent bookings.</td></tr>";
}

echo "</table>";

?>
</center>
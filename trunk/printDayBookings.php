<?php
	session_start();
	include_once("classes/security.php");
	include_once("classes/booking.php");
	include_once("classes/settings.php");
	include_once("general.php");
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Pragma" content="no-cache">
<title>Print Sheet</title>
<style type="text/css" media="all">

table {
	margin: 0px;
	padding: 0px;
}
/* to change the height of the boxes - as say the preview window prints onto 2 pages */
td {
	text-align:center;
	vertical-align:middle;
	height: <?PHP $total = 840; echo $total/settings::get_numberOfPeriods(); ?>px;
}
.alright {
	text-align:right;
}
.alleft {
	text-align:left;
}
tr.bold td {
	font-weight:bold;
	height: auto;
}
.floatleft {
	float:left;
}
.floatright {
	float: right;
}
.largetext {
	font-size: 22px;
}
.border {
	margin: 5px;
	
	border: 1px solid #000000;
}
</style>
<script language="javascript1.2" type="text/javascript">
<!-- 
// sets delay before the print screen is shown of 100milliseconds
// this is to let the window load before the print screen displays
setTimeout("window.print();",100);

// -->
</script>
</head>

<body>
<?php
if (isset($_GET['day']) && isset($_GET['month']) && isset($_GET['year']) && isset($_GET['room']) && security::get_level('') >= 2)
{
	$year = $_GET['year'];
	$month = $_GET['month'];
	$day = $_GET['day'];
	$room_id = $_GET['room'];
	
	$roomName = database::getRoomName($room_id);

	echo "<table width='100%' border='0'>\n";
	echo "\t<tr class='bold'>\n\t\t<td colspan='5' class='largetext'>\n\t\t\t<span class='floatleft'>".date('l, jS F Y',mktime(1,1,1,$month,$day,$year))."</span>\n\t\t\t<span class='floatright'>".$roomName['name']."</span>\n\t\t</td>\n\t</tr>\n";
	echo "\t<tr class='bold'>\n\t\t<td>Period</td>\n\t\t<td>Time</td>\n\t\t<td>Teacher</td>\n\t\t<td>Class</td>\n\t\t<td>Comment</td>\n\t</tr>\n";


/* #################################
####################################
###########GET DATA CODE############
####################################
################################# */

	$dayTime = mkTime(23,59,59,$month,$day,$year); 	// get the timestamp of the current day
	$dayName = date("D",$dayTime);					// use the timestamp to find the name of the day
		 
	
	$repetitive = repetitive::getDayRoomBookings($dayName,$room_id,generateSchoolYear($month,$year));
	$normal = normal::getDayRoomBookings($year,$month,$day,$room_id);
	
	$grid = array();
	foreach ($normal as $key => $entry)
	{
		// build array that contains in order the different bookings [sorted by period]
		$grid[$entry->period] = $entry;
	}
	foreach ($repetitive as $key => $entry)
	{
		$grid[$entry->period] = $entry;
	}
	
	
/* ###############################
##################################
######## OUTPUT PAGE CODE ########
##################################
############################### */

	$periodTimes = database::getPeriodTimes();
	for ($i=1;$i<=settings::get_numberOfPeriods();$i++)
	{
		$data = $grid[$i];
		$times = $periodTimes[$i-1];
		echo "\t<tr>\n";
		echo "\t\t<td>".$i."</td>\n"; // this is the period name
		echo "\t\t<td nowrap=\"nowrap\">".$times['starttime'].'-'.$times['endtime']."</td>\n";	// this is the period times
		echo "\t\t<td>".$data->username."</td>\n"; 												// this is the username who booked the period
		echo "\t\t<td>".$data->classname."<br />".$data->subject."</td>\n"; 						// this is the class and then the subject data
		echo "\t\t<td width='75%' class='border'>&nbsp;</td>\n";  								// this is the box
		echo "\t</tr>\n"; 
	}

	echo "</table>\n";
}
else
	{
	echo "<center><b>Required data has not all been entered.</b></center>";
	}

?>
</body>
</html>

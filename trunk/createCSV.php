<?PHP
session_start();
include_once("classes/security.php");

if (security::get_level('1') >= 2)
{
	include_once("classes/database.php");
	include_once("general.php");
	header('Content-type: text/x-csv');
	$date = date("Ymd_Hi");
	header("Content-Disposition: attachment; filename=\"bookingExport-$date.csv\"");
	
	$schoolYear = raw_param('schoolYear');
	
	// get the booking data
	if (!empty($schoolYear))
	{
		$query = "SELECT * FROM bookingsingle WHERE schoolYear='$schoolYear'";
	}
	else
	{
		$query = "SELECT * FROM bookingsingle";
	}
	$bookings = database::executeQuery($query);
	
	
	// assemble the room names
	$rooms = database::executeQuery("SELECT * FROM bookingareas ORDER BY id");
	$roomData = array();
	foreach ($rooms as $room)
	{
		$roomData[$room['id']] = $room['name'];
	}
	// assemble the reasons
	$reasons = database::executeQuery("SELECT * FROM bookingreason");
	$reasonData = array();
	$reasonData[0] = "";
	foreach ($reasons as $reason)
	{
		$reasonData[$reason['id']] = $reason['reasonText'];
	}
	
	// display the header row
	echo "Unique ID, Username, Day, Month, Year, Room Name, Period, Timebooked, Date Made, Subject, Class, School Year, Reason\n";
	// output all the data in csv format
	foreach($bookings as $booking)
	{
		$dateText = date("H:i jS F Y",$booking['timebooked']);
		$reasonText = $reasonData[$booking['reasonID']];
		echo $booking['id'].",".$booking['username'].",".$booking['day'].",".$booking['month'].",".$booking['year'].",".$roomData[$booking['room']].",".$booking['period'].",".$booking['timebooked'].",".$dateText.",".$booking['subject'].",".$booking['class'].",".$booking['schoolYear'].",".$reasonText."\n";
	}
}
else
{
	echo "You do not have the access rights for this feature.";
}
?>

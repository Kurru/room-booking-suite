<?php
	session_start();
	include_once("classes/security.php");
	include_once("classes/booking.php");
	include_once("classes/settings.php");
	include_once("general.php");
	$day = raw_param("day");
	$month = raw_param("month");
	$year = raw_param("year");
	$room_id = raw_param("room");
	$room_data = database::getRoomName($room_id);
	$room_name = $room_data['name'];
	$period = raw_param("period");
	$type = raw_param("type");
	
	$user_level = security::get_level('');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?PHP 

$title = "Booking View";
if (!empty($_GET['dayName']) && !empty($_GET['schoolYear']))
{

	$title .= " for ".$_GET['dayName']."days";
}
else
{
	$day_name = date("D",mktime(1,1,1,$month,$day,$year));
	$title .= " for ".$room_name.' ['.date("l, jS F Y",mktime(1,1,1,$month,$day,$year)).']';
}
?>
<title><?PHP echo $title; ?></title>
<link rel="stylesheet" type="text/css" href="CSS/general.css" />
<style type="text/css" media="all">
table.review td.center {text-align:center;}
table {
	margin-top: 3px;
	border-width: thin;
	border-style: solid;
	border-color: #AAAAAA;
}
td {
	border-width: 1px;
	border-style: solid;
	border-color: #AAAAAA;
}
</style>
<script type="text/javascript" src="javascript/validiateBookingForm.js"></script>
<script type="text/javascript" src="javascript/popupRefresh.js"></script>
</head>

<body>
<div class="surround">
<center>
<?PHP
if (isset($_GET['type']))
{
	// display the option depending on the type selected
	if ($_GET['type'] == 'BookPeriod' && $user_level >= 1)
	{
		booking::handleBookRequest();
	}
	elseif ($_GET['type'] == 'Unbook' && !empty($_GET['schoolYear']) && !empty($_GET['dayName']) && $user_level >= 2)
	{
		booking::deletePermanentBooking();
	}
	elseif ($_GET['type'] == 'Unbook' && $user_level >= 1)
	{
		booking::deleteBooking();
	}
	elseif($_GET['type'] == 'blankBooking' && $user_level >= 2)
	{
		booking::blankBooking();
	}
}
elseif (isset($day) && isset($month) && isset($year) && isset($room_id) && isset($period) && $user_level >= 1)
{
	// user is logged in and has all required data

	// check if room is already booked for this period
	$normalBooked = normal::isPeriodBooked($day,$month,$year,$room_id,$period);
	$repetitiveBooked = repetitive::isPeriodBooked($day_name,$room_id,$period,generateSchoolYear($month,$year));
	
	if ($normalBooked == FALSE && $repetitiveBooked == FALSE)
	{
		booking::displayBookingForm();
	}
	else
	{
		// display the page detailing booked period [with edit if user is admin]
		booking::displayBookingData();
	}
}
else
{
	echo "ERROR: Insufficent Access Rights OR Data Provided.";
}
?>
</center>
</div>
</body>
</html>

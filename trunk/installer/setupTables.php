<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Booking Suite System - Setting Up Tables</title>
<link rel="stylesheet" type="text/css" href="../CSS/general.css" />

</head>

<body>
<div class="surround">
<h1 class="alcenter">Computer Suite Booking System Installer</h1>
<?PHP
include_once("../classes/database.php");

$query = array();
echo "Creating tables...<br /><br />";
$query[] = "CREATE TABLE `bookingareas` (`id` int(5) NOT NULL auto_increment, `name` varchar(255) NOT NULL, KEY `id` (`id`)) AUTO_INCREMENT=4";
$query[] = "CREATE TABLE `bookingclasses` (`id` int(11) NOT NULL auto_increment,`class` varchar(20) NOT NULL,KEY `id` (`id`)) AUTO_INCREMENT=37 ;";
$query[] = "CREATE TABLE `bookingnotes` (`day` char(3) NOT NULL,`month` int(2) NOT NULL,`year` int(4) NOT NULL,`schoolYear` int(8) NOT NULL,`text` varchar(255) NOT NULL ,UNIQUE KEY `day` (`day`))";
$query[] = "CREATE TABLE `bookingperiods` (`number` int(2) NOT NULL,`starttime` varchar(10) NOT NULL,`endtime` varchar(10) NOT NULL);";
$query[] = "CREATE TABLE `bookingpermanent` (`id` int(11) NOT NULL auto_increment,`username` varchar(50) NOT NULL,`day` varchar(5) NOT NULL,`room` varchar(5) NOT NULL,`period` int(2) NOT NULL,`timebooked` varchar(10) NOT NULL,`subject` varchar(30) NOT NULL,`class` varchar(20) NOT NULL,`schoolYear` int(8) NOT NULL,`reasonID` int(100) NOT NULL,KEY `id` (`id`)) AUTO_INCREMENT=14 ;";
$query[] = "CREATE TABLE `bookingreason` (`id` int(100) NOT NULL auto_increment,`reasonText` varchar(255) NOT NULL,`enabled` int(1) NOT NULL,KEY `id` (`id`)) AUTO_INCREMENT=3 ;";
$query[] = "CREATE TABLE bookingsettings (bookingRangeMax char(2) NOT NULL default '', bookingRangeMin char(2) NOT NULL default '', numberOfPeriods char(2) NOT NULL default '', SystemTitle varchar(255) NOT NULL default '', AdminName varchar(150) NOT NULL default '', bookingReason int(1) NOT NULL default '0', yearlyBookingCount int(5) NOT NULL default '0', mainPageMessage text NOT NULL);";
$query[] = "INSERT INTO bookingsettings VALUES ('60','0','10','Computer Suite Booking System','Admin Name',0,100,'Welcome');";
$query[] = "CREATE TABLE `bookingsingle` (`id` int(11) NOT NULL auto_increment,`username` varchar(50) NOT NULL,`day` varchar(2) NOT NULL,`month` varchar(2) NOT NULL,`year` varchar(4) NOT NULL,`room` int(5) NOT NULL,`period` int(2) NOT NULL,`timebooked` varchar(10) NOT NULL,`subject` varchar(30) NOT NULL,`class` varchar(20) NOT NULL,`schoolYear` int(8) NOT NULL,`reasonID` int(100) NOT NULL,KEY `id` (`id`)) AUTO_INCREMENT=3 ;";
$query[] = "CREATE TABLE `bookingsubjects` (`id` int(4) NOT NULL auto_increment,`subject` varchar(50) NOT NULL,KEY `id` (`id`)) AUTO_INCREMENT=23 ;";
$query[] = "INSERT INTO `bookingsubjects` (`id`, `subject`) VALUES (1, 'Mathematics'),(2, 'English'),(3, 'Computers'),(4, 'Success Maker'),(5, 'Art & Design'),(6, 'Geography'),(7, 'Religious Education'),(8, 'Physical Education'),(9, 'History'),(10, 'Science'),(11, 'Physics'),(12, 'Chemistry'),(13, 'Biology'),(14, 'French'),(15, 'German'),(16, 'Spanish'),(17, 'Music'),(18, 'Careers'),(19, 'Technology'),(20, 'Home Economics'),(21, 'Form'),(22, 'Road Safety');";
$query[] = "CREATE TABLE `bookingusers` (`username` varchar(50) NOT NULL,`password` varchar(32) NOT NULL,`level` char(1) NOT NULL,UNIQUE KEY `username` (`username`))";
$query[] = "INSERT INTO `bookingusers` (`username`, `password`, `level`) VALUES ('Kurru', 'd28d2d3560fa76f0dbb1a452f8c38169', '2'),('Admin', '21232f297a57a5a743894a0e4a801fc3', '2');";


foreach ($query as $sql)
{
	database::executeQuery($sql);
}
echo "Tables created successfully.<br /><br /><br /><br />";
echo "Booking Suite setup complete.";
echo "<br />";
echo "<br />";
echo "<br />";
echo "<br />";
echo "Sign in details: <br /><span class=`b`>username : \"Admin\"</span><br /> <span class=`b`>password : \"admin\"</span>.<br /><br />Change this password immediately.";
echo "<br />";
echo "<br />";
echo "<span class=`b`>ONCE YOU SIGN IN GO THROUGH THE `SYSTEM SETTINGS` LINKS IN THE ADMIN SECTION TO FINISH SETTING UP.</span>";
echo "<br />";
echo "<br />";
echo "Too continue, sign into the system <a href=\"../\">here</a>.";

?>
</div>
</body>
</html>

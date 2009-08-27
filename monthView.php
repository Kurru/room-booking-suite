<?php
	session_start();
	include_once("general.php");
	include_once("classes/monthView.php");
	include_once("classes/security.php");
	include_once("classes/settings.php");
	//user->authicateUser(); 
	$monthView = new monthView();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Pragma" content="no-cache">
<link rel="stylesheet" type="text/css" href="CSS/general.css" />
<link rel="stylesheet" type="text/css" href="CSS/frame.css" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php $monthView->displayTitle(); ?></title>
<?php $monthView->printJavascript(); ?>
</head>

<body onLoad="parent.dayView.location = 'dayView.php'">

<?php
// display the month view table with the month selection box
$monthView->displayView(); 
?>

</body>
</html>
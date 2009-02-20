<?php
	session_start();
	include_once("classes/dayView.php");
	include_once("classes/security.php");
	include_once("classes/booking.php");
	include_once("classes/notes.php");
	include_once("classes/settings.php");
	include_once("general.php");
	if (security::get_level('')==0){header("Location: ./");}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="CSS/general.css" />
<link rel="stylesheet" type="text/css" href="CSS/frame.css" />
<?php dayView::javascript(); ?>
<title><?php dayView::displayTitle(); ?></title>
</head>

<body>
<?php
dayView::displayView();
?>
</body>
</html>

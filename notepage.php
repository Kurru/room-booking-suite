<?php
	session_start();
/*	$_GET['year'] = "2008";
	$_GET['month'] = "06";
	$_GET['day'] = "20";*/
	include_once("classes/security.php");
	include_once("classes/notes.php");
	include_once("classes/database.php");
	include_once("general.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php notes::title(); ?></title>
<?php notes::css(); ?>
</head>

<body>
<?php
notes::displayEdit();
?>
</body>
</html>

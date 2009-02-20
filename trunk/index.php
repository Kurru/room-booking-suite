<?php 
session_start();						// open the session data
include_once("general.php");			// include some general functions
include_once("classes/database.php"); 	// include the database class
include_once("classes/security.php");	// include the security class
include_once("classes/settings.php");	// include the settings class
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" type="text/css" href="CSS/general.css" />
<title><?php echo settings::get_systemTitle(); ?></title>
<script type="text/javascript" src="javascript/general.php<?php echo "?page=".$_GET['page']."&section=".$_GET['section']."&confirmation=".$_GET['confirmation'].""; ?>"></script>
<script type="text/javascript">
<!--
window.status = "<?php echo settings::get_systemTitle(); ?>";
// -->
</script>
</head>
<body>
<div class="surround">
<?php
//print_r($_SERVER);
if (security::logged_in())
	{
	displayNavigationBar();
	$page = page();
	if (empty($page))
		{
		include_once("pages/main.php");
		}
	else
		{
		include_once("pages/".strtolower($page).".php");
		}
	}
else
	{
	if (page() == 'Failed')
		{
		include_once("pages/failed.php");
		}
	else
		{
		echo "<center><h1>Computer Suite Booking System</h1></center>";
		include_once("pages/signin.php");
		echo "<br />";
		echo "<center>Programmed by <a href='mailto:kurru@animeequation.com'>Richard Currie</a>.</center>";
		}
	}
//foreach ($_SESSION as $type => $value)
//	{ echo $type.' => '.$value.'<br />';}
?>
</div>
</body>
</html>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Booking Suite System - Updating</title>
<link rel="stylesheet" type="text/css" href="../CSS/general.css" />

</head>

<body>
<div class="surround">
<h1 class="alcenter">Computer Suite Booking System Updater</h1>
If the following fails. Run the following SQL on your database.<br />
<br />
<pre>ALTER TABLE `bookingperiods` ADD `name` VARCHAR( 255 ) NOT NULL AFTER `number`;</pre>
<br />
[Basically, add an extra field to the table <span class="b">bookingperiods</span> called <span class="b">name</span>]
<br />
<br />
<br />
<br />

<?PHP
include_once("../classes/database.php");

$query = array();
echo "Changing tables...<br />";
$query[] = "ALTER TABLE `bookingperiods` ADD `name` VARCHAR( 255 ) NOT NULL AFTER `number`;";

foreach ($query as $sql)
{
	database::executeQuery($sql);
}
echo "Table changed.<br /><br /><br /><br />";
echo "<br />";
echo "<br />";
echo "<br />";
echo "<span class='b'>To finish the update, sign in and in the administration section, set the period names to correct values. <br />
SAVE ONCE EVEN IF THEY ARE CORRECT BY DEFAULT</span>";
echo "<br />";
echo "<br />";
echo "Too continue, sign into the system <a href=\"../\">here</a>.";

?>
</div>
</body>
</html>

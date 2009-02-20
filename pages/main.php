Welcome to the online '<?php echo settings::get_systemTitle(); ?>'.<br /><br />
<center>

<?PHP
include_once("classes/booking.php");
if (booking::canBook())
{
	// if user can still book display forms to do so
?>
<iframe frameborder="0" scrolling="auto" src="monthView.php" name="calender" height="430px" width="240px"></iframe>
<iframe frameborder="0" scrolling="auto" src="" name="dayView" height="430px" width="528px"></iframe>
<?PHP
}
else
{
	// otherwise display booking limit exceeded message
	echo "You have reached your booking limit for this year.<br />\n";
	echo "No futher bookings can be made at this time.";
}
?>
</center>

<center>
<?php
$msg = settings::get_frontPageMessage();
if (!empty($msg))
{
	echo "<span class='frontPageAlert'>";
	echo $msg;
	echo "</span>";
	echo "<br /><br />";
}
?>
Please enter your details below.
<br /><br />

<form action="signon.php" method="post">
<table>
	<tr><td>Username:&nbsp;</td><td><input type="text" name="formUsername" /></td></tr>
	<tr><td>Password:&nbsp;</td><td><input type="password" name="formPassword" /></td></tr>
	<tr><td colspan="2"><center><input type="reset" value="Clear" /><input type="submit" value="Log In" /></center></td></tr>
</table>
</form>
</center>
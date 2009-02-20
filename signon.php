<?php
session_start();
include_once("classes/database.php"); 	// include the database class
include_once("classes/security.php");	// include the security class
include_once("general.php");

$username = $_POST['formUsername'];
$password = $_POST['formPassword'];


$signInSuccessfull = security::sign_in($username,security::generatePassword($password));

if ($signInSuccessfull == true)
{
	header('Location: ./');
	exit;
}
else
{
	header('Location: ./?page=Failed');
	exit;
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Signing In</title>
</head>

<body>
Sign In Processing...
<br /><br />
Page should proceed after a moment, if not <a href="?">proceed...</a>
</body>
</html>

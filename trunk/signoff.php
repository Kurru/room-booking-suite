<?php
session_start();
include_once("classes/security.php");
include_once("classes/database.php");
security::sign_out();
header('Location: ./');
exit;
?>

<?php

// Used to 'send users' either back to the login page from: //
//      * The 'logout' button on the 'main page' (mainPage.php) //
//      * When a user goes to the root (localhost) //

$_POST = array();
$_SESSION = array();
header("Location: parentLogin.php");

?>
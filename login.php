<?php

print_r($_POST);
$parentEmail = $_POST['email'];

$conn = new mysqli("localhost", "root", "", "parentlogin");
$response = $conn->query("SELECT * FROM parentlogininfo WHERE emailAddress LIKE CONCAT('$parentEmail', '%');");
$responseData = mysqli_fetch_all($response, MYSQLI_ASSOC);

if(count($responseData) == 0)
{
    echo "<h3>Login failed.</h3>";
}

?>

<html>
    <head>
        <title>Login</title>
    </head>
</html>
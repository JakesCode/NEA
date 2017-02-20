<?php
session_start();
//print_r($_POST);
//print_r($_SESSION);
error_reporting(E_ERROR);

function secondsToTime($seconds) {
    // http://stackoverflow.com/questions/8273804/convert-seconds-into-days-hours-minutes-and-seconds/19680778 //
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%a');
}

$firstName = $_SESSION['childInformation'][0]['Forename'];
$lastName = $_SESSION['childInformation'][0]['Surname'];
$tutor = $_SESSION['childTutor'][0]['firstName'] . " " . $_SESSION['childTutor'][0]['lastName'];

if(array_key_exists("singleDate", $_POST))
{
    $absenceDate = $_POST['singleDate'];
//    echo "<h1 class='alert-success jumbotron'>Complete!</h1>
//        <h3 class='alert alert-info' role='alert'>Your request has been submitted.</h3>";
} else
{
    $absenceDateFrom = $_POST['dateFrom'];
    $absenceDateFromAsPHPDate = date("m-d-Y", strtotime($absenceDateFrom));
    $absenceDateTo = $_POST['dateTo'];
    $absenceDateToAsPHPDate = date("m-d-Y", strtotime($absenceDateTo));
    $absenceDate = $absenceDateFrom . "." . $absenceDateTo;
}

if(isset($_POST['timeSelect']) && $_POST['timeSelect'] != "--:-- --")
{
    $absenceDate = $absenceDate . "|" . $_POST['timeSelect'];
}

$reason = $_POST['reasonBox'];
$parentEmail = $_SESSION['parentEmail'];

$conn = new mysqli("localhost", "root", "", "neaproject");
$query = "INSERT IGNORE INTO apprequests
(firstname, lastname, tutor, absences, reason, parentEmail)
VALUES
('$firstName', '$lastName', '$tutor', '$absenceDate', '$reason', '$parentEmail')";
$response = $conn->query($query);
?>
    <html>

    <head>
        <title>Complete</title>
        <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
    </head>

    <body>
    <?php
        // End User - if absence is longer than two days, needs to be approved by Head of Year //
    // If absence longer than three days, needs to be approved by Head of Sixth Form //
    
    if(secondsToTime(abs(strtotime($absenceDateTo) - strtotime($absenceDateFrom))) > 1 && secondsToTime(abs(strtotime($absenceDateTo) - strtotime($absenceDateFrom))) < 3)
    {
        echo "<h3 class='alert alert-warning jumbotron'>Since your child's absence is longer than two days, it must be approved by the <b><u>Head of Year</u></b>.<br><br><small>You will receive an email if the request is approved, or needs to be discussed.</small></h3>";
        
        // Find the ID of the absence request we just submitted //
        $conn = new mysqli("localhost", "root", "", "neaproject");
        $response = $conn->query("SELECT * FROM apprequests WHERE firstname = '$firstName' AND lastname = '$lastName' AND tutor = '$tutor' AND absences = '$absenceDate' AND reason = '$reason' AND parentEmail = '$parentEmail';");
        
        $urgentID = mysqli_fetch_all($response, MYSQLI_ASSOC)[0]['id'];
        
        // Add to 'urgent requests' database //
        $conn = new mysqli("localhost", "root", "", "neaproject");
        $query = "INSERT IGNORE INTO db
        (urgentID, severity)
        VALUES
        ('$urgentID', '1')";
        $response = $conn->query($query);
        
    } else if (secondsToTime(abs(strtotime($absenceDateTo) - strtotime($absenceDateFrom))) > 2)
    {
        echo "<h3 class='alert alert-danger jumbotron'>Since your child's absence is three days or longer, it must be approved by the <b><u>Head of Sixth Form</u></b>.<br><br><small>You will receive an email if the request is approved, or needs to be discussed.</small></h3>";
        
        $conn = new mysqli("localhost", "root", "", "neaproject");
        $response = $conn->query("SELECT * FROM apprequests WHERE firstname = '$firstName' AND lastname = '$lastName' AND tutor = '$tutor' AND absences = '$absenceDate' AND reason = '$reason' AND parentEmail = '$parentEmail';");
        
        $urgentID = mysqli_fetch_all($response, MYSQLI_ASSOC)[0]['id'];
        
        // Add to 'urgent requests' database //
        $conn = new mysqli("localhost", "root", "", "neaproject");
        $query = "INSERT IGNORE INTO db
        (urgentID, severity)
        VALUES
        ('$urgentID', '2')";
        $response = $conn->query($query);
    } else
    {
        echo "<h1 class='alert-success jumbotron'>Complete!</h1>
        <h3 class='alert alert-info' role='alert'>Your request has been submitted.</h3>";
    }
    ?>
    </body>

    </html>
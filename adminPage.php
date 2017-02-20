<html>

<head>
    <title>Admin Page</title>
    <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha256-/SIrNqv8h6QGKDuNoLGA4iret+kyesCkHGzVUUV0shc=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
    <style>
    th
    {
        padding-right: 5px;
    }
    </style>
</head>
    
<body>
    <h1>Admin Page - <a href="index.php">Logout</a></h1>
    
<!--https://www.tutorialspoint.com/html/html_tables.htm-->
    
<table border="1">
<tr>
<th>ID</th>
<th>Forename</th>
<th>Surname</th>
<th>Tutor</th>
<th>Absences</th>
<th>Reason for Absence</th>
<th>Parent Email</th>
</tr>
<?php
error_reporting(E_ERROR);
//print_r($_POST);
    
$conn = new mysqli("localhost", "root", "", "neaproject");
$response = $conn->query("SELECT * FROM apprequests;");
$responseData = mysqli_fetch_all($response, MYSQLI_ASSOC);

foreach($responseData as $singleStudent)
{
    $studentID = $singleStudent['id'];
    echo "<tr><td>$studentID</td>";
    $studentForename = $singleStudent['firstname'];
    echo "<td>$studentForename</td>";
    $studentSurname = $singleStudent['lastname'];
    echo "<td>$studentSurname</td>";
    $studentTutor = $singleStudent['tutor'];
    echo "<td>$studentTutor</td>";
    $studentAbsences = $singleStudent['absences'];
    echo "<td>$studentAbsences</td>";
    $studentReason = $singleStudent['reason'];
    echo "<td>$studentReason</td>";
    $studentParentEmail = $singleStudent['parentEmail'];
    echo "<td><a href='mailto:$studentParentEmail?subject=Response for Absence Request:  $studentAbsences for $studentForename $studentSurname&body=To whom it may concern,%0D%0AAn absence request for $studentForename $studentSurname has been submitted to us for the following dates:%0D%0A%0D%0A$studentAbsences%0D%0A%0D%0AADD COMMENT HERE%0D%0A%0D%0AThanks,%0D%0AThe Grammar School at Leeds (Login System)'>$studentParentEmail</a></td>";  
}
?>
</table>
    
</body>    

</html>
<?php

if (isset($_POST["email"]))
{   
    $parentEmail = $_POST['email'];
    $parentPassword = $_POST['password'];
    
    $conn = new mysqli("localhost", "root", "", "neaproject");
    $response = $conn->query("SELECT * FROM parentlogininfo WHERE emailAddress = '$parentEmail' AND password = '$parentPassword';");
    // For documentation ://
    // This bit allowed you to just put in the first letter of any valid email/account that was in the database, and that would let you in. //
    // Realised I was using 'LIKE' keyword. //
    // Then mention how I changed from CONCAT(x, y) to just x because CONCAT wasn't needed in the contexts I was using it in. //
    $responseData = mysqli_fetch_all($response, MYSQLI_ASSOC);

    if(count($responseData) == 0)
    {
        echo "<h3 class='alert-warning jumbotron'>That user couldn't be found.</h3>";
        
        // Retrieve admin password //
        $conn = new mysqli("localhost", "root", "", "neaproject");
        $response = $conn->query("SELECT * FROM admindb WHERE email = '$parentEmail' AND password = '$parentPassword';");
        $responseData = mysqli_fetch_all($response, MYSQLI_ASSOC);
        // End of retrieving admin password //
        if(sizeof($responseData) > 0)
        {
            if($parentEmail == $responseData[0]['email'] && $parentPassword == $responseData[0]['password'])
            {
//                $_SESSION['loggedIn'] = "y";
                header("Location: adminPage.php");
            }
        }
        
    } else
    {
        session_start();
        $_SESSION['parentEmail'] = $parentEmail;
        header('Location: mainPage.php');
        exit;
    }
}

if (isset($_POST["newEmail"]))
{
    $newFirstName = $_POST["newFirstName"];
    $newLastName = $_POST["newLastName"];
    $newEmail = $_POST["newEmail"];
    $newPassword = $_POST["newPassword"];
    $newPhoneNumber = $_POST["newPhoneNumber"];
    
    $validityCanary = True;
    
    foreach($_POST as $val)
    {
        if(empty($val))
        {
            $validityCanary = False;
        }
    }
    
    if((filter_var($newEmail, FILTER_VALIDATE_EMAIL)) && $validityCanary)
    {   
        // Check if anyone with that email already exists //
        
        $conn = new mysqli("localhost", "root", "", "neaproject");
        $response = $conn->query("SELECT * FROM parentlogininfo WHERE emailAddress = '$newEmail';");
        $responseData = mysqli_fetch_all($response, MYSQLI_ASSOC);
        if(count($responseData) > 0)
        {
            echo "<h4 class='alert-danger jumbotron'>Somebody with that email address already exists.<br>Please try logging in below.<h4>";
        } else
        {
            // If not, add them as a new user//
            
            // Get the ID of their child based on their dropdown input //

            if(isset($_POST["newChildL6"]) && !($_POST["newChildL6"] == "---- ....Select an L6 student.... ----"))
            {
                $yearGroup = "L6";
                $selectedChildBeforeSplit = explode(' ', $_POST['newChildL6']);
                $selectedChildForename = substr($selectedChildBeforeSplit[0], 0, -1);

                $conn = new mysqli("localhost", "root", "", "neaproject");
                $response = $conn->query("SELECT * FROM studentinformationl6 WHERE Surname = CONCAT('$selectedChildForename', '%') AND Forename = CONCAT('$selectedChildBeforeSplit[1]', '%');");
                $responseChild = mysqli_fetch_all($response, MYSQLI_ASSOC);
            } else
            {
                $yearGroup = "U6";
                $selectedChildBeforeSplit = explode(' ', $_POST['newChildU6']);
                $selectedChildForename = substr($selectedChildBeforeSplit[0], 0, -1);

                $conn = new mysqli("localhost", "root", "", "neaproject");
                $response = $conn->query("SELECT * FROM studentinformationu6 WHERE Surname LIKE CONCAT('$selectedChildForename', '%') AND Forename LIKE CONCAT('$selectedChildBeforeSplit[1]', '%');");
                $responseChild = mysqli_fetch_all($response, MYSQLI_ASSOC);
            }

            // End of child ID finding //
            print_r($responseChild);
            
            $childID = $responseChild[0]['id'];
            
            $conn = new mysqli("localhost", "root", "", "neaproject");
            $query = "INSERT INTO parentlogininfo (firstName, lastName, emailAddress, password, phoneNumber, childID, yearGroup)
            VALUES
            ('$newFirstName', '$newLastName', '$newEmail', '$newPassword', '$newPhoneNumber', '$childID', '$yearGroup')";
            $response = $conn->query($query);
            if($response == 1)
            {
                echo "<h4 class='alert-success jumbotron'>Success!<h4>";
            }
        }
    }
}

?>
    <html>

    <head>
        <title>Parent Login</title>
        <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha256-/SIrNqv8h6QGKDuNoLGA4iret+kyesCkHGzVUUV0shc=" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="bootstrap/css/bootstrap.css"> </head>
        
        <script type="text/javascript">
        $( document ).ready(function() {
            $('#newChildU6').prop('disabled', true);
            $('#newChildU6').css('background-color', 'lightgrey');
            $('#L6Checkbox').prop('checked', true);
        });
            
        function changeYearGroup(mode)
        {
            switch(mode)
            {
                case 'L6':
                    $('#newChildU6').prop('disabled', true);
                    $('#newChildL6').prop('disabled', false);
                    $('#newChildU6').css('background-color', 'lightgrey');
                    $('#newChildL6').css('background-color', 'white');
                    break;
                case 'U6':
                    $('#newChildL6').prop('disabled', true);
                    $('#newChildU6').prop('disabled', false);
                    $('#newChildL6').css('background-color', 'lightgrey');
                    $('#newChildU6').css('background-color', 'white');
                    break;
            }
        }
        </script>
    <body>
        <h1>Sign In</h1>
        <form method="POST" action="parentLogin.php">
            <input placeholder="Email" rows="1" style="width: 100%;" name="email">
            <input placeholder="Password" rows="1" style="width: 100%;" type="password" name="password">
            <input type="submit">
        </form>
        <hr>
        <h2>Register</h2>
        <form method="POST" action="parentLogin.php">
            <input placeholder="First Name" rows="1" style="width: 100%;" name="newFirstName" type="text">
            <input placeholder="Last Name" rows="1" style="width: 100%;" name="newLastName" type="text">
            <input placeholder="Email Address" rows="1" style="width: 100%;" name="newEmail" type="email">
            <input placeholder="Password" rows="1" style="width: 100%;" name="newPassword" type="password">
            <input placeholder="Phone Number" rows="1" style="width: 100%;" name="newPhoneNumber" type="tel">
            
            <input type="radio" id="L6Checkbox" name="YearCheckbox" onchange="changeYearGroup('L6')"> My child is in Lower Sixth.
            
            <select style="width: 100%;" name="newChildL6" id="newChildL6">
            <option>---- ....Select an L6 student.... ----</option>
            
            <?php
            $conn = new mysqli("localhost", "root", "", "neaproject");
            $response = $conn->query("SELECT * FROM `studentinformationl6`");
            $responseData = mysqli_fetch_all($response, MYSQLI_ASSOC);
                
            foreach($responseData as $singleStudent)
            {
                $studentSurname = $singleStudent['Surname'];
                $studentForename = $singleStudent['Forename'];
                $studentForm = $singleStudent['Reg'];
                
                echo "<option>$studentSurname, $studentForename ($studentForm)</option>";
            }
            ?>
            
            </select>
            
            <br><input type="radio" id="U6Checkbox" name="YearCheckbox" onchange="changeYearGroup('U6')" > My child is in Upper Sixth.
            
            <select style="width: 100%;" name="newChildU6" id="newChildU6">
            <option>---- ....Select a U6 student.... ----</option>

            <?php
            $conn = new mysqli("localhost", "root", "", "neaproject");
            $response = $conn->query("SELECT * FROM `studentinformationu6`");
            $responseData = mysqli_fetch_all($response, MYSQLI_ASSOC);
                
            foreach($responseData as $singleStudent)
            {
                $studentSurname = $singleStudent['Surname'];
                $studentForename = $singleStudent['Forename'];
                $studentForm = $singleStudent['Reg'];
                
                echo "<option>$studentSurname, $studentForename ($studentForm)</option>";
            }
            ?>
            
            </select>

            <br><input type="submit">
        </form>
    </body>

    </html>
<html>

<head>
    <?php
        
        session_start();
        $parentEmail = $_SESSION['parentEmail'];
        
        if(!(isset($parentEmail)))
        {
            header("Location: parentLogin.php");
        }
        
        $conn = new mysqli("localhost", "root", "", "parentlogin");
        $response = $conn->query("SELECT * FROM parentlogininfo WHERE emailAddress LIKE CONCAT('$parentEmail', '%');");
        $responseData = mysqli_fetch_all($response, MYSQLI_ASSOC);
        
        $parentFirstName = $responseData[0]['firstName'];
        $parentLastName = $responseData[0]['lastName'];
        
        echo "<h3 class='alert-info jumbotron'>Welcome back, $parentFirstName.</h3>";
        
        ?>
        <title>Search for a teacher....</title>
        <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha256-/SIrNqv8h6QGKDuNoLGA4iret+kyesCkHGzVUUV0shc=" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
        <script type="text/javascript">
            function selectTeacher($id) {
                // USE THIS JQUERY http://stackoverflow.com/questions/14460421/jquery-get-the-contents-of-a-table-row-with-a-button-click //
                alert($id);
                $('.' + $id).each(function () {
                    if ($(this).hasClass($id)) {
                        $chosenTeacherID = $(this).attr('id');
                    }
                });
            }
        </script>
</head>

<body>
    <h1>Type a form tutor's name here....</h1>
    <form method="POST">
        <input placeholder="Enter a form tutor's name...." name="teacherName" rows="1" style="width: 100%;" type="text">
        <br>
        <br>
        <input type="submit" name="submitButton" value='Search....' alt='Press the Search button, or press the Enter key to submit' /> </form>
    <?php
        if (isset($_POST["teacherName"]))
        {
            $conn = new mysqli("localhost", "root", "", "teachers");
            $teacherQuery = $_POST["teacherName"];
            
            if($teacherQuery == "")
            {
                echo "<b>No name was given. Showing all teachers instead....</b><br><br>";
            } else
            {
                echo "<b>Showing results for ' " . $teacherQuery . " '.</b><br><br>";
            }
            
            $response = $conn->query("SELECT * FROM teacherdata WHERE lastName LIKE CONCAT('$teacherQuery', '%') OR firstName LIKE CONCAT('$teacherQuery', '%') OR tutor LIKE CONCAT('$teacherQuery', '%');");
            $responseData = mysqli_fetch_all($response, MYSQLI_ASSOC);
        ?>
        <form method="POST" action="index.php">
            <table>
                <tr>
                    <th>Forename /</th>
                    <th>Surname /</th>
                    <th>Tutor Initials /</th>
                    <th>Year Group /</th>
                    <th>Select Teacher?</th>
                </tr>
                <?php
                for($index = 0; $index <= count($responseData) - 1; $index++)
                {
                    echo "<tr>";
                    $keys = array_keys($responseData[$index]);
                    for($innerIndex = 1; $innerIndex <= count($keys) - 1; $innerIndex++)
                    {
                        $chosenTeacherID = $responseData[$index][$keys[0]];
                        
                        if($responseData[$index][$keys[$innerIndex]] == 12)
                        {
                            echo "<td>Lower 6th (L6/12)</td>";
                        } elseif($responseData[$index][$keys[$innerIndex]] == 13)
                        {
                            echo "<td>Upper 6th (U6/13)</td>";
                        } else
                        {
                            echo "<td>" . $responseData[$index][$keys[$innerIndex]] . "</td>";
                        }
                    }
                    
                    $lastName = $responseData[$index]['lastName'];
                    $firstName = $responseData[$index]['firstName'];
                    
                    echo "<td><input type='submit' name='submitButton' value='Select $firstName $lastName'></input></td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </form>
        <?php
            $conn->close();
        }
        ?>
</body>

</html>
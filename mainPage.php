<html>

<head>
    <?php
        session_start();
        error_reporting(E_ERROR);
        

        $parentEmail = $_SESSION['parentEmail'];
        if($parentEmail == "")
        {
            $_POST = array();
            $_SESSION = array();
            header("Location: parentLogin.php");
        }
        
        $conn = new mysqli("localhost", "root", "", "neaproject");
        $response = $conn->query("SELECT * FROM parentlogininfo WHERE emailAddress LIKE CONCAT('$parentEmail', '%');");
        $responseData = mysqli_fetch_all($response, MYSQLI_ASSOC);
        
        $parentFirstName = $responseData[0]['firstName'];
        $parentLastName = $responseData[0]['lastName'];
        $childID = $responseData[0]['childID'];
    
        // Get teacher data from parent info //
        // This will involve looking at the form tutor of the linked child / Child ID //
    
        $conn = new mysqli("localhost", "root", "", "neaproject");
        if($responseData[0]['yearGroup'] == "L6")
        {
            $response = $conn->query("SELECT * FROM studentinformationl6 WHERE id LIKE $childID;");
        } else
        {
            $response = $conn->query("SELECT * FROM studentinformationu6 WHERE id LIKE $childID;");
        }
        $responseData = mysqli_fetch_all($response, MYSQLI_ASSOC);
        
        $_SESSION['childInformation'] = $responseData;
    
        $childForm = substr($responseData[0]['Reg'], 2);
        $childForename = $responseData[0]['Forename'];
        $childSurname = $responseData[0]['Surname'];

        $conn = new mysqli("localhost", "root", "", "neaproject");
        $response = $conn->query("SELECT * FROM teacherData WHERE tutor LIKE '$childForm';");
        $_SESSION['childTutor'] = mysqli_fetch_all($response, MYSQLI_ASSOC);
        // End of Tutor finding //
    
        echo "<h4 class='alert alert-info jumbotron'>Logged in as <u>$parentEmail</u> ($parentFirstName $parentLastName) - <a href='index.php' style='cursor: pointer'>Logout</a></h4>";
        echo "<h5 class='alert alert-success' jumbotron>Registering <b>$childForename $childSurname</b> to be absent.</h5>";
    ?>
    
        <title>Absence Form (Version 4)</title>
        <script src="https://code.jquery.com/jquery-3.1.0.min.js" integrity="sha256-cCueBR6CsyA4/9szpPfrX3s49M9vUU5BgtiJj06wt/s=" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
        <script type="text/javascript" src="teachers.json"></script>
        <script src="http://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
        <style>
            @import 'https://fonts.googleapis.com/css?family=Raleway';
            
            .ui-datepicker {
                background: #FFF none;
            }
            
            div.ui-datepicker
            {
                font-size: 15px;
                width: auto;
            }
        </style>
        <script type="text/javascript">
            // Regrettably Global Variables //
            var Teachers = {};
            var data = {};
            // Objects //
            class Teacher {
                constructor(TeacherObject) {
                    this.Tutor = TeacherObject.Tutor;
                    this.YearGroup = TeacherObject.YearGroup;
                    this.FullName = TeacherObject.FullName;
                    this.ID = TeacherObject.ID;
                }
            }
            class Student {
                constructor(Teacher, StudentName) {
                    this.StudentForm = Teacher;
                    this.StudentName = StudentName;
                }
            }
            class Request {
                constructor(Student, Dates) {
                    this.AbsentStudent = Student;
                    this.AbsenceDates = Dates;
                }
            }
            // Functions //
            $(document).ready(function () {
                $("#multiDateFrom, #multiDateTo").prop("disabled", true);
                $("#singleDateCheckbox").prop("checked", true);
                
//                $("#timeSelect").prop("disabled", true);
                $("#timeSelectCheckbox").prop("checked", false);
                
                $("#date").datepicker({
                    numberOfMonths: 1,
                    showButtonPanel: true,
                    minDate: new Date()
                });
                
                $("#multiDateFrom").datepicker({
                    numberOfMonths: 1,
                    showButtonPanel: true,
                    minDate: new Date()
                });
                
                $("#multiDateTo").datepicker({
                    numberOfMonths: 1,
                    showButtonPanel: true,
                    minDate: new Date()
                });
            });

            function swapDateInput(mode) {
                if (mode == 1) {
                    $("#multiDateFrom, #multiDateTo").prop("disabled", true);
                    $("#date").prop("disabled", false);
                }
                else {
                    $("#multiDateFrom, #multiDateTo").prop("disabled", false);
                    $("#date").prop("disabled", true);
                    $("#timeSelect").prop("disabled", true);
                }
            }
            
            function activateTimeSelect()
            {
                if($('#singleDateCheckbox').is(':checked'))
                {
                    $("#timeSelect").prop("disabled", false);
                } else
                {
                    $("#timeSelect").prop("disabled", true);
                }
            }
        </script>
</head>

<body style="font-family: 'Raleway', sans-serif;">
    <form method="POST" action="addToDatabase.php">
        <?php
            $chosenTeacherForename = array_pop(explode(' ', $_POST['submitButton']));
            $chosenTeacherSurname = explode(' ', $_POST['submitButton'])[1];

            $conn = new mysqli("localhost", "root", "", "neaproject");
            $response = $conn->query("SELECT * FROM teacherdata WHERE lastName = '$chosenTeacherForename' AND firstName = '$chosenTeacherSurname'");
            $responseData = mysqli_fetch_all($response, MYSQLI_ASSOC)[0];

            $firstNameLastNameCombo = $responseData['firstName'] . " " . $responseData['lastName'];

            $teacherFirstName = $_SESSION['childTutor'][0]['firstName'];
            $teacherLastName = $_SESSION['childTutor'][0]['lastName'];
            $teacherInitials = $_SESSION['childTutor'][0]['tutor'];
            $teacherYearGroup = $_SESSION['childTutor'][0]['yearGroup'];

            echo "<h4>$childForename's tutor's information: </h4>";
            echo "<h4><b>$teacherFirstName $teacherLastName</b></h4>";

            echo "<h4>Email: <a href='mailto:" . $teacherInitials . "@gsal.org.uk'>" . $teacherInitials . "@gsal.org.uk</a></h4>";
            echo "<h4>Tutor Group: " . $teacherYearGroup . $teacherInitials . "</h4>";
        ?>
            <hr noshade>
            <label>What date will your child be absent on?</label>
            <br>
            <input type="radio" id="singleDateCheckbox" name="dateModeSelect" onclick="swapDateInput(1);activateTimeSelect();">
            <label style="display: inline;"><u>Select a single date....</u></label>
            <input type="text" id="date" name="singleDate">
            <label>If your child will not be absent for the whole day, please specify the time they should be arriving to school below:</label><br>
            <input name="timeSelect" type="time" style="width: 100%" id="timeSelect"><br>
            <hr>
            <input type="radio" id="multiDateCheckbox" name="dateModeSelect" onclick="swapDateInput(2);">
            <label style="display: inline;"><u>Select multiple dates....</u> </label>
            <input type="text" id="multiDateFrom" name="dateFrom"> ----
            <input type="text" id="multiDateTo" name="dateTo">
            <br>
            <label style="font-size: 80%">Multiple Dates: Please select the day <i>before</i> your child returns to school, not the day they actually do.</label>
            <hr>
            <label>What is the reason for your child's absence?</label>
            <br>
            <input placeholder="What is your reason for absence?" rows="1" style="width: 100%" id="studentReasonBox" name="reasonBox">
            <br>
            <br>
            <input type="submit" value="Submit"></input>
    </form>
</body>

</html>
<html>

<head>
    <title>Mum Job</title>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="https://use.fontawesome.com/d3a2c09445.js"></script>
    <script
  src="http://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
  integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
  crossorigin="anonymous"></script>
    <?php
    session_start();
    
    if(count($_POST) > 0)
    {
        if(!(empty($_POST['jobDate']) || empty($_POST['jobAddress']) || $_POST['jobType'] == "Select Job Type...."))
        {
            $conn = new mysqli("localhost", "root", "", "mumJob");
            
            $jobDate = $_POST['jobDate'];
            $jobAddress = $_POST['jobAddress'];
            $jobType = $_POST['jobType'];
            
            // Check if job already exists //
            
            $response = $conn->query("SELECT * FROM jobs WHERE jobDate LIKE CONCAT('$jobDate', '%') AND jobAddress LIKE CONCAT('$jobAddress', '%') AND jobType LIKE CONCAT('$jobType', '%')");
            $responseData = mysqli_fetch_all($response, MYSQLI_ASSOC);
            if(count($responseData) == 0)
            {
                $query = "INSERT IGNORE INTO jobs
                (jobDate, jobAddress, jobType)
                VALUES
                ('$jobDate', '$jobAddress', '$jobType');";
                $response = $conn->query($query);
//                print_r($response);
            } else
            {
                echo("<h3 class='alert alert-danger jumbotron'>This job already exists. Please check below.</h3>");
            }
        }
    }
    
    $conn = new mysqli("localhost", "root", "", "mumJob");
    $response = $conn->query("SELECT * FROM jobs");
    $responseData = mysqli_fetch_all($response, MYSQLI_ASSOC);
    $_SESSION['responseData'] = $responseData;
    
    error_reporting(E_ERROR);
        
    if(isset($_POST['money']))
    {
        $money = $_POST['money'] . '|' . $_POST['expenses'];
        $address = $_SESSION['responseData'][0]["jobAddress"];
        $query = "UPDATE jobs
        SET expenses=$money
        WHERE jobAddress=$address";
        $response = $conn->query($query);
    }
    
    // URL Variables stuff (deleting from database) //
    if(null !== (htmlspecialchars($_GET["finish"])))
    {
        $jobAddress = htmlspecialchars($_GET["finish"]);
        $query = "UPDATE jobs
        SET completed='1'
        WHERE jobAddress='$jobAddress'";
        $response = $conn->query($query);
    }
    
    if(null !== (htmlspecialchars($_GET["del"])))
    {
        $jobAddress = htmlspecialchars($_GET["del"]);
        $query = "DELETE FROM jobs WHERE jobAddress='$jobAddress'";
        $response = $conn->query($query);
    }
    
    if(null !== (htmlspecialchars($_GET["money"])))
    {
        $jobAddress = htmlspecialchars($_GET["money"]);
        
        $_SESSION['address'] = $jobAddress;
        
        if(!($jobAddress == ""))
        {
            echo "<div class='bg bg-info'><h4 class='alert alert-info'>Editing money information for $jobAddress....</h4>";
        
            $response = $conn->query("SELECT * FROM jobs WHERE jobAddress='$jobAddress'");
            $responseData = mysqli_fetch_all($response, MYSQLI_ASSOC);

            echo "<form method='POST' action='index.php'>
            <label>Enter the total amount of money spent on this job: £</label>
            <input name='money' type='number' step='0.10' placeholder='0.00'>
            <label>Enter what this money was spent on (e.g. parking fee): </label>
            <input name='expenses' type='text' placeholder='Printer Ink'><br>
            <input type='submit' value='Submit'>
            </form><br></div>";
        }
    }
    ?>
    
    <script type="text/javascript">
        $( function() {
            $( "#datepicker" ).datepicker({minDate: new Date(), dateFormat: 'dd/mm/yy'});
        } );
    </script>
    
    <style type="text/css">
        th
        {
            padding-right: 4em;
        }
        
        .ui-datepicker {
            background: #FFF none;
        }

        div.ui-datepicker
        {
            font-size: 15px;
            width: auto;
        }
    </style>
</head>

<body>
    <h1>Inventory Management</h1>
    <hr>
    <h2>Add a new job</h2>
    <form method="POST" action="index.php">
        <input name="jobDate" type="text" id="datepicker">
        <input name="jobAddress" placeholder="Address" type="text">
        <select name="jobType">
            <option>Select Job Type....</option>
            <option>Check Out</option>
            <option>Check In</option>
            <option>Inventory</option>
            <option>Check In / Inventory</option>
            <option>Property Visit</option>
            <option>Smoke / CO Inspection</option>
        </select>
        <input type="submit">
    </form>
    <hr>
    <h2>View jobs</h2>
    <br>
    <table border="1">
        <tr>
            <th>Job Date</th>
            <th>Job Address</th>
            <th>Job Type</th>
            <th>Actions for this Job</th>
            <th>Expenses</th>
        </tr>
        <?php
        foreach($responseData as $singleJob)
        {
            $jobDate = $singleJob['jobDate'];
            $jobAddress = $singleJob['jobAddress'];
            $jobType = $singleJob['jobType'];
            
            echo "<tr>";
            echo "<td>$jobDate</td>";
            echo "<td>$jobAddress</td>";
            echo "<td>$jobType</td>";
            
            // Get completed status //
            $response = $conn->query("SELECT * FROM jobs WHERE jobDate LIKE CONCAT('$jobDate', '%') AND jobAddress LIKE CONCAT('$jobAddress', '%') AND jobType LIKE CONCAT('$jobType', '%')");
            $responseData = mysqli_fetch_all($response, MYSQLI_ASSOC);
            
            if($responseData[0]['completed'] == 1)
            {
                echo "<td align='center'><i class='fa fa-check fa-2x' title='This job has been marked as completed.' style='color: green'></i> <a href='index.php?del=$jobAddress' style='cursor: pointer' title='Delete this job.'><i class='fa fa-trash fa-2x'></i></a> <a href='index.php?money=$jobAddress' style='cursor: pointer; color: orange' title='Add money / expenses for this job.'><i class='fa fa-money fa-2x'></i></a></td>";
            } else
            {
                echo "<td align='center'><a href='index.php?finish=$jobAddress' style='cursor: pointer' title='Mark this job as complete.'><i class='fa fa-times fa-2x' style='color: red'></i></a></td>";
            }
            
            // Money and Expenses //
            
            
            echo "</tr>";
        }
        ?>
    </table>
    
</body>

</html>
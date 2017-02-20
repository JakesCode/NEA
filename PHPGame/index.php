<html>
    <head>
        <title>PHP Game</title>
        <script>
            <?php
            class Location
            {
                public $name;
                
                function __construct($locationName)
                {
                    $this->$name = $locationName;
                }
            }
            
            $locations = array("The Forest"=>new Location()->"m");
            ?>
        </script>
    </head>
    
    <body>
        <h1>PHP Game</h1>
        <hr>
    </body>
</html>
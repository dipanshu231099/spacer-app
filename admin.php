<?php

  	session_start();

    if(!isset($_SESSION['authenticated'])){
        header("Location: authenticate.php");
    }
    if(!($_SESSION['authenticated'])){
        header("Location: authenticate.php");
    }

    $config = include('config.php');
    $servername = $config["host"];
    $username = $config['username'];
    $password = $config['password'];
    $dbname = $config['dbname'];
    $conn = new mysqli($servername, $username, $password,$dbname);
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    if($_SERVER["REQUEST_METHOD"] == "POST"){

        $date = $_POST["date"];
        $a = strtotime($date);
        $newformat = date('M d Y',$a);
        $year =(int)date('Y',$a);
        $counters = $_POST["counters"];
        $status = $_POST["status"];
        $limit = $_POST["limit"];
        $limit = (int)$limit;

        $t1 = (string)$year;
        $t2 = (string)($year+1);
        $table_name = "calendar_".$t1."_".$t2;
        $query = "UPDATE $table_name SET status='".$status."', max_limit='".$limit."' , counters='".$counters."' WHERE date= '".$newformat."'";
        $results = $conn->query($query);
        if(!$results){
            die("sf");
        }

    }

?>

<?php
    $r1 = date("Y");   
    $a = "-01-01";
    $r1 = $r1.$a;                    //r1 and r2 are date of today and last day of nexy year  
    $r2 = (string)((int)$r1 + 1)."-12-31";
?>

<html>
	<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Spacer App </title>
        <link rel="stylesheet" href="stylesheets/style.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        
	</head>
	<body>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-1"></div>
                <div class="col-sm-10 landing-page">
                    <h1 style="text-align: center;">Spacer App</h1>
                    <div class="alert alert-info" role="alert"></div>
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                    
                        Enter date:<br>
                        
                        <input class="form-control"  type="date" id="date" name="date" value="" min=<?php echo $r1; ?> max=<?php echo $r2; ?>> <br>
                        
                        Enter number of counters:<br>
                        <input class="form-control" type="number" id="counters" placeholder="Enter" name="counters"><br>
                        
                        Enter limit:<br>
                        <input class="form-control" type="number" id="limit" placeholder="Enter" name="limit"><br>
                        
                        Select status:<br>
                        <select class="form-control" id="status" name="status">
                        <option>half</option>
                        <option>full</option>
                        <option>close</option>
                        </select><br>
                        
                        <input type="submit" value="Change settings">
                    </form>

                    <div class="container-fluid">
                        <div class="row">
                            
                            <form action="report.php" method="POST">
                                Enter the date of which report is generated:
                                <input class="form-control"  type="date" id="date" name="report_date" value=""> <br>
                                <input type="submit" value="Generate report">
                        
                            </form>
                        </div>
                    </div>

                
                </div>
            </div>
        </div>
	</body>
</html>

















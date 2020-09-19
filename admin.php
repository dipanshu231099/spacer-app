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
        $shop = $_POST["shop"];
        $limit = (int)$limit;

        $t1 = (string)$year;
        $t2 = (string)($year+1);
        //$table_name = "calendar_".$t1."_".$t2;
        $table_name = "";
        if($shop=="Groceries"){
            $table_name = "calendarGroceries";
        }
        else if($shop=="Groceries and Liquor") {
            $table_name = "calendargroceriesliquor";
        }
        else if($shop=="Liquor"){
            $table_name = "calendarLiquor";
        }
        $query = "UPDATE $table_name SET status='".$status."', max_limit='".$limit."' , counters='".$counters."' WHERE date= '".$newformat."'";
        $results = $conn->query($query);
        if(!$results){
            die("sf");
        }
        $_SESSION['message']="Changes made succesfully.";
        $_SESSION['good']=true;
        header("Location: admin_message.php");

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
        <title>ACSA admin </title>
        <link rel="stylesheet" href="stylesheets/style.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        
	</head>
	<body>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-1"></div>
                <div class="col-sm-10 landing-page">
                    <h1 style="text-align: center;">ACSA</h1>
                    <h4 style="text-align: center;">Army Canteen Scheduler App</h4>
                    <hr>
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                        <div class="row">
                            <div class="col">
                                <div class="alert alert-info" role="alert">
                                    The mentioned terms bring about following changes:
                                    <ul>
                                    <li><strong>Date:</strong> The date for which the values are to be altered.</li>
                                    <li><strong>Number of counters:</strong> number of counters available on a day. By default everyday the number of counters are assumed to be 3(1 for liquor and 2 for groceries).</li>
                                    <li><strong>Limit per counter:</strong> The maximum number of customers allowed during one time slot. By default it is 15 for liquor and 4 for grocery customers for every 30 minutes.</li>
                                    <li><strong>Status:</strong> It refers to whether the canteen is open for 
                                        <ul>
                                            <li>full day (9:30am to 3:30pm with lunch time) </li>
                                            <li>or, half day (9:30am to 1:00pm) </li>
                                            <li>or, close (canteen will not open at all) </li>
                                            <li>By default sundays are closed and saturdays are half days rest are full working days.</li>
                                            <li>Canteen will remain closed on last two days of a month.</li>
                                        </ul>
                                    </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group row">
                                    <label for="date" class="col-sm-2 col-form-label">Date</label>
                                    <div class="col-sm-10">
                                    <input class="form-control"  type="date" id="date" name="date" value="" min=<?php echo $r1; ?> max=<?php echo $r2;?> required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Type</label>
                                    <div class="col-sm-10">
                                    <select class="form-control" id="shop" name="shop" required>
                                        <option>Groceries</option>
                                        <option>Liquor</option>
                                        <option>Groceries and Liquor</option>
                                    </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Number of counters</label>
                                    <div class="col-sm-10">
                                    <input class="form-control" type="number" id="counters" placeholder="Enter" name="counters" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="date" class="col-sm-2 col-form-label">Limit per counter</label>
                                    <div class="col-sm-10">
                                    <input class="form-control" type="number" id="limit" placeholder="Enter" name="limit" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Status</label>
                                    <div class="col-sm-10">
                                    <select class="form-control" id="status" name="status" required>
                                        <option>half</option>
                                        <option>full</option>
                                        <option>close</option>
                                    </select>
                                    </div>
                                </div>
                                
                                <br>
                                <button type="submit" class="btn btn-outline-success w-100">Submit changes</button>
                            </div>
                            
                        </div>
                    </form>
                    <hr>
                    <form action="report.php" method="POST">
                        <div class="row">
                            
                            <div class="col">
                                <div class="alert alert-info" role="alert">
                                    To generate the customer schedule for a given date.
                                </div>
                            </div>
                            <div class="col">
                                <label>Enter the date of which report is generated:</label>
                                <input class="form-control"  type="date" id="date" name="report_date" value="" required><br>
                                <label>Enter counter type</label>
                                <select class="form-control" id="shop" name="shop" required>
                                        <option>Groceries</option>
                                        <option>Liquor</option>
                                        <option>Groceries and Liquor</option>
                                        
                                </select><br>
                                <button type="submit" class="btn btn-outline-success w-100">Generate Report</button>
                            </div>
                        


                        </div>
                    </form>
                    <form>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="alert alert-info" role="alert">
                                    For every new year it is advised to remake the default year-Table structure. Thus ensuring that dates corresponding to that year are known to to the application. Also if you wish to go back to the default settings for a year, making the present year table will do so.
                                    <br>If you have made a request to do so, it will take some time, kindly be patient, you will be automatically redirected, once the process is complete.
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <a href="populate.php"><button type="button" class="btn btn-outline-success w-100">Make present year-Table</button></a>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <a href="index.php"><button type="button" class="btn btn-success" style="width:100%;">Back to Home</button></a>
                </div>
            </div>
        </div>
	</body>
    <!-- Footer -->
    <footer class="page-footer font-small blue">

    <div class="footer-copyright text-center py-2">© 2020 Copyright:
        <a href="acknowledgement.php">Team Page</a>
    </div>

    </footer>
</html>

















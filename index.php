<?php
    session_start();
    $config = include('config.php');
    $servername = $config["host"];
    $username = $config['username'];
    $password = $config['password'];
    $dbname = $config['dbname'];
    $conn = new mysqli($servername, $username, $password,$dbname);
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

	date_default_timezone_set("Asia/Kolkata");

    function isHoliday($tp,$conn,$type){
        $date = date("M d Y",$tp);
        $current_year = date("Y",strtotime("today"));
        $table_name = "calendar".$type;
        $status_query = "SELECT status from $table_name WHERE date='".$date."';";
        $status = (($conn->query($status_query))->fetch_assoc())['status'];
        if($status=="close"){
            return true;
        }
        else {
            return false;
        }
    }
    
    function isWorkingHour($tp,$conn,$type){
        $timestring = date("Hi",$tp);
        $date = date("M d Y",$tp);
        $current_year = date("Y",strtotime("today"));
        $table_name = "calendar".$type;
        $sql = "SELECT * FROM $table_name WHERE date='".$date."';";
        $result = ($conn->query($sql))->fetch_assoc();
        
        $startH = $result['startH'];
        $startM = $result['startM'];
        $endH = $result['endH'];
        $endM = $result['endM'];

        $first_slot = date("Hi",strtotime("$startH:$startM"));
        $last_slot = date("Hi",strtotime("$endH:$endM -30 minutes"));
        
        if(date("H",$tp)!="13"){
            if($timestring>=$first_slot && $timestring<=$last_slot)return true;
        }
        return false;
    }
    
?>

<?php
    $rank = $name = $contact = $timestamp = "";
    $liquor = false;
    $groceries = false;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
//POST variables here------

        $name = test_input($_POST["name"]);

        $rank = test_input($_POST['rank']);

        $card_name = test_input($_POST["card_name"]);

        $contact = test_input($_POST["contact"]);

        //$timestamp_groceries = $_POST["timestamp_groceries"];
        //$timestamp_liquor= $_POST["timestamp_liquor"];
        
        //$endTime_groceries = date("h:ia M d Y",strtotime("+30 minutes", strtotime($_POST["timestamp_groceries"])));
        //$endTime_liquor = date("h:ia M d Y",strtotime("+30 minutes", strtotime($_POST["timestamp_liquor"])));

        $_SESSION['liquor']=$_SESSION['groceries']=false;
        $_SESSION['message_groceries']=$_SESSION['message_liquor']=NULL;

        if(isset($_POST['liquor']))
        {
            $_SESSION['liquor']=true;

            $liquor_card = test_input($_POST['liquor_card']);
            $timestamp_liquor = $_POST["timestamp_liquor"];
            $endTime_liquor = date("h:ia M d Y",strtotime("+30 minutes", strtotime($_POST["timestamp_liquor"])));

            $date_of_booking = strtotime(date("M d Y" , strtotime($timestamp_liquor)));

            $query_for_liquor = "SELECT * from bookingsLiquor WHERE card_id='$liquor_card' ORDER BY st_sec desc limit 1;";

            $last_booking_liquor = $conn->query($query_for_liquor);

            if(!$last_booking_liquor){
                die("failed liquor booking");
            }

            $last_booking_liquor = $last_booking_liquor->fetch_assoc();

            $last_date_liquor = $last_booking_liquor["st_sec"];

            $difference_liquor = ($date_of_booking-$last_date_liquor)/86400;

            $difference_liquor = abs((int)$difference_liquor);

            $_SESSION['liquor_fail']=false;

            if($difference_liquor<=10){
                $_SESSION["liquor_fail"] = true;
            }

            $current_year = date("Y",strtotime('today'));
            $date = date("M d Y",strtotime($_POST["timestamp_liquor"]));

            $cal_table_name = "calendarLiquor";

            // to fetch from caendar of liquor
            $sql = "SELECT * FROM $cal_table_name WHERE date='".$date."';";

            // catching the results
            $result = ($conn->query($sql))->fetch_assoc();
            $max_limit = $result['max_limit'];
            $total_counters = $result['counters'];

            //queries to select from table for liquor
            $query_count = "SELECT COUNT(*) as cnt from bookingsLiquor where start_time='".$timestamp_liquor."';";
            $query_token = "SELECT COUNT(*) as cnt from bookingsLiquor where start_time like '%".$date."';";
            
            // tells number of people already present with the exact timestamp (slot+date)
            $count_timestamp = (($conn->query($query_count))->fetch_assoc())['cnt'];
            
            // tells number of people already present on that day(only date)
            $count_token = (($conn->query($query_token))->fetch_assoc())['cnt'];

            $token = $count_token+1;
            $counter = (int)(($count_timestamp/$max_limit)) + 1;//extra liquor counters for liquor counter
            if($counter>$total_counters){
                die("sorry the spots just got filled");
            }

            if(!$_SESSION['liquor_fail']){
                $insert_sql = "INSERT INTO bookingsLiquor VALUES ('$liquor_card', '$name', '$timestamp_liquor', '$contact', '$counter', '$rank', '$card_name', $token, $date_of_booking);";
                $result = $conn->query($insert_sql);
                if(!$result){
                    die("Error processing the request.");
                }
                $_SESSION['message_liquor']="Your request to buy Liquor was successful.<br><br>
                Please visit Army Canteen, Palace Colony, Mandi, HP, India - 175001 between ". date('H:ia',strtotime($timestamp_liquor)) ." and ". date('H:ia',strtotime($endTime_liquor))." on ".date('M d Y',strtotime($timestamp_liquor))."<br> at counter number: $counter <br>Your token number: $token. <br>
                Liquor Card number: $liquor_card <br>
                Kindly collect your items within this time frame. <br>
                Please take a screenshot of this e-appointment to validate yourself at the gate/counter.";
            }
            else {
                $_SESSION['message_liquor']="You must wait for at least 10 days, before making a request to buy Liquor. <br>Last booking was made on ". date('M d Y',$last_date_liquor);
            }
        }


        if(isset($_POST['groceries'])){

            $_SESSION['groceries']=true;

            $grocery_card = test_input($_POST['grocery_card']);
            $timestamp_groceries = $_POST["timestamp_groceries"];
            $endTime_groceries = date("h:ia M d Y",strtotime("+30 minutes", strtotime($_POST["timestamp_groceries"])));

            $date_of_booking = strtotime(date("M d Y" , strtotime($timestamp_groceries)));

            $query_for_gro = "SELECT * from bookingsGroceries WHERE card_id='$grocery_card' ORDER BY st_sec desc limit 1;";

            $last_booking_gro = $conn->query($query_for_gro);

            if(!$last_booking_gro){
                die("failed grocery booking");
            }

            $last_booking_gro = $last_booking_gro->fetch_assoc();

            $last_date_Groceries = $last_booking_gro["st_sec"];

            $difference_Groceries = ($date_of_booking-$last_date_Groceries)/86400;

            $difference_Groceries = abs((int)$difference_Groceries);

            $_SESSION['grocery_fail']=false;

            if($difference_Groceries<=10){
                $_SESSION["groceries_fail"] = true;
            }

            $current_year = date("Y",strtotime('today'));
            $date = date("M d Y",strtotime($_POST["timestamp_groceries"]));

            $cal_table_name = "calendarGroceries";

            // to fetch from caendar of groceries
            $sql = "SELECT * FROM $cal_table_name WHERE date='$date';";

            // catching the results
            $result = ($conn->query($sql))->fetch_assoc();
            $max_limit = $result['max_limit'];
            $total_counters = $result['counters'];
            
            //query to get  number of liqour counters
            $qqq = "SELECT counters FROM calendarLiquor WHERE date='$date';";
            $qqq_result = ($conn->query($qqq))->fetch_assoc();
            $liquor_counters= $qqq_result['counters'];

            //queries to select from table for groceries
            $query_count = "SELECT COUNT(*) as cnt from bookingsGroceries where start_time='".$timestamp_groceries."';";
            $query_token = "SELECT COUNT(*) as cnt from bookingsGroceries where start_time like '%".$date."';";
            
            // tells number of people already present with the exact timestamp (slot+date)
            $count_timestamp = (($conn->query($query_count))->fetch_assoc())['cnt'];
            
            // tells number of people already present on that day(only date)
            $count_token = (($conn->query($query_token))->fetch_assoc())['cnt'];

            $token = $count_token+1;
            $counter = (int)(($count_timestamp/$max_limit)) + 1 + $liquor_counters;//extra +1 for liquor counter
            if($counter - $liquor_counters >$total_counters){
                die("sorry the spots just got filled");
            }

            if(!$_SESSION['groceries_fail']){
                $insert_sql = "INSERT INTO bookingsGroceries VALUES ('$grocery_card', '$name', '$timestamp_groceries', '$contact', '$counter', '$rank', '$card_name', $token, $date_of_booking);";
                $result = $conn->query($insert_sql);
                if(!$result){
                    die("Error processing the request.");
                }
                $_SESSION['message_groceries']="Your request to buy Groceries was successful.<br><br>
                Please visit Army Canteen, Palace Colony, Mandi, HP, India - 175001 between ". date('H:ia',strtotime($timestamp_groceries)) ." and ". date('H:ia',strtotime($endTime_groceries))." on ".date('M d Y',strtotime($timestamp_groceries))."<br> at counter number: $counter <br>Your token number: $token. <br>
                Grocery Card number: $grocery_card <br>
                Kindly collect your items within this time frame. <br>
                Please take a screenshot of this e-appointment to validate yourself at the gate/counter.";
            }
            else {
                $_SESSION['message_groceries']="You must wait for at least 10 days, before making a request to buy Groceries. <br>Last booking was made on ". date('M d Y',$last_date_Groceries);
            }
        }
        header("Location: message.php");
        exit();
    }
    
    function test_input($data) {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      return $data;
    }
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACSA Home</title>
    <link rel="stylesheet" href="stylesheets/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-1 "></div>
            <div class="col-sm-10 landing-page">
                <h1 style="text-align: center;">ACSA</h1>
                <h4 style="text-align: center;">Army Canteen Scheduler App</h4>
                <hr>
                <div class="alert alert-info" role="alert">
                    <p>
                   <strong>Welcome to ARTRAC ESM Canteen Mandi</strong>
                        <br>
                        <br>
                        Booking Policy
                        <br>
                        <br>
                        #  Only Card / authority letter holder is allowed to get inside the Canteen
                        <br>
                         <br>
                        #  Visit after getting an e-appointment.
                        <br>
                        <br>
                        #  Wearing of mask is mandatory.
                        <br>
                        <br>
                        #  Follow social distancing and COVID-19 instructions.
                        <br>
                        <br>
                        # Payment through ATM/credit/debit cards only
                        <br>
                        <br>
                        # Entry after showing e-appointment and CSD Card at the gate.
                        <br>
                        <br>
                        # Next visit to canteen permitted after 10 days.
                        <br>
                        <br>
                        # Visit Canteen on given date and time
                        <br>
                        <br>
                        # Saturday half-day
                        <br>
                        <br>
                        # Sunday/gazetted holidays closed
                        <br>
                        <br>
                        # Last two days of every month - Stock Taking
                        <br>
                        <br>
                        # For assistance contact - 01905 - 223450
                        <br>
                        <br>
                        We request your cooperation in providing service to maximum customers.
                        <br>
                        <br>

                        <strong> Stay Home Stay Safe </strong><br><br> Maj KS Thakur
                    </p>
                </div>
                <hr>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
                    <div class="form-group row">
                        <label for="rank" class="col-sm-2 col-form-label">Rank</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control is-valid" id='rank' name="rank" placeholder="Rank (optional)">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id='user_name' name="name" placeholder="Name" required>
                            <div class="invalid-feedback">
                                No empty string or special chars allowed.
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Mobile number</label>
                        <div class="col-sm-10">
                            <input type="number" id='contact_number' class="form-control" name="contact" placeholder="Mobile number" required>
                            <div class="invalid-feedback">
                                The number must be exactly 10 digits in length.
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Name as on card</label>
                        <div class="col-sm-10">

                            <input type="text" class="form-control" id='card_name' name="card_name" placeholder="Name as on the card. This will be used to verify you." required>

                            <div class="invalid-feedback">
                                No empty string or special chars allowed.
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h4 class='text-center'>What items you wish to buy?</h4>
                    <div class="row">
                        
                        <div class="col"  style='border-right:solid 1px black'>
                            <div class="form-check text-center">
                                <input type="checkbox" class="form-check-input" name="liquor" id='liquor'>
                                <label class="form-check-label" for="liquor">Liquor</label>
                            </div>
                            <div class="form-group">
                            <br>
                            <label for="visit_time">List of time windows available for liquor</label>
                            <select class="form-control" id="dropdown_liquor" name="timestamp_liquor" disabled>
                            <?php
                                date_default_timezone_set("Asia/Kolkata");
                                $present_time = strtotime('next hour');
                                $presnt_minute = (int)date("i",$present_time);
                                $present_time = strtotime("-$presnt_minute minutes",$present_time);
                                $new_tp = $present_time;

                                $slots = 20;
                                while($slots>0){
                                    $new_timestamp = date("h:ia M d Y",$new_tp);
                                    $current_year = date("Y",strtotime('today'));
                                    $date = date("M d Y",$new_tp);
                                    $table = "calendarLiquor";
                                    $sql = "SELECT * FROM $table WHERE date='".$date."';";
                                    $result = ($conn->query($sql))->fetch_assoc();
                                    $max_limit = $result['max_limit'];
                                    $total_counters = $result['counters'];
                                    while(isHoliday($new_tp,$conn,"Liquor")){
                                        $new_tp = strtotime('+24 hours',$new_tp);
                                    }
                                    if(isWorkingHour($new_tp,$conn,"Liquor")){
                                        $sql = "SELECT COUNT(*) as cnt from bookingsLiquor where start_time='".$new_timestamp."';";
                                        $results = (($conn->query($sql))->fetch_assoc())['cnt'];
                                        if($results<$max_limit*$total_counters){
                                            echo "<option>". $new_timestamp ."</option>";
                                            $slots--;
                                        }
                                    }
                                    $new_tp = strtotime("+30 minutes",$new_tp);
                                }
                            ?> 
                            </select>
                            </div>
                            <label for="liquor_card" class="col-form-label">Liquor card number</label>
                            <input type="text" id='liquor_card' class="form-control" name="liquor_card" placeholder="Liquor card number. This will be used to verify you." disabled required>
                            <div class="invalid-feedback">
                                Must be 17 characters long
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-check text-center">
                                <input type="checkbox" class="form-check-input" name="groceries" id='groceries'>
                                <label class="form-check-label" for="groceries">Groceries</label>
                            </div>
                            <br>
                            <div class="form-group">
                            <label for="visit_time">List of time windows available for groceries</label>
                            <select class="form-control" id="dropdown_groceries" name="timestamp_groceries" disabled>
                            <?php
                                date_default_timezone_set("Asia/Kolkata");
                                $present_time = strtotime('next hour');
                                $presnt_minute = (int)date("i",$present_time);
                                $present_time = strtotime("-$presnt_minute minutes",$present_time);
                                $new_tp = $present_time;


                                $slots = 20;
                                while($slots>0){
                                    $new_timestamp = date("h:ia M d Y",$new_tp);
                                    $current_year = date("Y",strtotime('today'));
                                    $date = date("M d Y",$new_tp);
                                    $table= "calendarGroceries";
                                    $sql = "SELECT * FROM $table WHERE date='".$date."';";
                                    $result = ($conn->query($sql))->fetch_assoc();
                                    $max_limit = $result['max_limit'];
                                    $total_counters = $result['counters'];

                                    while(isHoliday($new_tp,$conn,"Groceries")){
                                        $new_tp = strtotime('+24 hours',$new_tp);
                                    }

                                    if(isWorkingHour($new_tp,$conn,"Groceries")){
                                        $sql = "SELECT COUNT(*) as cnt from bookingsGroceries where start_time='".$new_timestamp."';";
                                        $results = (($conn->query($sql))->fetch_assoc())['cnt'];
                                        if($results<$max_limit*$total_counters){
                                            echo "<option>". $new_timestamp ."</option>";
                                            $slots--;
                                        }
                                    }
                                    $new_tp = strtotime("+30 minutes",$new_tp);
                                }
                            ?> 
                            </select>
                            </div>
                            <label for="grocery_card" class="col-form-label">Grocery card number</label>
                            <input type="text" id='grocery_card' class="form-control" name="grocery_card" placeholder="Grocery card number. This will be used to verify you." disabled required>
                            <div class="invalid-feedback">
                                Must be 17 characters long
                            </div>
                        </div>

                    </div>
                    <hr>
                    <button type="submit" id='submit' class="btn btn-success mb-2 w-100" disabled>Make Booking</button>
                </form>
            </div>
            <div class="col-sm-1 "></div>
        </div>
    </div>
</body>
<!-- Footer -->
<footer class="page-footer font-small blue">

<div class="footer-copyright text-center py-2">© 2020 Copyright:
    <a href="acknowledgement.php">Team Page</a>
  </div>

</footer>
<script src="javascripts/errors.js"></script>
</html>

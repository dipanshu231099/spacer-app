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

    function isHoliday($tp,$conn){
        $date = date("M d Y",$tp);
        $current_year = date("Y",strtotime("today"));
        $table_name = "calendar_".$current_year."_".($current_year+1);
        $status_query = "SELECT status from $table_name WHERE date='".$date."';";
        $status = (($conn->query($status_query))->fetch_assoc())['status'];
        if($status=="close"){
            return true;
        }
        else {
            return false;
        }
    }
    
    function isWorkingHour($tp,$conn){
        $timestring =date("Hi",$tp);
        $date = date("M d Y",$tp);
        $current_year = date("Y",strtotime("today"));
        $table_name = "calendar_".$current_year."_".($current_year+1);
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
    $name = $email = $contact = $timestamp = "";
    $liquor = false;
    $groceries = false;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //post variables here-------------------
        $name = test_input($_POST["name"]);
        $card_name = test_input($_POST["card_name"]);
        $contact = test_input($_POST["contact"]);
        $timestamp = $_POST["timestamp"];
        $endTime = date("h:ia M d Y",strtotime("+30 minutes", strtotime($_POST["timestamp"])));
        if(isset($_POST['liquor']))$liquor=true;
        if(isset($_POST['groceries']))$groceries=true;
        $liquor_card = test_input($_POST['liquor_card']);
        $grocery_card = test_input($_POST['grocery_card']);
        $rank = test_input($_POST['rank']);
        $date_of_booking = strtotime(date("M d Y" , strtotime($timestamp)));
        //queries for timestamp 
        $query_for_liq = "SELECT * from allotment WHERE liquor_card='".$liquor_card."'  ORDER BY st_sec_L desc limit 1;";
        $query_for_gro = "SELECT * from allotment WHERE liquor_card='".$liquor_card."' ORDER BY st_sec_G desc limit 1;";
        $last_booking_liq = $conn->query($query_for_liq);
        $last_booking_gro = $conn->query($query_for_gro);
        if(!$last_booking_liq){
            die("failed liq");
        }
        if(!$last_booking_gro){
            die("failed gro");
        }
        $last_booking_liq = $last_booking_liq->fetch_assoc();
        $last_booking_gro = $last_booking_gro->fetch_assoc();

        //finding time differences in bookings
        $last_date_Liqour = $last_booking_liq["st_sec_L"];
        $last_date_Groceries = $last_booking_gro["st_sec_G"];
        $difference_Liquor = ($date_of_booking-$last_date_Liqour)/86400;
        $difference_Liquor = (int)$difference_Liquor;
        $difference_Liquor = abs($difference_Liquor);
        $difference_Groceries = ($date_of_booking-$last_date_Groceries)/86400;
        $difference_Groceries = (int)$difference_Groceries;
        $difference_Groceries = abs($difference_Groceries);

        $_SESSION['liquor_fail']=$_SESSION['groceries_fail']=false;

        if($liquor==true){          
            if($difference_Liquor<=10){
                $liquor = false;
                $_SESSION["liquor_fail"] = true;
            }       
        }
        if($groceries==true){
            if($difference_Groceries<=10){
                $groceries = false;
                $_SESSION["groceries_fail"] = true;
            }
        }
        
        if($_SESSION["groceries_fail"] == true || $_SESSION["liquor_fail"] == true){
            header("Location: rule_message.php");
            exit();
        }
        else{
            $current_year = date("Y",strtotime('today'));
            $date = date("M d Y",strtotime($_POST["timestamp"]));
            $table_name = "calendar_".$current_year."_".($current_year+1);
            $sql = "SELECT * FROM $table_name WHERE date='".$date."';";
            $result = ($conn->query($sql))->fetch_assoc();
            $max_limit = $result['max_limit'];
            $total_counters = $result['counters'];

            //queries here----------
            $query_count = "SELECT COUNT(*) as cnt from allotment where start_time='".$timestamp."';";
            $query_token = "SELECT COUNT(*) as cnt from allotment where start_time like '%".$date."';";
            /*
            /tells number of people already present with the exact timestamp (slot+date)
            */
            $count_timestamp = (($conn->query($query_count))->fetch_assoc())['cnt'];
            /*
                tells number of people already present on that day(only date)
            */
            $count_token = (($conn->query($query_token))->fetch_assoc())['cnt'];

            $token = $count_token+1;
            $date_of_booking_G = NULL;
            $date_of_booking_L = NULL;
            $counter_number = (int)($count_timestamp/$max_limit)+1;
            if($groceries==true){
                $date_of_booking_G = $date_of_booking;
            }
            if($liquor==true){
                $date_of_booking_L = $date_of_booking;
            }
            
            $query_insertion = "INSERT INTO allotment (token,customer_name,contact,start_time,groceries,liquor,counter,rank,grocery_card,liquor_card,card_name,st_sec_L,st_sec_G) VALUES ($token,'".$name."','".$contact."','" . $timestamp . "',". ($groceries?1:0) .",".($liquor?1:0).",'".$counter_number."','".$rank."','".$grocery_card."','".$liquor_card."','".$card_name."','".(($date_of_booking_L==NULL)?NULL:$date_of_booking_L)."','".(($date_of_booking_G==NULL)?NULL:$date_of_booking_G)."');";
           
            if($count_timestamp>=12){
                $_SESSION['message'] = "Cannot allot the selected time as it just got fulfilled.";
                $_SESSION['good']=false;
                header("Location: message.php");
            }
            else {
                $results = $conn->query($query_insertion);
                $_SESSION['message']="Succesfully created your request. Please visit Army Canteen, Palace Colony, Mandi, HP, India - 175001 between <strong>".date('h:ia',strtotime($timestamp))."</strong> and <strong>".date('h:ia',strtotime( $endTime))."</strong> on <strong>".date('M d Y',strtotime($timestamp))."</strong> at counter number: <strong>".$counter_number. " with token number $token </strong><br>Grocery Card number: $grocery_card <br> Liquor Card number: $liquor_card<br>Kindly collect your items within this time frame.<br>Please<strong> take a photo/snapshot </strong>of this e-appointment to show at the gate/counter.<br>";
                $_SESSION['good']=true;
                header("Location: message.php");
            }
        }
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
                        #  Wearing of Mask is mandatory.
                        <br>
                        <br>
                        #  Follow Social distancing and COVID-19 instructions.
                        <br>
                        <br>
                        # Payment through ATM/credit/debit Cards only
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
                        We request your cooperation in providing service to maximum customers.
                        <br>
                        <br>

                        <strong> Stay Home Stay Safe </strong>
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
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Contact no.</label>
                        <div class="col-sm-10">
                            <input type="number" id='contact_number' class="form-control" name="contact" placeholder="Contact" required>
                            <div class="invalid-feedback">
                                The number must be exactly 10 digits in length.
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Card no.</label>
                        <div class="col-sm-5">

                            <label for="grocery_card" class="col-sm-2 col-form-label">Grocery</label>
                            <input type="text" id='grocery_card' class="form-control" name="grocery_card" placeholder="Grocery card number. This will be used to verify you." required>
                            <div class="invalid-feedback">
                                Must be 17 characters long
                            </div>
                        </div>
                        <div class="col-sm-5">

                            <label for="liquor_card" class="col-sm-2 col-form-label">Liquor</label>
                            <input type="text" id='liquor_card' class="form-control" name="liquor_card" placeholder="Liquor card number. This will be used to verify you." required>
                            <div class="invalid-feedback">
                                Must be 17 characters long
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
                    <h4>What items you wish to buy?</h4>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="groceries" id='groceries'>
                        <label class="form-check-label" for="groceries">Groceries</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="liquor" id='liquor'>
                        <label class="form-check-label" for="liquor">Liquor</label>
                    </div>
                    <hr>
                    <div class="form-group">
                        <h4>Select a time preferable to you.</h4>
                        <label for="visit_time">List of time windows available</label>
                        <select class="form-control" id="visit_time" name="timestamp">
                        <?php
                            date_default_timezone_set("Asia/Kolkata");
                            $present_time = strtotime('next hour');
                            $presnt_minute = (int)date("i",$present_time);
                            $present_time = strtotime("-$presnt_minute minutes",$present_time);
                            $new_tp = $present_time;


                            $slots = 500;
                            while($slots>0){
                                $new_timestamp = date("h:ia M d Y",$new_tp);
                                $current_year = date("Y",strtotime('today'));
                                $date = date("M d Y",$new_tp);
                                $table_name = "calendar_".$current_year."_".($current_year+1);
                                $sql = "SELECT * FROM $table_name WHERE date='".$date."';";
                                $result = ($conn->query($sql))->fetch_assoc();
                                $max_limit = $result['max_limit'];
                                $total_counters = $result['counters'];

                                while(isHoliday($new_tp,$conn)){
                                    $new_tp = strtotime('+24 hours',$new_tp);
                                }

                                if(isWorkingHour($new_tp,$conn)){
                                    $sql = "SELECT COUNT(*) as cnt from allotment where start_time='".$new_timestamp."';";
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
                    <hr>
                    <button type="submit" id='submit' class="btn btn-success mb-2 w-100" disabled>Make Booking</button>
                </form>
                <hr>
                <div class="alert alert-info" role="alert">
                    <a href="acknowledgement.php"><button type="button" class="btn btn-info w-100">Developer Acknowledgement</button></a>
                </div>
                <hr>
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

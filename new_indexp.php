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
    $groceriesliquor = false;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
//POST variables here------

        $name = test_input($_POST["name"]);

        $rank = test_input($_POST['rank']);

        $card_name = test_input($_POST["card_name"]);

        $contact = test_input($_POST["contact"]);

        $_SESSION['liquor']=$_SESSION['groceries']=$_SESSION['groceriesliquor']=false;
        $_SESSION['message_groceries']=$_SESSION['message_liquor']=$_SESSION['message_groceriesliquor']=NULL;

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

            $_SESSION['liquo_fail'] = false;

            if($difference_liquor<=10){
                $_SESSION["liquor_fail"] = true;
            } 
           
            // GROLIQ VERIFICATION
            $query_for_liq = "SELECT * from bookingsgroceriesliquor WHERE card_id_liquor='$liquor_card' AND l=1 ORDER BY st_sec desc limit 1;";

            $last_booking_liq = $conn->query($query_for_liq);

            if(!$last_booking_liq){
                die("failed liquor booking");
            }

            $last_booking_liq = $last_booking_liq->fetch_assoc();

            $last_date_liq = $last_booking_liq["st_sec"];

            $difference_liq = ($date_of_booking-$last_date_liq)/86400;

            $difference_liq = abs((int)$difference_liq);

            $_SESSION['liquo_fail'] = false;

            if($difference_liq<=10){
                $_SESSION["liquor_fail"] = true;
            }
            //END GROLIQ

            $current_year = date("Y",strtotime('today'));
            $date = date("M d Y",strtotime($_POST["timestamp_liquor"]));

            $cal_table_name = "calendarLiquor";

            // to fetch from calendar of liquor
            $sql = "SELECT * FROM $cal_table_name WHERE date='".$date."';";

            // catching the results
            $result = ($conn->query($sql))->fetch_assoc();
            $max_limit = $result['max_limit'];
            $total_counters = $result['counters'];

            //queries to select from table for liquor
            $query_count = "SELECT COUNT(*) as cnt from bookingsLiquor where start_time='".$timestamp_liquor."';";
       // $query_count2 = "SELECT COUNT(*) as cnt from bookingsgroceriesliquor where start_time='".$timestamp_liquor."';";

            //$query_token = "SELECT COUNT(*) as cnt from bookingsLiquor where start_time like '%".$date."';";
            $query_token = "SELECT max(token) as cnt from bookingsLiquor where start_time like '%".$date."';";
            //$query_token2 = "SELECT max(token) as cnt from bookingsgroceriesliquor where start_time like '%".$date."';";

            // tells number of people already present with the exact timestamp (slot+date)
            $count_timestamp = (($conn->query($query_count))->fetch_assoc())['cnt'];
            //$count_timestamp2 = (($conn->query($query_count2))->fetch_assoc())['cnt'];
            $count_timestamp = $count_timestamp + $count_timestamp2;
            // tells number of people already present on that day(only date)
            $count_token = (($conn->query($query_token))->fetch_assoc())['cnt'];
            //$count_token2 = (($conn->query($query_token2))->fetch_assoc())['cnt'];
            //$count_token = max($count_token,$count_token2);


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
                Please visit Army Canteen, Palace Colony, Mandi, HP, India - 175001 between ". date('H:i',strtotime($timestamp_liquor)) ." and ". date('H:i',strtotime($endTime_liquor))." on ".date('M d Y',strtotime($timestamp_liquor))."<br> at counter number: $counter <br>Your token number: $token. <br>
                Liquor Card number: $liquor_card <br>
                Kindly collect your items within this time frame. <br>
                Please take a screenshot of this e-appointment to validate yourself at the gate/counter.";
            }
            else {
                if($difference_liquor<=10) {
                    $_SESSION['message_liquor']="You must wait for at least 10 days after your previous visit, before making a request to buy Liquor. <br>Last booking was scheduled for ". date('M d Y',$last_date_liquor) ;
                } 
                else {
                    $_SESSION['message_liquor']="You must wait for at least 10 days after your previous visit, before making a request to buy Liquor. <br>Last booking was scheduled for " .date('M d Y',$last_date_liq);

                }
            }
        }


        if(isset($_POST['groceries'])){

            $_SESSION['groceries']=true;

            $grocery_card = test_input($_POST['grocery_card']);
            echo "1. " .$grocery_card ."<br>";
            $timestamp_groceries = $_POST["timestamp_groceries"];
            echo "2. " .$timestamp_groceries ."<br>";

            $endTime_groceries = date("h:ia M d Y",strtotime("+30 minutes", strtotime($_POST["timestamp_groceries"])));
            echo "3. " .$endTime_groceries ."<br>";

            $date_of_booking = strtotime(date("M d Y" , strtotime($timestamp_groceries)));
            echo "4. " .$date_of_booking ."<br>";

            $query_for_gro = "SELECT * from bookingsGroceries WHERE card_id='$grocery_card' ORDER BY st_sec desc limit 1;";
            echo "5. " .$query_for_gro ."<br>";

            $last_booking_gro = $conn->query($query_for_gro);

            if(!$last_booking_gro){
                die("failed grocery booking");
            }

            $last_booking_gro = $last_booking_gro->fetch_assoc();
            // echo "6. " .$last_booking_gro ."<br>";

            $last_date_Groceries = $last_booking_gro["st_sec"];
            echo "7. " .$last_date_Groceries ."<br>";

            $difference_Groceries = ($date_of_booking-$last_date_Groceries)/86400;
            echo "8. " .$difference_Groceries ."<br>";

            $difference_Groceries = abs((int)$difference_Groceries);
            echo "9. " .$difference_Groceries ."<br>";

            $_SESSION['grocery_fail']=false;

            if($difference_Groceries<=10){
                $_SESSION["groceries_fail"] = true;
            }
            //vaeificaation groliq with gro
            $query_for_grl = "SELECT * from bookingsgroceriesliquor WHERE card_id_groceries='$grocery_card' AND g=1 ORDER BY st_sec desc limit 1;";
            echo "5gl. " .$query_for_grl ."<br>";

            $last_booking_grl = $conn->query($query_for_grl);

            if(!$last_booking_grl){
                die("failed grocery booking");
            }

            $last_booking_grl = $last_booking_grl->fetch_assoc();
            // echo "6gl. " .$last_booking_gro ."<br>";

            $last_date_grl = $last_booking_grl["st_sec"];
            echo "7gl. " .$last_date_grl ."<br>";

            $difference_grl = ($date_of_booking-$last_date_grl)/86400;
            echo "8gl. " .$difference_grl ."<br>";

            $difference_grl = abs((int)$difference_grl);
            echo "9gl. " .$difference_grl ."<br>";

            //$_SESSION['grocery_fail']=false;

            if($difference_grl<=10){
                $_SESSION["groceries_fail"] = true;
            }
            //end groliq verification

            $current_year = date("Y",strtotime('today'));
            echo "10. " .$current_year ."<br>";

            $date = date("M d Y",strtotime($_POST["timestamp_groceries"]));
            echo "11. " .$date ."<br>";

            $cal_table_name = "calendarGroceries";

            // to fetch from caendar of groceries
            $sql = "SELECT * FROM $cal_table_name WHERE date='$date';";

            // catching the results
            $result = ($conn->query($sql))->fetch_assoc();
            $max_limit = $result['max_limit'];
            echo "12. " .$max_limit ."<br>";

            $total_counters = $result['counters'];
            echo "13. " .$total_counters ."<br>";

            //query to get  number of liqour counters
            $qqq = "SELECT counters FROM calendarLiquor WHERE date='$date';";
            $qqq_result = ($conn->query($qqq))->fetch_assoc();
            $liquor_counters= $qqq_result['counters'];
            echo "14. " .$liquor_counters ."<br>";


            //queries to select from table for groceries
        $query_count = "SELECT COUNT(*) as cnt from bookingsGroceries where start_time='".$timestamp_groceries."';";
            echo "15. " .$query_count ."<br>";
        $query_count2 = "SELECT COUNT(*) as cnt from bookingsgroceriesliquor where start_time='".$timestamp_groceries."';";
            //$query_token = "SELECT COUNT(*) as cnt from bookingsGroceries where start_time like '%".$date."';";
            $query_token = "SELECT max(token) as cnt from bookingsGroceries where start_time like '%".$date."';";
            $query_token2 = "SELECT max(token) as cnt from bookingsgroceriesliquor where start_time like '%".$date."';";

            echo "16. " .$query_token ."<br>";

            
            // tells number of people already present with the exact timestamp (slot+date)
            $count_timestamp = (($conn->query($query_count))->fetch_assoc())['cnt'];
            $count_timestamp2 = (($conn->query($query_count2))->fetch_assoc())['cnt'];
            echo "17. " .$count_timestamp ."<br>";

            // tells number of people already present on that day(only date)
            $count_token = (($conn->query($query_token))->fetch_assoc())['cnt'];
            $count_token2 = (($conn->query($query_token2))->fetch_assoc())['cnt'];

            $count_timestamp = $count_timestamp + $count_timestamp2;
            $count_token = max($count_token , $count_token2);

            $token = $count_token+1;

            $counter = (int)(($count_timestamp/$max_limit)) + 1 + $liquor_counters;//extra +1 for liquor counter

            if($counter - $liquor_counters >$total_counters){
                die("sorry the spots just got filled");
            }

            if(!$_SESSION['groceries_fail']){
                $insert_sql = "INSERT INTO bookingsGroceries VALUES ('$grocery_card', '$name', '$timestamp_groceries', '$contact', '$counter', '$rank', '$card_name', $token, $date_of_booking);";
                echo "21. " .$insert_sql ."<br>";

                $result = $conn->query($insert_sql);
                if(!$result){
                    die("Error processing the request.");
                }
                $_SESSION['message_groceries']="Your request to buy Groceries was successful.<br><br>
                Please visit Army Canteen, Palace Colony, Mandi, HP, India - 175001 between ". date('H:i',strtotime($timestamp_groceries)) ." and ". date('H:i',strtotime($endTime_groceries))." on ".date('M d Y',strtotime($timestamp_groceries))."<br> at counter number: $counter <br>Your token number: $token. <br>
                Grocery Card number: $grocery_card <br>
                Kindly collect your items within this time frame. <br>
                Please take a screenshot of this e-appointment to validate yourself at the gate/counter.";
            }
            else {
                if($difference_Groceries<=10) {
                    $_SESSION['message_groceries']="AAYou must wait for at least 10 days after your previous visit, before making a request to buy Groceries. <br>Last booking was scheduled for ". date('M d Y',$last_date_Groceries);
                }
                else {
                    $_SESSION['message_groceries']="AA1You must wait for at least 10 days after your previous visit, before making a request to buy Groceries. <br>Last booking was scheduled for ". date('M d Y',$last_date_grl);
                }
            }
        }

        if(isset($_POST['groceriesliquor'])){

            $_SESSION['groceriesliquor']=true;

            $gro_card = test_input($_POST['gro_card']);

            $liq_card = test_input($_POST['liq_card']);

            $timestamp_gl = $_POST["timestamp_groceriesliquor"];

            $endTime_gl = date("h:ia M d Y",strtotime("+30 minutes", strtotime($_POST["timestamp_groceriesliquor"])));

            $date_of_booking = strtotime(date("M d Y" , strtotime($timestamp_gl)));

           
            //checking groceries and liquor
            $query_for_gl = "SELECT * from bookingsgroceriesliquor WHERE card_id_groceries='$gro_card' AND card_id_liquor='$liq_card' AND g=1 AND l=1 ORDER BY st_sec desc limit 1;";
            $last_booking_gl = $conn->query($query_for_gl);
            if(!$last_booking_gl){
                die("fdfailed grocery and liquor booking");
            }

            $last_booking_gl = $last_booking_gl->fetch_assoc();

            $last_date_gl = $last_booking_gl["st_sec"];

            $difference_gl = ($date_of_booking-$last_date_gl)/86400;

            $difference_gl = abs((int)$difference_gl);
            $_SESSION['groceryliquor_fail']=false;

            if($difference_gl<=10){
                $_SESSION["groceriesliquor_fail"] = true;
            }
            //end groliq verification

           //checking groceries 
            $query_for_g = "SELECT * from bookingsGroceries WHERE card_id='$gro_card' ORDER BY st_sec desc limit 1;";
$query_for_g2 = "SELECT * from bookingsgroceriesliquor WHERE card_id_groceries='$gro_card' AND g=1 ORDER BY st_sec desc limit 1;";
            $last_booking_g = $conn->query($query_for_g);
            $last_booking_g2 = $conn->query($query_for_g2);
            if(!$last_booking_g || !$last_booking_g2){
                die("$query_for_g2");
            }

            $last_booking_g = $last_booking_g->fetch_assoc();
            $last_booking_g2 = $last_booking_g2->fetch_assoc();

            $last_date_g = $last_booking_g["st_sec"];
            $last_date_g2 = $last_booking_g2["st_sec"];
            $last_date_g = max($last_date_g , $last_date_g2);
            $difference_g = ($date_of_booking-$last_date_g)/86400;
            // echo "1456. " .$difference_g ."<br>";

            $difference_g = abs((int)$difference_g);
            $_SESSION['groceryliquor_fail']=false;
            $grocery_fail = 0;
            if($difference_g<=10){
                //$_SESSION["groceriesliquor_fail"] = true;
                $grocery_fail = 1; 
            }
            //end groceries verification

            //checking liquor 
            $query_for_l = "SELECT * from bookingsLiquor WHERE card_id='$liq_card' ORDER BY st_sec desc limit 1;";
$query_for_l2 = "SELECT * from bookingsgroceriesliquor WHERE card_id_liquor='$liq_card' AND l=1 ORDER BY st_sec desc limit 1;";
            $liquor_fail = 0;
            $last_booking_l = $conn->query($query_for_l);
            $last_booking_l2 = $conn->query($query_for_l2);

            if(!$last_booking_l || !$last_booking_l2){
                die("failed grocery and liquor booking");
            }

            $last_booking_l = $last_booking_l->fetch_assoc();
            $last_booking_l2 = $last_booking_l2->fetch_assoc();

            $last_date_l = $last_booking_l["st_sec"];
            $last_date_l2 = $last_booking_l2["st_sec"];
            $last_date_l = max($last_date_l , $last_date_l2);
            $difference_l = ($date_of_booking-$last_date_l)/86400;

            $difference_l = abs((int)$difference_l);
            //$_SESSION['groceryliquor_fail']=false;

            if($difference_l<=10){
                //$_SESSION["groceriesliquor_fail"] = true;
                $liquor_fail = 1;

            }
            if($grocery_fail==1 && $liquor_fail==1){
                $_SESSION["groceriesliquor_fail"] = true;
            }
            //end liquor verification

            $current_year = date("Y",strtotime('today'));

            $date = date("M d Y",strtotime($_POST["timestamp_groceriesliquor"]));

            $cal_table_name = "calendarGroceries";

            // to fetch from caendar of groceries and liquor
            $sql = "SELECT * FROM $cal_table_name WHERE date='$date';";

            // catching the results
            $result = ($conn->query($sql))->fetch_assoc();
            $max_limit = $result['max_limit'];
            $total_counters = $result['counters'];

            //query to get  number of liqour counters
            $qqq = "SELECT counters FROM calendarLiquor WHERE date='$date';";
            $qqq_result = ($conn->query($qqq))->fetch_assoc();
            $liquor_counters= $qqq_result['counters'];

            //queries to select from table for groceries and liquor
           
            $query_count = "SELECT COUNT(*) as cnt from bookingsgroceriesliquor where start_time='".$timestamp_gl."';";
            $query_count2 = "SELECT COUNT(*) as cnt from bookingsGroceries where start_time='".$timestamp_gl."';";
            //$query_token = "SELECT COUNT(*) as cnt from bookingsgroceriesliquor where start_time like '%".$date."';";
            $query_token = "SELECT max(token) as cnt from bookingsgroceriesliquor where start_time like '%".$date."';";
            $query_token2 = "SELECT max(token) as cnt from bookingsGroceries where start_time like '%".$date."';";
        
            
            // tells number of people already present with the exact timestamp (slot+date)
            $count_timestamp = (($conn->query($query_count))->fetch_assoc())['cnt'];
            $count_timestamp2 = (($conn->query($query_count2))->fetch_assoc())['cnt'];

            // tells number of people already present on that day(only date)
            $count_token = (($conn->query($query_token))->fetch_assoc())['cnt'];
            $count_token2 = (($conn->query($query_token2))->fetch_assoc())['cnt'];
            $count_timestamp  = $count_timestamp2 + $count_timestamp;
            $count_token  = max($count_token , $count_token2);

            $token = $count_token+1;
           
            $counter = (int)(($count_timestamp/$max_limit)) + 1 + $liquor_counters;//extra +1 for liquor counter
        
            if($counter - $liquor_counters >$total_counters){
                die("sorry the spots just got filled");
            }

            if(!$_SESSION['groceriesliquor_fail']){
                $g1 = 1;
                $g2 = 1;
                if($grocery_fail==1){
                    $g1 = 0;
                }
                if($liquor_fail==1){
                    $g2 = 0;
                }
                $insert_sql = "INSERT INTO bookingsgroceriesliquor VALUES ('$gro_card','$liq_card', '$name', '$timestamp_gl', '$contact', '$counter', '$rank', '$card_name', $token, $date_of_booking,$g1,$g2);";
                echo "21. " .$insert_sql ."<br>";

                $result = $conn->query($insert_sql);
                if(!$result){
                    die("Error processing the request.");
                }

                if($liquor_fail==0 && $grocery_fail==1) {
                    $_SESSION['message_groceriesliquor']="Your request to buy Groceries was <b>unsuccessful</b> and Liquor was <b>successful</b>.<br><br>
                Please visit Army Canteen, Palace Colony, Mandi, HP, India - 175001 between ". date('H:i',strtotime($timestamp_gl)) ." and ". date('H:i',strtotime($endTime_gl))." on ".date('M d Y',strtotime($timestamp_gl))."<br> at counter number: $counter <br>Your token number: $token. <br>
                Grocery Card number: $gro_card <br> Liquor Card number: $liq_card <br>
                Kindly collect your items within this time frame. <br>
                Please take a screenshot of this e-appointment to validate yourself at the gate/counter.";
                } 
                else if($liquor_fail==1 && $grocery_fail==0) {
                    $_SESSION['message_groceriesliquor']="Your request to buy Groceries was <b>successful</b> and Liquor was <b>unsuccessful</b>.<br><br>
                Please visit Army Canteen, Palace Colony, Mandi, HP, India - 175001 between ". date('H:i',strtotime($timestamp_gl)) ." and ". date('H:i',strtotime($endTime_gl))." on ".date('M d Y',strtotime($timestamp_gl))."<br> at counter number: $counter <br>Your token number: $token. <br>
                Grocery Card number: $gro_card <br> Liquor Card number: $liq_card <br>
                Kindly collect your items within this time frame. <br>
                Please take a screenshot of this e-appointment to validate yourself at the gate/counter.";
                } else {
                    $_SESSION['message_groceriesliquor']="Your request to buy Groceries and Liquor was <b>successful</b>.<br><br>
                    Please visit Army Canteen, Palace Colony, Mandi, HP, India - 175001 between ". date('H:i',strtotime($timestamp_gl)) ." and ". date('H:i',strtotime($endTime_gl))." on ".date('M d Y',strtotime($timestamp_gl))."<br> at counter number: $counter <br>Your token number: $token. <br>
                    Grocery Card number: $gro_card <br> Liquor Card number: $liq_card <br>
                    Kindly collect your items within this time frame. <br>
                    Please take a screenshot of this e-appointment to validate yourself at the gate/counter.";

                }
               
            }
            else {
                $_SESSION['message_groceriesliquor']="You must wait for at least 10 days after your previous visit, before making a request to buy Groceries and Liquor.";
                /*if($difference_gl==0){

                    $_SESSION['message_groceriesliquor']="You must wait for at least 10 days after your previous visit, before making a request to buy Groceries. <br>Last booking was scheduled for ". date('M d Y',$last_date_gl);

                }
                if($difference_g==4) {
                    $_SESSION['message_groceriesliquor']="You must wait for at least 10 days after your previous visit, before making a request to buy Groceries. <br>Last booking was scheduled for ". date('M d Y',$last_date_g);

                } 
                else {
                    $_SESSION['message_groceriesliquor']="You must wait for at least 10 days after your previous visit, before making a request to buy Groceries. <br>Last booking was scheduled for ". date('M d Y',$last_date_l);

                }*/

            }
            if($difference_gl<10 || $difference_g<10 || $difference_L<10 ){
//$_SESSION['message_groceriesliquor']="You must wait for at least 10 days after your previous visit, before making a request . <br>Last booking was scheduled for ". date('M d Y',max($last_date_l,$last_date_g , $last_date_gl));
                
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
                   <strong style="color:red">Welcome to ARTRAC ESM Canteen Mandi, Himachal Pradesh, India</strong>
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

                        <strong> Stay Home Stay Safe </strong><br><br> Maj KS Thakur<br>Manager, ARTRAC ESM Canteen<br>Mandi, HP, India
                    </p>
                </div>
                <hr>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
                    <div class="form-group row">
                        <label for="rank" class="col-sm-2 col-form-label"><b>Rank</b></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control is-valid" id='rank' name="rank" placeholder="Rank (optional)">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label"><b>Name</b></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id='user_name' name="name" placeholder="Name" required>
                            <div class="invalid-feedback">
                                No empty string or special chars allowed.
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label"><b>Mobile number</b></label>
                        <div class="col-sm-10">
                            <input type="number" id='contact_number' class="form-control" name="contact" placeholder="Mobile number" required>
                            <div class="invalid-feedback">
                                The number must be exactly 10 digits in length.
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label"><b>Name as on card</b></label>
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
                                <label class="form-check-label" for="liquor"><b>Liquor Only</b></label>
                            </div>
                            <div class="form-group">
                            <br>
                            <label for="visit_time"><b>List of time windows available for liquor</b></label>
                            <select class="form-control" id="dropdown_liquor" name="timestamp_liquor" disabled>
                            <?php
                                date_default_timezone_set("Asia/Kolkata");
                                $present_time = strtotime('next hour');
                                $presnt_minute = (int)date("i",$present_time);
                                $present_time = strtotime("-$presnt_minute minutes",$present_time);
                                $new_tp = $present_time;

                                $slots = 20;
                                while($slots>0){
                                    $new_timestamp = date("H:i M d Y",$new_tp);
                                    $current_year = date("Y",strtotime('today'));
                                    $date = date("M d Y",$new_tp);
                                    $table = "calendarLiquor";
                                    $sql = "SELECT * FROM $table WHERE date='".$date."';";
                                    $result = ($conn->query($sql))->fetch_assoc();
                                    $max_limit = $result['max_limit'];
                                    $total_counters = $result['counters'];
                                    while(isHoliday($new_tp,$conn,"Liquor")){
                                        $new_tp = strtotime('+30 minutes',$new_tp);
                                    }
                                    if(isWorkingHour($new_tp,$conn,"Liquor")){
                                        $sql = "SELECT COUNT(*) as cnt from bookingsLiquor where start_time='".$new_timestamp."';";
                                        //$sql2 = "SELECT COUNT(*) as cnt from bookingsgroceriesliquor where start_time='".$new_timestamp."';";

                                        $results = (($conn->query($sql))->fetch_assoc())['cnt'];
                                        //$results2 = (($conn->query($sql2))->fetch_assoc())['cnt'];
                                        //$results = $results + $results2;

                                        if($results<$max_limit*$total_counters){
                                            echo "<option>". $new_timestamp ."</option>";
                                            
                                        }
                                        $slots--;
                                    }
                                    $new_tp = strtotime("+30 minutes",$new_tp);
                                }
                            ?> 
                            </select>
                            </div>
                            <label for="liquor_card" class="col-form-label"><b>Liquor card number</b></label>
                            <input type="text" id='liquor_card' class="form-control" name="liquor_card" placeholder="Liquor card number. This will be used to verify you." disabled required>
                            <div class="invalid-feedback">
                                Must be 19 characters long.
                            </div>
                        </div>

                        <div class="col" style='border-right:solid 1px black'>
                            <div class="form-check text-center">
                                <input type="checkbox" class="form-check-input" name="groceries" id='groceries'>
                                <label class="form-check-label" for="groceries"><b>Groceries Only</b></label>
                            </div>
                            <br>
                            <div class="form-group">
                            <label for="visit_time"><b>List of time windows available for groceries</b></label>
                            <select class="form-control" id="dropdown_groceries" name="timestamp_groceries" disabled>
                            <?php
                                date_default_timezone_set("Asia/Kolkata");
                                $present_time = strtotime('next hour');
                                $presnt_minute = (int)date("i",$present_time);
                                $present_time = strtotime("-$presnt_minute minutes",$present_time);
                                $new_tp = $present_time;


                                $slots = 20;
                                while($slots>0){
                                    $new_timestamp = date("H:i M d Y",$new_tp);
                                    $current_year = date("Y",strtotime('today'));
                                    $date = date("M d Y",$new_tp);
                                    $table= "calendarGroceries";
                                    $sql = "SELECT * FROM $table WHERE date='".$date."';";
                                    $result = ($conn->query($sql))->fetch_assoc();
                                    $max_limit = $result['max_limit'];
                                    $total_counters = $result['counters'];

                                    while(isHoliday($new_tp,$conn,"Groceries")){
                                        $new_tp = strtotime('30 minutes',$new_tp);
                                    }

                                    if(isWorkingHour($new_tp,$conn,"Groceries")){
                                        $sql1 = "SELECT COUNT(*) as cnt from bookingsgroceriesliquor where start_time='".$new_timestamp."';";
                                        $sql2 = "SELECT COUNT(*) as cnt from bookingsGroceries where start_time='".$new_timestamp."';";

                                        $results1 = (($conn->query($sql1))->fetch_assoc())['cnt'];
                                        $results2 = (($conn->query($sql2))->fetch_assoc())['cnt'];
                                        $results = $results1 + $results2;

                                        if($results<$max_limit*$total_counters){
                                            echo "<option>". $new_timestamp ."</option>";
                                            
                                        }
                                        $slots--;
                                    }
                                    $new_tp = strtotime("+30 minutes",$new_tp);
                                }
                            ?> 
                            </select>
                            </div>
                            <label for="grocery_card" class="col-form-label"><b>Grocery card number</b></label>
                            <input type="text" id='grocery_card' class="form-control" name="grocery_card" placeholder="Grocery card number. This will be used to verify you." disabled required>
                            <div class="invalid-feedback">
                                Must be 19 characters long.
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check text-center">
                                <input type="checkbox" class="form-check-input" name="groceriesliquor" id='groceriesliquor'>
                                <label class="form-check-label" for="groceriesliquor"><b>Groceries and Liquor</b></label>
                            </div>
                            <br>
                            <div class="form-group">
                            <label for="visit_time"><b>List of time windows available for groceries and liquor</b></label>
                            <select class="form-control" id="dropdown_groceriesliquor" name="timestamp_groceriesliquor" disabled>
                            <?php
                                date_default_timezone_set("Asia/Kolkata");
                                $present_time = strtotime('next hour');
                                $presnt_minute = (int)date("i",$present_time);
                                $present_time = strtotime("-$presnt_minute minutes",$present_time);
                                $new_tp = $present_time;


                                $slots = 20;
                                while($slots>0){
                                    $new_timestamp = date("H:i M d Y",$new_tp);
                                    $current_year = date("Y",strtotime('today'));
                                    $date = date("M d Y",$new_tp);
                                    $table= "calendarGroceries";
                                    $sql = "SELECT * FROM $table WHERE date='".$date."';";
                                    $result = ($conn->query($sql))->fetch_assoc();
                                    $max_limit = $result['max_limit'];
                                    $total_counters = $result['counters'];

                                    while(isHoliday($new_tp,$conn,"Groceries")){
                                        $new_tp = strtotime('30 minutes',$new_tp);
                                    }

                                    if(isWorkingHour($new_tp,$conn,"Groceries")){
                                        $sql1 = "SELECT COUNT(*) as cnt from bookingsgroceriesliquor where start_time='".$new_timestamp."';";
                                        $sql2 = "SELECT COUNT(*) as cnt from bookingsGroceries where start_time='".$new_timestamp."';";

                                        $results1 = (($conn->query($sql1))->fetch_assoc())['cnt'];
                                        $results2 = (($conn->query($sql2))->fetch_assoc())['cnt'];
                                        $results = $results1 + $results2;

                                        if($results<$max_limit*$total_counters){
                                            echo "<option>". $new_timestamp ."</option>";
                                            
                                        }
                                        $slots--;
                                    }
                                    $new_tp = strtotime("+30 minutes",$new_tp);
                                }
                            ?> 
                            </select>
                            </div>
                            <label for="gro_card" class="col-form-label"><b>Grocery card number</b></label>
                            <input type="text" id='gro_card' class="form-control" name="gro_card" placeholder="Grocery card number. This will be used to verify you." disabled required>
                            <label for="liq_card" class="col-form-label"><b>Liquor card number</b></label>
                            <input type="text" id='liq_card' class="form-control" name="liq_card" placeholder="Liquor card number. This will be used to verify you." disabled required>
                            <div class="invalid-feedback">
                                Must be 19 characters long.
                            </div>
                        </div>

                    </div>
                    <hr>
                    <button type="submit" id='submit' class="btn btn-success mb-2 w-100" disabled>Make Booking</button>
                </form>
                <div class='alert alert-dark' role='alert'>
                    <b>Already had a booking ?</b>
                    <a href="old_bookings.php"><button class="btn btn-primary mb-2 w-100">Manage your previous bookings</button></a>
                </div>
                <div class='alert alert-warning' role='alert'>
                    <b>Admin ?</b>
                    <a href="admin.php"><button class="btn btn-warning mb-2 w-100">Admin Dashboard</button></a>
                </div>
            </div>
            <div class="col-sm-1 "></div>
        </div>
    </div>
</body>
<!-- Footer -->
<footer class="page-footer font-small blue">

<div class="footer-copyright text-center py-2">© 2021 Copyright:
    <a href="acknowledgement.php">Team Page</a>
  </div>

</footer>
<script src="javascripts/script1.js"></script>
</html>

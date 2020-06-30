<?php
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
        // die("$timestring");
        
        if(date("H",$tp)!="13"){
            if($timestring>=$first_slot && $timestring<=$last_slot)return true;
        }
        return false;
    }
    

    echo isWorkingHour(mktime(),$conn);

?>
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
	
	function is_last_two_day($date){
		$raw_date = strtotime($date);
		$present_month = date("M" , strtotime($date));
		$next_day_month = date("M" , strtotime("+24 hours" , $raw_date));
		$next_to_next_day_month = date("M" , strtotime("+48 hours" , $raw_date));
		
		if($next_to_next_day_month!=$present_month){
			return true;
			}
		return false;
		
	}

	function populate($year,$conn,$table_name){
		$a = (string)$year;
		$b = (string)($year+1);

		$q = "drop table if exists $table_name";
		$results = $conn->query($q);
		if(!$results)die("echo 'could not execute';");
		$q2 = "create table $table_name(date varchar(100),day varchar(20),status varchar(20),startH int(32),startM int(32),endH int(32),endM int(32),max_limit int(32),counters int(32))";
		$results2 = $conn->query($q2);
		if(!$results2)die("echo 'could not execute q2';");

		$flag=1;
    	$first=mktime(0, 0, 0, 1, 1, $year);
		$temp = $first;
		
		if($table_name=="calendarLiquor") {
		    $max_counters=1;
		} elseif ($table_name=="calendarGroceriesLiquor") {
		     $max_counters=2;
		} elseif ($table_name=="calendarGroceries") {
		    $max_counters=2;
		}
			if($table_name=="calendarLiquor") {
		    $max_limit=6;
		} elseif ($table_name=="calendarGroceriesLiquor") {
		     $max_limit=4;
		} elseif ($table_name=="calendarGroceries") {
		    $max_limit=5;
		}
// 		$max_counters=($table_name=="calendarLiquor")?1:($table_name=="calendarGroceriesLiquor")?2:2;
// 		$max_limit=($table_name=="calendarLiquor")?6:($table_name=="calendarGroceriesLiquor")?4:5;
// 		$max_counters=($table_name=="calendarLiquor")?1:2;
// 		$max_limit=($table_name=="calendarLiquor")?6:5;
		
    	while($flag==1){
	    	if(date("Y",$temp)==(string)$year+2){
	    		$flag=0;
	    		continue;
	    	}
	    	$day = date("l",$temp);
			$date = date("M d Y",$temp);
			if ($day=="Sunday" || is_last_two_day($date)) {
	    		$status = "close";
	    		$query2 = "INSERT INTO $table_name (date , day , status) VALUEs('".$date."','".$day."','".$status."')";
	    		$results2 = $conn->query($query2);
	    	}
	    	elseif($day=="Saturday"){
	    		$startH = 9;
	    		$startM = 30;
	    		$endH = 13;
	    		$endM = 0;
	    		$status = "half";
	    		$query1 = "INSERT INTO $table_name (date , day , status,startH,startM,endH,endM,max_limit,counters) VALUEs('".$date."',
	    		'".$day."','".$status."','".$startH."','".$startM."','".$endH."','".$endM."',$max_limit,$max_counters)";
	    		$results1 = $conn->query($query1);
	    	}
	    	
	    	else{
	    		$startH = 9;
	    		$startM = 30;
	    		$endH = 15;
	    		$endM = 30;
	    		$status = "full";
	    		
	    		$query3 = "INSERT INTO $table_name (date , day , status,startH,startM,endH,endM,max_limit,counters) VALUEs('".$date."',
	    		'".$day."','".$status."','".$startH."','".$startM."','".$endH."','".$endM."',$max_limit,$max_counters)";
	    		$results3 = $conn->query($query3);
	    	}
	    	$temp = strtotime('+24 hours',$temp);
    	}
	}

	if(!isset($_SESSION['authenticated'])){
        header("Location: authenticate.php");
    }
    if(!($_SESSION['authenticated'])){
        header("Location: authenticate.php");
	}
	else {
		echo "Reforming present year's table data. Please wait.";
		$present_year = date("Y",strtotime("today"));
		populate($present_year,$conn,"calendarGroceries");
		populate($present_year,$conn,"calendarGroceriesLiquor");
		populate($present_year,$conn,"calendarLiquor");
		//populate($present_year,$conn,"calendarGroceries");
		$_SESSION['message']= "New Calendar has been created. ";
		$_SESSION['good']=true;
		header("Location: admin_message.php");
	}
?>















































<?php
    /*$table_name = "t20202021";
    $flag=1;
    $first=mktime(0, 0, 0, 1, 1, 2020);
    $temp = $first;
    while($flag==1){
    	if(date("Y",$temp)=="2022"){
    		$flag=0;
    		continue;
    	}
    	$day = date("l",$temp);
    	$date = date("M d Y",$temp);
    	if($day=="Saturday"){
    		$startH = 9;
    		$startM = 30;
    		$endH = 15;
    		$endM = 30;
    		$status = "half";
    		
    		$query1 = "INSERT INTO $table_name (date , day , status,startH,startM,endH,endM,max_limit) VALUEs('".$date."',
    		'".$day."','".$status."','".$startH."','".$startM."','".$endH."','".$endM."',4)";
    		$results1 = $conn->query($query1);

    	}
    	elseif ($day=="Sunday") {
    		$status = "close";
    		$query2 = "INSERT INTO $table_name (date , day , status) VALUEs('".$date."','".$day."','".$status."')";
    		$results2 = $conn->query($query2);


    	}
    	else{
    		$startH = 9;
    		$startM = 30;
    		$endH = 15;
    		$endM = 30;
    		$status = "full";
    		
    		$query3 = "INSERT INTO $table_name (date , day , status,startH,startM,endH,endM,max_limit) VALUEs('".$date."',
    		'".$day."','".$status."','".$startH."','".$startM."','".$endH."','".$endM."',4)";
    		$results3 = $conn->query($query3);
    	}
    	$temp = strtotime('+24 hours',$temp);


    }*/





?>


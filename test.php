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

    if($_SERVER["REQUEST_METHOD"] == "POST"){
    	$date = $_POST["date"];
    	$date = strtotime($date);
    	$date = date("M d Y" , $date);
    	echo $date;
    	echo "<br>";
    	$liquor_card = "ssssssssssssssfssq";

 
    	$query = "SELECT * from allotment WHERE liquor_card='".$liquor_card."'  ORDER BY start_time desc limit 1";
    	$results = $conn->query($query);
    	if(!$results)die($query);
    	
    	$results = $results->fetch_assoc();
    	if($results==NULL){
    		die("first time");
    	}
    	var_dump($results);
    	$last_date = $results["start_time"];

    	$last_date = date("M d Y" , strtotime($last_date));
    	//echo $last_date;
    	//echo "<br>"; 
    	//var_dump((strtotime($last_date) - strtotime($date))/3600);



    }


?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
		<input type="date" name="date">
		<input type="submit">
	</form>
</body>
</html>
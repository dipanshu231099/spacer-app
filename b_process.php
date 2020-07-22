<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>Document</title>
</head>
<body>
    
</body>
</html>

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


?>
<div class="container">
    <div class="row">
        <div class="col-sm-1"></div>
        <div class="col-sm-10">
        <?php
            if($_SERVER["REQUEST_METHOD"] == "POST"){
                $table_name = $_POST["op_table"];
                $card_number = $_POST["card_number"];
                $start_time = $_POST["start_time"];
                $query_to_delete = "delete from $table_name where card_id='".$card_number."' and start_time='".$start_time."';";
                $delete_result = $conn->query($query_to_delete);
                if(!$delete_result){
                    die($delete_result);
                }
                $message = "Booking of $card_number at $start_time cancelled";
                echo "<h1>$message</h1>";
            }
        ?>
        <a href="index.php"><button class='btn btn-primary w-100'>Home</button></a>
        </div>
        <div class="col-sm-1"></div>
    </div>
</div>



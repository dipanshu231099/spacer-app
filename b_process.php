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
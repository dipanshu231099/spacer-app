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
?>

<?php
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $date = $_POST["report_date"];
        $date = date("M d Y" , strtotime($date));
        //$today = date("M d Y",strtotime('now'));

        $sql = "SELECT * FROM allotment WHERE start_time like '%".$date."';";
        // echo $sql;
        $results = $conn->query($sql);
        if(!$results)die('invalid please refresh');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>ACSA Report</title>
</head>
<body style="padding:1%;">
    <table class='table table-dark table-hover'>
        <thead>
            <tr>
                <th scope="col">Grocery_card</th>
                <th scope="col">Liquor_card</th>
                <th scope="col">Token</th>
                <th scope="col">Counter#</th>
                <th scope="col">Name</th>
                <th scope="col">Collect time</th>
                <th scope="col">Contact</th>
                <th scope="col">Groceries</th>
                <th scope="col">Liquor</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $results->fetch_assoc()){ ?>
                <tr>
                    <td><?php echo $row['grocery_card']; ?></td>
                    <td><?php echo $row['liquor_card']; ?></td>
                    <td><?php echo $row['token']; ?></td>
                    <td><?php echo $row['counter']; ?></td>
                    <td><?php echo $row['customer_name']; ?></td>
                    <td><?php echo $row['start_time']; ?></td>
                    <td><?php echo $row['contact']; ?></td>
                    <td><?php echo $row['groceries']; ?></td>
                    <td><?php echo $row['liquor']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <a href="admin.php"><button type="button" class="btn btn-success" style="width:100%;">Back to Admin Page</button></a>
</body>
<!-- Footer -->
<footer class="page-footer font-small blue">

<div class="footer-copyright text-center py-2">© 2020 Copyright:
    <a href="acknowledgement.php">Team Page</a>
  </div>

</footer>
</html>

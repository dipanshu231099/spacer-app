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
    $today = date("M d Y",strtotime('now'));
    $sql = "SELECT * FROM allotment WHERE start_time like '%".$today."';";
    // echo $sql;
    $results = $conn->query($sql);
    if(!$results)die('invalid please refresh');
    session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>Spacer App</title>
</head>
<body style="padding: 1%;">
        <table class='table table-dark table-hover'>
            <thead>
                <tr>
                    <th scope="col">Email</th>
                    <th scope="col">Name</th>
                    <th scope="col">Collect time</th>
                    <th scope="col">Contact</th>
                    <th scope="col">Groceries</th>
                    <th scope="col">Liquor</th>
                    <th scope="col">Counter#</th>
                    <th scope="col">Token</th>>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $results->fetch_assoc()){ ?>
                    <tr>
                        <td><?php echo $row['customer_id']; ?></td>
                        <td><?php echo $row['customer_name']; ?></td>
                        <td><?php echo $row['start_time']; ?></td>
                        <td><?php echo $row['contact']; ?></td>
                        <td><?php echo $row['groceries']; ?></td>
                        <td><?php echo $row['liquor']; ?></td>
                        <td><?php echo $row['counter']; ?></td>
                        <td><?php echo $row['token']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
</body>
<footer class="page-footer font-small blue">

  <div class="footer-copyright text-center py-2">SpacerApp © 2020 Copyright:
    <a href="https://www.linkedin.com/in/dipanshu-verma-955068183/"> Dipanshu </a>and <a href="https://www.linkedin.com/in/ayushman-dixit-4812b9171">Ayushman</a>
  </div>

</footer>
</html>

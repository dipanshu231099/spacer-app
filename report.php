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
    $shop = $_POST["shop"];
    //echo $shop;
?>

<?php
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $date = $_POST["report_date"];
        $date = date("M d Y" , strtotime($date));
        $shop = $_POST["shop"];
        //$today = date("M d Y",strtotime('now'));
        $table_name = "";
        if($shop=="Groceries"){
            $table_name = "bookingsGroceries";
        }
        else if($shop=="Groceries and Liquor")
        {
            $table_name = "bookingsgroceriesliquor";
        }
        else if($shop=="Liquor"){
            $table_name = "bookingsLiquor";
        }
        $sql = "SELECT * FROM $table_name WHERE start_time like '%".$date."' order by start_time asc;";
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
                <?php ?>
                <?php if ($shop=='Groceries') { echo '<th scope="col">card_id</th>';}?>
                <?php if ($shop=='Liquor') { echo '<th scope="col">card_id</th>';}?>
                <?php if ($shop=='Groceries and Liquor') { echo '<th scope="col">card_id_groceries</th>';}?>
                <?php if ($shop=='Groceries and Liquor') { echo '<th scope="col">card_id_liquor</th>';}?>
                <th scope="col">Token</th>
                <th scope="col">Counter#</th>
                <th scope="col">Name</th>
                <th scope="col">Collect time</th>
                <th scope="col">Contact</th>
                <?php if ($shop=='Groceries and Liquor') { echo '<th scope="col">Grocereies fail=0</th>';}?>
                <?php if ($shop=='Groceries and Liquor') { echo '<th scope="col">liquor fail=0</th>';}?>

            </tr>
        </thead>
        <tbody>
            <?php while($row = $results->fetch_assoc()){ ?>
                <tr>
                <?php if ($shop=='Groceries') { echo '<td>'.$row['card_id'].'</td>';}?>
                <?php if ($shop=='Liquor') { echo '<td>'.$row['card_id'].'</td>';}?>
                <?php if ($shop=='Groceries and Liquor') { echo '<td>'.$row['card_id_groceries'].'</td>';}?>
                <?php if ($shop=='Groceries and Liquor') { echo '<td>'.$row['card_id_liquor'].'</td>';}?>
                    
                    <td><?php echo $row['token']; ?></td>
                    <td><?php echo $row['counter']; ?></td>
                    <td><?php echo $row['customer_name']; ?></td>
                    <td><?php echo $row['start_time']; ?></td>
                    <td><?php echo $row['contact']; ?></td> 
                    <?php if ($shop=='Groceries and Liquor') { echo '<td>'.$row['g'].'</td>';}?>
                    <?php if ($shop=='Groceries and Liquor') { echo '<td>'.$row['l'].'</td>';}?>

                </tr>
            <?php } ?>
        </tbody>
    </table>
    <a href="admin.php"><button type="button" class="btn btn-success" style="width:100%;">Back to Admin Page</button></a>
</body>
<!-- Footer -->
<footer class="page-footer font-small blue">

<div class="footer-copyright text-center py-2">© 2021 Copyright:
    <a href="acknowledgement.php">Team Page</a>
  </div>

</footer>
</html>

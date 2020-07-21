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
        $liquor_card = $_POST["liquor_number"];
        $grocery_card = $_POST["grocery_number"];
        $sql_query_for_grocery = "select * from bookingsgroceries where card_id='".$grocery_card."';";
        $sql_query_for_liquor = "select * from bookingsliquor where card_id='".$liquor_card."';";

        $results_liquor = $conn->query($sql_query_for_liquor);
        $results_grocery = $conn->query($sql_query_for_grocery);
        if(!$results_liquor){
            die($sql_query_for_liquor);
        }
        if(!$results_grocery){
            die($sql_query_for_grocery);
        }

    }
?>



<html>
    <head>
        <title></title>
    </head>

    <body>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            Enter Liquor:
            <input type="text" name="liquor_number"><br>
            Enter Grocery:
            <input type="text" name="grocery_number"><br>
            <input type="submit" value="submit">
        </form>

        <?php if($_SERVER["REQUEST_METHOD"] == "POST"){ ?>
            <table class='table table-dark table-hover'>
                <thead>

                    <tr>
                        <th scope="col">card_id</th>
                        
                        <th scope="col">date and time</th>
                        <th scope="col">operation#</th>
                    </tr>

                </thead>
                <tbody>
                    <?php while($row = $results_liquor->fetch_assoc()){ ?>
                        <tr>
                            <td><?php echo $row['card_id']; ?></td>
                            <td><?php echo $row['start_time']; ?></td>
                            <td>
                                <form method="post" action="b_process.php">
                                    <input type="hidden" name="card_number" value="<?php echo $row['card_id']; ?>"/>
                                    <input type="hidden" name="start_time" value="<?php echo $row['start_time']; ?>"/>
                                    <input type="hidden" name="op_table" value="bookingsliquor">
                                    <input type="submit" value="cancel"/>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <table class='table table-dark table-hover'>
                <thead>
                    <tr>
                        <th scope="col">card_id</th>
                        <th scope="col">date and time</th>
                        <th scope="col">operation#</th>
                    </tr>
                </thead>
                
                <tbody>
                    <?php while($row1 = $results_grocery->fetch_assoc()){ ?>
                        <tr>
                            <td><?php echo $row1['card_id']; ?></td>
                            <td><?php echo $row1['start_time']; ?></td>
                            <td>
                                <form method="post" action="b_process.php"> 
                                    <input type="hidden" name="card_number" value="<?php echo $row1['card_id']; ?>"/>
                                    <input type="hidden" name="start_time" value="<?php echo $row1['start_time']; ?>"/>
                                    <input type="hidden" name="op_table" value="bookingsgroceries">
                                    <input type="submit" value="cancel">
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>


            </table>


        <?php } ?>



    </body>
</html>
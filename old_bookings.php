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

?>


<?php
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST['liquor'])){
        $liquor_card = $_POST["liquor_card"];
        $sql_query_for_liquor = "select * from bookingsLiquor where card_id='".$liquor_card."';";
        $results_liquor = $conn->query($sql_query_for_liquor);
        if(!$results_liquor){
            die($sql_query_for_liquor);
            }
        }

        if(isset($_POST['groceries'])){
        $grocery_card = $_POST["grocery_card"];
        $sql_query_for_grocery = "select * from bookingsGroceries where card_id='".$grocery_card."';";
        $results_grocery = $conn->query($sql_query_for_grocery);
        if(!$results_grocery){
            die($sql_query_for_grocery);
            }
        }
        if(isset($_POST['groceriesliquor'])){
            $gro_card = $_POST["gro_card"];
            $liq_card = $_POST["liq_card"];
            $sql_query_for_groceriesliquor = "select * from bookingsgroceriesliquor where card_id_groceries='".$gro_card."' and card_id_liquor='".$liq_card."';";
            $results_groceriesliquor = $conn->query($sql_query_for_groceriesliquor);
            if(!$results_groceriesliquor){
                die($sql_query_for_groceriesliquor);
                }
            }
        
        

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
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
                    <h4 class='text-center'>Which canteen you want to manage bookings?</h4>
                    <div class="row">
                        
                        <div class="col"  style='border-right:solid 1px black'>
                            <div class="form-check text-center">
                                <input type="checkbox" class="form-check-input" name="liquor" id='liquor'>
                                <label class="form-check-label" for="liquor">Liquor Only</label>
                            </div>
                            <br>
                            <label for="liquor_card" class="col-form-label">Liquor card number</label>
                            <input type="text" id='liquor_card' class="form-control" name="liquor_card" placeholder="Liquor card number. This will be used to verify you." disabled required>
                            <div class="invalid-feedback">
                                Must be 19 characters long.
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-check text-center">
                                <input type="checkbox" class="form-check-input" name="groceries" id='groceries'>
                                <label class="form-check-label" for="groceries">Groceries Only</label>
                            </div>
                            <br>

                            <label for="grocery_card" class="col-form-label">Grocery card number</label>
                            <input type="text" id='grocery_card' class="form-control" name="grocery_card" placeholder="Grocery card number. This will be used to verify you." disabled required>
                            <div class="invalid-feedback">
                                Must be 19 characters long.
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check text-center">
                                <input type="checkbox" class="form-check-input" name="groceriesliquor" id='groceriesliquor'>
                                <label class="form-check-label" for="groceriesliquor">Groceries and Liquor</label>
                            </div>
                            <br>

                            <label for="gro_card" class="col-form-label">Grocery card number</label>
                            <input type="text" id='gro_card' class="form-control" name="gro_card" placeholder="Grocery card number. This will be used to verify you." disabled required>
                            <label for="liq_card" class="col-form-label">Liquor card number</label>
                            <input type="text" id='liq_card' class="form-control" name="liq_card" placeholder="Liquor card number. This will be used to verify you." disabled required>
                            <div class="invalid-feedback">
                                Must be 19 characters long.
                            </div>
                        </div>

                    </div>
                    <hr>
                    <button type="submit" id='submit' class="btn btn-success mb-2 w-100" disabled>Show</button>
                </form>
                <hr>
              <a href="index.php"><button class="btn btn-info mb-2 w-100">Home</button></a>
                <hr>
                <?php if($_SERVER["REQUEST_METHOD"] == "POST"){ ?>
                <?php if(isset($_POST['liquor'])){ ?>
                <hr>
                    <h3 class='text-center'>Liquor Bookings</h3>
                    <table class='table table-dark table-hover'>
                        <thead>
                            <tr>
                                <th scope="col">card_id</th>
                                <th scope="col">date and time</th>
                                <th scope="col"></th>
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
                                            <input type="hidden" name="op_table" value="bookingsLiquor">
                                            <button <?php if(strtotime(date("h:i:sa"))>strtotime($row['start_time'])){echo "disabled";}?> type='submit' class='btn btn-primary w-100'>Cancel</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } ?>
                <?php if(isset($_POST['groceries'])){ ?>
                <hr>
                    <h3 class='text-center'>Grocery Bookings</h3>
                    <table class='table table-dark table-hover'>
                        <thead>
                            <tr>
                                <th scope="col">card_id</th>
                                <th scope="col">date and time</th>
                                <th scope="col"></th>
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
                                            <input type="hidden" name="op_table" value="bookingsGroceries">
                                            <button <?php if(strtotime(date("h:i:sa"))>strtotime($row1['start_time'])){echo "disabled";}?> type='submit' class='btn btn-primary w-100'>Cancel</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <?php } ?>
                    <?php if(isset($_POST['groceriesliquor'])){ ?>
                    <hr>
                    <h3 class='text-center'>Grocery and Liquor Bookings</h3>
                    <table class='table table-dark table-hover'>
                        <thead>
                            <tr>
                                <th scope="col">card_id_groceries</th>
                                <th scope="col">card_id_liquor</th>
                                <th scope="col">date and time</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row2 = $results_groceriesliquor->fetch_assoc()){ ?>
                                <tr>
                                    <td><?php echo $row2['card_id_groceries']; ?></td>
                                    <td><?php echo $row2['card_id_liquor']; ?></td>
                                    <td><?php echo $row2['start_time']; ?></td>
                                    <td>
                                        <form method="post" action="b_process.php"> 
                                            <input type="hidden" name="card_number1" value="<?php echo $row2['card_id_groceries']; ?>"/>
                                            <input type="hidden" name="card_number2" value="<?php echo $row2['card_id_liquor']; ?>"/>
                                            <input type="hidden" name="start_time" value="<?php echo $row2['start_time']; ?>"/>
                                            <input type="hidden" name="op_table" value="bookingsgroceriesliquor">
                                            <button <?php if(strtotime(date("h:i:sa"))>strtotime($row2['start_time'])){echo "disabled";}?> type='submit' class='btn btn-primary w-100'>Cancel</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                    </table>
                    <?php } ?>


                <?php } ?>

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



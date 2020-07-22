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
        if(isset($_POST['liquor'])){
        $liquor_card = $_POST["liquor_card"];
        $sql_query_for_liquor = "select * from bookingsliquor where card_id='".$liquor_card."';";
        $results_liquor = $conn->query($sql_query_for_liquor);
        if(!$results_liquor){
            die($sql_query_for_liquor);
            }
        }

        if(isset($_POST['groceries'])){
        $grocery_card = $_POST["grocery_card"];
        $sql_query_for_grocery = "select * from bookingsgroceries where card_id='".$grocery_card."';";
        $results_grocery = $conn->query($sql_query_for_grocery);
        if(!$results_grocery){
            die($sql_query_for_grocery);
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
                
                <hr>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
                    <hr>
                    <h4 class='text-center'>Which canteen you want to manage bookings?</h4>
                    <div class="row">
                        
                        <div class="col"  style='border-right:solid 1px black'>
                            <div class="form-check text-center">
                                <input type="checkbox" class="form-check-input" name="liquor" id='liquor'>
                                <label class="form-check-label" for="liquor">Liquor</label>
                            </div>
                            <div class="form-group">
                            <br>
                            
                            </div>
                            <label for="liquor_card" class="col-form-label">Liquor card number</label>
                            <input type="text" id='liquor_card' class="form-control" name="liquor_card" placeholder="Liquor card number. This will be used to verify you." disabled required>
                            <div class="invalid-feedback">
                                Must be 19 characters long.
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-check text-center">
                                <input type="checkbox" class="form-check-input" name="groceries" id='groceries'>
                                <label class="form-check-label" for="groceries">Groceries</label>
                            </div>
                            <br><br>

                            <label for="grocery_card" class="col-form-label">Grocery card number</label>
                            <input type="text" id='grocery_card' class="form-control" name="grocery_card" placeholder="Grocery card number. This will be used to verify you." disabled required>
                            <div class="invalid-feedback">
                                Must be 19 characters long.
                            </div>
                        </div>

                    </div>
                    <hr>
                    <button type="submit" id='submit' class="btn btn-success mb-2 w-100" disabled>Show</button>
                </form>

            </div>
            <div class="col-sm-1 "></div>
        </div>
    </div>
</body>


    <?php if($_SERVER["REQUEST_METHOD"] == "POST"){ ?>
        <?php if(isset($_POST['liquor'])){ ?>
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
            <?php } ?>
            <?php if(isset($_POST['groceries'])){ ?>

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


        <?php } ?>




<!-- Footer -->
<footer class="page-footer font-small blue">

<div class="footer-copyright text-center py-2">© 2020 Copyright:
    <a href="acknowledgement.php">Team Page</a>
  </div>

</footer>
<script src="javascripts/errors2.js"></script>
</html>



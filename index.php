<?php
    $servername = "localhost";
    $username = "spacer-app";
    $password = '$$ArmySpacerApp$$';
    $dbname = 'spacerApp';
    $conn = new mysqli($servername, $username, $password,$dbname);
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
?>

<?php

    $name = $email = $contact = $timestamp = "";
    $liquor = $groceries = false;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = test_input($_POST["name"]);
        $email = test_input($_POST["email"]);
        $contact = test_input($_POST["contact"]);
        $timestamp = $_POST["timestamp"];
        if(isset($_POST['liquor']))$liquor=true;
        if(isset($_POST['groceries']))$groceries=true;
        $query = "INSERT INTO allotment (customer_id,customer_name,contact,start_time,groceries,liquor) VALUES ('".$email."','".$name."','".$contact."','" . $timestamp . "',". ($groceries?1:0) .",".($liquor?1:0). ");";
        // echo $query;
        $results = $conn->query($query);
        if(!$results)echo "Error inserting data into sql";
        else {
            // $conn->close();
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        }
    }
    
    function test_input($data) {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      return $data;
    }
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spacer App </title>
    <link rel="stylesheet" href="stylesheets/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
    <div class="container-fluid">
        <div class="row">

            <div class="col-sm-6 left-pane">
                <h2 style="text-align: center;">welcome</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">First Name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control is-invalid" name="name" placeholder="first name" required>
                            <div class="invalid-feedback">
                                either the name is empty or should contain only alphabet
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control is-valid" name="email" placeholder="Email" required>
                            <div class="invalid-feedback">
                                <%= errors.email %>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Contact no.</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control is-valid" name="contact" placeholder="Conatact" required>
                            <div class="invalid-feedback">
                                <%= errors.contact %>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h3>What items you wish to buy</h3>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="groceries" id='groceries'>
                        <label class="form-check-label" for="groceries">Groceries</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="liquor" id='liquor'>
                        <label class="form-check-label" for="liquor">Liquor</label>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="visit_time">Select your visit time</label>
                        <select class="form-control" id="visit_time" name="timestamp">
                        <?php
                            date_default_timezone_set("Asia/Kolkata");
                            $present_time = strtotime('now');
                            $present_hour = (int)date("H",$present_time);
                            $present_minute = (int)date("h",$present_time);
                            $present_a = (int)date("A",$present_time);
                            $present_year = (int)date("y",$present_time);
                            $present_date = (int)date("d",$present_time);
                            $present_month = (int)date("m",$present_time); 
                            for ($i=$present_hour+1; $i <= $present_hour+24 ; $i++) { 
                                if($i%24<10 || $i%24>19){continue;}
                                for($j=0;$j<=40;$j+=20){
                                    $new_timestamp = mktime($i,$j,0,$present_month,$present_date,$present_year);
                                    $new_timestamp = date("h:ia M d Y",$new_timestamp);
                                    $sql = "SELECT COUNT(*) as cnt from allotment where start_time='".$new_timestamp."';";
                                    $results = (($conn->query($sql))->fetch_assoc())['cnt'];
                                    if($results<12)echo "<option>". $new_timestamp ."</option>";
                                }
                            }
                        ?> 
                        </select>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary mb-2">Request validity</button>
                </form>
            </div>

            <div class="col-sm-6 right-pane">
                <h1 style="text-align: center;">Spacer-App</h1>
                
            </div>
        </div>
    </div>
</body>
</html>
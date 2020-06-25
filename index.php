<?php
    date_default_timezone_set("Asia/Kolkata");
    $present_time = strtotime('now');
    $present_hour = (int)date("H",$present_time);
    $present_minute = (int)date("h",$present_time);
    $present_a = (int)date("A",$present_time);
    $present_year = (int)date("y",$present_time);
    $present_date = (int)date("d",$present_time);
    $present_month = (int)date("m",$present_time);
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
                <form action="/create_timing" method="POST">
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">First Name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control is-valid" name="name" placeholder="first name" required>
                            <div class="invalid-feedback">
                                <%= errors.name %>
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
                            for ($i=$present_hour+1; $i <= $present_hour+24 ; $i++) { 
                                if($i%24<10 || $i%24>19){continue;}
                                for($j=0;$j<=40;$j+=20){
                                    $new_timestamp = mktime($i,$j,0,$present_month,$present_date,$present_year);
                                    echo "<option>". date("d M,Y h:ia",$new_timestamp) ."</option>";
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
                <h2 style="text-align: center;">welcome2</h2>
                
            </div>
        </div>
    </div>
</body>
</html>
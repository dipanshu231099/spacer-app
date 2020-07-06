<?php
    session_start();
    if(!isset($_SESSION['message'])){
      header("Location: index.php");
      exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="stylesheets/style.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

  <title>ACSA message</title>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-1"></div>
      <div class="col-sm-10 landing-page">
          <h1 style="text-align: center;">ACSA</h1>
          <h4 style="text-align: center;">Army Canteen Scheduler App</h4>
          <hr>
          <div class="alert <?php echo ($_SESSION['good_liquor']?"alert-success":"alert-danger") ?>" style="<?php echo ($_SESSION['liquor']==false && $_SESSION['liquor_fail']==false)?"display:none":" " ?>" role="alert">
              <p>
                  <?php
                    if($_SESSION['good_liquor'])echo "Your request for Liquor has been successfully approved.";
                    else echo "Oops! Your request for Liquor has been rejected. Another request for buying Liquor must be 10 days later than your booking date.";
                  ?>
              </p>
          </div>
          <div class="alert <?php echo ($_SESSION['good_groceries']?"alert-success":"alert-danger") ?>" style="<?php echo (($_SESSION['groceries']==false && $_SESSION['groceries_fail']==false)?"display:none":" ") ?>" role="alert">
              <p>
                  <?php
                    if($_SESSION['good_groceries'])echo "Your request for Groceries has been successfully approved.";
                    else echo "Oops! Your request for Groceries has been rejected. Another request for buying Groceries must be 10 days later than your booking date.";
                  ?>
              </p>
          </div>
          <div class="alert <?php echo ($_SESSION['good']?"alert-success":"alert-danger") ?>" role="alert">
              <p>
                  <?php
                    echo $_SESSION['message'];
                  ?>
              </p>
          </div>
          <div class="row">
            <div class="col">
              <a href="index.php"><button class="btn btn-info mb-2 w-100">Home</button></a>
              
            </div>
            
          </div>
      </div>
    </div>
  </div>
  
</body>
<!-- Footer -->
<footer class="page-footer font-small blue">
  <div class="footer-copyright text-center py-2">© 2020 Copyright:
    <a href="acknowledgement.php">Team Page</a>
  </div>
</footer>
</html>

<?php
  session_destroy();
?>

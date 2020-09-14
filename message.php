<?php
    session_start();
    if($_SESSION['liquor']==false && $_SESSION['groceries']==$_SESSION['groceriesliquor']==false){
      header("Location: index.php");
      exit();
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
          <div class="alert <?php echo ((!$_SESSION['liquor_fail'])?"alert-success":"alert-danger") ?>" style="<?php echo ($_SESSION['liquor']==false)?"display:none":" " ?>" role="alert">
              <p>
                  <?php
                    echo $_SESSION['message_liquor'];
                  ?>
              </p>
          </div>
          <div class="alert <?php echo ((!$_SESSION['groceries_fail'])?"alert-success":"alert-danger") ?>" style="<?php echo (($_SESSION['groceries']==false)?"display:none":" ") ?>" role="alert">
              <p>
                  <?php
                    echo $_SESSION['message_groceries'];
                  ?>
              </p>
          </div>
          <div class="alert <?php echo ((!$_SESSION['groceriesliquor_fail'])?"alert-success":"alert-danger") ?>" style="<?php echo (($_SESSION['groceriesliquor']==false)?"display:none":" ") ?>" role="alert">
              <p>
                  <?php
                    echo $_SESSION['message_groceriesliquor'];
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

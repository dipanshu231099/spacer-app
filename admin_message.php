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

  <title>ACSA admin</title>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-1"></div>
      <div class="col-sm-10 landing-page">
          <h1 style="text-align: center;">Admin</h1>
          <h4 style="text-align: center;">Army Canteen Scheduler App</h4>
          <hr>
          <div class="alert <?php echo ($_SESSION['good']?"alert-success":"alert-danger") ?>" role="alert">
              <p>
                  <?php
                   echo $_SESSION['message'];
                   echo "Thank you for using the application";
                  ?>
              </p>
          </div>
          <div class="row">
            <div class="col">
              <a href="admin.php"><button class="btn btn-info mb-2 w-100">Admin Page</button></a>
            </div>
            
          </div>
      </div>
    </div>
  </div>
  
</body>
<footer class="page-footer font-small blue">

  <div class="footer-copyright text-center py-2">© 2021 Copyright:
  <a href="acknowledgement.php">Team Page</a>
    <!-- <a href="https://www.linkedin.com/in/dipanshu-verma-955068183/"> Dipanshu </a>and <a href="https://www.linkedin.com/in/ayushman-dixit-4812b9171">Ayushman</a> -->
  </div>

</footer>
</html>
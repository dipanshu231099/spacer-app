<?php
  session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="stylesheets/style.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

  <title>ACSA error</title>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-1"></div>
      <div class="col-sm-10 landing-page">
          <h1 style="text-align: center;">ACSA</h1>
          <hr>
          <div class='alert alert-danger'>
              <p>
                  <?php
                    if($_SESSION['liquor_fail'] && $_SESSION['groceries_fail'])echo "We are sorry! Your request can not be processed. You must wait for at least 10 days from the previous booking date before making a new request for either of the products.";
                    else if($_SESSION['liquor_fail'])echo "We are sorry! Seems like your request includes LIQUOR, and thus can not be processed. You must wait for at least 10 days from the previous booking date made for liquor.";
                    else if($_SESSION['groceries_fail'])echo "We are sorry! Seems like your request includes GROCERIES, and thus can not be processed. You must wait for at least 10 days from the previous booking date made for groceries";
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
<?php
  session_destroy();
?>
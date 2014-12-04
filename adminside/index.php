<?php
	
  //*** Start a session
  session_start();
  
  
  //*** Start the buffer
	ob_start();
  
  
  //*** If user is logged in, display a Welcome message
  	if (isset($_SESSION['logged'])) {
  		echo "Welcome, ".$_SESSION['logged'];
  	}

  
  //*** Get the current page
	if (isset($_GET['page'])) {
		$page = $_GET['page'];
	} else {
		$page = "home"; // default
	}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap 101 Template</title>

    <!-- Bootstrap -->
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js does not work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <!-- Main content area -->
    <div class="container">
      
      <!-- Navigation bar -->
      <div class="header">
        <ul class="nav nav-pills pull-right">

          <!-- *** Nav buttons -->
          
         <?php
	$options = array("home", "about", "contact", "upload", "login");
	foreach ($options as $option) {
		echo "<li ";
		echo ($page==$option ) ? 'class="active"' : '';
		echo "><a href='?page=$option'>"; echo ucwords($option);
		echo "</a></li>";
	}
	?>

        </ul>
        <h3 class="text-muted">My Bootstrap Demo</h3>
      </div> <!-- End nav bar -->
      <hr>
      
      <!-- Page content goes here -->
      <?php
        include ($page . ".php");
      ?>
      
      <!-- Footer -->
      <hr>
      <footer>
        <p>&copy; Company 2014</p>
      </footer>
      
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="../bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>

<?php
  //*** Flush buffer
	ob_flush();

?>
<?php
  include "../config/cred.php";
  //If form is posted
  if (isset($_POST['submit'])) {
    
    //*** Get email and pwd from POST array
	$email = $_POST['email'];
	$pwd = $_POST['password'];
	/* echo "Email: $email, Password: $pwd"; */    


    //*** If student, set session and redirect
	if (strpos($email, "@mail.greenriver.edu") !== false
		and $pwd == "student") {
		$_SESSION['logged'] = "student";
		header('location:?page=home');
	}    
    //*** Else if admin, set session and redirect
	else if(strpos($email, $adminName) !== false
		and $pwd == $adminPwd) {
		$_SESSION['logged'] = "admin";
		header('location:?page=home');
	}
    //*** Else display error message
	else {
      		//echo "<p>Invalid login. Please try again.</p>";
	}
    
    
  }
?>

<h2>Log in</h2>

<form role="form" action="" method="post">
  <div class="form-group">
    <label for="email">Email address</label>
    <input type="email" class="form-control" name="email" placeholder="Enter email">
  </div>
  <div class="form-group">
    <label for="password">Password</label>
    <input type="password" class="form-control" name="password" placeholder="Password">
  </div>
  <button type="submit" name="submit" class="btn btn-default">Submit</button>
</form>
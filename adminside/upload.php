<style>
    p.error {
        color:darkred;
    }
    p.success {
	color:darkgreen;
    }
</style>


<?php
/*
    File Uploader
    http://tina.greenrivertech.net/305/file_upload.php

    1. Create database table in phpMyAdmin:
	CREATE TABLE IF NOT EXISTS `uploads` (
	  `file_id` int(11) NOT NULL AUTO_INCREMENT,
	  `filename` varchar(100) NOT NULL,
	  PRIMARY KEY (`file_id`)
	);
	
    2.  Create upload directory in cPanel File Manager and allow write permissions
*/
?>

<!-- *** Create upload form *** -->

<form method="post" action="" enctype="multipart/form-data">
	<fieldset><legend>Add a new term</legend>
		<label for="term">Term: 
			<input type="text" name="term" id="term" size="99" maxlength="99" required>
		</label><br>
		<label for="img">Image File: 
			<input type="file" name="img" id="img" required>
		</label>
		<fieldset>
                    <legend>Sound</legend>
		    <label><input type="radio" value="pickup" name="method" id="method">&nbsp;Pick-up</label><br>
		    <label><input type="radio" value="delivery" name="method" id="method">&nbsp;Delivery</label>		
                </fieldset>
		</label>
		<input type="submit" name="submit_file" value="Submit" >
		<input type="reset" name="reset" value="Reset" >
	</fieldset>
</form>



<?php

    
    //*** Connect to the database ***
	include '../config/dblogin.php';

	try {
		$dbh = new PDO("mysql:host=$hostname;
				dbname=logan_grcc", $username, $password);
		//echo "<p>Connected to database.</p>";
	} catch (PDOException $e) {
		//echo $e->getMessage();
	}


    

    /*
     *  Accept file information from html form, then move the
     *  file to designated folder.
     */

    //Define upload directory
    $dirName = "upload/";
	
    //Define valid file types
    $valid_types = array("image/gif", "image/jpeg", "image/pjpeg", "image/tiff");

    //Check file size - 2 MB maximum
    if($_SERVER['CONTENT_LENGTH'] > 2000000) {
		echo "<p class='error'>File is too large to upload. Maximum file size is 2 MB.</p>";
    }
    //Check file type
    else if (in_array($_FILES['file']['type'], $valid_types)) {

		if ($_FILES['file']['error'] > 0)
			echo "<p class='error'>Return Code: {$_FILES['file']['error']}</p>";

		//Check for duplicate file
		if (file_exists($dirName . $_FILES['file']['name']))
			echo "<p class='error'>Error uploading: {$_FILES['file']['name']} already exists.</p>";
		else {
			//Move file to upload directory
			move_uploaded_file($_FILES['file']['tmp_name'],
			$dirName . $_FILES['file']['name']);
			echo "<p class='success'>Uploaded {$_FILES['file']['name']} successfully!</p>";

			// store the filename in the database
			$sql = "INSERT INTO uploads (filename)
				VALUES ('{$_FILES['file']['name']}');";
			echo "<p>$sql</p>";
			$dbh->exec($sql);
			//print_r($dbh->errorInfo());
		}
    }
    //Invalid file type
    else if ($_FILES['file']['type'] != "") {
	echo "<p class='error'>Invalid file type. Allowed types:  gif, jpg, tiff.</p>";
    }


    /*
     *	List each file as a hyperlink.
     */

    //Open file directory
    $dir = opendir($dirName);

	
    //Get names of files
    $sql = "SELECT * FROM uploads ORDER BY filename;";
    $result = $dbh->query($sql);

    if (sizeof($result) >= 1) {
	
	echo "<h2>Uploaded Files</h2>";
	
	//Display filenames
	foreach ($result as $row) {
		$file = $row['filename'];
		if ($file != "." && $file != "..") {
			echo "<a href='$dirName$file' target='_blank'>$file</a><br>";
		}
	}
    }


    //Close file directory
    closedir($dir);
<style>
    p.error {
        color:darkred;
    }
    p.success {
	color:darkgreen;
    }
</style>
<style> 
		td,th,table {
		border: solid 1px;
		border-collapse: collapse
		}
		table {
		width:100%;
		}
		td, td {
		 padding: 5px;
		 width: 70px;
		 text-align: center;
		 }
		 .altrow{background:lightgray;}
		 p.error {
		        color:red;
		    }
	</style>
<link rel="stylesheet" type="text/css" href="http://cdn.datatables.net/1.10.4/css/jquery.dataTables.min.css">

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
	<fieldset><legend>New Term</legend>
		<div class="form-group">
		<label for="term">Text: </label>
			<input type="text" placeholder="Enter term" name="term" id="term" class="form-control" maxlength="99" required>
		</div>
	</fieldset>
	<fieldset><legend>Picture</legend>
		<div class="form-group">
		<label for="img">Image File: </label>
			<input type="file" name="img" id="img" required>
		</div>
	</fieldset>
	<fieldset><legend>Sound</legend>
		<div class="form-group">
		        <label for="snd"><input type="radio" value="pickup" name="method" id="method"> Upload Audio File:</label>
				<label class="sr-only" for="snd">Audio File:</label>
				<input type="file" name="snd" id="snd">
		</div>
		<div class="form-group">
	    		<label>
	    			<input type="radio" value="delivery" name="method" id="method" checked> Automatically Generate Audio File
	    		</label>	
		</div>	
        </fieldset>
		<input type="submit" class="btn btn-primary" name="submit_file" value="Submit" >
		<input type="reset" class="btn btn-default" name="reset" value="Reset" >
</form>


<?php
	/*
	print "<pre>";
	print_r($_FILES);
	print "</pre>";	
	*/
	
	/*paste form data here
	
	Array
	(
	    [term] => Test
	    [method] => pickup
	    [submit_file] => Submit
	)
	
	Array
	(
	    [img] => Array
	        (
	            [name] => ailerons.jpg
	            [type] => image/jpeg
	            [tmp_name] => /tmp/phphLq74d
	            [error] => 0
	            [size] => 26653
	        )
	
	    [snd] => Array
	        (
	            [name] => ailero01.wav
	            [type] => audio/wav
	            [tmp_name] => /tmp/phpkY9vSS
	            [error] => 0
	            [size] => 8813
	        )
	
	)
	*/

    
    //*** Connect to the database ***
	include 'config/dblogin.php';

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

    $validImg = false;
    if (isset($_POST['term'])) {
    	$term = trim(htmlspecialchars(stripslashes($_POST['term'])));
    }
    
    //Define upload directory
    $dirName = "upload/";
	
    //Define valid file types
    $valid_types = array("image/gif", "image/jpeg", "image/pjpeg", "image/tiff");
    $valid_snd_types = array("audio/mpeg3", "audio/x-mpeg3", "audio/wav", "audio/x-wav", "mp3", "audio/mp3", "audio/mpeg", "audio/x-mpeg");

    //Check file size - 2 MB maximum
    if($_SERVER['CONTENT_LENGTH'] > 2000000) {
		echo "<p class='error'>File is too large to upload. Maximum file size is 2 MB.</p>";
    }
    //Check img type
    else if (in_array($_FILES['img']['type'], $valid_types)) {

		if ($_FILES['img']['error'] > 0)
			echo "<p class='error'>Return Code: {$_FILES['file']['error']}</p>";

		//Check for duplicate file
		if (file_exists($dirName . $_FILES['img']['name']))
			echo "<p class='error'>Error: {$_FILES['img']['name']} already exists.</p>";
		else {
			// valid
			$validImg = true;
		}
    }
    //Invalid file type
    else if ($_FILES['img']['type'] != "") {
	echo "<p class='error'>Invalid image file type. Allowed types:  gif, jpg, tiff.</p>";
    }
    	
	
	$validSnd = false;
	$sndName = "";
	$mp3 = "";
	
    if ($validImg === true) { // validity of snd doesn't matter if img is bad too
    	if ($_POST['method'] == "pickup") {
	    	if (in_array($_FILES['snd']['type'], $valid_snd_types)) {
	    		if ($_FILES['snd']['error'] > 0){
	    			echo "<p class='error'>Return Code: {$_FILES['snd']['error']}</p>";
	    		}
	    		$sndName = "audio/" . $_FILES['snd']['name'];
	    		if (file_exists($dirName . $sndName)){
	    			echo "<p class='error'>Error: {$_FILES['snd']['name']} already exists.</p>";
	    		} else {
	    			//valid
	    			$validSnd = true;
	    		}
	    	}
	    	else if ($_FILES['snd']['type'] != "") {
			echo "<p class='error'>Invalid audio file type. Allowed types:  .mp3, .wav</p>";
		    }
	    }
	    else if ($_POST['method'] == "delivery") {
	    	$words = urlencode(substr($term, 0, 100));
 
		// Name of the MP3 file generated using the MD5 hash
		   $sndName = md5($words);
		  
		// Save the MP3 file in this folder with the .mp3 extension 
		   $sndName = "audio/" . $sndName. ".mp3";
		 
		// If the MP3 file exists, do not create a new request
		   if (!file_exists($sndName)) {
		     $mp3 = file_get_contents(
		        'http://translate.google.com/translate_tts?tl=en&q=' . $words);
		     // file_put_contents($sndName, $mp3);
		     if (isset($mp3)) {
		     	$validSnd = true;
		     	
		     }
		     else {
		     	echo '<p class="error">Automatic sound retrieval failed.</p>';
		     }
		   }
	    }
    }
    
	if ($validImg && $validSnd){
		if (empty($term)) {
			echo "<p class='error'>Term text required.</p>";
		}
		else {
			//Move file to upload directory
			move_uploaded_file($_FILES['img']['tmp_name'],
			$dirName . $_FILES['img']['name']);
			echo "<p class='success'>Uploaded {$_FILES['img']['name']} successfully!</p>";
			
			if ($_POST['method'] == "pickup") {
				move_uploaded_file($_FILES['snd']['tmp_name'], 
				$dirName . $sndName);
				echo "<p class='success'>Uploaded {$_FILES['snd']['name']} succussfully!</p>";
			} else if ($_POST['method'] == "delivery") {
				file_put_contents($dirName . $sndName, $mp3);
				echo "<p class='success'>Automatic audio succeeded!</p>";
			}
		
			// store the filename in the database
			$sql = "INSERT INTO terms (term, img, snd)
				VALUES (:term, :img, :snd)";
			$statement = $dbh->prepare($sql);
			$statement->bindParam(':term', $term, PDO::PARAM_STR);
			$statement->bindParam(':img', $_FILES['img']['name'], PDO::PARAM_STR);
			$statement->bindParam(':snd', $sndName, PDO::PARAM_STR);
			$statement->execute();	
			// print_r($dbh->errorInfo());
		}
	}
	

    /*
     *	List each file as a hyperlink.
     */

    //Open file directory
    $dir = opendir($dirName);

	?>
<h2>Uploaded Terms</h2>
<div>
<table id="myTable">
	<thead>
		<tr>
			<th>Term</th>
			<th>Picture</th>
			<th>Sound</th>
			<th>Section</th>
		</tr>
	</thead>
	<tbody>
<?php
    //Get names of files
    $sql = "SELECT * FROM terms ORDER BY term;";
    $result = $dbh->query($sql);

    if (sizeof($result) >= 1) {	
	
	//Display filenames
	foreach ($result as $row) {
			echo '<tr>'; 
				echo '<td>' . $row['term'] . '</td>';
				echo '<td><a href="' . $dirName . $row['img'] . '" target="_blank">Image</a></td>';
				echo '<td><a href="' . $dirName . $row['snd'] . '" target="_blank">Audio</a></td>';
				echo '<td>' . ' _ ' . '</td>';
			echo '</tr>';
		}
    }
    
    //Close file directory
    closedir($dir);
    ?>
    	</tbody>
    </table>
   </div>
	   
    	<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="http://cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"></script>
	<script>
		
		$(document).ready(function(){
		    $("#myTable").DataTable();
		});
	</script>
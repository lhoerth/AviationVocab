<?php
							//*** Connect to the database ***
							require 'config/dblogin.php';
						
							try {
								$dbh = new PDO("mysql:host=$hostname;
										dbname=logan_grcc", $username, $password);
								//echo "<p>Connected to database.</p>";
							} catch (PDOException $e) {
								//echo $e->getMessage();
							}
								
						$dirName = "upload/";
						// $dir = opendir($dirName);
						    //Get names of files
						    $sql = "SELECT * FROM terms ORDER BY term;";
						    $result = $dbh->query($sql);
							/*Display filenames
								foreach ($result as $row) {
									echo '<tr>'; 
										echo '<td>' . $row['term'] . '</td>';
										echo '<td><a href="' . $row['img'] . '">Image</a></td>';
										echo '<td><a href="' . $row['snd'] . '">Audio</a></td>';
										echo '<td>' . '</td>';
									echo '</tr>';
								} */
						/* 
						closedir($dir); */
						$js = "";
						foreach ($result as $row) {
							
							$js .= "nTerms = nTerms + 1; \n" . 
							"img[img.length] = new Image (); \n" . 
							"img[img.length-1].src = '$dirName{$row['img']}';\n" . 
							"snd[snd.length] = new Audio ();\n" . 
							"snd[snd.length-1].src = '$dirName{$row['snd']}';\n" . 
							"text[text.length] = '{$row['term']}';\n";
						} 
						//echo $js;
						?>
<h2>Learn the terms</h2>

			<p>
			
			<IMG alt="aviation picture" SRC="upload/fuselage.JPG" id="mypic" class="slide" HEIGHT="200" WIDTH="250">
				<div class="audio-wrap" onclick="playSound">
					<audio id="word">
						<source src="upload/audio/95ebf75983a8d259e1521a7ffb072f3f.mp3" type="audio/mpeg">
						</audio>
					</div>
					<div class="knowledge">
						<p><a href="javascript: audioElement.setAttribute('src', snd[num].src); audioElement.play();" id="audioBtn">
							<img alt="sound" src="images/speaker.PNG" width="22" height="22">
						</a><span id="label1">Fuselage</span></p>
					</div>
					<div id="prevNext">
						<input class="prevNext" type="button" onclick="JavaScript:slideshowBack()" value="<-Back">
						<input class="prevNext" type="button" onclick="JavaScript:slideshowUp()" value="Next->">
					</div>
					<script src="http://code.jquery.com/jquery.js"></script>
					<script>
						/*
						var nTerms = 8 ;
						function BuildArray(size){
							this.length = size;
							for (var i = 1; i <= size; i++)
							{this[i] = null;}
							return this;
						}*/
						
						var img = ["a"];
						var snd = ["b"];
						var text = ["c"];
						
						var num=1;
						var nTerms = 0;
						
						<?php echo $js; ?>

						var audioElement = document.createElement('audio');
						audioElement.setAttribute('src', 'upload/audio/fuselage.mp3');

						/*
						$('#audioBtn').click(function() {
							audioElement.setAttribute('src', snd[num].src);
							audioElement.play();
						});
						*/


						function slideshowUp()
						{
							num=num+1;
							if (num>=nTerms+1)
								{num=1;}
							$("#mypic").attr('src', img[num].src);

							$("#label1").html(text[num]);
							audioElement.setAttribute('src', snd[num].src);
							audioElement.play();
						}

						function slideshowBack()
						{
							num=num-1;
							if (num<=0)
								{num=nTerms;}
							$("#mypic").attr('src', img[num].src);

							$("#label1").html(text[num]);
							audioElement.setAttribute('src', snd[num].src);
							audioElement.play();
						}


					</script>
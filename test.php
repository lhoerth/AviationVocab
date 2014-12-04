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
							
							$js .= "NumberOfWords = NumberOfWords + 1; \n" . 
							"img[img.length] = new Image (); \n" . 
							"img[img.length-1].src = '$dirName{$row['img']}';\n" . 
							"snd[snd.length] = new Audio ();\n" . 
							"snd[snd.length-1].src = '$dirName{$row['snd']}';\n" . 
							"text[text.length] = '{$row['term']}';\n";
						} 
						//echo $js;
						?>
<h2>Test your knowledge</h2>
<IMG alt="slide" SRC="images/fuselage.JPG" id="slide" class="slide" HEIGHT="200" WIDTH="250">
						<div class="chss">
							<div class="chs">
								<a href="javascript:hear[1].play();">
									<img alt="sound" src="images/speaker.PNG" width="22" height="22">
								</a>
								<input type="button" id="btn1" value="button1">
							</div>
							<div class="chs">
								<a href="javascript:hear[2].play();">
									<img alt="sound" src="images/speaker.PNG" width="22" height="22">
								</a>
								<input type="button" id="btn2" value="button2">
							</div>
							<div class="chs" id="b3">
								<a id="speaker" href="javascript:hear[3].play();">
									<img alt="sound" src="images/speaker.PNG" width="22" height="22">
								</a>
								<input type="button" id="btn3" value="button3">
							</div>
						</div>
					<div class="knowledge">
					<p id="result"></p>
					<a id="proceed" href="javascript:nextSlide();">Next Question</a></div>

				<script src="http://code.jquery.com/jquery.js"></script>
				<SCRIPT>
					// number of terms completely added to our pool.
					var NumberOfWords = 0;
					
					
					var img = ["a"];
					var snd = ["b"];
					var text = ["c"];					
					var num=1;
					<?php echo $js;	?>
					
					
					var crntSld = 1; // current slide
					var crctBtn = 1;
					var crctBtnID = "";

					function BuildArray(size){
						this.length = size;
						for (var i = 1; i <= size; i++)
						{this[i] = null;}
						return this;
					}

					var hear = new BuildArray(3);

					/* These are played when the speakers are clicked */
					hear[1] = document.createElement('audio');
					hear[2] = document.createElement('audio');
					hear[3] = document.createElement('audio');


					/* Decides the next correct button (left, middle, or right). */
					function ChsCrcctBtn() {
						crctBtn = Math.ceil(Math.random() * 3);
						crctBtnID = "#btn"+crctBtn;
						/* For debugging:
						alert("crctBtn: " + crctBtnID);
						*/

						hear[crctBtn].src = snd[crntSld].src;
						$(crctBtnID).attr("value",text[crntSld]);
					}

					/* General purpose random picker */
					function randW() {
						// Generate a random number between 1 and NumberOfWords
						var rnd = Math.ceil(Math.random() * NumberOfWords);

						// return the random word to the button value
						return rnd;
					}

					/* Sets the text of the buttons, making sure each is different. */
					function setBtns() {
						var i2 = ((crctBtn+3)%3)+1;
						var ID2 = "#btn"+i2;

						var i3 = ((i2+3)%3)+1;
						var ID3 = "#btn"+i3;

						var newWord1 = randW();

						/* Make sure new word is not taken by A */
						while(text[newWord1]===text[crntSld]){
							newWord1 = randW();
						}
						$(ID2).attr("value", text[newWord1]);
						hear[i2].src = snd[newWord1].src;

						var newWord2 = randW();

						/* Make sure new word is not taken by A nor B*/
						while (	text[newWord2]===text[crntSld] ||
								text[newWord2]===text[newWord1]	)	{
							newWord2 = randW();
						}
						$(ID3).attr("value", text[newWord2]);
						hear[i3].src = snd[newWord2].src;
					}

					/* Moves to the next challenge. */
					function nextSlide()
					{
						$("#btn1").css("box-shadow","none");
						$("#btn2").css("box-shadow","none");
						$("#btn3").css("box-shadow","none");
						$("#btn1").prop( "disabled", false );
						$("#btn2").prop( "disabled", false );
						$("#btn3").prop( "disabled", false );
						$("#proceed").hide();
						$("#result").hide();
						crntSld=crntSld+1;
						if (crntSld >= NumberOfWords+1)
						{ crntSld = 1; }
						$("#slide").attr("src",img[crntSld].src);
						ChsCrcctBtn();
						setBtns();
					}

					// Generate random term selections for the buttons when the DOM is ready.
					$(document).ready(function(){
						$("#proceed").hide();
						ChsCrcctBtn();
						setBtns();
						$("#slide").attr("src",img[crntSld].src);
					});

					/* Checks whether correct button was clicked */
					$(".chss :button").click(function(){
						$("#btn1").prop( "disabled", true );
						$("#btn2").prop( "disabled", true );
						$("#btn3").prop( "disabled", true );
						if ($(this).attr("value") == text[crntSld]){
							// correct
							$(this).css("box-shadow","0 0 0.5em #00ff00");
							$("#result").html("Correct!");
							$("#result").css("color", "#00ff00");
							hear[crctBtn].play();
						}else{
							// wrong
							$(this).css("box-shadow","0 0 0.5em #ff0000, 0 0 0.5em #ff0000");
							$("#result").html("Wrong. It was " + text[crntSld]+". ");
							$("#result").css("color", "#ff0000");
						}
						$("#result").show()
						$("#proceed").delay( 800 ).fadeIn("slow");
						lsapi.setNextPanel(3);
						lsapi.updateClass($(this));
					});

				</SCRIPT>
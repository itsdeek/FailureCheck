<?php
/*********************************************************************************************
Author: Lewis Beasley
Company: Hosting.com
Created: 10/1/2017
Version: 1.0

The purpose of this script is to ping a single host and define it's status (OK, WARNING, FAILURE)
according to the number of failures. This script requires a "FailureCheckResult.txt" text file in the
same directory. Additionally, in order to have this script execute on specified intervals,
you'll need to create a cron job / scheduled task. This script file is executed by
a cron job / scheduled task. It then updates a text file. The text file is what the monitoring
system will monitor -  eg. http://YourSite.com/TextFile.txt .
*********************************************************************************************/

/**************************************
PING TEST VARIABLES
**************************************/
$host1 = "google.com";
$status1 = ping($host1); //execute our ping function
$PingResult = implode(' ',$result); //This takes the  $results array from the ping test and dumps it into a string.

/**************************************
TEXT FILE FUNCTION VARIABLES
**************************************/
$TxtFileRead = fopen("FailureCheckResult.txt", "r") or die("Unable to open file!");


// Ping our host 1 time. A returned 0 = False Not Down | A returned 1 = TRUE IS DOWN
function ping($host1){
	global $result;
	exec(sprintf("ping -c 1 %s", escapeshellarg($host1)), $result, $ReturnValue); //execute our ping command, escape our special chars
	return $ReturnValue;
}

		// IF THE PING TEST IS SUCCESSFUL, RESET FAILURE COUNT TO 0 AND UPDATE OUR TXT FILE WITH THE FOLLOWING.
		If ($status1 == "0"){
			$TextFileNum = 0;  //Reset the failure count to 0
			$TxtFileRead = fopen("FailureCheckResult.txt", "w") or die("Unable to open file!"); //re-open the file for write access
			$TextFileNum .= "\n";
			$TextFileNum .= "Status: ";
			$TextFileNum .= "PING OK";
			$TextFileNum .= "\n";
			$TextFileNum .= "Last Poll: ";
			$TextFileNum .= date('l jS F Y h:i:s A');
			$TextFileNum .= "\n";
			$TextFileNum .= "Result: ";
			$TextFileNum .= $PingResult;
			fwrite($TxtFileRead, $TextFileNum); //write our 0 reset value to our txt file
		    fclose($TxtFileRead);
			}

				// EACH TIME THE PING TEST FAILS INCREMENT THE FAILURE COUNT AND UPDATE TXT FILE
				If ($status1 == "1"){
					$TextFileNum = fgets($TxtFileRead); //grab the number from text filetail
					$TextFileNum = $TextFileNum + 1; //increment the number for our text file by one.
					$TxtFileRead = fopen("FailureCheckResult.txt", "w") or die("Unable to open file!"); //re-open the file for write access
					$TextFileNum .= "\n"; // new line carriage return

					// Let's determine how many failure must occur
							IF($TextFileNum == 0){ 			// If failure count is 0. Everything is OK
								$MonStatus = "PING OK";
							}
								IF($TextFileNum > 0 AND $TextFileNum < 5){ // If failure count is 1-4. trigger a WARNING
									$MonStatus = "WARNING";
								}
									IF ($TextFileNum >= 5){ 	// If failure count is 5 or greater than trigger FAILURE
										$MonStatus = "FAILURE";
									}

					$TextFileNum .= "Status: ";
					$TextFileNum .= $MonStatus;
					$TextFileNum .= "\n";
					$TextFileNum .= "Last Poll: ";
					$TextFileNum .= date('l jS F Y h:i:s A');
					$TextFileNum .= "\n";
					$TextFileNum .= "Result: ";
					$TextFileNum .= $PingResult;
					fwrite($TxtFileRead, $TextFileNum); //write the incremented number to the text file
					fclose($TxtFileRead);

					//ignore this test note.
				}



?>

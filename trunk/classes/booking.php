<?php
class booking
{
	var $id;
	var $username;
	var $room;
	var $period;
	var $timebooked;
	var $subject;
	var $classname;
	var $bookingID;
	
	function booking($id,$username,$room,$period,$timebooked,$subject,$classname,$bookingID)
	{
		$this->id = $id;
		$this->username = $username;
		$this->room = $room;
		$this->period = $period;
		$this->timebooked = $timebooked;
		$this->subject = $subject;
		$this->classname = $classname;
		$this->bookingID = $bookingID;
	}
	
	function displayBookingData()
	{
		$day = raw_param("day");
		$month = raw_param("month");
		$year = raw_param("year");
		$room_id = raw_param("room");
		$room_data = database::getRoomName($room_id);
		$room_name = $room_data['name'];
		$period = raw_param("period");
		$user_level = security::get_level('');
		$day_name = date("D",mktime(1,1,1,$month,$day,$year));


		// get the period's name
		$query = "SELECT * FROM bookingperiods WHERE number='$period'";
		$result = database::executeQuery($query);
		$periodName = $result[0]['name'];



		$normalBooking = normal::getBookingData($day,$month,$year,$room_id,$period);
		$repetitiveBooking = repetitive::getBookingData($day_name,$room_id,$period,generateSchoolYear($month,$year));
	
		$isbooker = false;
		// find out what type of booking this is
		if (count($normalBooking) > 0)
		{
			$typeNormal = true;
			$booking = $normalBooking[0];
			if ($_SESSION['SESSIONusername'] == $normalBooking[0]['username'])
			{
				$isbooker = true;
			}
			
		}
		elseif (count($repetitiveBooking) > 0)
		{
			$typeNormal = false;
			$booking = $repetitiveBooking[0];
			if ($_SESSION['SESSIONusername'] == $repetitiveBooking[0]['username'])
			{
				$isbooker = true;
			}
		}
		else
		{
			$typeNormal = NULL;
			echo "\n\n\nERROR: ERROR 12432. No booking made for this spacetime location.\n\n\n";
		}
		$editable = $user_level >= 2 || $isbooker;										// store if this is editable or not for the current session
		$urlstring = "day=$day&month=$month&year=$year&period=$period&room=$room_id"; 	// build the main url string containing the booking data
		
		echo "<h2>Booking Details</h2>\n";
		
		echo "The selected booking is for <b>$periodName</b> for <b>$room_name</b> on <b>".date('l, jS F Y',mktime(1,1,1,$month,$day,$year))."</b>\n<br /><br />\n";
		echo "<table class=\"review\" border='0'>\n";
		echo "\t<tr><td>Booked By:</td><td class='center'>".$booking['username']."</td></tr>\n";
		echo "\t<tr><td>Date:</td><td class='center'>".date('l, jS F Y',mktime(1,1,1,$month,$day,$year))."</tr>\n";
		echo "\t<tr><td>Room:</td><td class='center'>".$room_name."</tr>\n";
		echo "\t<tr><td>Period:</td><td class='center'>".$periodName."</tr>\n";
		echo "\t<tr><td>Class:</td><td class='center'>".$booking['class']."</tr>\n";
		echo "\t<tr><td>Subject:</td><td class='center'>".$booking['subject']."</tr>\n";
		echo "\t<tr><td>Type of Booking:</td><td class='center'>";if($typeNormal===false){echo "Yearly";}elseif($typeNormal === true){echo "Single";} else {echo "ERROR: ERROR 12432. No booking detected.";} echo"</td></tr>\n";
		if ($user_level >= 2)
		{
			$reasonID = $booking['reasonID'];
			if ($reasonID > 0)
			{
				$query = "SELECT * FROM bookingreason WHERE id='$reasonID'";
				$reasonData = database::executeQuery($query);
				$reasonText = $reasonData[0]['reasonText'];
			}
			else
			{
				$reasonText = "No reason stored.";
			}
			if (settings::get_bookingReasonEnabled() == true || $reasonID > 0)
			{
				// if booking reason is enabled OR there is a reason stored, display it.
				echo "\t<tr><td>Reason For Booking</td><td class='center'>".$reasonText."</td></tr>\n";
			}
		}
		if ($user_level >= 2) 
		{	// if user is admin then display the time the booking was made
			echo "\t<tr><td>Time Booked</td><td class='center'>".date('G:i - jS F Y',$booking['timebooked'])."</td></tr>\n";
		}
		$result = database::executeQuery("SELECT starttime FROM bookingperiods WHERE number='$period'");
		$time = explode(".",$result[0]['starttime']);
		$startTimeOfBooking = mktime($time[0],$time[1],0,$month,$day,$year);

		if ($typeNormal === true && (time() < $startTimeOfBooking && $editable || $user_level >= 2))
		{
			echo "\t<tr><td colspan='2' class='center'>Don't want the room anymore? <a href=\"?type=Unbook&{$urlstring}\">Unbook It!</a></td></tr>\n";
		}
		elseif($typeNormal === false && $editable)
		{
			echo "\t<tr><td colspan='2' class='center'>"; 
			if ($user_level >= 2)
			{
				echo "Don't need room all year? <a href=\"?type=Unbook&{$urlstring}\">Unbook It</a>";
			}
			else
			{
				echo "Don't need room all year? Ask ".settings::get_AdminName()." to delete it.";
			} 
		echo "</td></tr>\n";
		}

		echo "</table>\n";
	}
	
	function displayBookingForm()
	{
		$day = raw_param("day");
		$month = raw_param("month");
		$year = raw_param("year");
		$room_id = raw_param("room");
		$room_data = database::getRoomName($room_id);
		$room_name = $room_data['name'];
		$period = raw_param("period");
		$day_name = date("D",mktime(1,1,1,$month,$day,$year));
		$user_level = security::get_level('');

		$query = "SELECT * FROM bookingperiods WHERE number='$period'";
		$result = database::executeQuery($query);
		$periodName = $result[0]['name'];

		// display the form to book the period
		echo "<h2>Booking</h2>";
		echo "You wish to book $periodName in $room_name on ".date('l, jS F Y',mktime(1,1,1,$month,$day,$year))."\n";
		echo "<br /><br />\n";
		echo "Select the class and the subject for which you wish to book the room for.\n<br /><br />\n";
		echo "<form name=\"bookingform\" id=\"bookingForm\" method=\"post\" action=\"?day=$day&month=$month&year=$year&period=$period&room=$room_id&type=BookPeriod\">\n";
		echo "\tSelect Class <select name=\"class\" id='class'>\n";
		echo "\t\t<option value=\"\">-----</option>\n";
		
		$get_classes = "SELECT * FROM bookingclasses ORDER BY class";
		$classes = database::executeQuery($get_classes);
		foreach ($classes as $class){ echo "\t\t<option value=\"".$class['class']."\">".$class['class']."</option>\n";}
		echo "\t</select>\n";
		echo "\t Select Subject \n\t<select name=\"subject\" id='subject'>\n";
		echo "\t\t<option value=\"\">----------------------</option>\n";
		
		$get_subjects = "SELECT * FROM bookingsubjects ORDER BY subject";
		$subjects = database::executeQuery($get_subjects);
		foreach ($subjects as $subject){ echo "\t\t<option value=\"".$subject['subject']."\">".$subject['subject']."</option>\n";}
		echo "\t</select>\n\t<br /><br />\n";
		
		if (settings::get_bookingReasonEnabled() == true)
		{
			echo "\tReason for Booking \n";
			echo "\t<select name='reason' id='reason'>\n";
			$query = "SELECT * FROM bookingreason WHERE enabled=1";
			$reasons = database::executeQuery($query);
			if (count($reasons) > 0)
			{
				echo "\t\t<option value=''>--Select Reason--</option>\n";
				foreach ($reasons as $index => $reason)
				{
					$reason_id = $reason['id'];
					$reason_text = $reason['reasonText'];
					echo "\t\t<option value='{$reason_id}'>{$reason_text}</option>\n";
				}
			}
			else
			{
				echo "\t\t<option value='0'>No Reasons Yet</option>";
			}
			echo "\t</select>";
			echo "\t<br /><br /><br />\n";
		}
		
		if ($user_level >= 2) 
		{
			echo "\n\tIf this is to be a year booking on this day and period, tick this box and select. \n";
			echo "\t<input type=\"checkbox\" name=\"perm\" id='perm' value=\"yes\" />\n\t<br />\n";
			echo "\tSelect Teacher : \n";
			echo "\t<select name='teacher' onChange=\"bookingform.perm.checked='checked';if(this.value == '') {bookingform.perm.checked='';}\">\n";
			echo "\t\t<option value=''>--Select Username--</option>\n";
			$query = "SELECT * FROM bookingusers ORDER BY username";
			$result = database::executeQuery($query);
			foreach($result as $index => $user)
			{
				echo "\t\t<option value='".$user['username']."'>".$user['username']."</option>\n";
			};
			echo "\t</select>\n";
	//		echo "<input type='text' onMouseOver=\"this.value=bookingform.perm.checked\">";
	//		echo "<input type='text' onMouseOver=\"this.value=bookingform.teacher.value\">";
			echo "\t<br /><br />\n";
			echo "\tWeekly bookings have priority and will over-rule single bookings already set on this period,day and room <b>with no warning</b> to original booker.<br /><br />However, future bookings on this day and period will be not be possible once period has been saved until this weekly booking is deleted.<br />\n";
		}
		echo "\t<br />\n";
		echo "\t<div class='errorMsg' id='errorMsg1'>&nbsp;</div>\n";
		echo "\t<br />\n";
		echo "\t<input type='submit' value='Book Period' />\n"; 

		echo "</form>\n";
	}
	
	function handleBookRequest()
	{
		// check the period is still available
		// then if available, book the period for the teacher currently logged on.

		$user_level = security::get_level('');
		// collect the data from the url
		$day = raw_param("day");
		$month = raw_param("month");
		$year = raw_param("year");
		$room_id = raw_param("room");
		$room_data = database::getRoomName($room_id);
		$room_name = $room_data['name'];
		$period = raw_param("period");
		$type = raw_param("type");
		// collect the data from the form
		$class = raw_param_post('class');
		$subject = raw_param_post('subject');
		$perm = raw_param_post('perm');
		$teacher = raw_param_post('teacher');
		$bookingReason = raw_param_post('reason');
		
		// check month length is correct
		if (strlen($month) == 1)
		{
			$month = "0".$month;
		}
		
		if (empty($teacher))
		{
			$teacher = $_SESSION['SESSIONusername'];
		}
		
		$day_name = date("D",mktime(1,1,1,$month,$day,$year));		
		
		echo "<h2>Booking</h2>";

		// check class and subject selected and booking reason is entered if required.
		
		if (settings::get_bookingReasonEnabled() == true)
		{
			if ($bookingReason > 0)
			{
				$bookingReasonPass = true;
			}
			else
			{
				if ($bookingReason < 0)
				{
					$bookingReason = 0;
				}
				$bookingReasonPass = false;
			}
		}
		else
		{
			$bookingReason = 0;
			$bookingReasonPass = true;
		}
		
		if (!empty($class) && !empty($subject) && $bookingReasonPass)
		{
			// check period isnt already booked
			$normalBooked = normal::isPeriodBooked($day,$month,$year,$room_id,$period);
			$repetitiveBooked = repetitive::isPeriodBooked($day_name,$room_id,$period,generateSchoolYear($month,$year));
							
			if ($normalBooked != 0)
			{
				echo "Period already booked. <a href=\"javascript:window.close();\">Please select another</a>\n.";
			}
			elseif ($repetitiveBooked != 0)
			{
				echo "This period is <b>always booked</b> for a weekly class. <a href=\"javascript:window.close();\">Please select another</a>.\n";
			}
			else // period not already booked into system. so is avaible for booking. booking the period is allowed.
			{
				if ($perm == "yes")
				{
					if ($user_level >= 2) // user is autherised to set weekly bookings
					{
						$timebooked = time();
						$dayName = date("D",mktime(1,1,1,$month,$day,$year));
						$username = $teacher;
						
						$schoolYear = generateSchoolYear($month,$year);
						
						$query = "SELECT * FROM bookingsingle WHERE period='$period' AND room='$room_id' AND schoolYear='$schoolYear'";
						$results = database::executeQuery($query);
						$deleted = 0;
						foreach ($results as $index => $booking)
						{
							$timestamp = mktime(1,1,1,$booking['month'],$booking['day'],$booking['year']);
							if ($dayName == date("D",$timestamp))
							{
								$bookingid = $booking['id'];
								$query = "DELETE FROM bookingsingle WHERE id='$bookingid' AND schoolYear='$schoolYear'";
								database::executeQuery($query);
								$deleted++;
							}
						};

						echo "$deleted single bookings have been deleted from the system.<br />\n";
						$query = "INSERT INTO bookingpermanent (username,day,room,period,timebooked,subject,class,schoolYear,reasonID) VALUES ('$username','$dayName','$room_id','$period','$timebooked','$subject','$class','$schoolYear','$bookingReason')";
						database::executeQuery($query);
						
						$query = "SELECT * FROM bookingperiods WHERE number='$period'";
						$result = database::executeQuery($query);
						$periodName = $result[0]['name'];
						
						echo "Period has been successfully booked for every {$dayName} during {$periodName} in {$room_name}.\n<br /><br /><a href=\"javascript:window.close();\">Close Window</a>\n";
					}
					else // user is not autherised to set weekly bookings.
					{
						echo "You are not autherised to book the same period on a weekly basis. Booking not made.<br /><a href=\"javascript:window.close();\">Exit</a>\n";
					}
				}
				else
				{
					if (booking::canBook())
					{
						$maxBookingTime = settings::get_bookingRangeMax()*24*60*60; // in seconds
						$minBookingTime = settings::get_bookingRangeMin()*24*60*60; // in seconds
						$bookingTime = mktime(23,59,59,$month,$day,$year);
						if ($user_level >= 1)
						{
							if (($bookingTime > time() + $minBookingTime && $bookingTime < time() + $maxBookingTime && $user_level >= 1) || $user_level >= 2)
							{
								$timebooked = time();
								$username = $_SESSION['SESSIONusername'];
								
								$schoolYear = generateSchoolYear($month,$year);
								
								$query = "INSERT INTO bookingsingle (username,day,month,year,room,period,timebooked,subject,class,schoolYear,reasonID) VALUES ('$username','$day','$month','$year','$room_id','$period','$timebooked','$subject','$class','$schoolYear','$bookingReason')";
								database::executeQuery($query);
								echo "Period has been successfully booked.\n<br /><a href=\"javascript:window.close();\">Close Window</a>\n";
							}
							else
							{
								echo "Period <b>NOT</b> booked as not within valid booking time ranges.\n";
								echo "<br /><a href=\"javascript:window.close();\">Close Window</a>\n";
							}
						}
						else
						{
							echo "Period <b>NOT</b> booked as not logged in.\n";
							echo "<br /><a href=\"javascript:window.close();\">Close Window</a>\n";
						}
					}
					else
					{
						echo "Period <b>NOT</b> booked.<br />\nYou can not book futher periods as you have reached your booking limit for this year.";
					}
				}
			}
		}
		else
		{
			if (settings::get_bookingReasonEnabled() == true)
			{
				echo "You need to select the <b>subject, the booking reason and the class </b> the room is being booked for <b>before</b> you book. <a href=\"javascript:history.back()\">Go Back</a> and fix this mistake.";
			}
			else
			{
				echo "You need to select the <b>subject and class</b> the room is being booked for <b>before</b> you book. <a href=\"javascript:history.back()\">Go Back</a> and fix this mistake.";
			}
		}
	}

	function canBook()
	{
		if (security::get_level('') >= 2)
		{
			// if the security level is 2 or greater, no need to check the database as always true
			return true;
		}
		
		$username = $_SESSION['SESSIONusername'];
		$schoolYear = generateSchoolYear(date("m"),date("Y"));
		$bookings = database::executeQuery("SELECT * FROM bookingsingle WHERE username='$username' AND schoolYear='$schoolYear'");
		$numberOfBookings = count($bookings);
		
		$offsetData = database::executeQuery("SELECT * FROM bookingusers WHERE username='$username'");
		$offsetValue = $offsetData[0]['maxBookingOffset'];
		
		$usersMaxBookings = settings::get_numberOfBookingsPerYear() + $offsetValue;
		
		if ($numberOfBookings < $usersMaxBookings)
		{
			// if the current number of bookings made by this user for this year is less than the maximum then canBook = true
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function deleteBooking()
	{
		$user_level = security::get_level('');
		
		// collect the data from the url
	
		$day = raw_param("day");
		$month = raw_param("month");
		$year = raw_param("year");
		$room_id = raw_param("room");
		$room_data = database::getRoomName($room_id);
		$room_name = $room_data['name'];
		$period = raw_param("period");
		
		$query = "SELECT * FROM bookingperiods WHERE number='$period'";
		$result = database::executeQuery($query);
		$periodName = $result[0]['name'];
		
		$day_name = date("D",mktime(1,1,1,$month,$day,$year));
		$normalBooked = normal::getBookingData($day,$month,$year,$room_id,$period);
		$repetitiveBooked = repetitive::getBookingData($day_name,$room_id,$period,generateSchoolYear($month,$year));
		
		echo "<h2>Delete A Booking</h2>";
		
		
		$username = $_SESSION['SESSIONusername'];
			
		if ($_GET['confirmation'] != 1) // confirm u wish to delete this.
		{
			
			if ($username == $normalBooked[0]['username'] || $user_level >= 2)
			{
				// if user is the booker or an admin then allow deletion [this works out that normal users can only delete normal bookings but admin's can always delete bookings.
				$numberPermanent = count($repetitiveBooked);
				
				if ($numberPermanent >= 1 && $user_level >= 2) 
				{
					echo "Are you sure you wish to delete this booking? It has been booked for the entire <span class=\"attention\">year</span> for $periodName, $room_name on ".date('l, jS F Y',mktime(1,1,1,$month,$day,$year)).".\n";
					echo "<br />\n<br />\n";
					echo "Are you sure you wish to delete this booking?\n";
					echo "<form method=\"post\" action=\"?type=Unbook&confirmation=1&day=$day&month=$month&year=$year&period=$period&room=$room_id\">\n";
					echo "\t<input type=\"submit\" value=\"Yes\" />\n\t<input type=\"button\" value=\"No\" onClick=\"window.close();\" />\n";
					echo "</form>\n";
				}
				elseif ($user_level >= 1) 
				{
					echo "You have selected to delete a booking you made for $periodName in $room_name on ".date('l, jS F Y',mktime(1,1,1,$month,$day,$year)).".\n";
					echo "<br />\n<br />\n";
					echo "Are you sure you wish to delete this booking?\n";
					echo "<form method=\"post\" action=\"?type=Unbook&confirmation=1&day=$day&month=$month&year=$year&period=$period&room=$room_id\">\n";
					echo "\t<input type=\"submit\" value=\"Yes\" />\n\t<input type=\"button\" value=\"No\" onClick=\"window.close();\" />\n";
					echo "</form>\n";
				}
				else
				{
					echo "You do not have the privialges to delete this booking.\n";
				}
			}
			else
			{
				echo "You do not have the privialges to delete this booking.\n";
			}
		}
		else	
		{ 
			// user has confirmed they wish to delete this. check its theirs to delete and has yet to start then delete the record
			$result = database::executeQuery("SELECT starttime FROM bookingperiods WHERE number='$period'");
			$time = explode(".",$result[0]['starttime']);
			$startTimeOfBooking = mktime($time[0],$time[1],0,$month,$day,$year);
			
			if ($user_level >= 2 || ($username == $normalBooked[0]['username'] && time() < $startTimeOfBooking))
			{
				$numberSingle = count($normalBooked);
				$numberRepetitive = count($repetitiveBooked);
				if ($numberSingle > 0)
				{
					$query = "DELETE FROM bookingsingle WHERE day='$day' AND month='$month' AND year='$year' AND period='$period' AND room='$room_id' LIMIT 1";
				}
				elseif ($numberRepetitive > 0)
				{
					if ($user_level >= 2) 
					{
						$schoolYear = generateSchoolYear($month,$year);
						
						$query = "DELETE FROM bookingpermanent WHERE day='$day_name' AND period='$period' AND room='$room_id' AND schoolYear='$schoolYear' LIMIT 1";
					}
					else
					{
						$msg = "You are not allowed to delete this type of booking. See ".settings::get_AdminName()." to get this deleted for you.";
					}
				}
				else // period not already booked into system. so is not available to be deleted.
				{
					$query = NULL;
					$msg = "Nothing to delete.";
				}
				if (!empty($query))
				{
					$result = database::executeQuery($query);
					echo "Booking has been successfully deleted from the system.\n";
				}
				else
				{
					echo $msg."\n";
				}
			}
			else
			{
				echo "You do not have permission to delete this booking. Possibly as the booking time frame has already begun.\n";
			}
			echo "<br />\n<br />\n<a href=\"javascript:window.close();\">Close Window</a>\n";
		}
	}
	function deletePermanentBooking()
	{
		$user_level = security::get_level('');
		
		// collect the data from the url
		$day_name = raw_param("dayName");
		$schoolYear = raw_param("schoolYear");
		$room_id = raw_param("room");
		$room_data = database::getRoomName($room_id);
		$room_name = $room_data['name'];
		$period = raw_param("period");
	
		$query = "SELECT * FROM bookingperiods WHERE number='$period'";
		$result = database::executeQuery($query);
		$periodName = $result[0]['name'];
		
		$repetitiveBooked = repetitive::getBookingData($day_name,$room_id,$period,$schoolYear);
		
		echo "<h2>Delete A Permanent Booking</h2>";
		
		
		$username = $_SESSION['SESSIONusername'];
			
		if ($_GET['confirmation'] != 1) // confirm you wish to delete this.
		{
			
			if ($user_level >= 2)
			{
				// if user is an admin then allow deletion
				$numberPermanent = count($repetitiveBooked);
				
				if ($numberPermanent >= 1) 
				{
					echo "Are you sure you wish to delete this booking? It has been booked for the entire <span class=\"attention\">year</span> for $periodName, $room_name for {$day_name}days.\n";
					echo "<br />\n<br />\n";
					echo "Are you sure you wish to delete this booking?\n";
					echo "<form method=\"post\" action=\"?type=Unbook&confirmation=1&dayName=$day_name&schoolYear={$schoolYear}&period=$period&room=$room_id\">\n";
					echo "\t<input type=\"submit\" value=\"Yes\" />\n\t<input type=\"button\" value=\"No\" onClick=\"window.close();\" />\n";
					echo "</form>\n";
				}
				else
				{
					echo "The is no permanent booking for this period/room/day.\n";
				}
			}
			else
			{
				echo "You do not have the privialges to delete this booking.\n";
			}
		}
		else	
		{ 
			// user has confirmed they wish to delete this. 
			if ($user_level >= 2)
			{
				$numberRepetitive = count($repetitiveBooked);
				if ($numberRepetitive > 0)
				{
					$query = "DELETE FROM bookingpermanent WHERE day='$day_name' AND period='$period' AND room='$room_id' AND schoolYear='$schoolYear' LIMIT 1";
				}
				else // period not already booked into system. so is not available to be deleted.
				{
					$query = NULL;
					$msg = "Nothing to delete.";
				}
				if (!empty($query))
				{
					database::executeQuery($query);
					echo "Booking has been successfully deleted from the system.\n";
				}
				else
				{
					echo $msg."\n";
				}
			}
			else
			{
				echo "You do not have permission to delete this booking.\n";
			}
			echo "<br />\n<br />\n<a href=\"javascript:window.close();\">Close Window</a>\n";
		}
	}
	function blankBooking()
	{
		$user_level = security::get_level('');
		
		// collect the data from the url
		$day = raw_param("day");
		$month = raw_param("month");
		$year = raw_param("year");
		$room_id = raw_param("room");
		$room_data = database::getRoomName($room_id);
		$room_name = $room_data['name'];
		
		$day_name = date("D",mktime(1,1,1,$month,$day,$year));
		
		
		if ($user_level >= 2)
		{
			if ($_GET['confirmation'] == 1)
			{
				echo "<h2>Blank Book This Room For This Day</h2>";
				
				$username = $_SESSION['username'];
				$bookedat = time();
				
				$query = "DELETE FROM bookingsingle WHERE day='$day' AND month='$month' AND year='$year' AND room='$room_id'";
				database::executeQuery($query);
				
				$schoolYear = generateSchoolYear($month,$year);
				
				for($i=1;$i<=settings::get_numberOfPeriods();$i++)
				{
					$period = $i;
					$query = "INSERT INTO bookingsingle (username,day,month,year,room,period,timebooked,subject,class,schoolYear) VALUES ('$username','$day','$month','$year','$room_id','$period','$bookedat','NO-SUBJECT','NO-CLASS','$schoolYear')";
					database::executeQuery($query);
				};

				echo "All bookings overwritten with blank bookings successfully.\n";
				echo "<br />\n<a href=\"javascript:window.close();\">Close Window</a>\n";
			}
			else
			{
				echo "<h2>Blank Book This Room For This Day</h2>\n";

				echo "Are you sure you wish to fill this room on this day with blank bookings?\n<br />\n<br />\n";
				echo "<b>By continuing, you will remove the following bookings by the following users.</b>\n<br />\n<br />\n";
				echo "Please <b>note these down</b> now or press 'Crtl + P' to print this page as this information will not be avaible if you continue.\n";
				echo "<br />\n<br />\n<br />\n";
				echo "<table class='centertable'>\n";
				echo "\t<tr>\n\t\t<td class='attention'>Username</td>\n\t\t<td class='attention'>Class</td>\n\t\t<td class='attention'>Subject</td>\n\t\t<td class='attention'>Period</td>\n\t</tr>\n";

				$query = "SELECT * FROM bookingsingle WHERE day='$day' AND month='$month' AND year='$year' AND room='$room_id' ORDER BY period";
				$result = database::executeQuery($query);
				if (count($result) == 0) {echo "\t<tr>\n\t\t<td colspan='4'><i>No bookings to delete.</i></td>\n\t</tr>\n";}
				else
				{
					foreach ($result as $index => $booking)
					{
						echo "\t<tr>\n\t\t<td>".$booking['username']."</td>\n\t\t<td>".$booking['class']."</td>\n\t\t<td>".$booking['subject']."</td>\n\t\t<td>".$booking['period']."</td>\n\t</tr>\n";
					};
				}
				echo "\t<tr>\n\t\t<td colspan='4'>&nbsp;</td>\n\t</tr>\n";
				
				$schoolYear = generateSchoolYear($month,$year);
				
				$query = "SELECT * FROM bookingpermanent WHERE day='$day_name' AND room='$room_id' AND schoolYear='$schoolYear' ORDER BY period";
				$result = database::executeQuery($query);
				if (count($result) == 0)
				{
					echo "\t<tr>\n\t\t<td colspan='4'><i>No yearly bookings affected.</i></td>\n\t</tr>\n";
				}
				else
				{
					echo "\t<tr>\n\t\t<td colspan='4' class='attention'>Yearly Bookings Affected</td>\n\t</tr>\n";
					foreach ($result as $index => $booking)
					{
						echo "\t<tr>\n\t\t<td>".$booking['username']."</td>\n\t\t<td>".$booking['class']."</td>\n\t\t<td>".$booking['subject']."</td>\n\t\t<td>".$booking['period']."</td>\n\t</tr>";
					};
				}

				echo "</table>\n";
				echo "<br />\n";
				echo "If you are ready to continue, <a href=\"?type=blankBooking&day=$day&month=$month&year=$year&room=$room_id&confirmation=1\">Click Here</a>.\n";
			}
		}
		else
		{
			echo "You are not allowed to perform that action.\n";
		}
	}
} // ends the booking class

class normal extends booking
{
	var $year;
	var $month;
	var $day;
	
	function normal ($id,$username,$room,$period,$timebooked,$subject,$classname,$year,$month,$day,$bookingID)
	{
		$this->Booking($id,$username,$room,$period,$timebooked,$subject,$classname,$bookingID);
		$this->year = $year;
		$this->month = $month;
		$this->day = $day;
	}
	
	function getDayBookings($year,$month,$day)
	{
		$bookings = database::getDaySingleBookingsData($year,$month,$day);
		$dayBookings = array();
		foreach($bookings as $item)
		{
			$dayBookings[] = & new normal($item['id'],$item['username'],$item['room'],$item['period'],$item['timebooked'],$item['subject'],$item['class'],$item['year'],$item['month'],$item['day'],$item['bookingID']);
		}
		return $dayBookings;
	}
	
	function getDayRoomBookings($year,$month,$day,$room)
	{
		$query = "SELECT * FROM bookingsingle WHERE day='$day' AND month='$month' AND year='$year' AND room='$room'";
		$bookings = database::executeQuery($query);

		$dayBookings = array();
		foreach($bookings as $item)
		{
			$dayBookings[] = & new normal($item['id'],$item['username'],$item['room'],$item['period'],$item['timebooked'],$item['subject'],$item['class'],$item['year'],$item['month'],$item['day'],$item['bookingID']);
		}
		return $dayBookings;
	}
	function getBookingData($day,$month,$year,$room,$period)
	{
		if (strlen($month) == 1)
		{
			$month = "0".$month;
		}
		$query = "SELECT * FROM bookingsingle WHERE day='$day' AND month='$month' AND year='$year' AND room='$room' AND period='$period'";
		$result = database::executeQuery($query);
		return $result;
	}
	function isPeriodBooked($day,$month,$year,$room,$period)
	{
		$result = normal::getBookingData($day,$month,$year,$room,$period);
		return count($result);
	}
} // ends the normal class

class repetitive extends booking
{
	var $dayNumber; // monday=1,tuesday=2,wednesday=3...
	
	function repetitive ($id,$username,$room,$period,$timebooked,$subject,$classname,$daynumber,$bookingID)
	{
		$this->Booking($id,$username,$room,$period,$timebooked,$subject,$classname,$bookingID);
		$this->dayNumber = $daynumber;
	}
	
	function getDayBookings($dayName,$schoolYear)
	{
		$query = "SELECT * FROM bookingpermanent WHERE day='$dayName' AND schoolYear='$schoolYear'";
		$bookings = database::executeQuery($query);

		$dayBookings = array();
		foreach($bookings as $item)
		{
			$dayBookings[] = & new repetitive($item['id'],$item['username'],$item['room'],$item['period'],$item['timebooked'],$item['subject'],$item['class'],$item['daynumber'],$item['bookingID']);
		}
		return $dayBookings;
	}
	
	function getDayRoomBookings($dayName,$room,$schoolYear)
	{
		$query = "SELECT * FROM bookingpermanent WHERE day='$dayName' AND room='$room' AND schoolYear='$schoolYear'";
		$bookings = database::executeQuery($query);

		$dayBookings = array();
		foreach($bookings as $item)
		{
			$dayBookings[] = & new repetitive($item['id'],$item['username'],$item['room'],$item['period'],$item['timebooked'],$item['subject'],$item['class'],$item['daynumber'],$item['bookingID']);
		}
		return $dayBookings;
	}
	
	function getBookingData($dayName,$room,$period,$schoolYear)
	{
		$query = "SELECT * FROM bookingpermanent WHERE day='$dayName' AND room='$room' AND period='$period' AND schoolYear='$schoolYear'";
		$result = database::executeQuery($query);
		return $result;
	}
	
	function isPeriodBooked($dayName,$room,$period,$schoolYear)
	{
		$result = repetitive::getBookingData($dayName,$room,$period,$schoolYear);
		return count($result);
	}
	
} // ends the repetitive class
?>
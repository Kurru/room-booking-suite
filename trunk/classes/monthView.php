<?php

class monthView
{
	var $months;
	function monthView()
	{
	// initialise the months
		$this->months["09"] = "September";
		$this->months["10"] = "October";
		$this->months["11"] = "November";
		$this->months["12"] = "December";
		$this->months["01"] = "January";
		$this->months["02"] = "February";
		$this->months["03"] = "March";
		$this->months["04"] = "April";
		$this->months["05"] = "May";
		$this->months["06"] = "June";
	}
	
	function displayView ()
	{
		// This function displays a table of days for the indicated month
		$month = $_GET['month'];
		$year = $_GET['year'];
		
		if ($month == NULL)
		{
			$month = date("m");
		}
		if ($year == NULL)
		{
			$year = date("Y");
		}
		
		$nextMonth = $month + 1;
		$previousMonth = $month - 1;
		$previousYear = $year;
		$nextYear = $year;
		if ($month == 6) {$nextMonth = 9; $nextYear = $year - 1;}
		if ($month == 9) {$previousMonth = 6; $previousYear = $year + 1;}
		if ($month == 12) {$nextMonth = 1; $nextYear = $year + 1;}
		if ($month == 1) {$previousMonth = 12; $previousYear = $year - 1;}
	?>
<table border="0">
<tr>
<td colspan="5">
<?php $this->displaySelection($year,$month); ?>
</td>
	</tr>
	<tr><td colspan="5"><hr /></td></tr>
<?php $this->displayMonthDays($year,$month); ?>
	<tr><td colspan="5"><hr /></td></tr>
	<tr>
		<td colspan="5">
			<div style="float:left;"><a href="?year=<?php echo $previousYear; ?>&month=<?php echo $previousMonth; ?>">Prev Month</a></div>
			<div style="float:right"><a href="?year=<?php echo $nextYear; ?>&month=<?php echo $nextMonth; ?>">Next Month</a></div>
		</td>
	</tr>
</table>


<?php

	} // end display viwe



	function displaySelection($year,$month)
	{
	// this function displays the drop down selection for picking the month to view
	// $month is the current month
	// $year is the current year
	
?>Select Month: 
<form style="display:inline;" method="POST">
<select size="1" onChange="location = '' + this.options[this.selectedIndex ].value;">
<?php 


foreach ($this->months as $monthNumber => $monthName)
{
	if ($month <= 7 && $monthNumber >= 8 )
	{
		$year2 = $year - 1;
	}
	elseif ($month >= 8 && $monthNumber <= 7)
	{
		$year2 = $year + 1;
	}
	else
	{
		$year2 = $year;
	}

	echo "\t<option value=\"?year=$year2&month=$monthNumber\"";
	if ($monthNumber==$month)
	{
	// if month is the currently selected month have this month selected at start point
		echo " selected=\"selected\"";
	}
	echo ">$monthName $year2</option>\n";
}
?>
</select>
</form>
<?php

	} // end display selection
	
	function displayMonthDays($year,$month)
	{
?>
	<tr><td>Mon</td><td>Tue</td><td>Wed</td><td>Thu</td><td>Fri</td></tr>
	<tr><td colspan="5"><hr /></td></tr>
<?php 
		$firstDayOfMonth = mktime(1,1,1,$month,1,$year);	// gets the timestamp of the 1st day of the month
		$numberOfDaysInMonth = date("t",$firstDayOfMonth);  // gets the number of days in this month
		$DayNumberOfFirstDay = date("w",$firstDayOfMonth);  // finds out what the first day is [sunday=0,monday=1....]
		$day = 1;
	
		if ($DayNumberOfFirstDay == 0)
		{
			$day += 1; 			// if the 1st day of the month is a sunday, move the starting point to the monday
		}
		elseif($DayNumberOfFirstDay == 6)
		{
			$day += 2; 			// if the 1st day of the month is a saturday, move the starting point to the monday
		}
		
		$firstWeek = true;
		$weekC = 0;
		while ($day <= $numberOfDaysInMonth)
		{
			if ($weekC == 0)
			{
				echo "\t<tr>\n"; 	// at start of row so open a row
			}
			if ($firstWeek)
			{
				$blankDays = $DayNumberOfFirstDay - 1;
				if ($blankDays < 0)
				{
					// if the first day of the month is a sunday this will cause blankdays to be negetitive.
					$blankDays = 0;
				}
				if ($blankDays == 5)
				{
					// if the first day of the month is a saturday this will cause blankdays to be 5 so a whole empty week will be produced
					// this removes that blank week
					$blankDays = 0;
				}
				for ($i=0;$i<$blankDays;$i++)
				{
					echo "\t\t<td></td>\n";
				}
				$weekC+=$blankDays;
				$firstWeek = false;
			}
			$weekC++;
			echo "\t\t<td>"; 		// create a new cell
			// create the link
			if (security::get_level('') == 2)
			{
				// advanced user, always avaible to make bookings, so make link avaible
				$this->displayLink($year,$month,$day,true);
			}
			else
			{
				// normal user, can not book x days in the further
				$maxRange = settings::get_bookingRangeMax();
				$currentTime = time();
				$maxTime = $currentTime + $maxRange*24*60*60;
				$displayedTime = mkTime(1,1,1,$month,$day,$year);
				if ($displayedTime <= $maxTime)
				{
					// if the current day to display is before the max day then display a link!
					$this->displayLink($year,$month,$day,true);
				}
				else
				{
					$this->displayLink($year,$month,$day,false);
				}
			}
			echo "</td>\n";		// close the cell
			if ($weekC == 5)
			{
				echo "\t</tr>\n"; 	// at end of the week so end the row
				$weekC = 0;		// reset the week back to the start of week
				$day += 2;		// jump the days at the weekend
			}
			$day++;
		} // end of while loop outputting the calendar and links
	}
	
	function displayLink($year,$month,$day,$print)
	{
		if ($print == true)
		{
			echo "<a id=\"$year$month$day\" target=\"dayView\" onclick=\"colorLink($year$month$day,1);\" href=\"dayView.php?year=$year&month=$month&day=$day\">$day</a>";
		}
		else
		{
			echo $day;
		}
	}
	
	function displayTitle ()
	{
		echo "Month View";
	}
	
	
	function printJavascript()
	{
	// this adds the javascript for this page to the window
?>
<script type="text/javascript">
<!-- 
var countx = 0;
var iddss = null;
function ele(eleName) {
 if(document.getElementById && document.getElementById(eleName)) {return document.getElementById(eleName);}
  else if (document.all && document.all(eleName)) {return document.all(eleName);}
  else if (document.layers && document.layers[eleName]) {return document.layers[eleName];}
  else {return false;}
}
function colorLink(fieldNama,disclose) {
var fieldName = ele(fieldNama);
if (disclose == 1){
	if(countx == 0) { 
		countx = 1;
		iddss = fieldNama;
	}
	else {
		colorLink(iddss,0);
		iddss = fieldNama;
		countx = 1;
	}
}
else {countx = 0;}

// apply/restore the colours to the link
if (disclose == 1) {fieldName.style.color = '#9933CC';}
else {fieldName.style.color = '#EE0000';}
}
// -->
</script>
<?php
	} // end printJavascript 
}	// close the class

?>
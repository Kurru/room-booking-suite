<?php


class notes
{

	function displayNote($year,$month,$day,$numberOfColumns)
	{
		// get the note for the current day
		$note = database::getNote($year,$month,$day);

		$level = security::get_level('');
		if (!is_null($note) || $level >= 2)
		{
			// there is a note already for this day so display it
			// OR if user is admin then display it
			$display = true;
		}
		else
		{
			// there is not note for this day so display nothing or else the edit link
			$display = false;
		}
		
		if ($display == true)
		{
			echo "\t<tr>\n"; // start blank row
			echo "\t\t<td colspan=\"$numberOfColumns\">&nbsp;</td>\n";
			echo "\t</tr>\n";
		
			echo "\t<tr>\n";	// start note row
			echo "\t\t<td colspan=\"$numberOfColumns\">\n";
			
			if (is_null($note) && $level >= 2)
			{
				// no note for this day
				echo "\t\t\t<div id=\"text_short\" style='display:inline;'>\n";
				echo "\t\t\t\t<a href='#' onclick=\"showTags('text', 'true');\">Add A Note</a><br />\n";
				echo "\t\t\t</div>\n";
			}
			elseif ($level >= 2)
			{
				// there is a note, but is the user an admin
				// if yes, print note and have edit link
				echo "\t\t\t<div id=\"text_short\" style='display:inline;'>\n";
				echo "\t\t\t\t".$note['text']." - <a href='#' onclick=\"showTags('text', 'true');\">Edit</a><br />\n";
				echo "\t\t\t</div>\n";

			}
			else
			{
				// normal user, just display the note
				echo "\t\t\t".$note['text']."\n";
			}
			if ($level >= 2)
			{
				echo "\t\t\t<div id=\"text_long\" style=\"display:none;\">\n";
				echo "\t\t\t\t<a href='#' onclick=\"showTags('text', 'false');\">Hide</a> \n";
				echo "\t\t\t\t<iframe id=\"text_frame\" name=\"notes\" marginwidth=\"0px\" vspace=\"0px\" frameborder=\"0\" scrolling=\"no\" src=\"notepage.php?year=$year&month=$month&day=$day\" width=\"0px\" style=\"display:inline;\" height=\"0px\"></iframe>\n";
				echo "\t\t\t</div>\n";
			}
			echo "\t\t</td>\n";
			echo "\t</tr>\n";

		}
		

	}

	function displayEdit()
	{
		if (security::get_level('') < 2)
		{
			echo "Not authorised.";
		}
		else
		{
			// user is admin or greater
			
			
			if (!isset($_POST['edit']))
			{
				// form hasn't been submitted so display it.
				$year = $_GET['year'];
				$month = $_GET['month'];
				$day = $_GET['day'];
				$result = database::getNote($year,$month,$day);
				if (is_null($result))
				{
					$currentNote = "";
				}
				else
				{
					$currentNote = $result['text'];
				}
				
				echo "<form style='display:inline;' action='' method='post'>\n";
				echo "<input type='hidden' name='edit' value='1' />\n";
				echo "<input type='hidden' name='day' value='$day' />\n";
				echo "<input type='hidden' name='month' value='$month' />\n";
				echo "<input type='hidden' name='year' value='$year' />\n";
				echo "<input type='text' name='note' size='29' value='".$currentNote."' />\n";
				echo "<input type='submit' value='Save' />\n";
				echo "</form>\n";
			}
			else
			{
				// form has been submitted
				$year = raw_param_post("year");
				$month = raw_param_post("month");
				$day = raw_param_post("day");
				$note = raw_param_post("note");
				if (empty($month) || empty($year) || empty($day))
				{
					// dont do anything
					echo "empty value";
				}
				else
				{
					$notes = database::getNote($year,$month,$day);
					if (is_null($notes))
					{
						// there is no booking on this day, add the new one
						if (!empty($note))
						{
							// there is a new entry to save so add it to the database
							$schoolYear = generateSchoolYear($month,$year);
							$query = "INSERT INTO bookingnotes (day,month,year,schoolYear,text) VALUES ('$day','$month','$year','$schoolYear','$note')";
							database::executeQuery($query);
							echo "Note Added.";
						}
						else
						{
							echo "Note Not Added.";
						}
						
					}
					else
					{
						// update or delete the current one
						if (empty($note))
						{
							// delete the note
							$query = "DELETE FROM bookingnotes WHERE day='$day' AND month='$month' AND year='$year'";
							$result = database::executeQuery($query);
							echo "Note Removed.";
						}
						else
						{
							// update the note
							$query = "UPDATE bookingnotes SET text='$note' WHERE day='$day' AND month='$month' AND year='$year'";
							$result = database::executeQuery($query);
							echo "Note Updated.";
						}
					}
				}
				notes::javascriptRedirect($year,$month,$day);
				
			}
		}
	}
	
	function javascriptRedirect($year,$month,$day)
	{
?>
<script language="javascript" type="text/javascript">
<!-- 
parent.resetNoteScreen(<?PHP echo "'".$year."','".$month."','".$day."'"; ?>);
// -->
</script>
<?PHP 
 
	}
	function title()
	{
		echo "Day Note";
	}
	
	function css()
	{
	?>
<style media="all" type="text/css">
body {
	background-color:#EDEDED;
	padding: 2px 0px 0px 0px;
	margin: 0px 0px 0px 0px;
}

</style>
	<?php
	} // end css function
}
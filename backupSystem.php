<?PHP
session_start();
include_once("classes/security.php");

if (security::get_level('1') >= 2)
{
	include_once("classes/database.php");
	header('Content-type: application/sql');
	$date = date("Ymd_Hi");
	header("Content-Disposition: attachment; filename=\"backupSystem-$date.sql\"");
	
	$tables = array();
	$tables[] = "bookingareas";
	$tables[] = "bookingclasses";
	$tables[] = "bookingnotes";
	$tables[] = "bookingperiods";
	$tables[] = "bookingpermanent";
	$tables[] = "bookingreason";
	$tables[] = "bookingsettings";
	$tables[] = "bookingsingle";
	$tables[] = "bookingsubjects";
	$tables[] = "bookingusers";
	
	$introduction = "-- BOOKING SUITE DATABASE DUMP
-- VERSION 2.0
-- HTTP://WWW.ANIMEEQUATION.COM/BOOKINGSUITE
-- GENERATION TIME: ".date("jS F Y \a\t h:ia")."

-- ----------------------------------------------------------
";
	echo $introduction;
	foreach($tables as $index => $table)
	{
		$query = "SELECT * FROM $table";
		$areas = database::executeQuery($query);
		
		$deleteData = "DELETE FROM $table;";
		
		$sectionIntroduction = "

$deleteData

--
-- Table Data for table '$table'
--

";
		echo $sectionIntroduction;
		
		if (count($areas) >= 1)
		{
			$datas = "";
			foreach ($areas as $index => $area)
			{
			
				$entryQuery = "INSERT INTO $table ";
				$indexs = "(";
				$datas .= "(";
				
				foreach ($area as $index => $data)
				{
					$indexs .= $index.',';
					$datas .= "'".$data."',"; 
				}
				$indexs = substr($indexs,0,-1);
				$datas = substr($datas,0,-1);
				$indexs .= ")";
				$datas .= "),";	
			}
		$datas = substr($datas,0,-1);

		$entryQuery = $entryQuery.$indexs." VALUES ".$datas.";
";
		echo $entryQuery;
		}
		else
		{
			echo "-- No data in this table
";
		}
	}
}
else
{
	echo "You do not have the access rights for this feature.";
}
?>

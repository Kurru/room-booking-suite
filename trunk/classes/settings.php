<?php
settings::setSettings(); // initialise the static variables

class settings
{
/*	static $systemTitle = NULL; // the title tag used by the whole system
	static $bookingRangeMax = NULL; // the maximum number of days into the future a normal user can book
	static $bookingRangeMin = NULL; // the minimum number of days into the future a normal user can book
	static $numberOfPeriods = NULL; // the number of periods in a day
	*/


	function setSettings()
	{
		$settings = database::getSettings();							// gets the user set data from the database
		$GLOBALS['settings'] = $settings;
/*		settings::$systemTitle = $settings['SystemTitle']; 				// sets the System Title name
		settings::$bookingRangeMax = $settings['bookingRangeMax'];		// sets the booking range max
		settings::$bookingRangeMin = $settings['bookingRangeMin'];		// sets the booking range min
		settings::$numberOfPeriods = $settings['numberOfPeriods'];		// sets the number of periods
*/
	}
	
	function get_AdminName()
	{
		return $GLOBALS['settings']['AdminName'];
	}
	
	function get_systemTitle()
	{
		return $GLOBALS['settings']['SystemTitle'];
	}
	function get_bookingRangeMax()
	{
		return $GLOBALS['settings']['bookingRangeMax'];
	}
	function get_bookingRangeMin()
	{
		return $GLOBALS['settings']['bookingRangeMin'];
	}
	function get_numberOfPeriods()
	{
		return $GLOBALS['settings']['numberOfPeriods'];
	}
	function get_bookingReasonEnabled()
	{
		return $GLOBALS['settings']['bookingReason'];
	}
	function get_frontPageMessage()
	{
		return $GLOBALS['settings']['mainPageMessage'];
	}
	function get_numberOfBookingsPerYear()
	{
		return $GLOBALS['settings']['yearlyBookingCount'];
	}
}
?>
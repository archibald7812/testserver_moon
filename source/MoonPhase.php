<?php
include 'source/MoonDay.php';
define('MP_NEW_MOON_NAME', 'Новолуние (1)');
define('MP_NEW_MOON_ID', 0);
define('MP_WAXING_CRESCENT_NAME', 'Молодая луна (2)');
define('MP_WAXING_CRESCENT_ID', 1);
define('MP_FIRST_QUARTER_NAME', 'Первая четверть (3)');
define('MP_FIRST_QUARTER_ID', 2);
define('MP_WAXING_GIBBOUS_NAME', 'Прибывающая луна (4)');
define('MP_WAXING_GIBBOUS_ID', 3);
define('MP_FULL_MOON_NAME', 'Полнолуние (5)');
define('MP_FULL_MOON_ID', 4);
define('MP_WANING_GIBBOUS_NAME', 'Убывающая луна (6)');
define('MP_WANING_GIBBOUS_ID', 5);
define('MP_THIRD_QUARTER_MOON_NAME', 'Последняя четверть (7)');
define('MP_THIRD_QUARTER_MOON_ID', 6);
define('MP_WANING_CRESCENT_NAME', 'Старая луна (8)');
define('MP_WANING_CRESCENT_ID', 7);
define('MP_DAY_IN_SECONDS', 60 * 60 * 24);

class moonPhase
{

	var $allMoonPhases = array(
		MP_NEW_MOON_NAME,
		MP_WAXING_CRESCENT_NAME,
		MP_FIRST_QUARTER_NAME,
		MP_WAXING_GIBBOUS_NAME,
		MP_FULL_MOON_NAME,
		MP_WANING_GIBBOUS_NAME,
		MP_THIRD_QUARTER_MOON_NAME,
		MP_WANING_CRESCENT_NAME
	);
	var $dateAsTimeStamp;
	var $moonPhaseIDforDate;
	var $moonPhaseNameForDate;
	var $periodInDays = 29.5861; // == complete moon cycle
	var $periodInSeconds = -1; // gets set when you ask for it
	var $someFullMoonDate;
	var $phaseInfoForCurrentDate = array();

	/*
* CONSTRUCTOR
*/

	function calcMoonPhase()
	{
		$position = $this->getPositionInCycle();
		if ($position >=  0.0 && $position <= 0.131)
			$phaseInfoForCurrentDate = array(MP_NEW_MOON_ID, MP_NEW_MOON_NAME);
		else if ($position >= 0.131 && $position <= 0.261)
			$phaseInfoForCurrentDate = array(MP_WAXING_CRESCENT_ID, MP_WAXING_CRESCENT_NAME);
		else if ($position >= 0.261 && $position <= 0.394)
			$phaseInfoForCurrentDate = array(MP_FIRST_QUARTER_ID, MP_FIRST_QUARTER_NAME);
		else if ($position >= 0.394 && $position <= 0.527)
			$phaseInfoForCurrentDate = array(MP_WAXING_GIBBOUS_ID, MP_WAXING_GIBBOUS_NAME);
		else if ($position >= 0.527 || $position <= 0.645)
			$phaseInfoForCurrentDate = array(MP_FULL_MOON_ID, MP_FULL_MOON_NAME);
		else if ($position >= 0.645 && $position <= 0.762)
			$phaseInfoForCurrentDate = array(MP_WANING_GIBBOUS_ID, MP_WANING_GIBBOUS_NAME);
		else if ($position >= 0.762 && $position <= 0.881)
			$phaseInfoForCurrentDate = array(MP_THIRD_QUARTER_MOON_ID, MP_THIRD_QUARTER_MOON_NAME);
		else if ($position >= 0.881 && $position <= 1)
			$phaseInfoForCurrentDate = array(MP_WANING_CRESCENT_ID, MP_WANING_CRESCENT_NAME);
		list($this->moonPhaseIDforDate, $this->moonPhaseNameForDate) = $phaseInfoForCurrentDate;
	} // END function calcMoonPhase() {

	/*
* PUBLIC
* return (array) all moon phases as ID => Name
*/
	function getAllMoonPhases()
	{
		return $this->allMoonPhases;
	} // END function getAllMoonPhases() {

	/*
* PUBLIC
*/
	function getBaseFullMoonDate()
	{
		$this->someFullMoonDate = strtotime("March 2 2022 20:38 Europe/Moscow");
		return $this->someFullMoonDate;
	} // END function getBaseFullMoonDate() {

	/*
* PUBLIC
* return (int) timestamp of the current date being calculated
*/
	function getDateAsTimeStamp()
	{
		if (isset($_POST['now'])) {
			$this->dateAsTimeStamp = time();
		}
		if (isset($_POST['date'])) {
			$this->dateAsTimeStamp = strtotime($_POST['date']);
			return $this->dateAsTimeStamp;
		} else {
			return $this->dateAsTimeStamp;
		}
	} // END function getDateAsTimeStamp() {
	/*
* PUBLIC
* returns the percentage of how much lunar face is visible
*/
	function getPercentOfIllumination()
	{
		// from http://www.lunaroutreach.org/cgi-src/qpom/qpom.c
		// C version: // return (1.0 - cos((2.0 * M_PI * phase) / (LPERIOD/ 86400.0))) / 2.0;
		$percentage = 1 - ((1.0 + cos(2 * M_PI * $this->getPositionInCycle())) / 2.0);
		$percentage *= 100;
		$percentage = round($percentage, 1) . '%';
		return $percentage;
	} // END function getPercentOfIllumination()
	/*
* PUBLIC
*/
	function getPeriodInSeconds()
	{
		//if ($this->periodInSeconds > -1) return $this->periodInSeconds; // in case it was cached
		$this->periodInSeconds = $this->periodInDays * MP_DAY_IN_SECONDS;
		return $this->periodInSeconds;
	} // END function getPeriodInSeconds() {

	/*
* PUBLIC
*/
	function getPhaseID()
	{
		$this->calcMoonPhase();
		return $this->moonPhaseIDforDate;
	} // EMD function getPhaseID() {


	/*
* PUBLIC
* $ID (int) ID of phase, default is to get the phase for the current date passed in constructor
*/
	function getPhaseName()
	{
		$this->calcMoonPhase();
		return $this->moonPhaseNameForDate; // get name for this current date
	} // END function getPhaseName() {

	/*
* PUBLIC
* return (float) number between 0 and 1. 0 or 1 is the beginning of a cycle (full moon)
* and 0.5 is the middle of a cycle (new moon).
*/
	function getPositionInCycle()
	{
		if (isset($_POST['date'])) {
			$curr = strtotime($_POST['date']);
		} else {
			$curr = time();
		}
		$diff = $curr - $this->getBaseFullMoonDate();
		$periodInSeconds = $this->getPeriodInSeconds();
		$position = ($diff % $periodInSeconds) / ($periodInSeconds);
		if ($position < 0)
			$position += 1;
		return $position;
	} // END function getPositionInCycle() {

	function calcMoonDay()
	{
		$md = new Moon();
		//$day = $md->calculateMoonTimes();
		$position = $this->getPositionInCycle();
		$day = round($position * $this->periodInDays, 3);
		return $day;
	}
	/*
* PUBLIC
* sets the internal date for calculation and calulates the moon phase for that date.
* called from the constructor.
* $timeStamp (int) date to set as unix timestamp
*/
	function setDate($timeStamp = -1)
	{
		if ($timeStamp == '' or $timeStamp == -1) {
			$timeStamp = time();
		};
		$this->dateAsTimeStamp = $timeStamp;
		$this->calcMoonPhase();
	} // END function setDate($timeStamp) {
	/*
* PUBLIC
* $newStartingDateAsTimeStamp (int) set a new date to start the week at, or use the current date
* return (array[6]) weekday timestamp => phase for weekday
*/
	/*
* PUBLIC
*/
	function getDaysUntilPreviousNewMoon()
	{
		$position = $this->getPositionInCycle();
		if (isset($_POST['date'])) {
			$newMoon = date("M d Y H:i:s", (strtotime($_POST['date']) - ($this->periodInSeconds - ((1 - $position) * $this->periodInSeconds))));
		} else {
			$newMoon = date("M d Y H:i:s", (time() - ($this->periodInSeconds - ((1 - $position) * $this->periodInSeconds))));
		}
		return $newMoon;
	} // ENDfunction getDaysUntilNextNewMoon() {

	function getDaysUntilNextNewMoon()
	{
		$position = $this->getPositionInCycle();
		if (isset($_POST['date'])) {
			$newMoon = date("M d Y H:i:s", ((1 - $position) * $this->periodInSeconds) + strtotime($_POST['date']));
		} else {
			$newMoon = date("M d Y H:i:s", ((1 - $position) * $this->periodInSeconds) + time());
		}
		return $newMoon;
	} // ENDfunction getDaysUntilNextNewMoon() {

	/*
* PUBLIC
*/
	function getDaysUntilNextFirstQuarterMoon()
	{
		$position = $this->getPositionInCycle();
		if (isset($_POST['date'])) {
			if ($position < 0.261)
				$lastQuarter = date("M d Y H:i:s", ((0.261 - $position) * $this->periodInSeconds) + strtotime($_POST['date']));
			else if ($position >= 0.261)
				$lastQuarter = date("M d Y H:i:s", ((1.261 - $position) * $this->periodInSeconds) + strtotime($_POST['date']));
		} else {
			if ($position < 0.261)
				$lastQuarter = date("M d Y H:i:s", ((0.261 - $position) * $this->periodInSeconds) + time());
			else if ($position >= 0.261)
				$lastQuarter = date("M d Y H:i:s", ((1.261 - $position) * $this->periodInSeconds) + time());
		}
		return $lastQuarter;
	} // END function getDaysUntilNextFirstQuarterMoon() {

	function getDaysUntilNextFullMoon()
	{
		$position = $this->getPositionInCycle();
		if (isset($_POST['date'])) {
			if ($position < 0.527)
				$fullMoon = date("M d Y H:i:s", ((0.527 - $position) * $this->periodInSeconds) + strtotime($_POST['date']));
			else if ($position >= 0.527)
				$fullMoon = date("M d Y H:i:s", ((1.527 - $position) * $this->periodInSeconds) + strtotime($_POST['date']));
		} else {
			if ($position < 0.527)
				$fullMoon = date("M d Y H:i:s", ((0.527 - $position) * $this->periodInSeconds) + time());
			else if ($position >= 0.527)
				$fullMoon = date("M d Y H:i:s", ((1.527 - $position) * $this->periodInSeconds) + time());
		}
		return $fullMoon;
	} // END function getDaysUntilNextFullMoon() {
	/*
* PUBLIC
*/
	function getDaysUntilNextLastQuarterMoon()
	{
		$position = $this->getPositionInCycle();
		if (isset($_POST['date'])) {
			if ($position < 0.762)
				$lastQuarter = date("M d Y H:i:s", ((0.762 - $position) * $this->periodInSeconds) + strtotime($_POST['date']));
			else if ($position >= 0.762)
				$lastQuarter = date("M d Y H:i:s", ((1.762 - $position) * $this->periodInSeconds) + strtotime($_POST['date']));
		} else {
			if ($position < 0.762)
				$lastQuarter = date("M d Y H:i:s", ((0.762 - $position) * $this->periodInSeconds) + time());
			else if ($position >= 0.762)
				$lastQuarter = date("M d Y H:i:s", ((1.762 - $position) * $this->periodInSeconds) + time());
		}
		return $lastQuarter;
	} // END function getDaysUntilNextLastQuarterMoon() {

	/*
* PUBLIC
*/



	function getUpcomingWeekArray($newStartingDateAsTimeStamp = -1)
	{
		$newStartingDateAsTimeStamp = ($newStartingDateAsTimeStamp > -1)
			? $newStartingDateAsTimeStamp
			: $this->getDateAsTimeStamp();
		$moonPhaseObj = get_class($this);
		$weeklyPhase = new $moonPhaseObj($newStartingDateAsTimeStamp);
		$upcomingWeekArray = array();
		for (
			$day = 0, $thisTimeStamp = $weeklyPhase->getDateAsTimeStamp();
			$day < 7;
			$day++, $thisTimeStamp += MP_DAY_IN_SECONDS
		) {
			$weeklyPhase->setDate($thisTimeStamp);
			$upcomingWeekArray[$thisTimeStamp] = $weeklyPhase->getPhaseID();
		} // END for($day = 0; $day < 7; $day++) {
		unset($weeklyPhase);
		return $upcomingWeekArray;
	} // END function getUpcomingWeekArray($newStartingDateAsTimeStamp = -1) {
} // END class moonPhase {

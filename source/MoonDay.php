<?php

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

class Moon
{
	public function get_nearest_timezone($cur_lat, $cur_long, $country_code = '')
	{
		$timezone_ids = ($country_code) ? DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $country_code)
			: DateTimeZone::listIdentifiers();

		if ($timezone_ids && is_array($timezone_ids) && isset($timezone_ids[0])) {

			$time_zone = '';
			$tz_distance = 0;

			//only one identifier?
			if (count($timezone_ids) == 1) {
				$time_zone = $timezone_ids[0];
			} else {

				foreach ($timezone_ids as $timezone_id) {
					$timezone = new DateTimeZone($timezone_id);
					$location = $timezone->getLocation();
					$tz_lat   = $location['latitude'];
					$tz_long  = $location['longitude'];

					$theta    = $cur_long - $tz_long;
					$distance = (sin(deg2rad($cur_lat)) * sin(deg2rad($tz_lat)))
						+ (cos(deg2rad($cur_lat)) * cos(deg2rad($tz_lat)) * cos(deg2rad($theta)));
					$distance = acos($distance);
					$distance = abs(rad2deg($distance));
					// echo '<br />'.$timezone_id.' '.$distance; 

					if (!$time_zone || $tz_distance > $distance) {
						$time_zone   = $timezone_id;
						$tz_distance = $distance;
					}
				}
			}
			return  $time_zone;
		}
		return 'unknown';
	}
	public function calculateMoonTimes($month, $day, $year, $lat, $lon)
	{
		if (isset($_POST['date'])) {
			if ($_POST['date'] == "" || $_POST['date'] == 0) {
				$date1 = "now";
			} else {
				$date0 = explode("T", $_POST['date']);
				$date1 = $date0[0];
			}
		} else {
			$date1 = "now";
		}
		$dateTimeZone = new DateTimeZone(self::get_nearest_timezone($lat, $lon));
		$gmtTimeZone = new DateTimeZone('UTC');
		$gmtDateTime = new DateTime((string)$date1, $gmtTimeZone);
		$timezone = ($dateTimeZone->getOffset($gmtDateTime)) / 3600;
		$utrise = $utset = 0;

		$date = self::modifiedJulianDate($month, $day, $year);
		$date -= $timezone / 24;
		$latRad = deg2rad($lat);
		$sinho = 0.0023271056;
		$sglat = sin($latRad);
		$cglat = cos($latRad);

		$rise = false;
		$set = false;
		$above = false;
		$hour = 1;
		$ym = self::sinAlt($date, $hour - 1, $lon, $cglat, $sglat) - $sinho;

		$above = $ym > 0;
		while ($hour < 25 && (false == $set || false == $rise)) {

			$yz = self::sinAlt($date, $hour, $lon, $cglat, $sglat) - $sinho;
			$yp = self::sinAlt($date, $hour + 1, $lon, $cglat, $sglat) - $sinho;

			$quadout = self::quad($ym, $yz, $yp);
			$nz = $quadout[0];
			$z1 = $quadout[1];
			$z2 = $quadout[2];
			$xe = $quadout[3];
			$ye = $quadout[4];

			if ($nz == 1) {
				if ($ym < 0) {
					$utrise = $hour + $z1;
					$rise = true;
				} else {
					$utset = $hour + $z1;
					$set = true;
				}
			}

			if ($nz == 2) {
				if ($ye < 0) {
					$utrise = $hour + $z2;
					$utset = $hour + $z1;
				} else {
					$utrise = $hour + $z1;
					$utset = $hour + $z2;
				}
			}

			$ym = $yp;
			$hour += 2.0;
		}
		// Convert to unix timestamps and return as an object
		$retVal = new stdClass();
		$utrise = self::convertTime($utrise);
		$utset = self::convertTime($utset);
		$retVal->moonrise = $rise ? mktime($utrise['hrs'], $utrise['min'], 0, $month, $day, $year) : mktime(0, 0, 0, $month, $day + 1, $year);
		$retVal->moonset = $set ? mktime($utset['hrs'], $utset['min'], 0, $month, $day, $year) : mktime(0, 0, 0, $month, $day + 1, $year);
		return $retVal;
	}
	private function quad($ym, $yz, $yp)
	{

		$nz = $z1 = $z2 = 0;
		$a = 0.5 * ($ym + $yp) - $yz;
		$b = 0.5 * ($yp - $ym);
		$c = $yz;
		$xe = -$b / (2 * $a);
		$ye = ($a * $xe + $b) * $xe + $c;
		$dis = $b * $b - 4 * $a * $c;
		if ($dis > 0) {
			$dx = 0.5 * sqrt($dis) / abs($a);
			$z1 = $xe - $dx;
			$z2 = $xe + $dx;
			$nz = abs($z1) < 1 ? $nz + 1 : $nz;
			$nz = abs($z2) < 1 ? $nz + 1 : $nz;
			$z1 = $z1 < -1 ? $z2 : $z1;
		}

		return array($nz, $z1, $z2, $xe, $ye);
	}
	private function sinAlt($mjd, $hour, $glon, $cglat, $sglat)
	{

		$mjd += $hour / 24;
		$t = ($mjd - 51544.5) / 36525;
		$objpos = self::minimoon($t);

		$ra = $objpos[1];
		$dec = $objpos[0];
		$decRad = deg2rad($dec);
		$tau = 15 * (self::lmst($mjd, $glon) - $ra);

		return $sglat * sin($decRad) + $cglat * cos($decRad) * cos(deg2rad($tau));
	}
	private function degRange($x)
	{
		$b = $x / 360;
		$a = 360 * ($b - (int)$b);
		$retVal = $a < 0 ? $a + 360 : $a;
		return $retVal;
	}
	private function lmst($mjd, $glon)
	{
		$d = $mjd - 51544.5;
		$t = $d / 36525;
		$lst = self::degRange(280.46061839 + 360.98564736629 * $d + 0.000387933 * $t * $t - $t * $t * $t / 38710000);
		return $lst / 15 + $glon / 15;
	}
	private function minimoon($t)
	{

		$p2 = 6.283185307;
		$arc = 206264.8062;
		$coseps = 0.91748;
		$sineps = 0.39778;

		$lo = self::frac(0.606433 + 1336.855225 * $t);
		$l = $p2 * self::frac(0.374897 + 1325.552410 * $t);
		$l2 = $l * 2;
		$ls = $p2 * self::frac(0.993133 + 99.997361 * $t);
		$d = $p2 * self::frac(0.827361 + 1236.853086 * $t);
		$d2 = $d * 2;
		$f = $p2 * self::frac(0.259086 + 1342.227825 * $t);
		$f2 = $f * 2;

		$sinls = sin($ls);
		$sinf2 = sin($f2);

		$dl = 22640 * sin($l);
		$dl += -4586 * sin($l - $d2);
		$dl += 2370 * sin($d2);
		$dl += 769 * sin($l2);
		$dl += -668 * $sinls;
		$dl += -412 * $sinf2;
		$dl += -212 * sin($l2 - $d2);
		$dl += -206 * sin($l + $ls - $d2);
		$dl += 192 * sin($l + $d2);
		$dl += -165 * sin($ls - $d2);
		$dl += -125 * sin($d);
		$dl += -110 * sin($l + $ls);
		$dl += 148 * sin($l - $ls);
		$dl += -55 * sin($f2 - $d2);

		$s = $f + ($dl + 412 * $sinf2 + 541 * $sinls) / $arc;
		$h = $f - $d2;
		$n = -526 * sin($h);
		$n += 44 * sin($l + $h);
		$n += -31 * sin(-$l + $h);
		$n += -23 * sin($ls + $h);
		$n += 11 * sin(-$ls + $h);
		$n += -25 * sin(-$l2 + $f);
		$n += 21 * sin(-$l + $f);

		$L_moon = $p2 * self::frac($lo + $dl / 1296000);
		$B_moon = (18520.0 * sin($s) + $n) / $arc;

		$cb = cos($B_moon);
		$x = $cb * cos($L_moon);
		$v = $cb * sin($L_moon);
		$w = sin($B_moon);
		$y = $coseps * $v - $sineps * $w;
		$z = $sineps * $v + $coseps * $w;
		$rho = sqrt(1 - $z * $z);
		$dec = (360 / $p2) * atan($z / $rho);
		$ra = (48 / $p2) * atan($y / ($x + $rho));
		$ra = $ra < 0 ? $ra + 24 : $ra;

		return array($dec, $ra);
	}
	private function frac($x)
	{
		$x -= (int)$x;
		return $x < 0 ? $x + 1 : $x;
	}
	private function modifiedJulianDate($month, $day, $year)
	{

		if ($month <= 2) {
			$month += 12;
			$year--;
		}

		$a = 10000 * $year + 100 * $month + $day;
		$b = 0;
		if ($a <= 15821004.1) {
			$b = -2 * (int)(($year + 4716) / 4) - 1179;
		} else {
			$b = (int)($year / 400) - (int)($year / 100) + (int)($year / 4);
		}

		$a = 365 * $year - 679004;
		return $a + $b + (int)(30.6001 * ($month + 1)) + $day;
	}
	private function convertTime($hours)
	{

		$hrs = (int)($hours * 60 + 0.5) / 60.0;
		$h = (int)($hrs);
		$m = (int)(60 * ($hrs - $h) + 0.5);
		return array('hrs' => $h, 'min' => $m);
	}

	///////
	///////
	///////
	///////

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

	public function calcMoonPhase()
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
	public function getAllMoonPhases()
	{
		return $this->allMoonPhases;
	} // END function getAllMoonPhases() {
	public function getBaseFullMoonDate()
	{
		$this->someFullMoonDate = strtotime("March 2 2022 20:38 Europe/Moscow");
		return $this->someFullMoonDate;
	} // END function getBaseFullMoonDate() {
	public function getDateAsTimeStamp()
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
	public function getPercentOfIllumination()
	{
		// from http://www.lunaroutreach.org/cgi-src/qpom/qpom.c
		// C version: // return (1.0 - cos((2.0 * M_PI * phase) / (LPERIOD/ 86400.0))) / 2.0;
		$percentage = 1 - ((1.0 + cos(2 * M_PI * $this->getPositionInCycle())) / 2.0);
		$percentage *= 100;
		$percentage = round($percentage, 1) . '%';
		return $percentage;
	} // END function getPercentOfIllumination()
	public function getPeriodInSeconds()
	{
		//if ($this->periodInSeconds > -1) return $this->periodInSeconds; // in case it was cached
		$this->periodInSeconds = $this->periodInDays * MP_DAY_IN_SECONDS;
		return $this->periodInSeconds;
	} // END function getPeriodInSeconds() {
	public function getPhaseID()
	{
		$this->calcMoonPhase();
		return $this->moonPhaseIDforDate;
	} // EMD function getPhaseID() {
	public function getPhaseName()
	{
		$this->calcMoonPhase();
		return $this->moonPhaseNameForDate; // get name for this current date
	} // END function getPhaseName() {
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
	public function calcMoonDay()
	{
		$start = $this->getDaysUntilPreviousNewMoon();
		//$day = $md->calculateMoonTimes();
		$position = $this->getPositionInCycle();
		$day = round($position * $this->periodInDays, 3);
		return $day;
	}
	public function setDate($timeStamp = -1)
	{
		if ($timeStamp == '' or $timeStamp == -1) {
			$timeStamp = time();
		};
		$this->dateAsTimeStamp = $timeStamp;
		$this->calcMoonPhase();
	} // END function setDate($timeStamp) {
	public function getDaysUntilPreviousNewMoon()
	{
		$position = $this->getPositionInCycle();
		if (isset($_POST['date'])) {
			$newMoon = date("M d Y H:i:s", (strtotime($_POST['date']) - ($this->periodInSeconds - ((1 - $position) * $this->periodInSeconds))));
		} else {
			$newMoon = date("M d Y H:i:s", (time() - ($this->periodInSeconds - ((1 - $position) * $this->periodInSeconds))));
		}
		return $newMoon;
	} // ENDfunction getDaysUntilNextNewMoon() {
	public function getDaysUntilNextNewMoon()
	{
		$position = $this->getPositionInCycle();
		if (isset($_POST['date'])) {
			$newMoon = date("M d Y H:i:s", ((1 - $position) * $this->periodInSeconds) + strtotime($_POST['date']));
		} else {
			$newMoon = date("M d Y H:i:s", ((1 - $position) * $this->periodInSeconds) + time());
		}
		return $newMoon;
	} // ENDfunction getDaysUntilNextNewMoon() {

	public function getDaysUntilNextFirstQuarterMoon()
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
	public function getDaysUntilNextFullMoon()
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
	public function getDaysUntilNextLastQuarterMoon()
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
	public function getUpcomingWeekArray($newStartingDateAsTimeStamp = -1)
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
	}
}

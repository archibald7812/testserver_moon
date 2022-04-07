<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="/css/style.css">
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,700;1,400&display=swap" rel="stylesheet">
	<title>Лунный календарь</title>




</head>

<body>
	<main class="wrapper">
		<header class="header">
			<div class="header__row">
				<h1><a href="">Лунный гороскоп</a></h1>
				<article>Живи на одной волне с луной!!</article>
			</div>
		</header>
		<div class="content">
			<div class="calc">
				<div class="container">
					<div class="calc__row">
						<div class="calc__js">
							<script src="/scripts/main.js"></script>

						</div>
						<div class="calc__date">
							<form action="" method="POST">
								<input type="datetime-local" name="date">
								<input type="submit" value="Подтвердить!">
							</form>
							<form action="" method="POST">
								<input type="submit" name="now" value="Сейчас!">
							</form>
						</div>
						<div class="calc__today">
							<?php
							include 'source/MoonDay.php';
							if (isset($_POST['date']) || isset($_POST['now'])) {
								if (isset($_POST['now'])) {
									$date = time();
									$date = date("m-d-Y", $date);
									$date = explode("-", $date);
								} else {
									$date = strtotime($_POST['date']);
									$date = date("m-d-Y", $date);
									$date = explode("-", $date);
								}
								$md = new Moon();
								//$mp = new moonPhase();
								$md1 = $md->calculateMoonTimes($date[0], (int)$date[1], (int) $date[2],	55.56263,	37.88581);
								$md2 = $md->calculateMoonTimes('03', '2', '2022',	50.98187,	0.19904);
								$md3 = $md->calculateMoonTimes('03', '2', '2022',	36.91559,	140.05343);
								$md4 = $md->calculateMoonTimes('03', '2', '2022',	-28.05259,	152.06543);
								$md5 = $md->calculateMoonTimes('03', '2', '2022',	-33.89474,	19.86328);
								$md6 = $md->calculateMoonTimes('03', '2', '2022',	24.09844,	56.54629);
								$md7 = $md->calculateMoonTimes('03', '2', '2022',	40.71455,	-74.00714);
								$md71 = $md->calculateMoonTimes('08', '2', '2022',	40.71455,	-74.00714);
								$md8 = $md->calculateMoonTimes('03', '2', '2022',	-17.97356,	-39.79604);
								//print_r($mp->moonPhaseNameForDate);
								//echo "<pre> Moscow ", date("H:i:s", $md1->moonrise) . " " . date("H:i:s", $md1->moonset) . "  |||| 07:52:33 17:28:14 " . "</pre>";
								//echo "<pre> London ", date("H:i:s", $md2->moonrise) . " " . date("H:i:s", $md2->moonset) . "  |||| 07:09:17 17:19:25" . "</pre>";
								//echo "<pre> Tokio  ", date("H:i:s", $md3->moonrise) . " " . date("H:i:s", $md3->moonset) . "  |||| 06:07:57 16:57:16" . "</pre>";
								//echo "<pre> Sidney ", date("H:i:s", $md4->moonrise) . " " . date("H:i:s", $md4->moonset) . "  |||| 04:48:18 18:21:57" . "</pre>";
								//echo "<pre> SAR   ", date("H:i:s", $md5->moonrise) . " " . date("H:i:s", $md5->moonset) . "  |||| 05:52:19 19:30:55" . "</pre>";
								//echo "<pre> UAE   ", date("H:i:s", $md6->moonrise) . " " . date("H:i:s", $md6->moonset) . "  |||| 06:30:29 18:03:35" . "</pre>";
								//echo "<pre> NY    ", date("H:i:s", $md7->moonrise) . " " . date("H:i:s", $md7->moonset) . "  |||| 06:50:33 17:51:52" . "</pre>";
								//echo "<pre> NY    ", date("H:i:s", $md71->moonrise) . " " . date("H:i:s", $md71->moonset) . "  |||| 10:34:33 22:51:12" . "</pre>";
								//echo "<pre> RIO   ", date("H:i:s", $md8->moonrise) . " " . date("H:i:s", $md8->moonset) . "  |||| 05:22:30 18:19:41" . "</pre>";
								$Marchs = array(
									$March1 = strtotime("March 2 2022 20:38 Europe/Moscow"),
									$March2 = strtotime("March 3 2022 08:10 Europe/Moscow"),
									$March3 = strtotime("March 4 2022 08:19 Europe/Moscow"),
									$March4 = strtotime("March 5 2022 08:29 Europe/Moscow"),
									$March5 = strtotime("March 6 2022 08:37 Europe/Moscow"),
									$March6 = strtotime("March 7 2022 08:47 Europe/Moscow"),
									$March7 = strtotime("March 8 2022 08:59 Europe/Moscow"),
									$March8 = strtotime("March 9 2022 09:15 Europe/Moscow"),
									$March9 = strtotime("March 10 2022 09:37 Europe/Moscow"),
									$March10 = strtotime("March 11 2022 10:10 Europe/Moscow"),
									$March11 = strtotime("March 12 2022 10:56 Europe/Moscow"),
									$March12 = strtotime("March 13 2022 11:58 Europe/Moscow"),
									$March13 = strtotime("March 14 2022 13:11 Europe/Moscow"),
									$March14 = strtotime("March 15 2022 14:30 Europe/Moscow"),
									$March15 = strtotime("March 16 2022 15:52 Europe/Moscow"),
									$March16 = strtotime("March 17 2022 17:23 Europe/Moscow"),
									$March17 = strtotime("March 18 2022 18:39 Europe/Moscow"),
									$March18 = strtotime("March 19 2022 20:05 Europe/Moscow"),
									$March19 = strtotime("March 20 2022 21:24 Europe/Moscow"),
									$March20 = strtotime("March 21 2022 23:06 Europe/Moscow"),
									$March21 = strtotime("March 23 2022 00:41 Europe/Moscow"),
									$March22 = strtotime("March 24 2022 02:15 Europe/Moscow"),
									$March23 = strtotime("March 25 2022 03:38 Europe/Moscow"),
									$March24 = strtotime("March 26 2022 04:38 Europe/Moscow"),
									$March25 = strtotime("March 27 2022 05:14 Europe/Moscow"),
									$March26 = strtotime("March 28 2022 05:47 Europe/Moscow"),
									$March27 = strtotime("March 29 2022 06:04 Europe/Moscow"),
									$March28 = strtotime("March 30 2022 06:17 Europe/Moscow"),
									$March29 = strtotime("March 31 2022 06:27 Europe/Moscow"),
									$March30 = strtotime("April 01 2022 06:36 Europe/Moscow"),
									$April1 = strtotime("April 01 2022 09:27 Europe/Moscow"),
								);
								$Aprils = array(
									$April1 = strtotime("April 01 2022 09:27 Europe/Moscow"),
									$April2 = strtotime("April 02 2022 06:45 Europe/Moscow"),
									$April3 = strtotime("April 03 2022 06:55 Europe/Moscow"),
									$April4 = strtotime("April 04 2022 07:06 Europe/Moscow"),
									$April5 = strtotime("April 05 2022 07:19 Europe/Moscow"),
									$April6 = strtotime("April 06 2022 07:40 Europe/Moscow"),
									$April7 = strtotime("April 07 2022 08:08 Europe/Moscow"),
									$April8 = strtotime("April 08 2022 04:06 Europe/Moscow"),
									$April9 = strtotime("April 09 2022 09:45 Europe/Moscow"),
									$April10 = strtotime("April 10 2022 10:54 Europe/Moscow"),
									$April11 = strtotime("April 11 2022 12:12 Europe/Moscow"),
									$April12 = strtotime("April 12 2022 13:33 Europe/Moscow"),
									$April13 = strtotime("April 13 2022 14:55 Europe/Moscow"),
									$April14 = strtotime("April 14 2022 16:18 Europe/Moscow"),
									$April15 = strtotime("April 15 2022 17:43 Europe/Moscow"),
									$April16 = strtotime("April 16 2022 19:10 Europe/Moscow"),
									$April17 = strtotime("April 17 2022 20:40 Europe/Moscow"),
									$April18 = strtotime("April 18 2022 22:15 Europe/Moscow"),
									$April19 = strtotime("April 19 2022 23:49 Europe/Moscow"),
									$April20 = strtotime("April 21 2022 01:20 Europe/Moscow"),
									$April21 = strtotime("April 22 2022 02:30 Europe/Moscow"),
									$April22 = strtotime("April 23 2022 03:20 Europe/Moscow"),
									$April23 = strtotime("April 24 2022 03:50 Europe/Moscow"),
									$April24 = strtotime("April 25 2022 04:10 Europe/Moscow"),
									$April25 = strtotime("April 26 2022 04:24 Europe/Moscow"),
									$April26 = strtotime("April 27 2022 04:35 Europe/Moscow"),
									$April27 = strtotime("April 28 2022 04:45 Europe/Moscow"),
									$April28 = strtotime("April 29 2022 04:54 Europe/Moscow"),
									$April29 = strtotime("April 30 2022 05:03 Europe/Moscow"),
									$April30 = strtotime("April 30 2022 23:30 Europe/Moscow"),
									$May1 = strtotime("May 01 2022 05:13 Europe/Moscow"),
								);
								$new1 = strtotime("April 01 2022 09:24 Europe/Moscow");
								$q1 = strtotime("April 09 2022 09:47 Europe/Moscow");
								$full = strtotime("April 16 2022 21:55 Europe/Moscow");
								$full = strtotime("April 23 2022 14:56 Europe/Moscow");
								$new2 = strtotime("April 30 2022 23:28 Europe/Moscow");

								for ($i = 0; $i < count($Marchs) - 1; $i++) {
									$k = $i + 1;
									//echo "<p> Март день $k: " . round(($Marchs[$i + 1] - $Marchs[$i]) / (24 * 60 * 60), 2) . "___________Апрель день $k: " . round(($Aprils[$i + 1] - $Aprils[$i]) / (24 * 60 * 60), 2) . "</p>";
								}
								echo "<p>Сейчас: ", date("M d Y H:i", $md->getDateAsTimeStamp()), ":</p>";
								echo "<p>Стадия взросления луны: ", round($md->getPositionInCycle(), 2) * 100, "%</p>";
								echo "<p>Лунный день: ", $md->calcMoonDay(), "</p>";
								echo "<p>Лунная фаза: ", $md->getPhaseName(), "</p>";
								echo "<p>Восход луны ", date("H:i", $md1->moonrise), "</p>";
								echo "<p>Заход луны ", date("H:i", $md1->moonset), "</p>";
								echo "<p>Процент освещенности луны: ", $md->getPercentOfIllumination(), "</p>";
								echo "<p>Последнее новолуние: ", $md->getDaysUntilPreviousNewMoon(), "</p>";
								echo "<p>Ближайшая первая четверть луны: ", $md->getDaysUntilNextFirstQuarterMoon(), "</p>";
								echo "<p>Ближайшее полнолуние: ", $md->getDaysUntilNextFullMoon(), "</p>";
								echo "<p>Ближайшая третья четверть луны: ", $md->getDaysUntilNextLastQuarterMoon(), "</p>";
								echo "<p>Ближайшее новолуние: ", $md->getDaysUntilNextNewMoon(), "</p>";
								//echo "<p>Лунные фазы на неделю: ", "</p>";
								//$UpcomingWeekArray = $mp->getUpcomingWeekArray();
								//foreach ($UpcomingWeekArray as $timeStamp => $phaseID)
								//	echo "&nbsp;&nbsp;", date('l', (int)$timeStamp), ": ", $mp->getPhaseName($phaseID), "<br />\n";
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<footer class="footer">
			<div class="footer__row">
				<div class="footer__text">Люблю Полинку!!!</div>
			</div>
		</footer>
	</main>
</body>

</html>
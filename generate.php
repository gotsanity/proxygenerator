<?php

$supported_games = array(
	"doomtown" => "http://dtdb.co/api/cards/",
	"netrunner" => "http://netrunnerdb.com/api/cards/"
);

$dir = dirname(__FILE__).'/'; 

foreach ($supported_games as $game => $address) {
	if(date("Y-m-d") < date("Y-m-d", filemtime($dir."assets/".$game.".txt"))) {
		// update card assets
		set_time_limit(0);
		$fp = fopen ($dir . 'assets/'.$game.'.txt', 'w+');//This is the file where we save the    information
		$ch = curl_init($address);
		curl_setopt($ch, CURLOPT_TIMEOUT, 50);
		curl_setopt($ch, CURLOPT_FILE, $fp); // write curl response to file
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_exec($ch); // get curl response
		curl_close($ch); 
		fclose($fp); 
	}
}

if ($_POST['game']) {
	$game = $_POST['game'];
	if ($_POST['game'] === "doomtown") {
		$dburl = 'http://dtdb.co';
	} else {
		$dburl = 'http://www.netrunnerdb.com/bundles/netrunnerdbcards/images/cards/en/';
	}
} else {
	$game = 'netrunner';
	$dburl = 'http://www.netrunnerdb.com/bundles/netrunnerdbcards/images/cards/en/';
}

$lines_coded = file_get_contents($dir."assets/".$game.".txt");
$lines = json_decode($lines_coded);
?>

<html>
<head>
	<link href="assets/print.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php

/* code for proxy cards */
foreach ($_POST as $k => $v) {
	foreach ($lines as $lk => $lv) {
		if ($lv->code == $k) {
			while ($v > 0) {
				
				print "<div class='card'>";
				print "<div class='$lv->type_code'>";
				print "<div class='$lv->faction_code'>";
				print "<div class='title'>$lv->title</div>";
				print "<div class='cardart'></div>";
				print "<div class='type'>$lv->subtype</div>";
				print "<div class='cost'>$lv->advancementcost$lv->cost</div>";
				print "<div class='agendapoints'>$lv->agendapoints</div>";
				print "<div class='text'>$lv->text<div class='flavor'>$lv->flavor</div></div>";
				print "</div></div></div>";

				$v--;
				//print "<pre>";
				//print_r($lv);
				//print "</pre>";
			}
		}
	}
}
/* end proxy code */

/*    code for images  */
foreach ($_POST as $k => $v) {
	foreach ($lines as $lk => $lv) {
		if ($lv->code == $k) {
			while ($v > 0) {
				echo "<img src='" . $dburl . $lv->code . ".png' />";
				$v--;
			}
		}
	}
}
/* end image code */

?>

</body>
</html>

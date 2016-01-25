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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="assets/print.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php

/*  code for placeholders */
foreach ($_POST as $k => $v) {
	foreach ($lines as $lk => $lv) {
		if ($lv->code == $k) {
			while ($v > 0) {
				printCard($lv);
				$v--;
			}
		}
	}
}

/* end code for placeholders */

/*  code for placeholders full list*/

if (!$_POST){
	foreach ($lines as $lk => $lv) {
		printCard($lv);
		$i++;
		if (($i % 9) == 0) {
			print "<div class='pagebreak'></div>";
		} 
	}
}
/* end code for placeholders */

function printCard($lv) {
	print "<div class='placeholder'>";
	print "<div class='row'><div class='set'>$lv->setname</div></div>";
	print "<div class='row'><div class='title'>$lv->title</div></div>";
	print "<div class='row'><div class='faction'>$lv->faction</div></div>";
	print "<div class='row'><div class='number'>Card Number: $lv->number</div></div>";
	print "</div>";
}

?>

</body>
</html>

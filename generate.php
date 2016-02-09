<?php

$supported_games = array(
	"doomtown" => "http://dtdb.co/api/cards/",
	"netrunner" => "http://netrunnerdb.com/api/cards/"
);

$_POST['proxytype'] = "basic";

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
	<meta charset="utf-8">
	<?php
		if ($_POST['proxytype']) {
			print '<link href="assets/basic.css" rel="stylesheet" type="text/css">';
		} else {
			print '<link href="assets/advanced.css" rel="stylesheet" type="text/css">';
		}
	?>
	<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="assets/icon-style.css" rel="stylesheet" type="text/css">
	<link href="assets/print.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php

/* code for proxy cards */
$numcards = 0;
print "<div class='pagebreak'>";
foreach ($_POST as $k => $v) {
	foreach ($lines as $lk => $lv) {
		if ($lv->code == $k) {
			while ($v > 0) {
				$numcards++;
				printCard($_POST, $lv, $numcards);
				if (($numcards % 9) == 0) {
					$numcards = 0;
					print "</div><div class='pagebreak'>";
				}				

				$v--;
				//print "<pre>";
				//print_r($lv);
				//print "</pre>";
			}
		}
	}
}
/* end proxy code */

function printCard($post, $lv, $pos) {
	if ($post['proxytype']) {
		print "<div class='pos-$pos'><div class='card $post[proxytype]'>";
	} else {
		print "<div class='card pos-$pos'>";
	}
	print "<div class='$lv->type_code'>";
	print "<div class='$lv->faction_code'>";
	print "<div class='titlebar'>";
	print "<div class='title'>$lv->title</div>";
	print "<div class='cardart'></div>";
	print "<div class='cost img-circle'>$lv->advancementcost$lv->cost</div>";
	print "</div>";
	print "<div class='centerart'>";
	$faction = iconify("[".$lv->faction_code."]");
	print "<div class='faction'>$faction</div>";
	print "<div class='agendapoints'>";
	if ($lv->agendapoints) {
		print "<span class='icon-agenda'></span>$lv->agendapoints";
	}
	print "</div></div>";
	$text = iconify($lv->text);
	print "<div class='textbox'>";
	print "<div class='type'>$lv->type";
	if ($lv->subtype) {
		print " - $lv->subtype";
	}
	print "</div>";
	print "<div class='text'>$text</div>";
	print "<div class='flavor'>$lv->flavor</div>";
	print "</div>";
	print "<div class='influence'>$faction";
	for ($i = 0; $i < $lv->factioncost; $i++) {
		print "<span class='icon-influence'></span>";
	}
	print "</div>";
	print "</div></div></div></div>";
}

function iconify($text) {

	$array_from_to = array (
		'[Click]' => '<span class="icon-click"></span>',
		'[Credits]' => '<span class="icon-credit"></span>',
		'[jinteki]' => '<span class="icon-jinteki"></span>',
		'[nbn]' => '<span class="icon-nbn"></span>',
		'[shaper]' => '<span class="icon-shaper-smooth"></span>',
		'[Recurring Credits]' => '<span class="icon-recurring-credit"></span>',
		'[Link]' => '<span class="icon-link"></span>',
		'[Trash]' => '<span class="icon-trash"></span>',
		'[Memory Unit]' => '<span class="icon-mu"></span>',
		'[Subroutine]' => '<span class="icon-subroutine"></span>',
		'[anarch]' => '<span class="icon-anarch"></span>',
		'[criminal]' => '<span class="icon-criminal"></span>',
		'[haas-bioroid]' => '<span class="icon-haas-bioroid"></span>',
		'[weyland-consortium]' => '<span class="icon-weyland-consortium"></span>',
		'[Rez]' => '<span class="icon-rez"></span>',
		'[apex]' => '<span class="icon-missing">Apex</span>',
		'[sunny]' => '<span class="">Sunny</span>',
		'[adam]' => '<span class="">Adam</span>',
		'[neutral]' => '<span class=""></span>'
	);

	$output = nl2br(str_replace(array_keys($array_from_to), $array_from_to, $text));
	return $output;
}
/*    code for images  
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

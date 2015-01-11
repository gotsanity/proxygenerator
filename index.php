<?php 

$supported_games = array(
	"doomtown" => "http://dtdb.co/api/cards/",
	"netrunner" => "http://netrunnerdb.com/api/cards/"
);

$dir = dirname(__FILE__).'/'; 

foreach ($supported_games as $game => $address) {
	if(date("Y-m-d") > date("Y-m-d", filemtime($dir."assets/".$game.".txt"))) {
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

if ($_GET['game']) {
	$game = $_GET['game'];
} else {
	$game = 'netrunner';
}

$lines_coded = file_get_contents($dir."assets/".$game.".txt");
$lines = json_decode($lines_coded);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
	<link href="assets/stylesheet.css" rel="stylesheet" type="text/css">
	<title>Project Mulligan LCG/ECG Proxy Generator</title>
	
	<script>
  $(function() {
    var dialog, form;
 

    function addCard() {
      var valid = true;

      if ( valid ) {

        dialog.dialog( "close" );
      }
      return valid;
    }
 
    dialog = $( "#dialog-form" ).dialog({
      autoOpen: false,
      modal: true,
      position: {my: "center", at: "top", of: window},
      buttons: {
        "0": addCard,
        "1": addCard,
        "2": addCard,
        "3": addCard,
      }
    });
 
    $( ".card-title" ).button().on( "click", function() {
      dialog.dialog( "open" );
    });
  });
  
	$('#toast').on('click', function () {
    $(this).button('toggle'),
    $('#dead').button('toggle')
  });
 
	</script>
	
</head>

<body>
<h1>Project Mulligan LCG/ECG Proxy Generator</h1>

<div class="navigation">
<?php
foreach ($supported_games as $g => $site) {
	echo "<div class='menu-item";
	if ($_GET['game'] == $g) {
		echo " selected";
	}
	echo "'><a href='?game=$g'>$g</a></div>";
}
?>

</div>
<hr />

<div id="dialog-form" title="Add Card">
  <p class="validateTips">All form fields are required.</p>
 
</div>

<table>
<?php

switch ($_GET['sort']) {
	case "title":
		usort($lines, "titlesort");
		break;
	case "set":
		usort($lines, "setsort");
		break;
	default:
		usort($lines, "titlesort");
}

echo "<tr><th><a href='?game=$game&sort=title'>Title</a></th><th><a href='?game=$game&sort=set'>Set</a></th><th>Action</th></tr>";

foreach ($lines as $key => $card) {
	if ($_GET['game'] == "doomtown") {
		$set = $card->pack;
	} else {
		$set = $card->setname;
	}
	if ($card->setname == "Alternates") {

	} else {
		echo "<tr class='card-container' data-index='$card->code'><td class='card-title'><a href='$card->url' id='create-user' target='_blank'>$card->title</a></td><td>$set</td><td>";
		echo "<div class='btn-group' data-toggle='buttons'>";
    echo "<label class='btn btn-xs btn-default'><input type='radio' name='qty-$card->code' value='0'>0</label>";
    echo "<label class='btn btn-xs btn-default'><input type='radio' name='qty-$card->code' value='1'>1</label>";
    echo "<label class='btn btn-xs btn-default'><input type='radio' name='qty-$card->code' value='2'>2</label>";
    echo "<label class='btn btn-xs btn-default'><input type='radio' name='qty-$card->code' value='3'>3</label>";
    if ($game == "doomtown") {
	    echo "<label class='btn btn-xs btn-default'><input type='radio' name='qty-$card->code' value='4'>4</label>";
    }
    echo '</div></td></tr>';
	}
}

?>
</table>

</body>

<pre>
<?php
print_r($lines);

// Functions go here

function titlesort($a, $b)
{
    return strcmp($a->title, $b->title);
}

function setsort($a, $b)
{
    return strcmp($a->cyclenumber, $b->cyclenumber);
}

?>
</pre>

</html>

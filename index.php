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
<html>

<head>
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
      height: 300,
      width: 350,
      modal: true,
      buttons: {
        "Create an account": addCard,
        Cancel: function() {
          dialog.dialog( "close" );
        }
      },
      close: function() {
        form[ 0 ].reset();
        allFields.removeClass( "ui-state-error" );
      }
    });
 
    form = dialog.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
      addCard();
    });
 
    $( ".create-user" ).button().on( "click", function() {
      dialog.dialog( "open" );
    });
  });
  
  $(function() {
    $( "#radio" ).buttonset();
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
		echo "<tr><td class='card-title'><a href='$card->url' id='create-user' target='_blank'>$card->title</a></td><td>$set</td><td>";
		echo '<form><div class="radio" id="radio">';
    echo "<input type='radio' id='radio0' name='radio' checked='checked'><label for='radio0'>0</label>";
    echo "<input type='radio' id='radio1' name='radio'><label for='radio1'>1</label>";
    echo "<input type='radio' id='radio2' name='radio'><label for='radio2'>2</label>";
    echo "<input type='radio' id='radio3' name='radio'><label for='radio3'>3</label>";
    if ($game == "doomtown") {
	    echo "<input type='radio' id='radio4' name='radio'><label for='radio4'>4</label>";
    }
    echo '</div></form></td></tr>';
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

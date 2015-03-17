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
	<script src="assets/jquery.min.js"></script>
	<link rel="stylesheet" href="assets/jquery/jquery-ui.css" />
	<link rel="stylesheet" href="assets/jquery/jquery-ui.theme.css" />
	<script src="assets/jquery/jquery-ui.min.js"></script>
	<link href="assets/stylesheet.css" rel="stylesheet" type="text/css">
	<title>Project Mulligan LCG/ECG Proxy Generator</title>
	
	<script>
  $(function() {
    var dialog, form, qty, card, cardObjects, cardid;
    cardObjects = <?php echo $lines_coded; ?>;


    function removeCard(thisCard) {
      var valid = true;
			var cardId = thisCard[0].code;
      if ( valid ) {
        dialog.dialog( "close" );
				$('.' + cardId).remove();
				$('#card-button-' + cardId)
					.removeAttr('disabled')
					.text("Add Card");
        console.log("removed " + cardId);
      }
      return valid;
    }

		function findCard(cardId){
				return $.grep(cardObjects, function(n, i){
				  return n.code == cardId;
				});
		};

    function addCard1() {
      var valid = true;

      if ( valid ) {
				cardid = $(this).data('index');
				var thisCard = findCard(cardid);
        dialog.dialog( "close" );
				appendCard(thisCard, 1);
      }
      return valid;
    }

    function addCard2() {
      var valid = true;

      if ( valid ) {
				cardid = $(this).data('index');
				var thisCard = findCard(cardid);
        dialog.dialog( "close" );
				appendCard(thisCard, 2);
      }
      return valid;
    }
    function addCard3() {
      var valid = true;

      if ( valid ) {
				cardid = $(this).data('index');
				var thisCard = findCard(cardid);
        dialog.dialog( "close" );
				appendCard(thisCard, 3);
      }
      return valid;
    }
    function addCard4() {
      var valid = true;

      if ( valid ) {
				cardid = $(this).data('index');
				var thisCard = findCard(cardid);
        dialog.dialog( "close" );
				appendCard(thisCard, 4);
      }
      return valid;
    }

		function appendCard(thisCard, qty) {
			$("#card-list").find('tbody')
				.append($('<tr>')
						.attr('data-index', cardid)
						.addClass(cardid)
						.append($('<td>')
			        .text(thisCard[0].title)
						)
						.append($('<td>')
			        .text(qty)
						)
						.append($('<td>')
			        .append($('<div>')
								.attr('data-index', cardid)
								.addClass('modify-card btn btn-xs btn-default')
								.text('Modify')
							)
			        .append($('<div>')
								.attr('data-index', cardid)
								.addClass('remove-card btn btn-xs btn-default')
								.text('Remove')
							).on( "click", function() {
								removeCard(thisCard);
					    })
						)
				);
		}

    dialog = $( "#dialog-form" ).dialog({
      autoOpen: false,
      modal: true,
      position: {my: "center", at: "center", of: window},
      buttons: {
        "0": removeCard,
        "1": addCard1,
        "2": addCard2,
        "3": addCard3,
      }
    });
 
    $( ".add-card" ).on( "click", function() {
			$(this).attr('disabled', true);
			$(this).text('Added');
			dialog.data('index', $(this).attr('data-index'));
      dialog.dialog( "open" );
    });
    
  });
 
	</script>
	
</head>

<body>
<h1>Project Mulligan LCG/ECG Proxy Generator</h1>

<div class="navigation">
<?php
foreach ($supported_games as $g => $site) {
	echo "<div class='btn-group btn-group-sm' role='group'>";
	if ($_GET['game'] == $g) {
		echo "<a href='/?game=$g' class='btn btn-default active' role='button'>$g</a>";
	} else {
		echo "<a href='/?game=$g' class='btn btn-default' role='button'>$g</a>";
	}
	echo "</div>";
}
?>

</div>
<hr />

<div class="container-fluid">
	<div class="row">

<div class="col-md-6">
	<table class="table table-striped table-bordered">
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
		echo "<tr class='card-container' data-index='$card->code'>\n<td class='card-title'>\n$card->title</td>\n<td>$set</td>\n<td>\n";
		echo "<div id='card-button-$card->code' data-index='$card->code' class='add-card btn btn-xs btn-default'>Add Card</div>\n";
    echo "</td>\n</tr>\n";
	}
}

?>
	</table>
</div>

<div class="col-md-6">
	<table id="card-list" class="table table-striped table-bordered">
		<tr>
			<th>Card</th>
			<th>Quantity</th>
			<th>Action</th>
		<tr>
			
		</tr>
	</table>
</div>

</div>
</div>

<div id="dialog-form" title="Add Card">
  <p class="validateTips">All form fields are required.</p>
 
</div>
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

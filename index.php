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
	<script src="assets/jquery.min.js"></script>
	<link rel="stylesheet" href="assets/jquery/jquery-ui.css" />
	<link rel="stylesheet" href="assets/jquery/jquery-ui.theme.css" />
	<script src="assets/jquery/jquery-ui.min.js"></script>
	<link href="assets/stylesheet.css" rel="stylesheet" type="text/css">
	<title>Project Mulligan LCG/ECG Proxy Generator</title>
	
	<script>

  $(function() {
    var dialog, form, qty, card, cardObjects, cardid;
	var decklist = ["toast", "toast2"];
    cardObjects = <?php echo $lines_coded; ?>;
	console.log(decklist);

	function appendCard(thisCard, qty) {
		var cardToPush = { };
		cardToPush.card = thisCard[0].code;
		cardToPush.qty = qty;
		decklist.push(cardToPush);
		$("#card-list").find('tbody')
			.append($('<tr>')
					.attr('data-index', thisCard[0].code)
					.addClass(cardid)
					.append($('<td>')
			    .text(thisCard[0].title)
					)
					.append($('<td>')
			    .text(qty)
					)
					.append($('<td>')
			    .append($('<div>')
							.attr('data-index', thisCard[0].code)
							.addClass('btn btn-xs btn-default')
							.text('Modify')
							.on( "click", function() {
								modifyCard(thisCard);
						  })
						)
			    .append($('<div>')
							.attr('data-index', thisCard[0].code)
							.addClass('btn btn-xs btn-default')
							.text('Remove')
							.click(function() {
								removeCard(thisCard);
						  })
						)
					)
			);
	}

    function removeCard(thisCard) {
      var valid = true;
			var cardId = thisCard[0].code;
      if ( valid ) {
        dialog.dialog( "close" );
				$('.' + cardId).remove();
				$('#card-button-' + cardId)
					.removeAttr('disabled')
					.text("Add Card");

				for (var key in decklist)
				{
					if (decklist[key].card === cardId)
					{
						console.log("deleting " + cardId + " at key " + key);
						delete decklist[key];
					}
				}

        console.log("removed " + cardId);
				console.log(decklist);
      }
      return valid;
    }

		function modifyCard(thisCard) {
			var valid = true;
			if ( valid ) {
				removeCard(thisCard);
				$('#card-button-' + thisCard[0].code).attr('disabled', true);
				$('#card-button-' + thisCard[0].code).text('Added');
				console.log("modifying " + thisCard[0].code);
				dialog.data('index', thisCard[0].code);
	      dialog.dialog( "open" );
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

		function getParameterByName(name) {
				name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
				var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
				    results = regex.exec(location.search);
				return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
		}

		function postData(decklist, url)
		{

		var postData = decklist;
    var postFormStr = "<form method='POST' action='" + url + "'>\n";

    for (var key in postData)
    {
			console.log(postData[key]);
      postFormStr += "<input type='hidden' name='" + postData[key].card + "' value='" + postData[key].qty + "'></input>";
    }

		if (getParameterByName('game') === "doomtown") {
    	postFormStr += "<input type='hidden' name='game' value='doomtown'></input>";
		}
    postFormStr += "</form>";

    var formElement = $(postFormStr);

    $('body').append(formElement);
    $(formElement).submit();
		}

		if (getParameterByName('game') === "doomtown") {
				dialog = $( "#dialog-form" ).dialog({
				  autoOpen: false,
				  modal: true,
				  position: {my: "center", at: "center", of: window},
				  buttons: {
				    "0": removeCard,
				    "1": addCard1,
				    "2": addCard2,
				    "3": addCard3,
				    "4": addCard4,
				  }
				});
		} else {
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

		}
 
    $( ".add-card" ).on( "click", function() {
			$(this).attr('disabled', true);
			$(this).text('Added');
			dialog.data('index', $(this).attr('data-index'));
      dialog.dialog( "open" );
    });

	$( "#decklist" ).on( "click", function() {
		console.log("Loading decklist");
		decktext = $( "#dialog-decklist" ).dialog({
			autoOpen: false,
			modal: true,
			width: "550",
			height: "700",
			position: {my: "center", at: "center", of: window},
			buttons: {
				"Submit": function() {
					text = $("#deck-text").val();
					deckid = $("#deck-id").val();
				    $(this).dialog("close");
					loadDecklist(text, deckid);
			}
	    }
	  });
		decktext.dialog( "open" );
		
	});

	function loadDecklist(deckText, deckId) {
		var line = deckText.split('\n');
		console.log("displaying lines of text");
		var list = [];
		for (var i = 0; i < line.length; ++i) {
			if(line[i].search(/\dx/) > -1) {
				var stripped = line[i].replace(/[\u2022]/g, "");
				var card = [line[i].charAt(0), stripped.substr(3).trim()];
				list.push(card);
			}
		}
		console.log(decklist);
		for (var i = 0; i < list.length; ++i) {
			var thisCard = findCardByTitle(list[i][1]);
			appendCard(thisCard, list[i][0]);
		}
	}

	function findCardByTitle(title) {
		return $.grep(cardObjects, function(n, i){
		  return n.title == title;
		});
	}

    $( "#generate" ).on( "click", function() {
			console.log(JSON.stringify(decklist));

			postData(decklist, "generate.php");
    });  

    $( "#placeholder-list" ).on( "click", function() {
			console.log(JSON.stringify(decklist));

			postData(decklist, "placeholder.php");
    });    

    $( "#placeholder-sets" ).on( "click", function() {
			console.log(JSON.stringify(decklist));

			postData(null, "placeholder.php");
    });


// Sort functions

    $( ".sort" ).on( "click", function() {
			toggleRow($(this).attr('data-index'));
			$(this).toggleClass("selected");
    });

    $( ".sort-faction" ).on( "click", function() {

    });
 
	function toggleRow(tag) {
		// toggle all rows hidden or shown based on tag passed

		if (tag === "neutral-runner") {
			$( ".neutral.runner" ).toggleClass("hidden");

		} else if (tag === "neutral-corp") {
			$( ".neutral.corp" ).toggleClass("hidden");	
		} else {
			$( "." + tag ).toggleClass("hidden");
		}
	}

	function showRow(tag) {
		// toggle all rows to be shown based on tag passed
		$( "." + tag ).removeClass("hidden");
	}

	function hideRow(tag) {
		// toggle all rows hidden or shown based on tag passed
		$( "." + tag ).addClass("hidden");
	}

  });
 
	</script>
	
</head>

<body>
<h1>Project Mulligan LCG/ECG Proxy Generator</h1>

<div class="navigation">
<?php
echo "<div class='align-left btn-group btn-group-sm' role='group'>";
foreach ($supported_games as $g => $site) {
	if ($_GET['game'] == $g) {
		echo "<a href='/?game=$g' class='btn btn-default active' role='button'>$g</a>";
	} else {
		echo "<a href='/?game=$g' class='btn btn-default' role='button'>$g</a>";
	}
}
echo "</div>";

echo "<div class='align-right btn-group btn-group-sm' role='group'>";
echo "<div id='decklist' class='btn btn-default' role='button'>Load Decklist</div>";
echo "<div id='generate' class='btn btn-default' role='button'>Generate Proxies</div>";
echo "<div id='placeholder-list' class='btn btn-default' role='button'>Generate Placeholders</div>";
echo "<div id='placeholder-sets' class='btn btn-default' role='button'>Generate Full Set of Placeholders</div>";
echo "</div>";

?>

</div>
<hr />

<div class="navigation">
	<div class="btn-toolbar" role="toolbar">
<!--		<div class="align-left btn-group btn-grp-sm" role="group">
			<div id="sort-faction" class=" sort-faction sort-faction-all btn btn-default selected" data-index="all" role="button">All</div>
			<div id="sort-faction" class=" sort-faction sort-faction-runner btn btn-default selected" data-index="runner" role="button">Runner</div>
			<div id="sort-faction" class="sort-faction sort-faction-corp btn btn-default selected" data-index="corp" role="button">Corporation</div>
		</div> -->
		<div class="align-left btn-group btn-grp-sm" role="group">
			<div id="sort" class="sort sort-anarch btn btn-default selected" data-index="anarch" role="button">Anarch</div>
			<div id="sort" class="sort sort-criminal btn btn-default selected" data-index="criminal" role="button">Criminal</div>
			<div id="sort" class="sort sort-shaper btn btn-default selected" data-index="shaper" role="button">Shaper</div>
			<div id="sort" class="sort sort-neutral btn btn-default selected" data-index="neutral-runner" role="button">Neutral</div>
		</div>
		<div class="align-left btn-group btn-grp-sm" role="group">
			<div id="sort" class="sort sort-haas-bioroid btn btn-default selected" data-index="haas-bioroid" role="button">Haas-Bioroid</div>
			<div id="sort" class="sort sort-jinteki btn btn-default selected" data-index="jinteki" role="button">Jinteki</div>
			<div id="sort" class="sort sort-nbn btn btn-default selected" data-index="nbn" role="button">NBN</div>
			<div id="sort" class="sort sort-weyland-consortium btn btn-default selected" data-index="weyland-consortium" role="button">Weyland Consortium</div>
			<div id="sort" class="sort sort-neutral btn btn-default selected" data-index="neutral-corp" role="button">Neutral</div>
		</div>
		<div class="align-left btn-group btn-grp-sm" role="group">
			<div id="sort" class="sort sort-adam btn btn-default selected" data-index="adam" role="button">Adam</div>
			<div id="sort" class="sort sort-apex btn btn-default selected" data-index="apex" role="button">Apex</div>
			<div id="sort" class="sort sort-sunny-lebeau btn btn-default selected" data-index="sunny-lebeau" role="button">Sunny Lebeau</div>
		</div>
	</div>
</div>

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
		echo "<tr class='card-container $card->side_code $card->faction_code $card->type_code $card->set_code' data-index='$card->code'>\n<td class='card-title'>\n$card->title</td>\n<td>$set</td>\n<td>\n";
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

<div id="dialog-decklist" title="Load from Decklist">
	<p class="validateTips">Place plain text decklist in textbox to be parsed or supply a deck id number from NRDB in the deckID field.</p>
	<label for="deck-text">Decklist (Plain Text)</label><br />
	<textarea id="deck-text" name="deck-text"></textarea><br />
	<label for="deck-id">Deck ID from NRDB (Optional)</label><br />
	<input type="text" id="deck-id" name="deck-id" /><br />
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

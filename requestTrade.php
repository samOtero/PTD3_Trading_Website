<?php
session_start();
$whichProfile = $_REQUEST[ 'whichProfile' ];
$showTopAd = "yes";
$showSideAd = "yes";
$id = $_SESSION[ 'myID' ];
$currentSave = $_SESSION[ 'currentSave3' ];
$loggedIn = true;
$pageTitle = "Request Trade";
require '../moveList.php';
include 'shared/basic.php';
include 'shared/cookies.php';

$db = connect_To_Trading_Database();

//Don't care if they saved outside here
//check_If_Saved_Outside($db, $profileInfo, $id, $currentSave);

$urlValidation = "whichProfile=" . $whichProfile;

//Request Trade Code
$pokeTradeID = $_REQUEST[ 'pokeId' ];
if ( !isset( $pokeTradeID ) ) {
	redirect_To_NotAllowed();
}
//Save url parameters for going back to search results
$urlParameters = parse_url( $_SERVER[ 'REQUEST_URI' ], PHP_URL_QUERY );

//Max number of pokemon that can be offered
$maxOffers = 6;

//Get pokemon that you are trying to trade for
$query = "SELECT num, lvl, shiny, currentTrainer, uniqueID, nickname, myTag, m1, m2, m3, m4, gender, item, happy, hasRequest, sndCost FROM ptdtrad_ptd2_trading.ptd3_trade_pokes WHERE uniqueID = ?";
$result = $db->prepare( $query );
$result->bind_param( "s", $pokeTradeID );
$result->execute();
$result->store_result();
$hmp = $result->affected_rows;
$result->bind_result( $pokeNum, $pokeLevel, $pokeShiny, $currentTrainer, $pokeID, $pokeNickname, $myTag, $move1, $move2, $move3, $move4, $pokeGender, $pokeItem, $pokeHoF, $hasRequest, $sndCost);
if ( $result->affected_rows == 0 ) {
	$result->free_result();
	redirect_To_NotAllowed();
} else {
	$result->fetch();
}
$result->free_result();
$result->close();

//Get owner nickame
$db2 = connect_To_Original_Database();
$query = "select accNickname, avatar1, avatar2, avatar3, whichAvatar from sndgame_ptdprofile5.poke_accounts WHERE trainerID = ?";
$result2 = $db2->prepare( $query );
$result2->bind_param( "i", $currentTrainer );
$result2->execute();
$result2->store_result();
$result2->bind_result( $accNickname, $avatar1, $avatar2, $avatar3, $whichAvatar );
$result2->fetch();
$result2->free_result();
$result2->close();

$howManyCoins = 0;
if ($sndCost > 0) {
	$query = "select howManyCoins from sndgame_ptdprofile5.sndCoins WHERE trainerID = ?";
	$result = $db2->prepare($query);
	$result->bind_param("i", $id);
	$result->execute();
	$result->store_result();
	$result->bind_result($howManyCoins);			
	if ($result->affected_rows) {
		$result->fetch();
	}else{
		$howManyCoins = 0;
	}
	$result->free_result();
	$result->close();
}
$db2->close();

//Get if Trade allows hacks or not
$allowHacked = 0;
$query = "SELECT allowHack FROM ptdtrad_ptd2_trading.ptd3_trade_pokes WHERE uniqueID = ?";
$result = $db->prepare($query);
$result->bind_param("s", $pokeTradeID);
$result->execute();
$result->store_result();
$result->bind_result($allowHacked);

include 'shared/head.php';
?>
<script src="scripts/requestTrade.js"></script>
<body>
	<? include 'shared/navbar.php'; ?>
	<?php

	?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12" style="padding-top: 70px">
				<ol class="breadcrumb">
					<li><a href="main.php?<?php echo $urlValidation ?>">Home</a>
					</li>
					<li><a href="searchTrades.php?<?php echo $urlValidation ?>">Search Trades</a>
					</li>
					<li><a href="searchResults.php?<?php echo $urlParameters ?>">Search Results</a>
					</li>
					<li class="active">Request Trade</li>
				</ol>
			</div>
		</div>
		<? include 'shared/adTop.php'; ?>
		<div class="row">
			<div class="col-lg-3 col-md-4 col-sm-6">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<img src="../trading_center/avatar/<?php echo ${avatar.$whichAvatar}?>.png"> <?php echo $accNickname?>
						<span class="pull-right">ID: <?php echo $currentTrainer ?></span>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<?php block_poke_trade_setup($pokeID, $pokeNum, $pokeNickname, $move1, $move2, $move3, $move4, $pokeLevel, $pokeShiny,
		$pokeItem, $myTag, $pokeHoF, $pokeGender, "top"); ?>
			</div>
		</div>
		<?php 
		if ($sndCost > 0) {
		?>
		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h4 class="panel-title">Adopt Now</h4>
					</div>
					<div class="panel-body">
						You have (<?php echo $howManyCoins?>) SnD Coins to use in adopting a Pokémon. <a href="http://samdangames.blogspot.com/p/get-snd-coins.html">Press here to get more SnD coins.</a><br/>
						This trade has an Adoption Cost of: (<?php echo $sndCost ?>) SnD Coins.<br/>
						<?php
							if ($sndCost > $howManyCoins) {
								echo "You don't have enough SnD coins to Adopt this Pokémon.";
							}else{
								$encodedID = ($id * 5) - 1;
								echo '<div id="adoptResult">';
								echo '<button type="button" id="adopt1Yes" class="btn btn-success">Adopt this Pokémon for <span class="badge">'.$sndCost.'</span> SnD Coins</button>';
								echo '</div>';
							}
						?>
					</div>
				</div>
			</div>
		</div>
		<script>
			//When pressing on Adopt, show a confirmation link
			$('body').on('click', "#adopt1Yes", function () {
				
				var confirm = 'Are you sure?<br/><a type="button" id="adopt2Yes" class="btn btn-success" href="adoptTrade.php?<?php echo $urlParameters?>&IDCheck=<?php echo $encodedID ?>&tradeID=<?php echo $pokeTradeID ?>">Yes, Adopt for <span class="badge"><?php echo $sndCost ?></span> SnD Coins</a> <button type="button" id="adoptNo" class="btn btn-danger">No</button>';
				$("#adoptResult").html(confirm);
			});
			//If we click no on the confirm then reset button
			$('body').on('click', "#adoptNo", function () {
				var confirm = '<button type="button" id="adopt1Yes" class="btn btn-success">Adopt this Pokémon for <span class="badge"><?php echo $sndCost ?></span> SnD Coins</button>';
				$("#adoptResult").html(confirm);
			});			
		</script>
		<?php
		}
		?>
		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h4 class="panel-title">Trainer's Request</h4>
					</div>
					<div class="panel-body">
						<?php
						//Show any Trade Wants, if there are any available
						if ( $hasRequest == 0 ) {
							echo '<p>No Pokémon Request were made.</p>';
						} else {
							echo '<div class="row"><div id="myRequest" class="col-lg-3 col-md-4 col-sm-6">Loading Request...</div></div>';
							?>
						<script>
							//Load trade request with ajax
							loadTradeRequest( '<?php echo $pokeID?>' );
						</script>
						<?php
						}
						if ($allowHacked == 0) {
							echo '<p>No hacked offers allowed.</p>';
						}
						?>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-9 col-md-8 col-sm-7 col-xs-12">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h4 class="panel-title">Your Pokémon to Offer</h4>
					</div>
					<div class="panel-body">
						<p>Pick up to
							<?php echo $maxOffers ?> Pokémon that you wish to offer for this trade. <b>If you match one of the request made the trade will happen automatically!</b>
						</p>
						<form id="form1" name="form1" method="post" action="offerMe.php?<?php echo $urlParameters?>&tradeID=<?php echo $pokeTradeID ?>">
							<div class="row">
								<div class="col-xs-12">
									<?php
									$hackedClause = "myTag = 'n' AND";
									if ($allowHacked == 1) {
										$hackedClause = "";
									}
									$query = "SELECT num, lvl, shiny, uniqueID, nickname, myTag, m1, m2, m3, m4, gender, item, happy FROM ptdtrad_ptd2_trading.ptd3_trade_pokes WHERE $hackedClause currentTrainer = ? AND uniqueID != ? ORDER BY num, lvl";
									$result = $db->prepare( $query );
									$result->bind_param( "is", $id, $pokeTradeID );
									$result->execute();
									$result->store_result();
									$hmp = $result->affected_rows;
									$result->bind_result( $pokeNum, $pokeLevel, $pokeShiny, $pokeID, $pokeNickname, $myTag, $move1, $move2, $move3, $move4, $pokeGender, $pokeItem, $pokeHoF );
									for ( $i = 1; $i <= $hmp; $i++ ) {
										$result->fetch();
										echo block_poke_request_trade( $pokeID, $pokeNum, $pokeNickname, $move1, $move2, $move3, $move4, $pokeLevel, $pokeShiny,
											$pokeItem, $myTag, $pokeHoF, $pokeGender );
									}
									$result->free_result();
									$result->close();
									?>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<div style="padding-top:10px;">
										<button type="button" id="submitOffer" class="btn btn-success">Submit Offer</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<? include 'shared/adSide.php'; ?>
		</div>
		<div id="checkedTooManyModal" class="modal fade" role="dialog">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Ooops</h4>
					</div>
					<div class="modal-body">
						<p id="modalContent">You can only offer up to
							<?php echo $maxOffers ?> Pokémon.</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		<script>
			var checkedAmount = 0;
			var maxChecked = <?php echo $maxOffers ?>;
			$( ":checkbox" ).change( function () {
				if ( $( this ).is( ":checked" ) ) {
					checkedAmount++;
					if ( checkedAmount > maxChecked ) {
						checkedAmount--;
						$( this ).prop( 'checked', false );
						$("#modalContent").html("You can only offer up to <?php echo $maxOffers ?> Pokémon.");
						$( "#checkedTooManyModal" ).modal();
					}

				} else {
					checkedAmount--;
				}
			} );
			$("#submitOffer").click(function() {
				if (checkedAmount <= 0) {
					$("#modalContent").html("You must offer at least 1 Pokémon.");
					$( "#checkedTooManyModal" ).modal();
				}else{
					$("#form1").submit();
				}
			});
		</script>
	</div>
	<? include 'shared/footer.php'; ?>
</body></html>
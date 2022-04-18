<?php
session_start();
$whichProfile = $_REQUEST[ 'whichProfile' ];
$showTopAd = "yes";
$showSideAd = "yes";
$id = $_SESSION[ 'myID' ];
$currentSave = $_SESSION[ 'currentSave3' ];
$loggedIn = true;
$pageTitle = "View Request";
require '../moveList.php';
include 'shared/basic.php';
include 'shared/cookies.php';

$db = connect_To_Trading_Database();

//Don't care if they saved outside here
//check_If_Saved_Outside($db, $profileInfo, $id, $currentSave);

$urlValidation = "whichProfile=" . $whichProfile;

//View Request Code
$pokeTradeID = $_REQUEST[ 'pokeId' ];
if ( !isset( $pokeTradeID ) ) {
	redirect_To_NotAllowed();
}

//Get the pokemon you want to view request for
$query = "SELECT num, lvl, shiny, currentTrainer, uniqueID, nickname, myTag, m1, m2, m3, m4, gender, item, happy, hasRequest FROM ptdtrad_ptd2_trading.ptd3_trade_pokes WHERE uniqueID = ? and currentTrainer = ?";
$result = $db->prepare( $query );
$result->bind_param( "si", $pokeTradeID, $id );
$result->execute();
$result->store_result();
$hmp = $result->affected_rows;
$result->bind_result( $pokeNum, $pokeLevel, $pokeShiny, $currentTrainer, $pokeID, $pokeNickname, $myTag, $move1, $move2, $move3, $move4, $pokeGender, $pokeItem, $pokeHoF, $hasRequest );
if ( $result->affected_rows == 0 ) {
	$result->free_result();
	redirect_To_NotAllowed();
} else {
	$result->fetch();
}
$result->free_result();
$result->close();

include 'shared/head.php';
?>
<script src="scripts/viewRequest.js"></script>
<body>
	<? include 'shared/navbar.php'; ?>
	<?php

	?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12" style="padding-top: 70px">
				<ol class="breadcrumb">
					<li><a href="main.php?<?php echo $urlValidation ?>">Home</a></li>
					<li><a href="myTrades.php?<?php echo $urlValidation ?>">My Trades</a></li>
					<li class="active">View Request</li>
				</ol>
			</div>
		</div>
		<? include 'shared/adTop.php'; ?>
		<div class="row">
			<div class="col-sm-12">
				<?php block_poke_trade_setup($pokeID, $pokeNum, $pokeNickname, $move1, $move2, $move3, $move4, $pokeLevel, $pokeShiny,
		$pokeItem, $myTag, $pokeHoF, $pokeGender, "top"); ?>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h4 class="panel-title">Your Request</h4>
					</div>
					<div class="panel-body">
						<?php
						//Show any Trade Wants, if there are any available
						if ( $hasRequest == 0 ) {
							echo '<p>No Request were made.</p>';
						} else {
							echo '<div class="row"><div id="myRequest" class="col-lg-3 col-md-4 col-sm-6">Loading Request...</div></div>';
							?>
						<script>
							//Load trade request with ajax
							loadTradeRequest( '<?php echo $pokeID?>' );
						</script>
						<?php
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
						<h4 class="panel-title">Offers for this trade</h4>
					</div>
					<div class="panel-body">
						<p>
						Review the Offers for this trade and press Accept or Deny once you made a decision.
						</p>
					</div>
					<ul class = "list-group">                
						<li class="list-group-item">
							<div class="row">
								<div class="col-xs-12">
									<?php echo tradeOfferBlock($db, $id, $urlValidation, $pokeTradeID);?>
								</div>
							</div>
						</li>
					</ul>
				</div>
			</div>
			<? include 'shared/adSide.php'; ?>
		</div>
	</div>
	<? include 'shared/footer.php';
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function tradeOfferBlock($db_New,$id, $urlValidation, $tradeID){
	$haveRequest = false;
	$offerIDList = array();
	//Get distinct offers for our trade
	$query2 = "SELECT DISTINCT offerID FROM ptdtrad_ptd2_trading.ptd3_trade_request WHERE tradePokeID = ? AND offerID != '-1'";
	$result2 = $db_New->prepare($query2);
	$result2->bind_param("s", $tradeID);
	$result2->execute();
	$result2->store_result();
	$hmr = $result2->affected_rows;
	$result2->bind_result($offerID);
	for ($b=1; $b<=$hmr; $b++) {
		$result2->fetch();
		$haveRequest = true;
		array_push($offerIDList, $offerID);
	}
	$result2->free_result();
	$result2->close();
	$db = connect_To_Original_Database();
	for ($b=0; $b<count($offerIDList); $b++) {
		$offerID = $offerIDList[$b];
		show_Offers($db_New, $db, $offerID, $urlValidation);
	}
	$db->close();
	$db_New->close();
	if ($haveRequest == false) {
		 echo 'You do not have any request for this Pokémon.';
	}
}	
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function show_Offers($db_New, $db, $offerID, $urlValidation) {
	$query2 = "SELECT tradePokeID, requestPokeID FROM ptdtrad_ptd2_trading.ptd3_trade_request WHERE offerID = ?";
	$result2 = $db_New->prepare($query2);
	$result2->bind_param("s", $offerID);
	$result2->execute();
	$result2->store_result();
	$hmr = $result2->affected_rows;
	$result2->bind_result($tradePokeID, $requestID);
	$result2->fetch();
	$result2->free_result();
	$result2->close();
	$query = "SELECT currentTrainer FROM ptdtrad_ptd2_trading.ptd3_trade_pokes WHERE uniqueID = ?";
	$result = $db_New->prepare($query);
	$result->bind_param("s", $requestID);
	$result->execute();
	$result->store_result();
	$result->bind_result($currentTrainer);
	$result->fetch();
	$result->free_result();
	$result->close();
	$query = "select accNickname, avatar1, avatar2, avatar3, whichAvatar from sndgame_ptdprofile5.poke_accounts WHERE trainerID = ?";
	$result3 = $db->prepare($query);
	$result3->bind_param("i", $currentTrainer);
	$result3->execute();
	$result3->store_result();
	$result3->bind_result($accNickname, $avatar1, $avatar2, $avatar3, $whichAvatar);			
	$result3->fetch();
	$result3->free_result();
	$result3->close();
	?>
   	<div class = "col-lg-3 col-md-4 col-sm-6" style="padding-left:5px;padding-right:5px" id="offerBlock<?php echo $offerID?>">
          <div class="panel panel-primary">
            <div class="panel-heading">
              <h4 class="panel-title">Offer from <img src="../trading_center/avatar/<?php echo ${avatar.$whichAvatar}?>.png"> <?php echo $accNickname ?></h4>
            </div>
            <div class = "panel-body" style="padding-left:0px;padding-right:0px;">
                <?php
				$query2 = "SELECT requestPokeID FROM ptdtrad_ptd2_trading.ptd3_trade_request WHERE offerID = ?";
				$result2 = $db_New->prepare($query2);
				$result2->bind_param("s", $offerID);
				$result2->execute();
				$result2->store_result();
				$hmr = $result2->affected_rows;
				$result2->bind_result($requestPokeID);
				for ($b=1; $b<=$hmr; $b++) {
					$result2->fetch();
					$query = "SELECT num, lvl, shiny, currentTrainer, uniqueID, nickname, myTag, gender, happy, m1, m2, m3, m4 FROM ptdtrad_ptd2_trading.ptd3_trade_pokes WHERE uniqueID = ?";
					$result = $db_New->prepare($query);
					$result->bind_param("s", $requestPokeID);
					$result->execute();
					$result->store_result();
				$result->bind_result($pokeNum, $pokeLevel,$pokeShiny, $currentTrainer, $tradeID, $pokeNickname, $myTag, $pokeGender, $pokeHoF, $m1, $m2, $m3, $m4);
					$result->fetch();
					$result->free_result();
					$result->close();
					$pokeNickname = stripslashes($pokeNickname);
					block_poke_long($tradeID, $pokeNum, $pokeNickname, $m1, $m2, $m3, $m4, $pokeLevel, $pokeShiny, $pokeItem, $myTag, $pokeHoF, $pokeGender, "offers".$offerID);
				}
				$result2->free_result();
				$result2->close();
				?>
            </div>
            <ul  class = "list-group">
            	<li class = "list-group-item text-center"><button type="submit" id="viewOfferBtn<?php echo $offerID?>" class="btn btn-success" data-toggle="collapse" data-target="#acceptConfirm<?php echo $offerID ?>">Accept Offer</button> <button type="submit" id="removeOfferBtn<?php echo $offerID?>" class="btn btn-danger" data-toggle="collapse" data-target="#denyConfirm<?php echo $offerID ?>">Deny Offer</button></li>
            	<li class = "list-group-item">
            		<div class="collapse" id="denyConfirm<?php echo $offerID?>">
            			<div class="row">
            				<div class="col-xs-12 text-center" id="removeOfferText<?php echo $offerID?>">
								Are you sure you want to deny this offer?<br/>
								<button type="button" class="btn btn-success btn-md" data-toggle="collapse" data-target="#denyConfirm<?php echo $offerID ?>">No</button>
								<button type="button" class="btn btn-danger btn-md" onClick="removeOffer('<?php echo $offerID?>', '<?php echo $requestID?>');return false;">Yes</button>
							</div>
						</div>
					</div>
					<div class="collapse" id="acceptConfirm<?php echo $offerID?>">
            			<div class="row">
            				<div class="col-xs-12 text-center">
								Are you sure you want to accept this offer?<br/>
								<button type="button" class="btn btn-success btn-md" data-toggle="collapse" data-target="#acceptConfirm<?php echo $offerID ?>">No</button>
								<a type="button" class="btn btn-danger btn-md" href="acceptRequest.php?<?php echo $urlValidation.'&offerId='.$offerID ?>">Yes</a>
							</div>
						</div>
					</div>
				</li>
			</ul>
		</div>
	</div>
    <?php
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	?>
</body></html>
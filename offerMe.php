<?php
session_start();
$whichProfile = $_REQUEST[ 'whichProfile' ];
$showTopAd = "no";
$showSideAd = "no";
$id = $_SESSION[ 'myID' ];
$currentSave = $_SESSION[ 'currentSave3' ];
$loggedIn = true;
$pageTitle = "Offer Made";
require '../moveList.php';
include 'shared/basic.php';
include 'shared/cookies.php';
require 'shared/trade_To_Pickup_By_ID.php';

$urlValidation = "whichProfile=" . $whichProfile;

//Offer Me Code
$pokeId = $_REQUEST[ 'tradeID' ];
$requestIDList = $_REQUEST[ 'offer' ];
//BASIC VALIDATION
//Make sure there is a trade ID
if ( !isset( $pokeId ) ) {
	redirect_To_NotAllowed();
}

//Make sure we have offers
if ( empty( $requestIDList ) ) {
	redirect_To_NotAllowed();
}

//Make sure we are not offering more than 6
$requestCount = count( $requestIDList );
$maxAmount = 6;
if ( $requestCount > $maxAmount ) {
	redirect_To_NotAllowed();
}

for ( $i = 0; $i < $requestCount; $i++ ) {
	$requestID = $requestIDList[ $i ];
	if ( $requestID == $tradeID ) { //Can't offer the trade to itself
		redirect_To_NotAllowed();
	}
	for ( $z = 0; $z < $requestCount; $z++ ) {
		$otherRequest = $requestIDList[ $z ];
		if ( $z != $i ) { //4
			if ( $requestID == $otherRequest ) { //Can't offer something twice
				redirect_To_NotAllowed();
			}
		}
	}
}

$db = connect_To_Original_Database();

//Check if you saved outside for sanity sake
check_If_Saved_Outside( $db, $profileInfo, $id, $currentSave );

$db->close();
//Save url parameters for going back to search results
$urlParameters = parse_url( $_SERVER[ 'REQUEST_URI' ], PHP_URL_QUERY );

//End Transaction

include 'shared/head.php';
?>
<script src="scripts/tradeSetup.js"></script>
<body>
	<? include 'shared/navbar.php'; ?>
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
					<li><a href="requestTrade.php?<?php echo $urlParameters ?>">Request Trade</a>
					</li>
					<li class="active">Offer Made</li>
				</ol>
			</div>
		</div>
		<? include 'shared/adTop.php'; ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h4 class="panel-title">Offer Made</h4>
					</div>
					<div class="panel-body">
						<?php echo do_Offer($requestCount, $requestIDList, $id, $pokeId); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php include 'shared/footer.php'; 
///////////////////////////////////////////////////////////////////////////////////////////////////////
	function do_Offer($requestCount, $requestIDList, $id, $tradeID) {
		$have588 = false; //Karrablast
		$have616 = false; //Shelmet
		$db_New = connect_To_Trading_Database();
		$returnMessage = "";
		//Check your offer pokemon
		for ($i=0; $i<$requestCount; $i++) {
			$requestID = $requestIDList[$i];
			$query = "SELECT currentTrainer, num FROM ptdtrad_ptd2_trading.ptd3_trade_pokes WHERE uniqueID = ? AND currentTrainer = ?";
			$result = $db_New->prepare($query);
			$result->bind_param("si", $requestID, $id);
			$result->execute();
			$result->store_result();
			$result->bind_result($tempCurrentT, $tempNum);
			if (!$result->affected_rows) {
				$result->free_result();
				$result->close();
				$db_New->close();
				return 'You cannot offer a Pokémon that does not belong to you.';
			}
			$result->fetch();
			$result->free_result();
			$result->close;
			if ($tempNum == 588) { //Are you trading a Karrablast
				$have588 = true;
			}else if ($tempNum == 616) {//Are you trading a Shelmet
				$have616 = true;
			}
			$query = "SELECT tradePokeID FROM ptdtrad_ptd2_trading.ptd3_trade_request WHERE tradePokeID = ? AND requestPokeID = ?";
			$result = $db_New->prepare($query);
			$result->bind_param("ss", $tradeID, $requestID);
			$result->execute();
			$result->store_result();
			if ($result->affected_rows) {
				$result->free_result();
				$result->close();
				$db_New->close();
				return 'You cannot offer the same Pokémon twice for the same trade.';
			}
			$result->free_result();
			$result->close();
		}
		//Check request to see if you match them (Note: May need to turn this into it's own function for the request Trade page to see if you can fufill trades?)
		$totalPossibleRequest = 3;
		$insertIntoRequest = true;
		for ($z=1; $z<=$totalPossibleRequest; $z++) {//2
			$wantsList = array();
			$query = "SELECT num, shiny, gender FROM ptdtrad_ptd2_trading.ptd3_trade_wants WHERE tradePokeID = ? AND whichRequest = ?";
			$result = $db_New->prepare($query);
			$result->bind_param("si", $tradeID , $z);
			$result->execute();
			$result->store_result();
			$hmw = $result->affected_rows;
			$result->bind_result($wantNum, $wantShiny, $wantGender);
			//If this trade has request, then see if our offer fufills it
			if ($hmw > 0) {
				for ($i=1; $i<=$hmw; $i++) {
					$result->fetch();
					array_push($wantsList, array($wantNum, $wantShiny, $wantGender));
				}
				$result->free_result();
				$result->close();
				if ($hmw <= $requestCount) {//4
					for ($i=0; $i<$requestCount; $i++) {//5
						$requestID = $requestIDList[$i];
						$query = "SELECT num, shiny, myTag, gender FROM ptdtrad_ptd2_trading.ptd3_trade_pokes WHERE uniqueID = ?";
						$result = $db_New->prepare($query);
						$result->bind_param("s", $requestID);
						$result->execute();
						$result->store_result();
						$hmr = $result->affected_rows;
						$result->bind_result($requestNum, $requestShiny, $requestTag, $requestGender);
						$result->fetch();
						$result->free_result();
						$result->close();
						if ($hmr == 0) {//6
							break;
						}//6
						if ($requestTag == "h") {//6
							break;
						}//6
						for ($b=0; $b<count($wantsList); $b++) {//6
							$currentWantPoke = $wantsList[$b];
							if ($currentWantPoke[0] != 0 && $requestNum != $currentWantPoke[0]){//7
								continue;
							}//7
							//Handle any elemental request
							if ($currentWantPoke[1] == -2) {
								if ($requestShiny < 3) {
									continue;
								}
							}else if ($currentWantPoke[1] != -1 && $requestShiny != $currentWantPoke[1]) {//7
								continue;
							}//7
							
							if ($currentWantPoke[2] != -1 && $requestGender != $currentWantPoke[2]) {//7
								continue;
							}//7
							array_splice($wantsList, $b, 1);
							break;
						}//6
					}//5
				}//4
				// If we matched all the request for this trade, then complete trade automatically
				if (count($wantsList) == 0) {
					$query = "SELECT currentTrainer, num FROM ptdtrad_ptd2_trading.ptd3_trade_pokes WHERE uniqueID = ?";
					$result = $db_New->prepare($query);
					$result->bind_param("s", $tradeID);
					$result->execute();
					$result->store_result();
					$result->bind_result($otherTrainer, $otherNum);
					$result->fetch();
					$doShelmet = false;
					if ($otherNum == 588) {//5
						if ($have616 == true) {//6
							$doShelmet = true;
						}//6
					}else if ($otherNum == 616) {//5
						if ($have588 == true) {//6
							$doShelmet = true;
						}//6
					}//5
					$db_New->autocommit(false);
					$transactionFlag = true;
					$transactionFlag = trade_To_Pickup($db_New, $tradeID, $id, $doShelmet, $transactionFlag);
					$query2 = "DELETE FROM ptdtrad_ptd2_trading.ptd3_trade_request WHERE tradePokeID = ? OR requestPokeID = ?";
					$result2 = $db_New->prepare($query2);
					$result2->bind_param("ss", $tradeID, $tradeID);
					if (!$result2->execute()) {//5
						$transactionFlag = false;
					}//5
					$result2->close();
					$query2 = "DELETE FROM ptdtrad_ptd2_trading.ptd3_trade_wants WHERE tradePokeID = ?";
					$result2 = $db_New->prepare($query2);
					$result2->bind_param("s", $tradeID);
					if (!$result2->execute()) {//5
						$transactionFlag = false;
					}//5
					$result2->close();
					for ($i=0; $i<$requestCount; $i++) {//5
						$requestID = $requestIDList[$i];
						$transactionFlag = trade_To_Pickup($db_New, $requestID, $otherTrainer, $doShelmet, $transactionFlag);
						$query2 = "DELETE FROM ptdtrad_ptd2_trading.ptd3_trade_request WHERE tradePokeID = ? OR requestPokeID = ?";
						$result2 = $db_New->prepare($query2);
						$result2->bind_param("ss", $requestID, $requestID);
						if (!$result2->execute()) {//6
							$transactionFlag = false;
						}//6
						$result2->close();
						$query2 = "DELETE FROM ptdtrad_ptd2_trading.ptd3_trade_wants WHERE tradePokeID = ?";
						$result2 = $db_New->prepare($query2);
						$result2->bind_param("s", $requestID);
						if (!$result2->execute()) {//6
							$transactionFlag = false;
						}//6
						$result2->close();
					}//5
					$insertIntoRequest = false;
					$result->free_result();
					$result->close();
					//Do we a trade is completed, may use this later for some purpose but remove for now (can find it on ptd2_basic)
					//$transactionFlag = trade_Successful($db_New, $id, $otherTrainer, $transactionFlag);
					if ($transactionFlag == true) {//5
						$db_New->commit();
						$returnMessage = 'You matched up all the request! The trade is completed! You can pick up your Pokémon by pressing Home and looking at the pickup area in the bottom of the page.';
					}else{//5
						$db_New->rollback();
						$returnMessage = 'An Error has occurred. Please go back and try again.';
					}//5
					$db_New->autocommit(true);
					break;
				}//4
			}else{//3
				$result->free_result();
				$result->close();	
			}//3
		}//2
		//If we need to add request (did not fulfill all the request or there weren't any)
		if ($insertIntoRequest == true) {//2
			$newOfferID = uniqid(true);
			$db_New->autocommit(false);
			$transactionFlag = true;
			$query = "INSERT INTO ptdtrad_ptd2_trading.ptd3_trade_request (tradePokeID, requestPokeID, offerID) VALUES (?,?,?)";
			$result = $db_New->prepare($query);
			for ($i=0; $i<$requestCount; $i++) {//3
				$requestID = $requestIDList[$i];
				$result->bind_param("sss", $tradeID, $requestID, $newOfferID);
				if (!$result->execute()) {
					//echo 'Failed: Insert into trade request. '.$db_New->error;
					$transactionFlag = false;
				}
			}//3
			if ($transactionFlag == true) {
				$db_New->commit();
				$returnMessage = 'Your offer has been made. You must wait until the other trainer decides to accept or deny your offer. You can remove this offer by going to the Your Trade Request Page.';
			}else{
				$db_New->rollback();
				$returnMessage = 'An Error has occurred. Please go back and try again.';
			}
		}//2
		$db_New->close();
		return $returnMessage;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////
?>
</body> </html>
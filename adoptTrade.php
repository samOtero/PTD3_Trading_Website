<?php
session_start();
$whichProfile = $_REQUEST[ 'whichProfile' ];
$showTopAd = "no";
$showSideAd = "no";
$id = $_SESSION[ 'myID' ];
$currentSave = $_SESSION[ 'currentSave3' ];
$loggedIn = true;
$pageTitle = "Adopt Now";
require '../moveList.php';
include 'shared/basic.php';
include 'shared/cookies.php';
require 'shared/trade_To_Pickup_By_ID.php';

$urlValidation = "whichProfile=" . $whichProfile;

//Adopt Trade Code
$pokeId = $_REQUEST[ 'tradeID' ];
$checkID = $_REQUEST['IDCheck'];
//BASIC VALIDATION
//Make sure there is a trade ID
if ( !isset( $pokeId ) ) {
	redirect_To_NotAllowed();
}

//Make sure we have the ID check
if ( empty( $checkID ) ) {
	redirect_To_NotAllowed();
}

//Make sure we are who the link says we are
$decodedCheckID = ($checkID + 1) / 5;
if ( $id != $decodedCheckID ) {
	redirect_To_NotAllowed();
}

$db = connect_To_Original_Database();

//Check if you saved outside for sanity sake
check_If_Saved_Outside( $db, $profileInfo, $id, $currentSave );

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
					<li class="active">Adopt Now</li>
				</ol>
			</div>
		</div>
		<? include 'shared/adTop.php'; ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h4 class="panel-title">Adopt Now</h4>
					</div>
					<div class="panel-body">
						<?php
						echo do_Adoption($id, $pokeId, $db);
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php include 'shared/footer.php'; 
///////////////////////////////////////////////////////////////////////////////////////////////////////
	function do_Adoption($id, $tradeID, $db) {
		//Check your coins
		$query = "select howManyCoins from sndgame_ptdprofile5.sndCoins WHERE trainerID = ?";
		$result = $db->prepare($query);
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
		
		$responseMessage = "";
		$db_New = connect_To_Trading_Database();
		$query = "SELECT num, lvl, shiny, currentTrainer, uniqueID, nickname, myTag, m1, m2, m3, m4, gender, item, sndCost, extra1, extra2, happy FROM ptdtrad_ptd2_trading.ptd3_trade_pokes WHERE uniqueID = ?";
		$result = $db_New->prepare($query);
		$result->bind_param("s", $tradeID);
		$result->execute();
		$result->store_result();
		$hmp = $result->affected_rows;
		$result->bind_result($pokeNum, $pokeLevel,$pokeShiny, $currentTrainer, $tradeID, $pokeNickname, $myTag, $move1, $move2, $move3, $move4, $pokeGender, $pokeItem, $sndCost, $extra1, $extra2, $pokeHoF);
		if ($hmp == 0) {
			return 'Sorry, but this Pokémon is no longer up for Trade.';
		}else{
			$result->fetch();
			if ($sndCost == 0) {
				return 'Sorry, but this Pokémon is not up for Adoption.';
			}else if ($sndCost > $howManyCoins) {
				return "Sorry, but you don't have enough coins to Adopt this Pokémon.";
			}else if ($sndCost < 0) { //Combat against snd coin hack
				return "Sorry, this Pokémon cannot be adopted for a negative amount of SnD Coins.";
			}else{				
				//Transaction start if we have enough coins and the pokemon is up for adoption
				$db->autocommit(false);
				$transactionFlag = true;
				//Transfer coins from one account to another
				$transactionFlag = change_Coins_Amount($currentTrainer, $sndCost, $db, $transactionFlag);
				$transactionFlag = change_Coins_Amount($id, -$sndCost, $db, $transactionFlag);
				//Keep track of adoptions
				$whoAdopting = -3; //Special tag for ptd3 adoptions
				$query = "INSERT INTO sndgame_ptdprofile5.sndCoins_usage (trainerID, usedCoins, usedOn) VALUES (?,?,?)";
				$result = $db->prepare($query);
				$result->bind_param("iii", $id, $sndCost, $whoAdopting);
				if (!$result->execute()) {
					$transactionFlag = false;
				}
				$result->close();
				//Start transaction for Trading DB
				$db_New->autocommit(false);
				//Transfer pokemon to your pickup area
				$doShelmet = false;
				$transactionFlag = trade_To_Pickup($db_New, $tradeID, $id, $doShelmet, $transactionFlag);
				//Delete any request and wants for the adopted pokemon to clean up the DB
				$query2 = "DELETE FROM ptdtrad_ptd2_trading.ptd3_trade_request WHERE tradePokeID = ? OR requestPokeID = ?";
				$result2 = $db_New->prepare($query2);
				$result2->bind_param("ss", $tradeID, $tradeID);
				if (!$result2->execute()) {
					$transactionFlag = false;
				}
				$result2->close();
				$query2 = "DELETE FROM ptdtrad_ptd2_trading.ptd3_trade_wants WHERE tradePokeID = ?";
				$result2 = $db_New->prepare($query2);
				$result2->bind_param("s", $tradeID);
				if (!$result2->execute()) {
					$transactionFlag = false;
				}
				$result2->close();
				if ($transactionFlag == true) {
					$db->commit();
					$db_New->commit();
					echo 'Congratulations! You have adopted the following Pokémon. It to like you a lot! Take good care of it! You can pick it up on Home Page.<br/>';
					echo block_poke_trade_setup($tradeID, $pokeNum, $pokeNickname, $move1, $move2, $move3, $move4, $pokeLevel, $pokeShiny,
		$pokeItem, $myTag, $pokeHoF, $pokeGender, "top");
				}else{
					$responseMessage = "An error has occured, please go back and try again.";
					$db->rollback();
					$db_New->rollback();
				}
				$db->autocommit(true);
				$db_New->autocommit(true);
				$db_New->close();
				$db->close();
			}
			//***
		}
		return $returnMessage;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////
function change_Coins_Amount($trainerID, $howMany, $db, $transactionFlag) {
	$query = "SELECT howManyCoins FROM sndgame_ptdprofile5.sndCoins WHERE trainerID = ?";
	$result = $db->prepare($query);
	$result->bind_param("i", $trainerID);
	$result->execute();
	$result->store_result();
	$result->bind_result($howManyCoins);	
	$hmp = $result->affected_rows;
	if ($hmp == 0) {
		$result->close();
		if ($howMany < 0) {
			$howMany = 0;
		}
		$query = "INSERT INTO sndgame_ptdprofile5.sndCoins (trainerID, howManyCoins) VALUES (?,?)";
		$result = $db->prepare($query);
		$result->bind_param("ii", $trainerID, $howMany);
		if (!$result->execute()) {
			$transactionFlag = false;
		}
		$result->close();
	}else{
		$result->fetch();
		$result->close();
		$howManyCoins += $howMany;
		if ($howManyCoins < 0) {
			$howManyCoins = 0;
		}
		$query = "UPDATE sndgame_ptdprofile5.sndCoins SET howManyCoins = ? WHERE trainerID = ?";
		$result = $db->prepare($query);
		$result->bind_param("ii", $howManyCoins, $trainerID);
		if (!$result->execute()) {
			$transactionFlag = false;
		}
		$result->close();
	}
	return $transactionFlag;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////
?>
</body> </html>
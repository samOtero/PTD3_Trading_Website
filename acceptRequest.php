<?php
session_start();
$whichProfile = $_REQUEST[ 'whichProfile' ];
$showTopAd = "no";
$showSideAd = "no";
$id = $_SESSION[ 'myID' ];
$currentSave = $_SESSION[ 'currentSave3' ];
$loggedIn = true;
$pageTitle = "Accept Request";
require '../moveList.php';
include 'shared/basic.php';
include 'shared/cookies.php';
require 'shared/trade_To_Pickup_By_ID.php';

$db = connect_To_Trading_Database();

$urlValidation = "whichProfile=" . $whichProfile;

//Accept Request Code
$offerID = $_REQUEST[ 'offerId' ];
if ( !isset( $offerID ) ) {
	redirect_To_NotAllowed();
}


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
					<li class="active">Accept Request</li>
				</ol>
			</div>
		</div>
		<? include 'shared/adTop.php'; ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h4 class="panel-title">Accept Request</h4>
					</div>
					<div class="panel-body">
						<?php
						//Attempt to accept request
						echo acceptOffer($db, $id, $offerID);
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<? include 'shared/footer.php';
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function acceptOffer($db_New,$id, $offerID){
	
	//Check to see if this offer is still available
	$query = "SELECT requestPokeID, tradePokeID FROM ptdtrad_ptd2_trading.ptd3_trade_request WHERE offerID = ? ";
	$result = $db_New->prepare($query);
	$result->bind_param("s", $offerID);
	$result->execute();
	$result->store_result();
	$result->bind_result($requestPokeID, $tradePokeID);
	$hmp = $result->affected_rows;
	if ($hmp == 0) {
		$result->free_result();
		$result->close();
		$db_New->close();
		return 'This offer is no longer available.';
	}
	
	$doChangeTo = false;
	$db_New->autocommit(false);
	$transactionFlag = true;
	$firstCheck = false;
	for ($i=1; $i<=$hmp; $i++) {//2
		$result->fetch();
		if ($firstCheck == false) {//3
			$firstCheck = true;
			//Check to see if you own the pokemon you are accepting for
			$query2 = "SELECT num FROM ptdtrad_ptd2_trading.ptd3_trade_pokes WHERE currentTrainer = ? AND uniqueID = ?";
			$result2 = $db_New->prepare($query2);
			$result2->bind_param("is", $id, $tradePokeID);
			$result2->execute();
			$result2->store_result();
			$result2->bind_result($pokeNum2);
			if ($result2->affected_rows == 0) {
				$result->free_result();
				$result->close();
				$result2->free_result();
				$result2->close();
				$db_New->close();
				return 'Error, you cannot accept this trade offer.';
			}
			$result2->fetch();
			$result2->free_result();
			$result2->close();
		}//3
		$query2 = "SELECT num, item, currentTrainer FROM ptdtrad_ptd2_trading.ptd3_trade_pokes WHERE uniqueID = ?";
		$result2 = $db_New->prepare($query2);
		$result2->bind_param("s", $requestPokeID);
		if (!$result2->execute()) {//3
			$transactionFlag = false;
		}//3
		$result2->store_result();
		$result2->bind_result($pokeNum, $item, $otherTrainer);
		$result2->fetch();
		$result2->free_result();
		$result2->close();
		require '../tradeEvolution2.php'; //handles the evolutions	from ptd2
		if ($item != 13) {//Everstone //3		
			if ($pokeNum == 588) {//4
				if ($pokeNum2 == 616) {//5
					$pokeNickname = "Escavalier";
					$pokeNum = 589;
					$doChangeTo = true;
				}//5
			}else if ($pokeNum == 616) {//4
				if ($pokeNum2 == 588) {//5
					$pokeNickname = "Accelgor";
					$pokeNum = 617;
					$doChangeTo = true;
				}//5			
			}//4
		}//3
		$transactionFlag = trade_To_Pickup($db_New, $requestPokeID, $id, $doChangeTo, $transactionFlag); 
		$query2 = "DELETE FROM ptdtrad_ptd2_trading.ptd3_trade_request WHERE tradePokeID = ? OR requestPokeID = ?";
		$result2 = $db_New->prepare($query2);
		$result2->bind_param("ss", $tradePokeID, $requestPokeID);
		if (!$result2->execute()) {//3
			$transactionFlag = false;
		}//3
		$result2->close();
		$query2 = "DELETE FROM ptdtrad_ptd2_trading.ptd3_trade_request WHERE tradePokeID = ? OR requestPokeID = ?";
		$result2 = $db_New->prepare($query2);
		$result2->bind_param("ss", $requestPokeID, $tradePokeID);
		if (!$result2->execute()) {//3
			$transactionFlag = false;
		}//3
		$result2->close();
		$query2 = "DELETE FROM ptdtrad_ptd2_trading.ptd3_trade_wants WHERE tradePokeID = ? OR tradePokeID = ?";
		$result2 = $db_New->prepare($query2);
		$result2->bind_param("ss", $requestPokeID, $tradePokeID);
		if (!$result2->execute()) {//3
			$transactionFlag = false;
		}//3
		$result2->close();
	}//2

	$transactionFlag = trade_To_Pickup($db_New, $tradePokeID, $otherTrainer, $doChangeTo, $transactionFlag);
	//Not doing any trade Successful things... YET
	//$transactionFlag = trade_Successful($db_New, $id, $otherTrainer, $transactionFlag);
	$returnMessage = "";
	if ($transactionFlag == true) {
		$db_New->commit();
		$returnMessage = "Trade Completed! You can find your new Pokémon at the Pickup Area. Click Home to get to the Pickup Area.";
	}else{
		$db_New->rollback();
		$returnMessage = "An Error has Occurred. Please Try Again.";
	}
	$db_New->autocommit(true);
	$db_New->close();
	return $returnMessage;
}	
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	?>
</body></html>
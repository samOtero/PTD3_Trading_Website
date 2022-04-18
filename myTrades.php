<?php
session_start();
$whichProfile = $_REQUEST[ 'whichProfile' ];
$showTopAd = "yes";
$showSideAd = "yes";
$id = $_SESSION[ 'myID' ];
$currentSave = $_SESSION[ 'currentSave3' ];
$loggedIn = true;
$pageTitle = "My Trades";
require '../moveList.php';
include 'shared/basic.php';
include 'shared/cookies.php';

$db = connect_To_Trading_Database();
$yourTrades = array();

$urlValidation = "whichProfile=" . $whichProfile;
$whichDB = $_COOKIE["db3"];

include 'shared/head.php';
?>
<script src="scripts/myTrades.js"></script>
<body>
<?php include 'shared/navbar.php'; ?>
<div class="container-fluid">
	<div class = "row">
        <div class = "col-sm-12" style="padding-top: 70px">
        	<ol class="breadcrumb">
            <li><a href="main.php?<?php echo $urlValidation ?>">Home</a></li>
            <li class="active">My Trades</li>
            </ol>
        </div>
    </div>
    <? include 'shared/adTop.php'; ?>
    <div class="row">
        <div class = "col-lg-9 col-md-8 col-sm-7 col-xs-12" >
          <div class="panel panel-primary">
            <div class="panel-heading">
              <h4 class="panel-title">My Trades</h4>
            </div>
            <div class = "panel-body">
                These are the Pokémon you have for trade. Press on "View Request" to view, accept and decline offer for that Pokémon.
            </div>
            <ul class = "list-group">                
                <li class="list-group-item">
                	<div class="row">
						<div class="col-xs-12">
							<?php echo getMyTrades($db, $id, $urlValidation, $yourTrades, $whichProfile, $whichDB);?>
						</div>
               		</div>
				</li>
			  </ul>
          </div>
      	</div>
      	<? include 'shared/adSide.php'; ?>
    </div>
    <div class="row">
        <div class = "col-sm-12" >
          <div class="panel panel-primary">
            <div class="panel-heading">
              <h4 class="panel-title">My Offers</h4>
            </div>
            <div class = "panel-body">
                These are the trades that you have sent offers to.
            </div>
            <ul class = "list-group">                
                <li class="list-group-item">
                	<div class="row">
						<div class="col-xs-12">
							<?php echo tradeOfferBlock($db, $id, $urlValidation, $yourTrades);?>
						</div>
               		</div>
				</li>
			  </ul>
          </div>
      	</div>
    </div>
</div>
<?php include 'shared/footer.php';
 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getMyTrades($db_New, $id, $urlValidation, &$yourTrades, $whichProfile, $whichDB) {
	$query = "SELECT num, lvl, shiny, currentTrainer, uniqueID, nickname, myTag, m1, m2, m3, m4, item, gender, sndCost, happy FROM ptdtrad_ptd2_trading.ptd3_trade_pokes WHERE currentTrainer = ? ORDER BY num DESC";
	$result = $db_New->prepare($query);
	$result->bind_param("i", $id);
	$result->execute();
	$result->store_result();
	$hmp = $result->affected_rows;
	//$yourTradesInfo = array();
	$result->bind_result($pokeNum, $pokeLevel,$pokeShiny, $currentTrainer, $tradeID, $pokeNickname, $myTag, $m1, $m2, $m3, $m4, $pokeItem, $pokeGender, $sndCost, $pokeHoF);
	if ($hmp == 0) {
		return 'You have no Pokémon up for trade. Go to the Create Trade Section to put some Pokémon up for trade.';
	}else{
		$tradeInfo = array();
		$requestInfo = array();
		for ($i=1; $i<=$hmp; $i++) {
			$result->fetch();
			array_push($yourTrades, $tradeID);
			$query2 = "SELECT distinct offerID FROM ptdtrad_ptd2_trading.ptd3_trade_request WHERE tradePokeID = ?";
			$result2 = $db_New->prepare($query2);
			$result2->bind_param("s", $tradeID);
			$result2->execute();
			$result2->store_result();
			$hmr = $result2->affected_rows;
			$tradeInfo[$tradeID] = array("num"=>$pokeNum, "lvl"=>$pokeLevel, "shiny"=>$pokeShiny, "nickname"=>$pokeNickname, "m1"=>$m1, "m2"=>$m2, "m3"=>$m3, "m4"=>$m4, "hmr"=>$hmr, "tradeID"=>$tradeID, "gender"=>$pokeGender, "item"=>$pokeItem, "cost"=>$sndCost, "HoF"=>$pokeHoF);
			$requestInfo[$tradeID] = $hmr;
			$result2->free_result();
			$result2->close();
		}
		arsort($requestInfo); //Sorting by which has the most request first
		foreach ($requestInfo as $x=>$x_value) {
			$pokeNickname = $tradeInfo[$x]["nickname"];
			$pokeLevel = $tradeInfo[$x]["lvl"];
			$pokeShiny = $tradeInfo[$x]["shiny"];
			$m1 = $tradeInfo[$x]["m1"];
			$m2 = $tradeInfo[$x]["m2"];
			$m3 = $tradeInfo[$x]["m3"];
			$m4 = $tradeInfo[$x]["m4"];
			$pokeNum = $tradeInfo[$x]["num"];
			$hmr = $x_value;
			$tradeID = $x;
			$pokeGender = $tradeInfo[$x]["gender"];
			$pokeItem = $tradeInfo[$x]["item"];
			$sndCost = $tradeInfo[$x]["cost"];
			$pokeHoF = $tradeInfo[$x]["HoF"];
			block_poke_my_trade($tradeID, $pokeNum, $pokeNickname, $m1, $m2, $m3, $m4, $pokeLevel, $pokeShiny, $pokeItem, $myTag, $pokeHoF, $pokeGender, $urlValidation, $hmr, $whichProfile, $whichDB);
		}
	}
	$result->free_result();
	$result->close();
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function tradeOfferBlock($db_New,$id, $urlValidation, &$yourTrades){
	$haveRequest = false;
	$hmp = count($yourTrades);
	$offerIDList = array();
	for ($i=0; $i<$hmp; $i++) {
		$currentTrade = $yourTrades[$i];
		$query2 = "SELECT offerID FROM ptdtrad_ptd2_trading.ptd3_trade_request WHERE requestPokeID = ?";
		$result2 = $db_New->prepare($query2);
		$result2->bind_param("s", $currentTrade);
		$result2->execute();
		$result2->store_result();
		$hmr = $result2->affected_rows;
		$result2->bind_result($offerID);
		for ($b=1; $b<=$hmr; $b++) {
			$result2->fetch();
			$original = true;
			$haveRequest = true;
			for ($c=0; $c<count($offerIDList); $c++) {
				$oldOfferID = $offerIDList[$c];
				if ($oldOfferID == $offerID) {
					$original = false;
					break;
				}
			}
			if ($original == true) {
				array_push($offerIDList, $offerID);
			}
		}
		$result2->free_result();
		$result2->close();
	}
	$db = connect_To_Original_Database();
	for ($b=0; $b<count($offerIDList); $b++) {
		$offerID = $offerIDList[$b];
		echo show_Offers($db_New, $db, $offerID, $urlValidation);
	}
	$db->close();
	if ($haveRequest == false) {
		return 'You have not made any offers.';
	}
}	
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function show_Offers($db_New, $db, $offerID, $urlValidation) {
	$query2 = "SELECT tradePokeID, requestID FROM ptdtrad_ptd2_trading.ptd3_trade_request WHERE offerID = ?";
	$result2 = $db_New->prepare($query2);
	$result2->bind_param("s", $offerID);
	$result2->execute();
	$result2->store_result();
	$hmr = $result2->affected_rows;
	$result2->bind_result($tradePokeID, $requestID);
	$result2->fetch();
	$result2->free_result();
	$result2->close();
	$query = "SELECT num, lvl, shiny, currentTrainer, uniqueID, nickname, myTag, gender, happy, m1, m2, m3, m4 FROM ptdtrad_ptd2_trading.ptd3_trade_pokes WHERE uniqueID = ?";
	$result = $db_New->prepare($query);
	$result->bind_param("s", $tradePokeID);
	$result->execute();
	$result->store_result();
	$result->bind_result($pokeNum, $pokeLevel,$pokeShiny, $currentTrainer, $tradeID, $pokeNickname, $myTag, $pokeGender, $pokeHoF, $m1, $m2, $m3, $m4);
	$result->fetch();
	$result->free_result();
	$result->close();
	$pokeNickname = stripslashes($pokeNickname);
	$query = "select  accNickname, avatar1, avatar2, avatar3, whichAvatar from sndgame_ptdprofile5.poke_accounts WHERE trainerID = ?";
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
              <h4 class="panel-title">Offer to <img src="../trading_center/avatar/<?php echo ${avatar.$whichAvatar}?>.png"> <?php echo $accNickname ?> for his/her:</h4>
            </div>
            <div class = "panel-body" style="padding-left:0px;padding-right:0px;">
                <?php
					block_poke_long($tradeID, $pokeNum, $pokeNickname, $m1, $m2, $m3, $m4, $pokeLevel, $pokeShiny, $pokeItem, $myTag, $pokeHoF, $pokeGender, "offers");
				?>
            </div>
            <ul  class = "list-group">
            	<li class = "list-group-item text-center"><button type="submit" id="viewOfferBtn<?php echo $offerID?>" class="btn btn-success" data-toggle="collapse" data-target="#myOffers<?php echo $offerID ?>">View Offers</button> <button type="submit" id="removeOfferBtn<?php echo $offerID?>" class="btn btn-danger" data-toggle="collapse" data-target="#removeOfferConfirm<?php echo $offerID ?>">Remove Offer</button></li>
            	<li class = "list-group-item">
            		<div class="collapse" id="removeOfferConfirm<?php echo $offerID?>">
            			<div class="row">
            				<div class="col-xs-12 text-center" id="removeOfferText<?php echo $offerID?>">
								Are you sure you want to remove this offer?<br/>
								<button type="button" class="btn btn-success btn-md" data-toggle="collapse" data-target="#removeOfferConfirm<?php echo $offerID ?>">No</button>
								<button type="button" class="btn btn-danger btn-md" onClick="removeOffer('<?php echo $offerID?>', '<?php echo $requestID?>');return false;">Yes</button>
							</div>
						</div>
					</div>
            		<div class="collapse" id="myOffers<?php echo $offerID ?>">
						You Offered:
						<div class="row">
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
					</div>
				</li>
			</ul>
		</div>
	</div>
    <?php
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
</body>
</html>
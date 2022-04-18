<?php
session_start();
$whichProfile = $_REQUEST[ 'whichProfile' ];
$showTopAd = "yes";
$showSideAd = "yes";
$id = $_SESSION[ 'myID' ];
$currentSave = $_SESSION[ 'currentSave3' ];
$loggedIn = true;
$pageTitle = "Mystery Gift";
require '../moveList.php';
include 'shared/basic.php';
include 'shared/cookies.php';

$db = connect_To_Trading_Database();

$urlValidation = "whichProfile=" . $whichProfile;

//Mystery Gift Code
$today = date("Y-m-d");
$query = "SELECT num, m1, m2, m3, m4, item, shiny, name FROM ptdtrad_ptd2_trading.ptd_3_mysterygift WHERE date <= ? ORDER BY DATE DESC LIMIT 1";
$result = $db->prepare($query);
$result->bind_param("s", $today);
$result->execute();
$result->store_result();
$hmr = $result->affected_rows;
$result->bind_result($pokeNum, $m1, $m2, $m3, $m4, $itemSelection, $shiny, $pokeName);
$result->fetch();
$result->free_result();
$result->close();
$db->close();

$trainerBasedGift = false;
//Handle Elemental Based on Trainer ID
if ($shiny == 99) {
	$trainerBasedGift = true;
	$idString = (string)$id;
	$idLength = strlen($idString);
	$lastDigit = $idString[$idLength-1];
	$secondLastDigit = "0";
	if ($idLength > 1) {
		$secondLastDigit = $idString[$idLength-2];
	}
	
	$lastInt = (int)$lastDigit;
	$secondInt = (int)$secondLastDigit;
	if ($lastInt == 9) {
		$lastInt = 0;
	}
	
	if ($secondInt >= 5) {
		if ($lastInt == 0) {
			$shiny = 3; //Grass
		}elseif ($lastInt == 1) {
			$shiny = 4; //Poison
		}elseif ($lastInt == 2) {
			$shiny = 5; //Water
		}elseif ($lastInt == 3) {
			$shiny = 6; //Fire
		}elseif ($lastInt == 4) {
			$shiny = 7; //Normal
		}elseif ($lastInt == 5) {
			$shiny = 8; //Flying
		}elseif ($lastInt == 6) {
			$shiny = 9; //Bug
		}elseif ($lastInt == 7) {
			$shiny = 10; //Ghost
		}elseif ($lastInt == 8) {
			$shiny = 11; //Steel
		}
	}else{
		if ($lastInt == 0) {
			$shiny = 12; //Rock
		}elseif ($lastInt == 1) {
			$shiny = 13; //Electric
		}elseif ($lastInt == 2) {
			$shiny = 14; //Ice
		}elseif ($lastInt == 3) {
			$shiny = 15; //Fighting
		}elseif ($lastInt == 4) {
			$shiny = 16; //Ground
		}elseif ($lastInt == 5) {
			$shiny = 17; //Dragon
		}elseif ($lastInt == 6) {
			$shiny = 18; //Dark
		}elseif ($lastInt == 7) {
			$shiny = 19; //Psychic
		}elseif ($lastInt == 8) {
			$shiny = 21; //Fairy
		}
	}
}

$shinyName = get_Shiny_Name($shiny);
$shinyName = ucfirst($shinyName); //First letter capitol
//$pokeName = "Eevee";

if ($itemSelection == -1) {
	$itemSelection = 100;
}



include 'shared/head.php';
?>
<!--<script src="scripts/myTrades.js"></script>-->
<body>
<?php 
	if ($id === 1) {
		echo "'<script>console.log(\"ID ($idString) LastInt ($lastInt) SecondInt ($secondInt)\")</script>'";
	}
	include 'shared/navbar.php'; ?>
<div class="container-fluid">
	<div class = "row">
        <div class = "col-sm-12" style="padding-top: 70px">
        	<ol class="breadcrumb">
            <li><a href="main.php?<?php echo $urlValidation ?>">Home</a></li>
            <li class="active">Mystery Gift</li>
            </ol>
        </div>
    </div>
    <? include 'shared/adTop.php'; ?>
    <div class="row">
        <div class = "col-lg-9 col-md-8 col-sm-7 col-xs-12" >
          <div class="panel panel-primary">
            <div class="panel-heading">
              <h4 class="panel-title">Mystery Gift</h4>
            </div>
            <div class = "panel-body">
              	<div class="row">
              		<div class="col-xs-12 text-center">
              			<img src="images/<?php echo $pokeName?>.png" class="img-responsive" style="display:inline-block">
					</div>
				</div>
               <div class="row">
               	<div class="col-xs-12 text-center">
               	<?php if ($trainerBasedGift == false) { ?>
						<h1>New Beginnings Giveaway!</h1>
               	<p>Get a different Elemental <?php echo $pokeName?> everyday for a week! Today's giveaway is <?php echo "$shinyName $pokeName!"?></p>
				<?php }else{ ?>
				<h1>Sam's Birthday Giveaway!</h1>
               	<p>Get an Elemental <?php echo $pokeName?> based on your Trainer ID! Your giveaway is <?php echo "$shinyName $pokeName!"?> Trade with other trainers to collect all the elements!</p>
				<?php } ?>
				   </div>
				</div>
            </div>
            <div class="row">
				<div class="col-lg-4 col-md-3"></div>
			<div class="col-xs-12 col-lg-4 col-md-6">
				<?php block_poke_long('1234', $pokeNum, $pokeName, $m1, $m2, $m3, $m4, 1, $shiny,
		$itemSelection, 'n', 0, -1, "top"); ?>
			</div>
			<div class="col-lg-4 col-md-3"></div>
		</div>
         <div class="row">
			<div class="col-sm-12 text-center">
				<p><a href="mysteryGiftReceived.php?<?php echo $urlValidation?>" class="btn btn-success">Claim your Mystery Gift!</a></p>
			</div>
		</div>
          </div>
      	</div>
      	<? include 'shared/adSide.php'; ?>
    </div>
</div>
<?php include 'shared/footer.php';
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
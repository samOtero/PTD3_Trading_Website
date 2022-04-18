<?php
session_start();
$whichProfile = $_REQUEST['whichProfile'];
$showTopAd = "yes";
$showSideAd = "yes";
$id = $_SESSION['myID'];
$currentSave = $_SESSION['currentSave3'];
$loggedIn = true;
$pageTitle = "Trade Created";
require '../moveList.php';
include 'shared/basic.php';
include 'shared/cookies.php';

$db = connect_To_Original_Database();

$urlValidation = "whichProfile=".$whichProfile;

//Trade Me Code
$whichDB = $profileInfo[3]; //get Which DB the Pokemon are stored in for this profile
$pokeId = $_REQUEST['pokeID'];
if (!isset($pokeId)) {
	redirect_To_NotAllowed();
}

//Check if the pokemon belongs to you
$query = "SELECT num, lvl, shiny, originalOwner, uniqueID, nickname, myTag, m1, m2, m3, m4, gender, item, happy, extra1, extra2, exp FROM sndgame_ptd3_basic.trainerPokemons".$whichDB." WHERE trainerID = ? AND whichProfile = ? AND uniqueID = ?";
$result = $db->prepare($query);
$result->bind_param("iii", $id, $whichProfile, $pokeId);
$result->execute();
$result->store_result();
$hmp = $result->affected_rows;
$result->bind_result($pokeNum, $pokeLevel,$pokeShiny, $originalOwner, $pokeID, $pokeNickname, $myTag, $move1, $move2, $move3, $move4, $pokeGender, $pokeItem, $pokeHoF, $pokeExtra1, $pokeExtra2, $pokeExp);
if ($result->affected_rows == 0) {
	$result->free_result();
	$result->close();
	redirect_To_NotAllowed();
}else{
	$result->fetch();
}
$result->free_result();
$result->close();

//Update your currentSave to prevent cheating, will redirect to saved outside page
$currentSave = update_Current_Save($db, $id, $currentSave);

$pokeNickname = strip_tags($pokeNickname);

//Start Transaction for removing the pokemon from your account to the trading center
$transactionFlag = true;
$db->autocommit(false);
$query = "DELETE FROM sndgame_ptd3_basic.trainerPokemons".$whichDB." WHERE uniqueID = ?";
$result = $db->prepare($query);
$result->bind_param("i", $pokeId);
if (!$result->execute()) {
	$transactionFlag = false;
}
$result->close();
$dbTrading = connect_To_Trading_Database();
$dbTrading->autocommit(false);
$pokeUnique = uniqid(true);
$newTime = date('Y-m-d');
$adoptNowPrice = 0;
$allowHack = 0;
$hasRequest = 0; 
//check to see if there is request here
if (have_Request() == true) {
	$hasRequest = 1;
}
if (isset($_REQUEST['adoptSel'])) { //GET ADOPT NOW PRICE FROM REQUEST
	$adoptNowPrice = $_REQUEST['adoptSel'];
}
if (isset($_REQUEST['hackSel'])) { //GET HACK REQUEST
	$allowHack = $_REQUEST['hackSel'];
}

//Protect against hack of getting free snd coins
if ($adoptNowPrice < 0) {
	$adoptNowPrice = 0;
}

$query = "INSERT INTO ptd3_trade_pokes (num, lvl, exp, shiny, nickname, m1, m2, m3, m4, item, uniqueID, originalTrainer, currentTrainer, myTag, gender, sndCost, happy, extra1, extra2, hasRequest, allowHack) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$result = $dbTrading->prepare($query);
$result->bind_param("iiiisiiiiisiisiiiiiii", $pokeNum, $pokeLevel, $pokeExp, $pokeShiny, $pokeNickname, $move1, $move2, $move3, $move4, $pokeItem, $pokeUnique, $originalOwner, $id, $myTag, $pokeGender, $adoptNowPrice, $pokeHoF, $pokeExtra1, $pokeExtra2, $hasRequest, $allowHack);
if (!$result->execute()) {
	$transactionFlag = false;
}
$result->close();

//DO REQUEST SETUP HERE
$content = trade_Request_Setup($pokeUnique, $dbTrading);
if ($content == "fail") {
	$transactionFlag = false;
}
//END REQUEST SETUP

if ($transactionFlag == true) {
	$db->commit();
	$dbTrading->commit();
}else{
	$db->rollback();
	$dbTrading->rollback();
}
$db->autocommit(true);
$dbTrading->autocommit(true);
$db->close();
$dbTrading->close();

if ($transactionFlag == false) {
	redirect_To_Error();
}
//End Transaction

include 'shared/head.php';
?>
<script src="scripts/tradeSetup.js"></script>
<body>
<? include 'shared/navbar.php'; ?>
<div class="container-fluid">
	<div class = "row">
        <div class = "col-sm-12" style="padding-top: 70px">
        	<ol class="breadcrumb">
            <li><a href="main.php?<?php echo $urlValidation ?>">Home</a></li>
            <li><a href="createTrade.php?<?php echo $urlValidation ?>">Create Trade</a></li>
            <li class="active">Trade Created</li>
            </ol>
        </div>
    </div>
    <? include 'shared/adTop.php'; ?>
    <div class="row">
    	<div class = "col-sm-12">
        <?php block_poke_trade_setup($pokeID, $pokeNum, $pokeNickname, $move1, $move2, $move3, $move4, $pokeLevel, $pokeShiny,
		$pokeItem, $myTag, $pokeHoF, $pokeGender); ?>
        </div>
    </div>
    <div class="row">
        <div class = "col-lg-9 col-md-8 col-sm-7 col-xs-12" >
          <div class="panel panel-primary">
            <div class="panel-heading">
              <h4 class="panel-title">Trade Created Summary</h4>
            </div>
            <div class = "panel-body">
                Your Pokémon is now available for trade! Here is its unique Trade number: <?php echo $pokeUnique?>.
            </div>
            <ul class = "list-group">
            	<!--ADOPT PRICE SECTION-->
                <li class="list-group-item">            
                    <h4>Adopt Now Price: <?php echo $adoptNowPrice?> Snd Coins</h4>
                </li>
                <!--END ADOPT PRICE SECTION-->
                <!--ALLOW HACK SECTION-->
                <li class="list-group-item">            
                    <h4>Allow Hacked Offers: <?php 
					if ($allowHack == 0) {
						echo "No";
					}else{
						echo "Yes";
					}
					?></h4>
                </li>
                <!--END ALLOW HACK SECTION-->            
            	<!--YOUR REQUEST SECTION-->
                <li class="list-group-item">            
                    <h4>Your Request for this Trade:</h4>
                    <?php echo $content?>
                </li>
                <!--END YOUR REQUEST SECTION-->
            </ul>
          </div>
      	</div>
      	<? include 'shared/adSide.php'; ?>
    </div>
</div>
<?php include 'shared/footer.php';
////////////////////////////////////////////////////////////////////////////////////////////////////////
function have_Request() {
	$tradeMadeRequest = false;
	$maxRequestNum = 3;
	 $maxPokePerRequest = 6;	 
	 for ($z=1; $z<=$maxRequestNum; $z++) {
		 for ($i=1; $i<=$maxPokePerRequest; $i++) {
			 $num = $_REQUEST['poke'.$i.'_'.$z];
			 if ( empty($num) || $num == -1) {
				 continue;
			 }
			 $tradeMadeRequest = true;
			 break;
		 }
		 if ($tradeMadeRequest == true) {
		 	break;
		 }
	 }
	return $tradeMadeRequest;
}
////////////////////////////////////////////////////////////////////////////////////////////////////////
	function trade_Request_Setup($pokeUnique, $db) {
	 $madeRequest = false;
	 $content = "";
	 $maxRequestNum = 3;
	 $maxPokePerRequest = 6;
	 $thisRequestStarted = false;
	 $startedARequest = false;
	 
	 for ($z=1; $z<=$maxRequestNum; $z++) {
		 $thisRequestStarted = false;
		 for ($i=1; $i<=$maxPokePerRequest; $i++) {
			 $num = $_REQUEST['poke'.$i.'_'.$z];
			 if ( empty($num) || $num == -1) {
				 continue;
			 }
			 
			 $shiny = $_REQUEST['type'.$i.'_'.$z];
			 $gender = $_REQUEST['gender'.$i.'_'.$z];
		 	$query = "INSERT INTO ptd3_trade_wants (tradePokeID, num, shiny, gender, whichRequest) VALUES (?,?,?,?,?)";
			$result = $db->prepare($query);
			$result->bind_param("siiii", $pokeUnique, $num, $shiny, $gender, $z);
			if (!$result->execute()) {
				return "fail";
			}
			if ($thisRequestStarted == false) { //IF THIS IS THE FIRST ENTRY IN THE CURRENT REQUEST
				$thisRequestStarted = true;
				$startedARequest = true;
				if ($madeRequest == true) { //BETWEEN REQUEST PUT AN OR
					$content .= '<div class="row">';
					$content .= '<div class="col-sm-6">';
					$content .= '<p style="font-weight:bold; text-align:center;">OR</p>';
					$content .= '</div>';
					$content .= '</div>';
				}
				$madeRequest = true;
				//TOP CONTENT
				$content .= '<div class="row">';
				$content .= '<div class="col-sm-6">';
				$content .= '<div class="panel panel-success" id="requestPanel'.$z.'">';
				$content .= '<div class="panel-heading"><h4>Request #'.$z.'</h4></div>';
                $content .= '<div class="list-group">';
			}
			//Pokemon Content
			//$content .= '<p>'.$poke.$genderIcon.' - Lvl ('.$myLevel.') '.$isShiny.'</p>';
			$content .= '<div class="list-group-item list-group-item-success">';
			$content .= '<div class="row">';
            $content .= '<div class="pokeblock panel-group col-xs-12" style="margin-bottom:0px;" id="pokeRequest'.$i.'_'.$z.'">';
            $content .= '<div class = "panel panel-primary" id="pokePanel'.$i.'_'.$z.'">';
            $content .= '<div class = "panel-heading" id="panel'.$i.'_'.$z.'">';
            $content .= '</div></div></div></div></div>';
			$content .= '<script>update_Poke_Request_Info('.$num.', '.$gender.', 1, -1, "'.$shiny.'", '.$i.', '.$z.');</script>'; //Updates the request with the right info
		 }
		 if ($startedARequest == true) {//IF THERE WAS A PREVIOUS REQUEST THAT NEEDS AN END
			$content .= '</div></div></div></div>';
			$startedARequest = false;
		}
	 }
	 if (!$madeRequest) {
		 $content .= '<p>You made no request for this trade.</p>';
	 }
	return $content;
 }
?>
</body>
</html>
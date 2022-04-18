<?php
session_start();
$whichProfile = $_REQUEST['whichProfile'];
$showTopAd = "yes";
$showSideAd = "yes";
$id = $_SESSION['myID'];
$currentSave = $_SESSION['currentSave3'];
$loggedIn = true;
$pageTitle = "Redeem Reward";
require '../moveList.php';
require '../breedingList.php';
include 'shared/basic.php';
include 'shared/cookies.php';
include 'shared/genderCheck.php';

$db = connect_To_Original_Database();

$urlValidation = "whichProfile=".$whichProfile;

//Create Trade Code
$whichDB = $profileInfo[3]; //get Which DB the Pokemon are stored in for this profile

$whichFamily = $_REQUEST['family'];

include 'shared/head.php';
?>
<body>
<? include 'shared/navbar.php'; ?>
<div class="container-fluid">
	<div class = "row">
        <div class = "col-sm-12" style="padding-top: 70px">
        	<ol class="breadcrumb">
            <li><a href="main.php?<?php echo $urlValidation ?>">Home</a></li>
            <li><a href="elementalLab.php?<?php echo $urlValidation ?>">Elemental Lab</a></li>
            <li class="active">Redeem Reward</li>
            </ol>
        </div>
    </div>
    <? include 'shared/adTop.php'; 
	
	//Get family list from your profile
	
	$familyList = array();
	
	
	
	$db2 = connect_To_Trading_Database();
	
	//Get current collected families
	$query = "SELECT pokeFamily FROM ptdtrad_ptd2_trading.ptd3_lab_records where trainerID = ? ORDER BY pokeFamily";
	$result = $db2->prepare($query);
	$result->bind_param("i", $id);
	$result->execute();
	$result->store_result();
	$hmp = $result->affected_rows;
	$result->bind_result($pokeFamily);
	for ($i=1; $i<=$hmp; $i++) {
		$result->fetch();
		for ($b=3; $b<=21; $b++) {
			if ($b != 20)
				$familyList = add_To_FamilyList($familyList, $pokeFamily, $b, 1);
		}
	}
	$result->free_result();
	$result->close();
				   
	$nextCost = $hmp +1; //Reward cost
	
	//Get family list from your profile
	$query = "SELECT num, lvl, shiny, originalOwner, uniqueID, nickname, myTag, m1, m2, m3, m4, gender, item, happy FROM sndgame_ptd3_basic.trainerPokemons".$whichDB." WHERE trainerID = ? and shiny > 2 ORDER BY num, lvl";
	$result = $db->prepare($query);
	$result->bind_param("i", $id);
	$result->execute();
	$result->store_result();
	$hmp = $result->affected_rows;
	$result->bind_result($pokeNum, $pokeLevel,$pokeShiny, $originalOwner, $pokeID, $pokeNickname, $myTag, $move1, $move2, $move3, $move4, $pokeGender, $pokeItem, $pokeHoF);
	for ($i=1; $i<=$hmp; $i++) {
		$result->fetch();
		$familyList = add_To_FamilyList($familyList, $pokeNum, $pokeShiny, 0);
		//echo block_poke_trade_setup($pokeID, $pokeNum, $pokeNickname, $move1, $move2, $move3, $move4, $pokeLevel, $pokeShiny,
		//$pokeItem, $myTag, $pokeHoF, $pokeGender);
	}
	$result->free_result();
	$result->close();
				   
	$getReward = 0;
	
	$totalRequired = 18; //How many different elementals do you need
				   
	$familyCount = count($familyList);
	for ($i = 0; $i<$familyCount; $i++) {
		$currentFamily = $familyList[$i];
		if ($currentFamily[0] == $whichFamily) { //This is the family we are interest in
			if ($currentFamily[3] === 1) { //If they are already redeemed
				$getReward = 3;
			}else if (count($currentFamily[1]) >= $totalRequired) { //You have enough!
				$getReward = 1;
			}else{ //Didn't have enough... how did you get here...!!!
				$getReward = 0;
			}
			break; //We don't need to check anything else
		}
	}
	//Check to see if there is a reward available
	if ($getReward === 1) {
		$query = "SELECT pokeName, pokeNum, elementNum FROM ptdtrad_ptd2_trading.ptd3_rewards WHERE type = 'lab' AND cost = $nextCost";
		$result = $db2->prepare($query);
		$result->execute();
		$result->store_result();
		$hmp = $result->affected_rows;
		$result->bind_result($pokeName, $pokeNum, $elementNum);
		if ($hmp > 0) {
			$result->fetch();
			//Get moves from basic evolution
			$eggInfo = get_New_Egg_Moves($pokeNum);
			$m1 = $eggInfo[0];
			$m2 = $eggInfo[1];
			$m3 = $eggInfo[2];
			$m4 = $eggInfo[3];
			$getReward = 1;
		}else{
			$getReward = 2;
		}
		$result->free_result();
		$result->close();
	}
	
	//If there was a reward available then redeem family and send pokemon to your pickup area
	if ($getReward === 1) {
		$itemSelection = 100;
		$exp = 0;
		$lvl = 1;
		$originalTrainer = -4;
		$myTag = 'n';
		
		$hof = 0;
		$extra1 = 0;
		$extra2 = 0;
		
		$query = "INSERT INTO ptdtrad_ptd2_trading.ptd3_pickup_pokes (num, lvl, exp, shiny, nickname, m1, m2, m3, m4, item, originalTrainer, currentTrainer, myTag, gender, happy, extra1, extra2) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		
		//Add three pairs
		for ($z = 1; $z<=3; $z++) {
			$gender = 1;
			//Change Gender as necessary. Ex - Mew will always be -1
			$gender = checkGender($pokeNum, $gender);
			$result = $db2->prepare($query);
			$result->bind_param("iiiisiiiiiiisiiii", $pokeNum, $lvl, $exp, $elementNum, $pokeName, $m1, $m2, $m3, $m4, $itemSelection, $originalTrainer, $id, $myTag, $gender, $hof, $extra1, $extra2);
			if (!$result->execute()) {
				redirect_To_Error();
				exit;
			}

			$gender = 2;
			//Change Gender as necessary. Ex - Mew will always be -1
			$gender = checkGender($pokeNum, $gender);
			$result = $db2->prepare($query);
			$result->bind_param("iiiisiiiiiiisiiii", $pokeNum, $lvl, $exp, $elementNum, $pokeName, $m1, $m2, $m3, $m4, $itemSelection, $originalTrainer, $id, $myTag, $gender, $hof, $extra1, $extra2);
			if (!$result->execute()) {
				redirect_To_Error();
				exit;
			}
		}
		
		$result->close();
		$dateNow = date("Y-m-d H:i:s");
		$query = "INSERT INTO ptdtrad_ptd2_trading.ptd3_lab_records (trainerId, pokeFamily, dateTime) VALUES ($id, $whichFamily,'$dateNow')";
		$result = $db2->prepare($query);
		$result->execute();
		$result->close();
	}
	
	
	?>
    <div class="row">
        <div class = "col-lg-9 col-md-8 col-sm-7 col-xs-12">
          <div class="panel panel-primary">
            <div class="panel-heading">
              <h4 class="panel-title">Redeem Reward</h4>
            </div>
            <div class = "panel-body">
				
           <?php
				//Get next reward!
			if ($getReward === 1) {
				echo "<p>You claimed $pokeName! It is waiting for you in the pickup area on the \"Home\" section.</p>";
				block_poke_long('1234', $pokeNum, $pokeName, $m1, $m2, $m3, $m4, 1, $elementNum, 0, 'n', 0, -1, "top");
			}else if ($getReward == 2) {
				echo "<p>You have collected all the current rewards! More will be added in the near future, stay tuned!</p>";
			}else if ($getReward == 3) {
				echo "<p>You have already collected your reward for this family.</p>";
			}else{
				echo "<p>Nice try... but you don't have enough elementals to redeem this family.</p>";
			}			
				?>
            </div>
          </div>
      </div>
      <? include 'shared/adSide.php'; ?>
    </div>
    
    <?php
	$db->close();
	$db2->close();

//Add pokemon family info if we don't already have it recorded
function add_To_FamilyList($familyList, $pokeNum, $pokeShiny, $isRedeemed) {
	$allFamilyList = get_Family($pokeNum);
	$familyNum = $allFamilyList[0];
	$totalFamilyEntries = count($familyList);
	$addEntry = true;
	for ($i=0; $i<$totalFamilyEntries; $i++) {
		$current = $familyList[$i];
		if ($current[0] == $familyNum) {
			$currentShiny = $current[1];
			$addShinyEntry = true;
			$totalShinyEntries = count($currentShiny);
			for ($b = 0; $b<$totalShinyEntries; $b++) {
				if ($currentShiny[$b] == $pokeShiny) {
					$addShinyEntry = false;
					break;
				}
			}
			if ($addShinyEntry == true) {
				array_push($familyList[$i][1], $pokeShiny);
				
			}
			$addEntry = false;
			break;
		}
	}
	if ($addEntry == true) {
		$shinyList = array($pokeShiny);
		$familyEntryList = array($familyNum, $shinyList, $allFamilyList, $isRedeemed);
		array_push($familyList, $familyEntryList);
	}
	return $familyList;
}
?>
</div>
<? include 'shared/footer.php'; ?>
</body>
</html>
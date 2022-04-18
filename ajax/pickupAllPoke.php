<?php
session_start();
//check for your id
$id = $_SESSION['myID'];
$currentSave = $_SESSION['currentSave3'];
if (!isset($currentSave) || !isset($id)) {
	echo 'Error, please try again.';
	exit;
}

//check for DB num
$whichDB = $_REQUEST['whichDB'];
if (!isset($whichDB)) {
	echo 'Error, no DB present.';
	exit;
}

//check for profile
$whichProfile = $_REQUEST['whichProfile'];
if (!isset($whichProfile)) {
	echo 'Error, no profile present.';
	exit;
}

include '../shared/database.php';
do_The_Stuff();
function do_The_Stuff() {
	global $id, $whichProfile, $whichDB;
	$db_New = connect_To_Trading_Database();
	
	$query = "SELECT num, lvl, exp, shiny, nickname, m1, m2, m3, m4, item, originalTrainer, myTag, gender, happy, extra1, extra2, pickupUniqueID FROM ptdtrad_ptd2_trading.ptd3_pickup_pokes WHERE currentTrainer = ?";
	$result = $db_New->prepare($query);
	$result->bind_param("i", $id);
	$result->execute();
	$result->store_result();
	$result->bind_result($pokeNum, $pokeLevel, $pokeExp, $pokeShiny, $pokeNickname, $m1, $m2, $m3, $m4, $item, $originalOwner, $myTag, $pokeGender, $pokeHoF, $extra1, $extra2, $pokeID);
	$hmpExtra = $result->affected_rows;
	if ($hmpExtra == 0) {
		$result->free_result();
		$result->close();
		$db_New->close();
		echo 'Error: You cannot call back this Pokémon.';
		return;
	}
	
	$db_New->autocommit(false);
	$transactionFlag = true;
	$dbActual = connect_To_Original_Database();
	$dbActual->autocommit(false);
	$pos = 999;
	
	//Delete query setup
	$queryDelete = "DELETE FROM ptdtrad_ptd2_trading.ptd3_pickup_pokes WHERE pickupUniqueID = ?";
	$resultDelete = $db_New->prepare($queryDelete);
	
	//Add to profile query setup
	$queryInsert = "INSERT INTO sndgame_ptd3_basic.trainerPokemons$whichDB (num, lvl, exp, shiny, nickname, m1, m2, m3, m4, item, originalOwner, trainerID, whichProfile, myTag, gender, happy, extra1, extra2, pos) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
	$resultInsert = $dbActual->prepare($queryInsert);
	
	for ($i=1; $i<=$hmpExtra; $i++) {
		//Get next pickup pokemon
		$result->fetch();
		
		//Delete from pickup table
		$resultDelete->bind_param("s", $pokeID);
		if (!$resultDelete->execute()) {
			$transactionFlag = false;
		}
		//Do evolutions, and get rid of uneeded items
		include '../../tradeEvolution2.php'; //handles the evolutions, same file as ptd2
		
		//Insert result into profile
		$resultInsert->bind_param("iiiisiiiiiiiisiiiii", $pokeNum, $pokeLevel, $pokeExp, $pokeShiny, $pokeNickname, $m1, $m2, $m3, $m4, $item, $originalOwner, $id, $whichProfile, $myTag, $pokeGender, $pokeHoF, $extra1, $extra2, $pos);
		if (!$resultInsert->execute()) {
			$transactionFlag = false;
		}
		
	}
	//Close results
	$resultInsert->close();
	$resultDelete->close();
	$result->free_result();
	$result->close();
	
	//Finally resolve transaction
	if ($transactionFlag == true) {
		$db_New->commit();
		$dbActual->commit();
	}else{
		$db_New->rollback();
		$dbActual->rollback();
	}
	$db_New->autocommit(true);
	$dbActual->autocommit(true);
	$db_New->close();
	$dbActual->close();
}
?>


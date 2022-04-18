<?php
session_start();
//check for your id
$id = $_SESSION['myID'];
$currentSave = $_SESSION['currentSave3'];
if (!isset($currentSave) || !isset($id)) {
	echo 'Error, please try again.';
	exit;
}
//check for poke ID
$pokeID = $_REQUEST['id'];
if (!isset($pokeID)) {
	echo 'Error, no pokeID present.';
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
	global $id, $pokeID, $whichProfile, $whichDB;
	$db_New = connect_To_Trading_Database();
	
	$query = "SELECT num, lvl, exp, shiny, nickname, m1, m2, m3, m4, item, originalTrainer, myTag, gender, happy, extra1, extra2 FROM ptdtrad_ptd2_trading.ptd3_pickup_pokes WHERE currentTrainer = ? AND pickupUniqueID = ?";
	$result = $db_New->prepare($query);
	$result->bind_param("is", $id, $pokeID);
	$result->execute();
	$result->store_result();
	$result->bind_result($pokeNum, $pokeLevel, $pokeExp, $pokeShiny, $pokeNickname, $m1, $m2, $m3, $m4, $item, $originalOwner, $myTag, $pokeGender, $pokeHoF, $extra1, $extra2);
	if ($result->affected_rows == 0) {
		$result->free_result();
		$result->close();
		$db_New->close();
		echo 'Error: You cannot call back this Pokémon.';
		return;
	}
	$result->fetch();
	$result->free_result();
	$result->close();
	$db_New->autocommit(false);
	$transactionFlag = true;
	$query = "DELETE FROM ptdtrad_ptd2_trading.ptd3_pickup_pokes WHERE pickupUniqueID = ?";
	$result = $db_New->prepare($query);
	$result->bind_param("s", $pokeID);
	if (!$result->execute()) {
		$transactionFlag = false;
	}
	$result->close();
	include '../../tradeEvolution2.php'; //handles the evolutions, same file as ptd2
	$dbActual = connect_To_Original_Database();
	$dbActual->autocommit(false);
	$pos = 999;
	$query = "INSERT INTO sndgame_ptd3_basic.trainerPokemons$whichDB (num, lvl, exp, shiny, nickname, m1, m2, m3, m4, item, originalOwner, trainerID, whichProfile, myTag, gender, happy, extra1, extra2, pos) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
	$result = $dbActual->prepare($query);
	$result->bind_param("iiiisiiiiiiiisiiiii", $pokeNum, $pokeLevel, $pokeExp, $pokeShiny, $pokeNickname, $m1, $m2, $m3, $m4, $item, $originalOwner, $id, $whichProfile, $myTag, $pokeGender, $pokeHoF, $extra1, $extra2, $pos);
	if (!$result->execute()) {
		$transactionFlag = false;
	}
	$result->close();
	if ($transactionFlag == true) {
		$db_New->commit();
		$dbActual->commit();
		?>
		Pickup completed!<br/><button type="button" class="btn btn-danger btn-sm" onClick="closeBox('<?php echo $pokeID?>');return false;">Close</button>
		<?php
	}else{
		$db_New->rollback();
		$dbActual->rollback();
		echo "Error occured. Please Try Again.";
	}
	$db_New->autocommit(true);
	$dbActual->autocommit(true);
	$db_New->close();
	$dbActual->close();
}
?>


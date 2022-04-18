<?php
session_start();

//check for which profile
$whichProfile = $_REQUEST['whichProfile'];
if (!isset($whichProfile)) {
	$success = false;
	$message = 'Error, no profile present.';
	send_message($success, $message);
}
//check for your id
$id = $_SESSION['myID'];
$currentSave = $_SESSION['currentSave3'];
if (!isset($currentSave) || !isset($id)) {
	$success = false;
	$message = 'Error, no id present.';
	send_message($success, $message);
}
//check for poke ID
$tradeID = $_REQUEST['tradeID'];
if (!isset($tradeID)) {
	$success = false;
	$message = 'Error, no tradeID present.';
	send_message($success, $message);
}

//check for which db
$whichDB = $_REQUEST['whichDB'];
if (!isset($whichDB)) {
	$success = false;
	$message = 'Error, no DB present.';
	send_message($success, $message);
}
include '../shared/database.php';
$success = true;
$message = "Call back successful! <button type=\"button\" class=\"btn btn-danger btn-sm\" onClick=\"closeTrade('$tradeID');return false;\">Close</button>";
do_The_Stuff($sucess, $message);
send_message($success, $message);
function do_The_Stuff(&$success, &$message) {
	global $id, $tradeID, $whichProfile, $whichDB, $success, $message;
	$db_New = connect_To_Trading_Database();
	$query = "SELECT num, lvl, exp, shiny, nickname, m1, m2, m3, m4, item, originalTrainer, myTag, gender, happy, extra1, extra2 FROM ptdtrad_ptd2_trading.ptd3_trade_pokes WHERE currentTrainer = ? AND uniqueID = ?";
	$result = $db_New->prepare($query);
	$result->bind_param("is", $id, $tradeID);
	$result->execute();
	$result->store_result();
	$result->bind_result($pokeNum, $pokeLevel, $pokeExp, $pokeShiny, $pokeNickname, $m1, $m2, $m3, $m4, $item, $originalOwner, $myTag, $pokeGender, $pokeHoF, $extra1, $extra2);
	if ($result->affected_rows == 0) {
		$result->free_result();
		$result->close();
		$db_New->close();
		$success = false;
		$message = 'Error: You cannot call back this Pokémon.';
		return;
	}
	$result->fetch();
	$result->free_result();
	$result->close();
	$db_New->autocommit(false);
	$transactionFlag = true;
	$query = "DELETE FROM ptdtrad_ptd2_trading.ptd3_trade_pokes WHERE uniqueID = ?";
	$result = $db_New->prepare($query);
	$result->bind_param("s", $tradeID);
	if (!$result->execute()) {
		$transactionFlag = false;
	}
	$result->close();
	$query = "DELETE FROM ptdtrad_ptd2_trading.ptd3_trade_wants WHERE tradePokeID = ?";
	$result = $db_New->prepare($query);
	$result->bind_param("s", $tradeID);
	if (!$result->execute()) {
		$transactionFlag = false;
	}
	$result->close();
	$query = "DELETE FROM ptdtrad_ptd2_trading.ptd3_trade_request WHERE tradePokeID = ? OR requestPokeID = ?";
	$result = $db_New->prepare($query);
	$result->bind_param("ss", $tradeID, $tradeID);
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
	}else{
		$db_New->rollback();
		$dbActual->rollback();
		$message = "Error occured. Please Try Again.";
	}
	$db_New->autocommit(true);
	$dbActual->autocommit(true);
	$db_New->close();
	$dbActual->close();
}
function send_message($success, $message) {
	echo json_encode(array("success" => $success, "message" => $message));
	exit;
}
?>


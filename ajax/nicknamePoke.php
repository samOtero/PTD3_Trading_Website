<?php
session_start();

//check for new nickname
$newNickname = $_REQUEST['nickname'];
if (!isset($newNickname)) {
	$success = false;
	$message = 'Error, no nickname present.';
	send_message($success, $message);
	exit;
}
//check for your id
$id = $_SESSION['myID'];
$currentSave = $_SESSION['currentSave3'];
if (!isset($currentSave) || !isset($id)) {
	$success = false;
	$message = 'Error, please try again.';
	send_message($success, $message);
	exit;
}
//check for poke ID
$pokeID = $_REQUEST['id'];
if (!isset($pokeID)) {
	$success = false;
	$message = 'Error, no pokeID present.';
	send_message($success, $message);
	exit;
}

//check for which db
$whichDB = $_REQUEST['pdb'];
if (!isset($whichDB)) {
	$success = false;
	$message = 'Error, no db present.';
	send_message($success, $message);
	exit;
}

//Is nickname in the right format?
$newNickname = strip_tags($newNickname);
if (!ctype_alpha($newNickname)) {
	$success = false;
	$message = "Error, you can't use numbers or symbols for your pokémon's nickname";
	send_message($success, $message);
	exit;
}

include '../shared/database.php';
do_The_Stuff();
function do_The_Stuff() {
	global $id, $pokeID, $whichDB, $newNickname;
	$success = true;
	$message = "";
	$db = connect_To_Original_Database();
	$query = "UPDATE sndgame_ptd3_basic.trainerPokemons".$whichDB." SET nickname = ? WHERE uniqueID = ? AND trainerID = ?";
	$result = $db->prepare($query);
	$result->bind_param("sii", $newNickname, $pokeID, $id);
	$result->execute();
	if ($result->sqlstate=="00000") {
		$success = true;
		$message = "Your Pokémon's Nickname has been changed!";				
	}else {
		$success = false;
		$message = 'Error in database. Try again.';
	}
	$result->close();
	$db->close();
	send_message($success, $message);	
}
function send_message($success, $message) {
	echo json_encode(array("success" => $success, "message" => $message));
	exit;
}
?>


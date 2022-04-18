<?php
session_start();
//check for which profile
$whichProfile = $_REQUEST['pid'];
if (!isset($whichProfile)) {
	echo 'Error, no profile present.';
	exit;
}
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

//check for which db
$whichDB = $_REQUEST['pdb'];
if (!isset($whichDB)) {
	echo 'Error, no db present.';
	exit;
}
include '../shared/database.php';
do_The_Stuff();
function do_The_Stuff() {
	global $id, $pokeID, $whichProfile, $whichDB;
	$db = connect_To_Original_Database();
		
	$query = "DELETE FROM sndgame_ptd3_basic.trainerPokemons".$whichDB." WHERE uniqueID = ? AND trainerID = ?";
	$result = $db->prepare($query);
	$result->bind_param("ii", $pokeID, $id);
	if ($result->execute()) {
		?>
		Goodbye, friend!<br/><button type="button" class="btn btn-danger btn-sm" onClick="closeBox('<?php echo $pokeID?>');return false;">Close</button>
		<?php
	}else{
		echo 'Error in database. Try again.';
	}
	$result->close();
	$db->close();
}
?>


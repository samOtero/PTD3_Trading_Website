<?php
session_start();
//check for your id
$id = $_SESSION['myID'];
if (!isset($id)) {
	$success = false;
	$message = 'Error, no id present.';
	send_message($success, $message);
}
//check for offer ID
$offerID = $_REQUEST['offerID'];
if (!isset($offerID)) {
	$success = false;
	$message = 'Error, no offerID present.';
	send_message($success, $message);
}

include '../shared/database.php';
$success = true;
$message = "Offer denied! <button type=\"button\" class=\"btn btn-danger btn-sm\" onClick=\"closeOffer('$offerID');return false;\">Close</button>";
do_The_Stuff($sucess, $message);
send_message($success, $message);
function do_The_Stuff(&$success, &$message) {
	global $id, $offerID;
	$db_New = connect_To_Trading_Database();
	$query = "SELECT ptdtrad_ptd2_trading.ptd3_trade_request.requestPokeID FROM ptdtrad_ptd2_trading.ptd3_trade_request, ptdtrad_ptd2_trading.ptd3_trade_pokes WHERE ptdtrad_ptd2_trading.ptd3_trade_pokes.currentTrainer = ? AND ptdtrad_ptd2_trading.ptd3_trade_request.offerID = ? AND ptdtrad_ptd2_trading.ptd3_trade_pokes.uniqueID = ptdtrad_ptd2_trading.ptd3_trade_request.tradePokeID";
	$result = $db_New->prepare($query);
	$result->bind_param("ii", $id, $offerID);
	$result->execute();
	$result->store_result();
	$hmp = $result->affected_rows;
	if ($hmp == 0) {
		$success = false;
		$message = "Error, can't remove this offer.";
		$result->free_result();
		$result->close();
		$db_New->close();
	}else{
		$result->free_result();
		$result->close();
		$db_New->autocommit(false);
		$transactionFlag = true;
		$query = "DELETE FROM ptdtrad_ptd2_trading.ptd3_trade_request WHERE offerID = ?";
		$result = $db_New->prepare($query);
		$result->bind_param("s", $offerID);
		if (!$result->execute()) {
			$transactionFlag = false;
		}
		 $result->close();
		 if ($transactionFlag == true) {
			 $db_New->commit();
		 }else{
			 $db_New->rollback();
			 $success = false;
		$message = "Error, please try again.";
		 }
		 $db_New->autocommit(true);
		 $db_New->close();
	 }
}
function send_message($success, $message) {
	echo json_encode(array("success" => $success, "message" => $message));
	exit;
}
?>


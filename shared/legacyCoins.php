<?php

/////////////////////////////////////////////////////////////////////////////////////////////////
function giveLegacyCoin($db, $id, $amount, $transactionFlag) { //Give or take away a user's legacy coins
	$query = "SELECT coins FROM ptdtrad_ptd2_trading.ptd3_legacy_coins WHERE trainerId = $id";
	$result = $db->prepare($query);
	$result->execute();
	$result->store_result();
	$hmr = $result->affected_rows;
	$result->bind_result($coinAmount);
	$result->fetch();
	$result->free_result();
	$result->close();
	if ($hmr == 0){
		$newCoinAmount = $amount;
		if ($newCoinAmount < 0) {
			$newCoinAmount =0;
		}
		$query = "INSERT INTO ptdtrad_ptd2_trading.ptd3_legacy_coins (trainerId, coins) VALUES ($id, $newCoinAmount)";
		$result = $db->prepare($query);
		if (!$result->execute()) {
			$transactionFlag = false;
		}
		$result->close();
	}else{
		$newCoinAmount = $coinAmount + $amount;
		if ($newCoinAmount < 0) {
			$newCoinAmount =0;
		}
		$query = "UPDATE ptdtrad_ptd2_trading.ptd3_legacy_coins SET coins = $newCoinAmount WHERE trainerId = $id";
		$result = $db->prepare($query);
		if (!$result->execute()) {
			$transactionFlag = false;
		}
		$result->close();
	}
	return $transactionFlag;
}
 /////////////////////////////////////////////////////////////////////////////////////////////////
function getLegacyCoin($db, $id) { //Get a user's legacy coin count
	$query = "SELECT coins FROM ptdtrad_ptd2_trading.ptd3_legacy_coins WHERE trainerId = $id";
	$result = $db->prepare($query);
	$result->execute();
	$result->store_result();
	$hmr = $result->affected_rows;
	$result->bind_result($coinAmount);
	$result->fetch();
	$result->free_result();
	$result->close();
	if ($hmr == 0){
		return 0;
	}
	return $coinAmount;
}
 /////////////////////////////////////////////////////////////////////////////////////////////////
function hasLoggedInTodayForLegacyCoins($db, $id) { //Checks to see if you already received your Legacy coin today
	$today = date("Y-m-d");
	$query = "SELECT Id FROM ptdtrad_ptd2_trading.ptd3_legacy_coins_record WHERE trainerId = $id AND dateReceived = '$today'";
	$result = $db->prepare($query);
	$result->execute();
	$result->store_result();
	$hmr = $result->affected_rows;
	$result->bind_result($rowId);
	$result->fetch();
	$result->free_result();
	$result->close();
	if ($hmr == 0){
		$query = "INSERT INTO ptdtrad_ptd2_trading.ptd3_legacy_coins_record (trainerId, dateReceived) VALUES ($id, '$today')";
		$result = $db->prepare($query);
		$result->execute();
		$result->close();
		return false;
	}else{
		return true;
	}
}
/////////////////////////////////////////////////////////////////////////////////////////////////

 ?>
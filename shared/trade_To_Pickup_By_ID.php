<?php
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function trade_To_Pickup($db_New, $tradePokeID, $newOwner, $shelmetTrade=false, $transactionFlag=true) { //PTD3
	$query2 = "SELECT num, lvl, exp, shiny, nickname, m1, m2, m3, m4, item, originalTrainer, myTag, gender, happy, extra1, extra2 FROM ptdtrad_ptd2_trading.ptd3_trade_pokes WHERE uniqueID = ?";
	$result2 = $db_New->prepare($query2);
	$result2->bind_param("s", $tradePokeID);
	$result2->execute();
	$result2->store_result();
	$result2->bind_result($pokeNum2, $pokeLevel2, $pokeExp2, $pokeShiny2, $pokeNickname2, $m12, $m22, $m32, $m42, $item2, $originalOwner2, $myTag2, $pokeGender2, $pokeHoF2, $pokeExtra1, $pokeExtra2);
	if ($result2->affected_rows == 0) {
		$result2->close();
		$transactionFlag = false;
		return $transactionFlag;
	}
	$result2->fetch();
	$result2->free_result();
	$result2->close();
	if ($shelmetTrade == true) {
		if ($pokeNum2 == 616) {
			$pokeNum2 = 617;
			$pokeNickname2 = "Accelgor";
		}else if ($pokeNum2 == 588) {
			$pokeNum2 = 589;
			$pokeNickname2 = "Escavalier";
		}
		$pokeHoF2 = 0;
	}
	$query2 = "INSERT INTO ptdtrad_ptd2_trading.ptd3_pickup_pokes (num, lvl, exp, shiny, nickname, m1, m2, m3, m4, item, originalTrainer, currentTrainer, myTag, gender, happy, extra1, extra2) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
	$result2 = $db_New->prepare($query2);
	$result2->bind_param("iiiisiiiiiiisiiii", $pokeNum2, $pokeLevel2, $pokeExp2, $pokeShiny2, $pokeNickname2, $m12, $m22, $m32, $m42, $item2, $originalOwner2, $newOwner, $myTag2, $pokeGender2, $pokeHoF2, $pokeExtra1, $pokeExtra2);
	if (!$result2->execute()) {
		$transactionFlag = false;
		return $transactionFlag;
	}
	$result2->close();
	$query2 = "DELETE FROM ptdtrad_ptd2_trading.ptd3_trade_pokes WHERE uniqueID = ?";
	$result2 = $db_New->prepare($query2);
	$result2->bind_param("s", $tradePokeID);
	if (!$result2->execute()) {
		$transactionFlag = false;
		return $transactionFlag;
	}
	$result2->close();
	return $transactionFlag;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
<?php

/////////////////////////////////////////////////////////////////////////////////////////////////
function giveSndCoins($trainerID, $howMany, $db, $transactionFlag) {
	$query = "SELECT howManyCoins FROM sndgame_ptdprofile5.sndCoins WHERE trainerID = ?";
	$result = $db->prepare($query);
	$result->bind_param("i", $trainerID);
	$result->execute();
	$result->store_result();
	$result->bind_result($howManyCoins);	
	$hmp = $result->affected_rows;
	if ($hmp == 0) {
		$result->close();
		if ($howMany < 0) {
			$howMany = 0;
		}
		$query = "INSERT INTO sndgame_ptdprofile5.sndCoins (trainerID, howManyCoins) VALUES (?,?)";
		$result = $db->prepare($query);
		$result->bind_param("ii", $trainerID, $howMany);
		if (!$result->execute()) {
			$transactionFlag = false;
		}
		$result->close();
	}else{
		$result->fetch();
		$result->close();
		$howManyCoins += $howMany;
		if ($howManyCoins < 0) {
			$howManyCoins = 0;
		}
		$query = "UPDATE sndgame_ptdprofile5.sndCoins SET howManyCoins = ? WHERE trainerID = ?";
		$result = $db->prepare($query);
		$result->bind_param("ii", $howManyCoins, $trainerID);
		if (!$result->execute()) {
			$transactionFlag = false;
		}
		$result->close();
	}
	return $transactionFlag;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////
function getSndCoin($dbOriginal, $id) { //Get a user's snd coins count
	$query = "SELECT howManyCoins FROM sndgame_ptdprofile5.sndCoins WHERE trainerID = $id";
	$result = $dbOriginal->prepare($query);
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
///////////

 ?>
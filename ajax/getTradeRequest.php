<?php

//check for trade ID
$tradeID = $_REQUEST['tradeID'];
if (!isset($tradeID)) {
	$success = false;
	$message = 'Error, no tradeID present.';
	send_message($success, $message);
	exit;
}

include '../shared/database.php';
include '../shared/basic2.php';
do_The_Stuff();
function do_The_Stuff() {
	global $tradeID;
	$success = true;
	$message = "";
	$madeRequest = false;
	$db = connect_To_Trading_Database();
	$query = "SELECT num, shiny, gender, whichRequest FROM ptdtrad_ptd2_trading.ptd3_trade_wants WHERE tradePokeID = ? ORDER BY whichRequest";
	$result = $db->prepare($query);
	$result->bind_param("s", $tradeID);
	$result->execute();
	$result->store_result();
	$hmp = $result->affected_rows;
	$result->bind_result($num, $shiny, $gender, $whichRequest);
	$currentRequest = 1;
	$startRow = 1;
	$endRow = 2;
	$requestCount = 1;
	$message = '<div class="row"><div class="xs-col-12 text-center" style="font-weight:bold">Trade Request:</div></div>';
	for ($i=1; $i<=$hmp; $i++) {
		$result->fetch();
		$genderName = get_Gender($gender);
		$genderIcon = "";
		if ($genderName != "none") {
			$genderIcon = '<img src = "http://www.ptdtrading.com/trading_center/images/'.$genderName.'.png"/>';
		}
		//Panel Style based on Shiny
		$panelType = get_Panel_Name($shiny);
	
		//Extra Name depending on shiny
		$extraName = get_Abr_Shiny_Name($shiny);

		if ($num == 0) {
			$poke = 'Any Pokémon';
		}else{
			$poke = '<img src="http://www.ptdtrading.com/games/ptd/small/'.$num.'_0.png"/>';
		}
		if (!$madeRequest) {
			$madeRequest = true;
			$currentRequest = $whichRequest;
		}
		if ($currentRequest != $whichRequest) {
			$currentRequest = $whichRequest;
			$message .= '<div class="row"><div class="xs-col-12 text-center" style="font-weight:bold">OR</div></div>'; 
		}
		$message .= '<div class="row">';
		$message .= '<div class="pokeblock panel-group col-xs-12" style="margin-bottom:0px;">';
        $message .= '<div class = "panel panel-primary '.$panelType.'">';
        $message .= '<div class = "panel-heading text-center">';
		$message .= $poke.' '.$genderIcon.' <span class="label label-success">'.$extraName.'</span>';
		$message .= '</div></div></div></div>';
		$requestCount++;
	}
	if (!$madeRequest) {
		$success = false;
		$message = '<div class="row"><div class="xs-col-12 text-center">This trainer made no request.</div></div>';
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


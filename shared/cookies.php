<?php
	$playingGame = 0;
	$cookieOutcome = "";
	if (isset($_COOKIE["ver"]) && isset($_COOKIE["db3"])) {
		$cookieOutcome .= "Have Cookie!";
		$playingGame = $_COOKIE["ver"];
		if ($playingGame != 3 || $_COOKIE["cProf"] != $whichProfile) {
			$cookieOutcome .= "Cookie Outdated!";
			$playingGame = 0;
		}else{
			$cookieOutcome .= "Cookie Good!";
			$profileInfo = array($_COOKIE["vName3"], $_COOKIE["nickname3"], $_COOKIE["moneys3"], $_COOKIE["db3"]);
		}
	}else{
		$cookieOutcome .= "No Cookie!";
	}
	if ($playingGame == 0) {
		$cookieOutcome .= "Setting Cookie!";
		$profileInfo = get_Basic_Profile_Info_PTD3($id, $whichProfile);
		$expireTime = time()+60*60*24;
		setcookie("ver", 3, $expireTime);
		setcookie("cProf", $whichProfile, $expireTime);
		setcookie("vName3", $profileInfo[0], $expireTime);
		setcookie("nickname3", $profileInfo[1], $expireTime);
		setcookie("moneys3", $profileInfo[2], $expireTime);
		setcookie("db3", $profileInfo[3], $expireTime);			
	}
?>
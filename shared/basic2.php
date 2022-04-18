<?php
/////////////////////////////////////////////////////////////////////////////////////////////////
function get_Gender($myGender) {
 	if ($myGender == 1) {
		$myGenderName = "male";
	}else if ($myGender == 2) {
		$myGenderName = "female";
	}else {
		$myGenderName = "none";
	}
	return $myGenderName;
 }
  /////////////////////////////////////////////////////////////////////////////////////////////////
  function get_Panel_Name($shiny) {
	$panelName = "";
	$shinyName = get_Shiny_Name($shiny);
	if ($shinyName != "") {
		$panelName = "panel-".$shinyName;
	}
	return $panelName;
}
/////////////////////////////////////////////////////////////////////////////////////////////////
function get_Abr_Shiny_Name($shiny) {
	$shinyName = "";
	switch($shiny) {
		case(-2):
			$shinyName = "Elmn";
			break;
		case(-1):
			$shinyName = "Any";
			break;
		case(1):
			$shinyName = "Shny";
			break;
		case(2):
			$shinyName = "Shdw";
			break;
		case(3):
			$shinyName = "Grss";
			break;
		case(4):
			$shinyName = "Psn";
			break;
		case(5):
			$shinyName = "Wtr";
			break;
		case(6):
			$shinyName = "Fire";
			break;
		case(7):
			$shinyName = "Nrml";
			break;
		case(8):
			$shinyName = "Fly";
			break;
		case(9):
			$shinyName = "Bug";
			break;
		case(10):
			$shinyName = "Ghst";
			break;
		case(11):
			$shinyName = "Stl";
			break;
		case(12):
			$shinyName = "Rock";
			break;
		case(13):
			$shinyName = "Elec";
			break;
		case(14):
			$shinyName = "Ice";
			break;
		case(15):
			$shinyName = "Fght";
			break;
		case(16):
			$shinyName = "Grnd";
			break;
		case(17):
			$shinyName = "Drgn";
			break;
		case(18):
			$shinyName = "Dark";
			break;
		case(19):
			$shinyName = "Psyc";
			break;
		case(21):
			$shinyName = "Fairy";
			break;
		default:
			$shinyName = "Reg";
			break;
	}
	return $shinyName;
}
/////////////////////////////////////////////////////////////////////////////////////////////////
function get_Shiny_Name($shiny) {
	$shinyName = "";
	switch($shiny) {
		case(1):
			$shinyName = "shiny";
			break;
		case(2):
			$shinyName = "shadow";
			break;
		case(3):
			$shinyName = "grass";
			break;
		case(4):
			$shinyName = "poison";
			break;
		case(5):
			$shinyName = "water";
			break;
		case(6):
			$shinyName = "fire";
			break;
		case(7):
			$shinyName = "normal";
			break;
		case(8):
			$shinyName = "flying";
			break;
		case(9):
			$shinyName = "bug";
			break;
		case(10):
			$shinyName = "ghost";
			break;
		case(11):
			$shinyName = "steel";
			break;
		case(12):
			$shinyName = "rock";
			break;
		case(13):
			$shinyName = "electric";
			break;
		case(14):
			$shinyName = "ice";
			break;
		case(15):
			$shinyName = "fight";
			break;
		case(16):
			$shinyName = "ground";
			break;
		case(17):
			$shinyName = "dragon";
			break;
		case(18):
			$shinyName = "dark";
			break;
		case(19):
			$shinyName = "physic";
			break;
		case(21):
			$shinyName = "fairy";
			break;
		default:
			$shinyName = "";
			break;
	}
	return $shinyName;
}
/////////////////////////////////////////////////////////////////////////////////////////////////
?>
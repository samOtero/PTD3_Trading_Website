<?php
/////////////////////////////////////////////////////////////////////////////////////////////////
function checkGender($whichPoke, $gender) { 
	if ($whichPoke == 151 || $whichPoke == 81 || $whichPoke == 146 || $whichPoke == 145 || $whichPoke == 144 || $whichPoke == 150 || $whichPoke == 137 || $whichPoke == 132 || $whichPoke == 100 || $whichPoke == 243 || $whichPoke == 244 || $whichPoke == 245 || $whichPoke == 249 || $whichPoke == 250 || $whichPoke == 493) { //Mew, Magnemite, Moltres, Zapdos, Articuno, Mewtwo, Porygon, Ditto, Arceus more should be added for more mystery gifts etc
		$gender = -1;
	}
	if ($whichPoke == 107 || $whichPoke == 106 || $whichPoke == 32 || $whichPoke == 128) { //Male only, Hitmonchan, Hitmonlee, Nidoran M
		$gender = 1;
	}
	if ($whichPoke == 113 || $whichPoke == 29 || $whichPoke == 115) { //Female only, Chansey, Nidoran F, Kangaskhan
		$gender = 2;
	}
	return $gender;
}
///////////////////////////////////////////////////////////////////////////////////////////////// 
 ?>
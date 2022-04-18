// JavaScript Document
//CREATE NEW REQUEST

var currentRequest = 1;
var requestLeft = 3;
var totalRequest = 3;
createNewRequest = function () {
	if (currentRequest <= totalRequest) {
		var myPanel = $("#requestPanel"+currentRequest)
		myPanel.show();
		currentRequest++;
		requestLeft--;
		$("#requestCount").html(requestLeft);
		var topCord = myPanel.offset().top - 75;
		$('html, body').animate({
			scrollTop:topCord
		}, 1000);
		if (requestLeft == 0) {
			$("#requestBtn").hide();
		}	
	}
}
$(document).ready(function() {
	$(".addPoke").click(function() {
		if (jQuery.hasData(this) == false) {
			return;
		}
		var which = $(this).data("which");
		var howManyLeft = $(this).data("pokeleft");
		var nextToAdd = 7-howManyLeft;
		if (howManyLeft > 0) {
			howManyLeft--;
			$(this).data("pokeleft", howManyLeft);
			$("#requestAddMore"+which).html(howManyLeft);
			$("#pokeRequestTop"+nextToAdd+"_"+which).show();
			if (howManyLeft == 0) {
				$(this).hide();
			}
		}
	});
});


update_Poke_Request_Info = function (num, gender, lvlComp, lvl, shiny, whichPoke, which) {
	if (num == -1) {
		shiny = 0;
		lvl = -1;
		gender = 0;
	}
	//Poke Icon
	var pokeIcon = '<img src="http://www.ptdtrading.com/games/ptd/small/'+num+'_0.png">';
	
    //get gender img if needed
	var genderName = get_Gender(gender);
	var genderIcon = "";
	if (genderName != "none") {
		genderIcon = '<img src = "http://www.ptdtrading.com/trading_center/images/'+genderName+'.png" title="'+genderName+'"/>';
	}	
	//Panel Style based on Shiny
	var panelType = get_Panel_Name(shiny);
	
	//Extra Name depending on shiny
	var extraName = get_Abr_Shiny_Name(shiny);
	
	//Level Comparison
	var levelComparison = "";
	var comparison = "";
	if (lvl != "-1") {
		if (lvlComp == "1") {
			comparison = "=";
		}else if (lvlComp == "2") {
			comparison = "<=";
		}else if (lvlComp == "3") {
			comparison = ">=";
		}else if (lvlComp == "4") {
			comparison = "<";
		}else if (lvlComp == "5") {
			comparison = ">";
		}
		levelComparison = "- Lvl "+comparison+" "+lvl;
	}
	
	//If nothing picked
	if (num == -1) {
		pokeIcon = "None";
		extraName = "";
	}else if (num == 0) {
		pokeIcon = "Any";
	}
	var name = "#panel"+whichPoke+"_"+which;
	var result = pokeIcon+" "+genderIcon+" <span class=\"label label-success\">"+extraName+"</span>";
	$(name).html(result);
	var newClass = "panel panel-primary "+panelType;
	var panelName = "#pokePanel"+whichPoke+"_"+which;
	$(panelName).attr("class", newClass);
}
/////////////////////////////////////////////////////////////////////////////////////////////////
get_Gender = function(myGender) {
	var myGenderName = "none";
 	if (myGender == 1) {
		myGenderName = "male";
	}else if (myGender == 2) {
		myGenderName = "female";
	}
	return myGenderName;
 }
  /////////////////////////////////////////////////////////////////////////////////////////////////
 get_Panel_Name = function(shiny) {
	var panelName = "";
	var shinyName = get_Shiny_Name(shiny);
	if (shinyName != "") {
		panelName = "panel-"+shinyName;
	}
	return panelName;
}
/////////////////////////////////////////////////////////////////////////////////////////////////
get_Abr_Shiny_Name= function(shiny) {
	var shinyName = "";
	switch(shiny) {
		case("-1"):
			shinyName = "";
			break;
		case("-2"):
			shinyName = "Elmn";
			break;
		case("1"):
			shinyName = "Shny";
			break;
		case("2"):
			shinyName = "Shdw";
			break;
		case("3"):
			shinyName = "Grss";
			break;
		case("4"):
			shinyName = "Psn";
			break;
		case("5"):
			shinyName = "Wtr";
			break;
		case("6"):
			shinyName = "Fire";
			break;
		case("7"):
			shinyName = "Nrml";
			break;
		case("8"):
			shinyName = "Fly";
			break;
		case("9"):
			shinyName = "Bug";
			break;
		case("10"):
			shinyName = "Ghst";
			break;
		case("11"):
			shinyName = "Stl";
			break;
		case("12"):
			shinyName = "Rock";
			break;
		case("13"):
			shinyName = "Elec";
			break;
		case("14"):
			shinyName = "Ice";
			break;
		case("15"):
			shinyName = "Fght";
			break;
		case("16"):
			shinyName = "Grnd";
			break;
		case("17"):
			shinyName = "Drgn";
			break;
		case("18"):
			shinyName = "Dark";
			break;
		case("19"):
			shinyName = "Psyc";
			break;
		case("21"):
			shinyName = "Fairy";
			break;
		default:
			shinyName = "Reg";
			break;
	}
	return shinyName;
}
/////////////////////////////////////////////////////////////////////////////////////////////////
get_Shiny_Name = function(shiny) {
	var shinyName = "";
	switch(shiny) {
		case("1"):
			shinyName = "shiny";
			break;
		case("2"):
			shinyName = "shadow";
			break;
		case("3"):
			shinyName = "grass";
			break;
		case("4"):
			shinyName = "poison";
			break;
		case("5"):
			shinyName = "water";
			break;
		case("6"):
			shinyName = "fire";
			break;
		case("7"):
			shinyName = "normal";
			break;
		case("8"):
			shinyName = "flying";
			break;
		case("9"):
			shinyName = "bug";
			break;
		case("10"):
			shinyName = "ghost";
			break;
		case("11"):
			shinyName = "steel";
			break;
		case("12"):
			shinyName = "rock";
			break;
		case("13"):
			shinyName = "electric";
			break;
		case("14"):
			shinyName = "ice";
			break;
		case("15"):
			shinyName = "fight";
			break;
		case("16"):
			shinyName = "ground";
			break;
		case("17"):
			shinyName = "dragon";
			break;
		case("18"):
			shinyName = "dark";
			break;
		case("19"):
			shinyName = "physic";
			break;
		case("21"):
			shinyName = "fairy";
			break;
		default:
			shinyName = "";
			break;
	}
	return shinyName;
}
/////////////////////////////////////////////////////////////////////////////////////////////////
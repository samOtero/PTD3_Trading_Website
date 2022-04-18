// JavaScript Document
abandonConfirmPickup = function (uniqueId) {
	$("#abandonContent_"+uniqueId).html("This Pokémon is leaving...");
	disableBtns(uniqueId);
	$.ajax({
		type: "POST",
		data: {id:uniqueId},
		url: "ajax/abandonPokePickup.php",
		success: function (result) {
			$("#abandonContent_"+uniqueId).html(result);
			//disableBtns(uniqueId);
		},
		error: function(result) {
			$("#abandonContent_"+uniqueId).html(result);
		}		
	});
};
closeBox = function (uniqueId) {
	$("#pokeblock"+uniqueId).hide();
};
abandonFirstTryPickup = function (uniqueId) {
	var nextText = '<h4>Are you sure?!</h4>';
	nextText += '<button type="button" class="btn btn-success btn-md" data-toggle="collapse" data-target="#collapse2_'+uniqueId+'" aria-expanded="true">No</button>';	nextText += ' <button type="button" class="btn btn-danger btn-md" onclick="abandonConfirmPickup(\''+uniqueId+'\');return false;">Yes</button>';
	$("#abandonContent_"+uniqueId).html(nextText);
};
resetAbandonPickup = function (uniqueId) {
	var resetText = 'Abandon this Pokémon forever?<br>';
	resetText += '<button type="button" class="btn btn-success btn-md" data-toggle="collapse" data-target="#collapse2_'+uniqueId+'" aria-expanded="true">No</button>';
	resetText += ' <button type="button" class="btn btn-danger btn-md" onclick="abandonFirstTryPickup(\''+uniqueId+'\');return false;">Yes</button>';
	$("#abandonContent_"+uniqueId).html(resetText);
	$("#collapse2_"+uniqueId).collapse("hide");
};
function disableBtns (uniqueId) {
	$("#abandonBtn"+uniqueId).prop("disabled", true);
	$("#profileBtn"+uniqueId).prop("disabled", true);
};

function pickupPoke (uniqueId, whichProfile, whichDB) {
	$("#collapse3_"+uniqueId).collapse("show");
	disableBtns(uniqueId);
	$.ajax({
		type: "POST",
		data: {id:uniqueId, whichProfile:whichProfile, whichDB:whichDB},
		url: "ajax/pickupPoke.php",
		success: function (result) {
			$("#pickupContent_"+uniqueId).html(result);
			//disableBtns(uniqueId);
		},
		error: function(result) {
			$("#pickupContent_"+uniqueId).html(result);
		}		
	});
}

function pickupAllPoke (whichProfile, whichDB) {
	$("#sendAllToProfileInfo").text("Sending all to profile...");
	$.ajax({
		type: "POST",
		data: {whichProfile:whichProfile, whichDB:whichDB},
		url: "ajax/pickupAllPoke.php",
		success: function (result) {
			hideAllPickupPoke();
			$("#sendAllToProfileInfo").text("Sent all to profile!");
		},
		error: function(result) {
			$("#sendAllToProfileInfo").text("An Error happened, refresh and try again!");
		}		
	});
}

function hideAllPickupPoke() {
	$('[id^=pokeblock]').hide();
}
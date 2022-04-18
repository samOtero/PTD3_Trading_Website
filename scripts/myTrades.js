// JavaScript Document

//LOAD TRADE REQUEST
loadTradeRequest = function (uniqueId) {
	$.ajax({
		type: "POST",
		data: {tradeID:uniqueId},
		dataType:"json",
		url: "ajax/getTradeRequest.php",
		success: function (result) {
			$("#myRequest").html(result.message);
		},
		error: function(result) {
			$("#myRequest").html("Error, please try again.");
		}		
	});
};
callBackPoke = function (uniqueId, whichProfile, whichDB) {
	disableBtns(uniqueId, true);
	$("#callback_"+uniqueId).html("Calling back this Pokémon...");
	$.ajax({
		type: "POST",
		data: {tradeID:uniqueId, whichProfile:whichProfile, whichDB:whichDB},
		dataType:"json",
		url: "ajax/callBack.php",
		success: function (result) {
			$("#callback_"+uniqueId).html(result.message);
		},
		error: function(result) {
			$("#callback_"+uniqueId).html("Error, please try again.");
		}		
	});
};
disableBtns = function (uniqueId, toggle) {
	if (toggle == true)
		$("#viewRequestBtn"+uniqueId).addClass('disabled');
	else
		$("#viewRequestBtn"+uniqueId).removeClass('disabled');
	//$("#viewRequestBtn"+uniqueId).attr('disabled', toggle);
	$("#callbackBtn"+uniqueId).prop("disabled", toggle);
}

closeTrade = function (uniqueId) {
	$("#pokeblock"+uniqueId).hide();
}
closeOffer = function (uniqueId) {
	$("#offerBlock"+uniqueId).hide();
}
removeOffer = function (uniqueId, requestID) {
	$("#viewOfferBtn"+uniqueId).prop("disabled", true);
	$("#removeOfferBtn"+uniqueId).prop("disabled", true);
	$("#removeOfferText"+uniqueId).html("Removing this offer...");
	$.ajax({
		type: "POST",
		data: {offerID:uniqueId, requestID:requestID},
		dataType:"json",
		url: "ajax/removeOffer.php",
		success: function (result) {
			$("#removeOfferText"+uniqueId).html(result.message);
		},
		error: function(result) {
			$("#removeOfferText"+uniqueId).html("Error, please try again.");
		}		
	});
};
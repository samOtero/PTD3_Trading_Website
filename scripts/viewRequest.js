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

closeOffer = function (uniqueId) {
	$("#offerBlock"+uniqueId).hide();
}
removeOffer = function (uniqueId) {
	$("#viewOfferBtn"+uniqueId).prop("disabled", true);
	$("#removeOfferBtn"+uniqueId).prop("disabled", true);
	$("#removeOfferText"+uniqueId).html("Denying this offer...");
	$.ajax({
		type: "POST",
		data: {offerID:uniqueId},
		dataType:"json",
		url: "ajax/removeTradeOffer.php",
		success: function (result) {
			$("#removeOfferText"+uniqueId).html(result.message);
		},
		error: function(result) {
			$("#removeOfferText"+uniqueId).html("Error, please try again.");
		}		
	});
};
// JavaScript Document

//LOAD TRADE REQUEST
loadTradeRequest = function (uniqueId) {
	//If we already got a result from this then don't load data again
	if ($("#collapse3_"+uniqueId).data("results") == "true") {
		return;
	}
	$("#viewRequestBtn"+uniqueId).prop("disabled", true);
	$.ajax({
		type: "POST",
		data: {tradeID:uniqueId},
		dataType:"json",
		url: "ajax/getTradeRequest.php",
		success: function (result) {
			$("#collapse3_"+uniqueId).data("results", "true"); //Set result as true if we got data back successfully
			$("#collapse3_"+uniqueId).html(result.message);
			$("#viewRequestBtn"+uniqueId).prop("disabled", false);
		},
		error: function(result) {
			$("#nicknameError"+uniqueId).html("Error, please try again.");
			$("#viewRequestBtn"+uniqueId).prop("disabled", false);
		}		
	});
}
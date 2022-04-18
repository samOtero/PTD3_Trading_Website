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
}
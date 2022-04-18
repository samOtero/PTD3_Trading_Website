// JavaScript Document
//Handle Completed button

$(document).ready(function() {
	$(".btnCompleted").click(function() {
		if (jQuery.hasData(this) == false) {
			return;
		}
		var whichFamily = $(this).data("family");
		var urlValidation = $(this).data("urlvalidation");
		window.location.href = "/ptd3/elementalLabRedeem.php?"+urlValidation+"&family="+whichFamily;
	});
});
/////////////////////////////////////////////////////////////////////////////////////////////////
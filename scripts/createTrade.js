// JavaScript Document
abandonConfirm = function (uniqueId, whichProfile, whichDB) {
	$("#abandonContent_"+uniqueId).html("This Pokémon is leaving...");
	$.ajax({
		type: "POST",
		data: {id:uniqueId, pid: whichProfile, pdb: whichDB},
		url: "ajax/abandonPoke.php",
		success: function (result) {
			$("#abandonContent_"+uniqueId).html(result);
			//disableBtns(uniqueId);
		},
		error: function(result) {
			$("#abandonContent_"+uniqueId).html(result);
		}		
	});
}
closeBox = function (uniqueId) {
	$("#pokeblock"+uniqueId).hide();
}
abandonFirstTry = function (uniqueId, whichProfile, whichDB) {
	var nextText = '<h4>Are you sure?!</h4>';
	nextText += '<button type="button" class="btn btn-success btn-md" data-toggle="collapse" data-target="#collapse2_'+uniqueId+'" aria-expanded="true">No</button>';	nextText += ' <button type="button" class="btn btn-danger btn-md" onclick="abandonConfirm(\''+uniqueId+'\', \''+whichProfile+'\', \''+whichDB+'\');return false;">Yes</button>';
	$("#abandonContent_"+uniqueId).html(nextText);
}
resetAbandon = function (uniqueId, whichProfile,whichDB) {
	var resetText = 'Abandon this Pokémon forever?<br>';
	resetText += '<button type="button" class="btn btn-success btn-md" data-toggle="collapse" data-target="#collapse2_'+uniqueId+'" aria-expanded="true">No</button>';
	resetText += ' <button type="button" class="btn btn-danger btn-md" onclick="abandonFirstTry(\''+uniqueId+'\', \''+whichProfile+'\', \''+whichDB+'\');return false;">Yes</button>';
	$("#abandonContent_"+uniqueId).html(resetText);
	$("#collapse2_"+uniqueId).collapse("hide");
}
disableBtns = function (uniqueId) {
	$("#abandonBtn"+uniqueId).prop("disabled", true);
	$("#tradeBtn"+uniqueId).prop("disabled", true);
	$("#nicknameBtn"+uniqueId).prop("disabled", true);
}

//NICKNAME CHANGE
changeNickname = function (uniqueId, newNickname, whichDB) {
	$("#nicknameError"+uniqueId).html("Changing nicknames...");
	$("#nicknameChange"+uniqueId).prop("disabled", true);
	$("#nicknameInput"+uniqueId).prop("disabled", true);
	$.ajax({
		type: "POST",
		data: {id:uniqueId, nickname: newNickname, pdb: whichDB},
		dataType:"json",
		url: "ajax/nicknamePoke.php",
		success: function (result) {
			if (result.success == true) {
				$("#nickname"+uniqueId).html(newNickname);
			}
			$("#nicknameError"+uniqueId).html(result.message);
			$("#nicknameChange"+uniqueId).prop("disabled", false);
			$("#nicknameInput"+uniqueId).prop("disabled", false);
			$("#nicknameInput"+uniqueId).val("");
			//disableBtns(uniqueId);
		},
		error: function(result) {
			$("#nicknameError"+uniqueId).html("Error, please try again.");
			$("#nicknameChange"+uniqueId).prop("disabled", false);
			$("#nicknameInput"+uniqueId).prop("disabled", false);
			$("#nicknameInput"+uniqueId).val("");
		}		
	});
}
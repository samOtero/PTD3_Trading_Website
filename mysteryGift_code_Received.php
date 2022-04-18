<?php
session_start();
$whichProfile = $_REQUEST[ 'whichProfile' ];
$showTopAd = "yes";
$showSideAd = "yes";
$id = $_SESSION[ 'myID' ];
$currentSave = $_SESSION[ 'currentSave3' ];
$loggedIn = true;
$pageTitle = "Claim Code Giveaway";
$code = $_POST['code'];
require '../moveList.php';
include 'shared/basic.php';
include 'shared/cookies.php';
include 'shared/genderCheck.php';

$db = connect_To_Trading_Database();

$urlValidation = "whichProfile=" . $whichProfile;

$codeCorrect = false;
$today = date("Y-m-d");

//Mystery Gift Code
$query = "SELECT num, m1, m2, m3, m4, item, shiny, name FROM ptdtrad_ptd2_trading.ptd3_mysterygift_code WHERE isActive = true AND code = ? ORDER BY Id LIMIT 1";
$result = $db->prepare($query);
$result->bind_param("s", $code);
$result->execute();
$result->store_result();
$hmr = $result->affected_rows;
$result->bind_result($pokeNum, $m1, $m2, $m3, $m4, $itemSelection, $shiny, $pokeName);
if ($hmr <= 0) {
	$result->free_result();
	$result->close();
}else{
	$codeCorrect = true;
	$result->fetch();
	$result->free_result();
	$result->close();
	
	$shinyName = get_Shiny_Name($shiny);
	$shinyName = ucfirst($shinyName); //First letter capitol
	//$pokeName = "Eevee";

	if ($itemSelection == -1) {
		$itemSelection = 100;
	}
	
	$exp = 0;
	$lvl = 1;
	$originalTrainer = -3;
	$myTag = 'n';
	$gender = 1;
	$hof = 0;
	$extra1 = 0;
	$extra2 = 0;
	//Change Gender as necessary. Ex - Mew will always be -1
	$gender = checkGender($pokeNum, $gender);

	$query = "INSERT INTO ptdtrad_ptd2_trading.ptd3_pickup_pokes (num, lvl, exp, shiny, nickname, m1, m2, m3, m4, item, originalTrainer, currentTrainer, myTag, gender, happy, extra1, extra2) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
	$result = $db->prepare($query);
	$result->bind_param("iiiisiiiiiiisiiii", $pokeNum, $lvl, $exp, $shiny, $pokeName, $m1, $m2, $m3, $m4, $itemSelection, $originalTrainer, $id, $myTag, $gender, $hof, $extra1, $extra2);
	if (!$result->execute()) {
		redirect_To_Error();
	}
	//Do the second giveaway, usually will be female
	$gender = 2;
	$gender = checkGender($pokeNum, $gender);
	$result->bind_param("iiiisiiiiiiisiiii", $pokeNum, $lvl, $exp, $shiny, $pokeName, $m1, $m2, $m3, $m4, $itemSelection, $originalTrainer, $id, $myTag, $gender, $hof, $extra1, $extra2);
	if (!$result->execute()) {
		redirect_To_Error();
	}
	$result->close();
	
	//Track mystery gift usage!
	$query = "SELECT id, timesForThis FROM ptdtrad_ptd2_trading.ptd3_mysterygift_tracking WHERE trainerID = $id AND num = $pokeNum AND shiny = $shiny AND date = '$today'";
	$result = $db->prepare($query);
	$result->execute();
	$result->store_result();
	$hmr = $result->affected_rows;
	$result->bind_result($trackID, $timesUsed);
	$result->fetch();
	$result->free_result();
	$result->close();
	if ($hmr == 0){
		$query = "INSERT INTO ptdtrad_ptd2_trading.ptd3_mysterygift_tracking (trainerID, num, shiny, timesForThis, date) VALUES ($id, $pokeNum, $shiny, 1, '$today')";
		$result = $db->prepare($query);
		$result->execute();
		$result->close();
	}else{
		$timesUsed = $timesUsed + 1;
		$query = "UPDATE ptdtrad_ptd2_trading.ptd3_mysterygift_tracking SET timesForThis = $timesUsed WHERE trainerID = $id AND num = $pokeNum AND shiny = $shiny AND date = '$today'";
		$result = $db->prepare($query);
		$result->execute();
		$result->close();
	}
}
$db->close();


include 'shared/head.php';
?>
<!--<script src="scripts/myTrades.js"></script>-->
<body>
<?php include 'shared/navbar.php'; ?>
<div class="container-fluid">
	<div class = "row">
        <div class = "col-sm-12" style="padding-top: 70px">
        	<ol class="breadcrumb">
            <li><a href="main.php?<?php echo $urlValidation ?>">Home</a></li>
            <li><a href="mysteryGift_code.php?<?php echo $urlValidation ?>">Code Giveaway</a></li>
            <li class="active">Claim Code Giveaway</li>
            </ol>
        </div>
    </div>
    <? include 'shared/adTop.php'; ?>
    <div class="row">
        <div class = "col-lg-9 col-md-8 col-sm-7 col-xs-12" >
          <div class="panel panel-primary">
            <div class="panel-heading">
              <h4 class="panel-title">Claim Code Giveaway</h4>
            </div>
            <?php if ($codeCorrect == true) { ?>
            <div class = "panel-body">
              
               <div class="row">
					<div class="col-xs-12 text-center">
						<p>You claimed <?php echo "$shinyName $pokeName"?>! It is waiting for you in the pickup area on the "Home" section.</p>
				   </div>
				</div>
            </div>
            <div class="row">
				<div class="col-lg-4 col-md-3"></div>
				<div class="col-xs-12 col-lg-4 col-md-6">
					<?php block_poke_long('1234', $pokeNum, $pokeName, $m1, $m2, $m3, $m4, 1, $shiny,
			$itemSelection, 'n', 0, -1, "top"); ?>
				</div>
				<div class="col-lg-4 col-md-3"></div>
			</div>
         <?php }else{ ?>
         	<div class = "panel-body">
               <div class="row">
					<div class="col-xs-12 text-center">
						<p>The code you entered is not valid, go back and try again. You can find the code in the <a href="http://pandoragaming.co.uk/index/">Pandora Forum</a> after registering.</p>
				   </div>
				</div>
            </div>
         <?php } ?>
          </div>
      	</div>
      	<? include 'shared/adSide.php'; ?>
    </div>
</div>
<?php include 'shared/footer.php';
 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
</body>
</html>
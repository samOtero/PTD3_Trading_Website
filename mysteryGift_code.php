<?php
session_start();
$whichProfile = $_REQUEST[ 'whichProfile' ];
$showTopAd = "yes";
$showSideAd = "yes";
$id = $_SESSION[ 'myID' ];
$currentSave = $_SESSION[ 'currentSave3' ];
$loggedIn = true;
$pageTitle = "Code Giveaway";
require '../moveList.php';
include 'shared/basic.php';
include 'shared/cookies.php';

$db = connect_To_Trading_Database();

$urlValidation = "whichProfile=" . $whichProfile;

//Mystery Gift Code
$today = date("Y-m-d");
$query = "SELECT num, m1, m2, m3, m4, item, shiny, name FROM ptdtrad_ptd2_trading.ptd3_mysterygift_code WHERE isActive = true ORDER BY Id LIMIT 1";
$result = $db->prepare($query);
$result->execute();
$result->store_result();
$hmr = $result->affected_rows;
$result->bind_result($pokeNum, $m1, $m2, $m3, $m4, $itemSelection, $shiny, $pokeName);
$result->fetch();
$result->free_result();
$result->close();
$db->close();

$shinyName = get_Shiny_Name($shiny);
$shinyName = ucfirst($shinyName); //First letter capitol
//$pokeName = "Eevee";

if ($itemSelection == -1) {
	$itemSelection = 100;
}



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
            <li class="active">Code Giveaway</li>
            </ol>
        </div>
    </div>
    <? include 'shared/adTop.php'; ?>
    <div class="row">
        <div class = "col-lg-9 col-md-8 col-sm-7 col-xs-12" >
          <div class="panel panel-primary">
            <div class="panel-heading">
              <h4 class="panel-title">Code Giveaway</h4>
            </div>
            <div class = "panel-body">
              	<div class="row">
              		<div class="col-xs-12 text-center">
              			<img src="images/<?php echo $pokeName?>.png" class="img-responsive" style="display:inline-block">
					</div>
				</div>
               <div class="row">
               	<div class="col-xs-12 text-center">
					<h1>Pandora Gaming Giveaway!</h1>
					<p>Get a <?php echo "$shinyName $pokeName"?> for becoming a member of the <a href="http://pandoragaming.co.uk/index/">Pandora Forum</a>!</p>
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
         <div class="row" style="padding-bottom: 20px">
         <div class="col-lg-3 col-md-2"></div>
			<div class="col-xs-12 col-lg-6 col-md-8 text-center">
				<form action="mysteryGift_code_Received.php?<?php echo $urlValidation ?>" method="post" name="form2" id="form2">
					<div class="input-group" style="padding-left: 10px;padding-right: 10px;">
						<input type="text" id="code" name="code" class="form-control" placeholder="Enter code here...">
						<span class="input-group-btn">
							<button class="btn btn-success" type="submit">Claim <?php echo "$shinyName $pokeName"?>!</button>
						</span>
					</div>
				</form>
			</div>
			<div class="col-lg-3 col-md-2"></div>
		</div>
          </div>
      	</div>
      	<? include 'shared/adSide.php'; ?>
    </div>
</div>
<?php include 'shared/footer.php'; ?>
</body>
</html>
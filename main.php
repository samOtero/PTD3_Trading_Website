<?php
session_start();
$whichProfile = $_REQUEST['whichProfile'];
$showTopAd = "yes";
$showSideAd = "yes";
$id = $_SESSION['myID'];
$currentSave = $_SESSION['currentSave3'];
$loggedIn = true;
$pageTitle = "Home";
require '../moveList.php';
include 'shared/basic.php';
include 'shared/legacyCoins.php';
include 'shared/cookies.php';

$db = connect_To_Original_Database();

//This page has pickups so we want to make sure the current save is up to date
check_If_Saved_Outside($db, $profileInfo, $id, $currentSave);
$db->close();
$db = connect_To_Trading_Database();

$urlValidation = "whichProfile=".$whichProfile;

$whichDB = $profileInfo[3]; //get Which DB the Pokemon are stored in for this profile

//Get the Total Count of Pokemon in this profile to make the pagination
$queryTotal = "SELECT COUNT(*) FROM ptdtrad_ptd2_trading.ptd3_pickup_pokes WHERE currentTrainer = ?";
$resultTotal = $db->prepare($queryTotal);
$resultTotal->bind_param("i", $id);
$resultTotal->execute();
$resultTotal->store_result();
$resultTotal->bind_result($totalPoke);
$resultTotal->fetch();
$resultTotal->free_result();
$resultTotal->close();

$limit = 100; //Limit of Pokemon Shown in a page
$page = mysql_escape_string($_GET['page']); //which page are we in?
$whichURL = "main.php?"; //which url for pagination
$paginateArray = get_Pagination_Text($page, $totalPoke, $limit, $whichURL, $urlValidation);
$paginateText = $paginateArray[0];
$start = $paginateArray[1];

include 'shared/head.php';


?>

<body>
<? include 'shared/navbar.php'; ?>
<script src="scripts/main.js"></script>
<div class="container-fluid">
    <div class = "row">
        <div class = "col-sm-12" style="padding-top: 70px">
        	<ol class="breadcrumb">
            <li class="active">Home</li>
            </ol>
        </div>
    </div>
    <? include 'shared/adTop.php'; ?>
<div class="row row-eq-height">
    <div class = "col-lg-9 col-md-8 col-sm-7 col-xs-12">
      <div class="panel panel-primary">
        <div class="panel-heading">
          <h4 class="panel-title"> Welcome!</h4>
        </div>
        <div class = "panel-body">
        	<p>Welcome to the Pokémon Tower Defense 3: Trading Center! Here you can trade your Pokémon with trainers from around the world.</p>
        </div>
        <div class = " list-group">
        
        <a href="elementalLab.php?<?php echo $urlValidation?>" class="list-group-item list-group-item-info"> <h4 class="list-group-item-heading">Elemental Lab</h4> <p class="list-group-item-text">Get rewarded for collecting elemental pokémon!</p> </a>
			<a href="mysteryGift.php?<?php echo $urlValidation?>" class="list-group-item list-group-item-info"> <h4 class="list-group-item-heading">Mystery Gift</h4> <p class="list-group-item-text">There is a special gift waiting for you inside! Keep checking back as this gift will change every day!</p> </a>
       		<a href="mysteryGift_code.php?<?php echo $urlValidation?>" class="list-group-item list-group-item-info"> <h4 class="list-group-item-heading">Code Giveaway</h4> <p class="list-group-item-text">Register in the Pandora Forum and receive a code for a special Elemental Pokémon!</p> </a>
        	<a href="myTrades.php?<?php echo $urlValidation?>" class="list-group-item"> <h4 class="list-group-item-heading">My Trades</h4> <p class="list-group-item-text">Check your trades and trade request. You can also call back Pokémon to your profile if you don't wish to trade it anymore.</p> </a>
        	<a href="createTrade.php?<?php echo $urlValidation?>" class="list-group-item"> <h4 class="list-group-item-heading">Create Trade</h4> <p class="list-group-item-text">Go here to select one of your Pokémon and put him up for trade, other players will be able to request trades for your pokémon. Once you put your pokémon to trade you will not be able to use him in your game unless you call him back.</p> </a>
            <a href="searchTrades.php?<?php echo $urlValidation?>" class="list-group-item"> <h4 class="list-group-item-heading">Search Trades</h4> <p class="list-group-item-text">Go here to search for trades that other trainers have posted.</p> </a>
            <a href="latestTrades.php?<?php echo $urlValidation?>" class="list-group-item list-group-item-info"> <h4 class="list-group-item-heading">Latest Trades</h4> <p class="list-group-item-text">Go here to see the latest trade that other trainers have posted.</p> </a>
            <a href="itemStore.php?<?php echo $urlValidation?>" class="list-group-item"> <h4 class="list-group-item-heading">Item Store</h4> <p class="list-group-item-text">Buy Items for evolving your pokémon!</p> </a>
            
            
        </div>
      </div>
  </div>
  <? include 'shared/adSide.php'; ?>
</div>
<div class="row">
    <div class = "col-sm-12">
      <div class="panel panel-primary">
        <div class="panel-heading">
			<h3 class="panel-title">Pickup Area <span class="badge"><?php echo $totalPoke ?></span></h3>
        </div>
        <div class = "panel-body">
        	<div class="row">
        		<div class="col-xs-12">
        			<?php echo $paginateText ?>
				</div>
			</div>
       		<div class="row" style="margin-top:10px">
        		<div class="col-xs-12" style="margin-left:15px">
        		<?php if ($totalPoke > 1) { ?>
					<button type="button" class="btn btn-success" id="sendAllToProfile" onClick="pickupAllPoke('<?php echo $whichProfile ?>', '<?php echo $whichDB ?>');return false;">Send All to Profile</button> <span id="sendAllToProfileInfo"></span>
        		<?php } ?>
				</div>
			</div>
        	
        	<div class="row" style="margin-top:10px">
				<div class="col-xs-12">
				<?php
				if ($totalPoke > 0) {
					$query = "SELECT num, lvl, shiny, pickupUniqueID, nickname, myTag, m1, m2, m3, m4, gender, item, happy FROM ptdtrad_ptd2_trading.ptd3_pickup_pokes WHERE currentTrainer = ? ORDER BY num, lvl LIMIT $start, $limit";
					$result = $db->prepare($query);
					$result->bind_param("i", $id);
					$result->execute();
					$result->store_result();
					$hmp = $result->affected_rows;
					$result->bind_result($pokeNum, $pokeLevel,$pokeShiny, $pokeID, $pokeNickname, $myTag, $move1, $move2, $move3, $move4, $pokeGender, $pokeItem, $pokeHoF);
					for ($i=1; $i<=$hmp; $i++) {
						$result->fetch();
						echo block_poke_pickup($pokeID, $pokeNum, $pokeNickname, $move1, $move2, $move3, $move4, $pokeLevel, $pokeShiny,
							$pokeItem, $myTag, $pokeHoF, $pokeGender, $whichProfile, $whichDB, $urlValidation);
					}
					$result->free_result();
					$result->close();
				}else{
					echo 'No Pokémon to pick up.';
				}
				?>
				</div>
			</div>
        </div>
      </div>
  </div>
</div>
<!-- Legacy Modal --> 
<div id="legacyModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">You got a Legacy Coin!</h4>
      </div>
      <div class="modal-body">
        <p>You got your daily Legacy coin just for logging in! Use your coins to purchase Evolution Items in the store. Log in again tomorrow for another coin!</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
</div>
<? 
	//Do legacy code
	if (hasLoggedInTodayForLegacyCoins($db, $id) == false) {
		giveLegacyCoin($db, $id, 1, true);	
	?>
	<script>
		$(document).ready(function() {
			$("#legacyModal").modal("show");
		});
	</script>
<?php
	}
	
include 'shared/footer.php'; ?>
</body>
</html>
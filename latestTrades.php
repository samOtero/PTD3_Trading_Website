<?php
session_start();
$whichProfile = $_REQUEST['whichProfile'];
$showTopAd = "yes";
$showSideAd = "yes";
$id = $_SESSION['myID'];
$currentSave = $_SESSION['currentSave3'];
$loggedIn = true;
$pageTitle = "Latest Trades";
require '../moveList.php';
include 'shared/basic.php';
include 'shared/cookies.php';

$db = connect_To_Trading_Database();

//Don't care if they saved outside here
//check_If_Saved_Outside($db, $profileInfo, $id, $currentSave);

$urlValidation = "whichProfile=".$whichProfile;

//Latest trade code
$whichURL = "";
$queryBody = "";
$maxResults = 200;

$queryBody = "FROM ptdtrad_ptd2_trading.ptd3_trade_pokes ORDER BY tradeUniqueID desc";
//Get total for pagination
$queryTotal = "SELECT COUNT(*) $queryBody";
$resultTotal = $db->prepare($queryTotal);
$resultTotal->execute();
$resultTotal->store_result();
$resultTotal->bind_result($totalPoke);
$resultTotal->fetch();
$resultTotal->free_result();
$resultTotal->close();

if ($totalPoke > $maxResults) {
	$totalPoke = $maxResults;
}

$limit = 100; //Limit of Pokemon Shown in a page
$page = mysql_escape_string($_GET['page']); //which page are we in?
$paginationURL = "latestTrades.php?"; //which url for pagination
$paginateArray = get_Pagination_Text($page, $totalPoke, $limit, $paginationURL, $urlValidation);
$paginateText = $paginateArray[0];
$start = $paginateArray[1];

//Set up Pokemon Query
$query = "SELECT num, lvl, shiny, originalTrainer, uniqueID, nickname, myTag, m1, m2, m3, m4, gender, item, happy, hasRequest, sndCost $queryBody LIMIT $start, $limit";
$result = $db->prepare($query);
//

include 'shared/head.php';
?>
<script src="scripts/searchResults.js"></script>
<body>
<? include 'shared/navbar.php'; ?>
<div class="container-fluid">
	<div class = "row">
        <div class = "col-sm-12" style="padding-top: 70px">
        	<ol class="breadcrumb">
            <li><a href="main.php?<?php echo $urlValidation ?>">Home</a></li>
            <li class="active">Latest Trades</li>
            </ol>
        </div>
    </div>
    <? include 'shared/adTop.php'; ?>
    <div class="row">
        <div class = "col-xs-12">
          <div class="panel panel-primary">
            <div class="panel-heading">
              <h4 class="panel-title">Results</h4>
            </div>
            <div class = "panel-body">
                <p>Here are the latest trades. Press on "Request Trade" to make your offer for this pokémon. You can also view that trade's request (if any) by pressing on "View Trade Request".
            </div>
          </div>
      </div>
    </div>
    <?php
	?>
    <div class="row">
        <div class = "col-lg-9 col-md-8 col-sm-7 col-xs-12">
        	<div class="alert alert-info text-center" role="alert">
                Total Pokémon found: <span class="label label-success"><?php echo $totalPoke ?></span>
                <?php echo $paginateText ?>
           </div>
      </div>
      <? include 'shared/adSide.php'; ?>
	</div>
<?php
if ($totalPoke > 0) {
	$result->execute();
	$result->store_result();
	$hmp = $result->affected_rows;
	if (!$page) {
		$page = 1;
	}
	$result->bind_result($pokeNum, $pokeLevel,$pokeShiny, $originalOwner, $pokeID, $pokeNickname, $myTag, $move1, $move2, $move3, $move4, $pokeGender, $pokeItem, $pokeHoF, $hasRequest, $sndCost);
	for ($i=1; $i<=$hmp; $i++) {
		$result->fetch();
		
		echo block_poke_search_trade($pokeID, $pokeNum, $pokeNickname, $move1, $move2, $move3, $move4, $pokeLevel, $pokeShiny,
			$pokeItem, $myTag, $pokeHoF, $pokeGender, $whichURL."&page=$page&".$urlValidation, $hasRequest, $sndCost);
	}
	$result->free_result();
	$result->close();
}
$db->close();
?>
<div class="row">
	<div class = "col-xs-12">
		<div class="alert alert-info text-center" role="alert">
			Total Pokémon found: <span class="label label-success"><?php echo $totalPoke ?></span>
			<?php echo $paginateText ?>
	   </div>
  </div>
	</div>
</div>
<? include 'shared/footer.php'; ?>
</body>
</html>
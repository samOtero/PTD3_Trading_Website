<?php
session_start();
$whichProfile = $_REQUEST['whichProfile'];
$showTopAd = "yes";
$showSideAd = "yes";
$id = $_SESSION['myID'];
$currentSave = $_SESSION['currentSave3'];
$loggedIn = true;
$pageTitle = "Search Results";
require '../moveList.php';
include 'shared/basic.php';
include 'shared/cookies.php';

$db = connect_To_Trading_Database();

//Don't care if they saved outside here
//check_If_Saved_Outside($db, $profileInfo, $id, $currentSave);

$urlValidation = "whichProfile=".$whichProfile;

//Search Trade Code

$searchType = $_REQUEST['searchType'];
$whichURL = "searchType=$searchType";
$queryBody = "";

//Different parameters for different searchtypes and create pagination url
if ($searchType == 1) {
	$whichPoke = $_REQUEST['pokeList'];
	$type = $_REQUEST['type'];
	$gender = $_REQUEST['gender'];
	$haveRequest = $_REQUEST['haveRequest'];
	$allowHack = $_REQUEST['hackSel'];
	$adoptNow = $_REQUEST['adoptNow'];
	$whichURL .= "&pokeList=$whichPoke&type=$type&gender=$gender&haveRequest=$haveRequest&hackSel=$allowHack&adoptNow=$adoptNow";
	$genderSection = "gender = ?";
	$typeSection = "shiny = ?";
	$requestSection = "hasRequest = ?";
	$hackSection = "allowHack = ?";
	$pokeSection = "num = ?";
	$adoptSection = "sndCost = ?";
	if ($adoptNow == -1) {
		$adoptSection = "(1=1 OR sndCost = ?)";
	}else if ($adoptNow == -2) {
		$adoptSection = "(sndCost > 0 OR sndCost = ?)";
	}
	if ($gender == -1) {
		$genderSection = "(1=1 OR gender = ?)";
	}
	if ($type == -1) {
		$typeSection = "(1=1 OR shiny = ?)";
	}else if ($type == -2) { //Elementals only
		$typeSection = "(shiny <> 0 OR shiny = ?)";
	}
	if ($haveRequest == -1) {
		$requestSection = "(1=1 OR hasRequest = ?)";
	}
	
	if ($allowHack == -1) {
		$hackSection = "(1=1 OR allowHack = ?)";
	}
	if ($whichPoke == 0) {
		$pokeSection = "(1=1 OR num = ?)";
	}
	$queryBody = "FROM ptdtrad_ptd2_trading.ptd3_trade_pokes WHERE $requestSection AND $hackSection AND $pokeSection AND $genderSection AND $typeSection AND $adoptSection";
	//Get total for pagination
	$queryTotal = "SELECT COUNT(*) $queryBody";
	$resultTotal = $db->prepare($queryTotal);
	$resultTotal->bind_param("iiiiii", $haveRequest, $allowHack, $whichPoke, $gender, $type, $adoptNow);
	$resultTotal->execute();
	$resultTotal->store_result();
	$resultTotal->bind_result($totalPoke);
	$resultTotal->fetch();
	$resultTotal->free_result();
	$resultTotal->close();
}else if($searchType == 2) {
	$tradeIDInput = $_REQUEST['tradeIDInput'];
	$whichURL .= "&tradeIDInput=$tradeIDInput";
	$queryBody = "FROM ptdtrad_ptd2_trading.ptd3_trade_pokes WHERE uniqueID = ?";
	//Get total for pagination
	$queryTotal = "SELECT COUNT(*) $queryBody";
	$resultTotal = $db->prepare($queryTotal);
	$resultTotal->bind_param("s", $tradeIDInput);
	$resultTotal->execute();
	$resultTotal->store_result();
	$resultTotal->bind_result($totalPoke);
	$resultTotal->fetch();
	$resultTotal->free_result();
	$resultTotal->close();
}else if($searchType == 3) {
	$trainerIDInput = $_REQUEST['trainerIDInput'];
	$whichURL .= "&trainerIDInput=$trainerIDInput";
	$queryBody = "FROM ptdtrad_ptd2_trading.ptd3_trade_pokes WHERE currentTrainer = ?";
	//Get total for pagination
	$queryTotal = "SELECT COUNT(*) $queryBody";
	$resultTotal = $db->prepare($queryTotal);
	$resultTotal->bind_param("i", $trainerIDInput);
	$resultTotal->execute();
	$resultTotal->store_result();
	$resultTotal->bind_result($totalPoke);
	$resultTotal->fetch();
	$resultTotal->free_result();
	$resultTotal->close();
}else if ($searchType == 4) {
	$whichPoke = $_REQUEST['pokeList'];
	$type = $_REQUEST['type'];
	$gender = $_REQUEST['gender'];
	$genderSection = "gender = ?";
	$typeSection = "shiny = ?";
	$pokeSection = "num = ?";
	if ($gender == -1) {
		$genderSection = "(1=1 OR gender = ?)";
	}
	if ($type == -1) {
		$typeSection = "(1=1 OR shiny = ?)";
	}else if ($type == -2) { //Elementals only
		$typeSection = "(shiny <> 0 OR shiny = ?)";
	}
	if ($whichPoke == 0) {
		$pokeSection = "(1=1 OR num = ?)";
	}
	$whichURL .= "&pokeList=$whichPoke&type=$type&gender=$gender";
	//Get Total as well as tradeIds for pagination and query
	$wantsQuery = "SELECT distinct tradePokeID FROM ptdtrad_ptd2_trading.ptd3_trade_wants WHERE $pokeSection AND $genderSection AND $typeSection"; 
	$tradePokeIDs = "";
	$resultExtra = $db->prepare($wantsQuery);
	$resultExtra->bind_param("iii", $whichPoke, $gender, $type);
	$resultExtra->execute();
	$resultExtra->store_result();
	$hmpExtra = $resultExtra->affected_rows;
	$resultExtra->bind_result($tradePokeIDExtra);
	if ($hmpExtra < 1) {
		$tradePokeIDs = "'none'";
	}
	for ($i=1; $i<=$hmpExtra; $i++) {
		$resultExtra->fetch();
		if ($i > 1) {
			$tradePokeIDs = $tradePokeIDs.', ';
		}
		$tradePokeIDs = $tradePokeIDs."'".$tradePokeIDExtra."'";
	}
	$resultExtra->free_result();
	$resultExtra->close();
	$totalPoke = $hmpExtra; //Already have total here
	$queryBody = "FROM ptdtrad_ptd2_trading.ptd3_trade_pokes WHERE uniqueID in ($tradePokeIDs)";
}

$limit = 100; //Limit of Pokemon Shown in a page
$page = mysql_escape_string($_GET['page']); //which page are we in?
$paginationURL = "searchResults.php?".$whichURL."&"; //which url for pagination
$paginateArray = get_Pagination_Text($page, $totalPoke, $limit, $paginationURL, $urlValidation);
$paginateText = $paginateArray[0];
$start = $paginateArray[1];

//Set up Pokemon Query
$query = "SELECT num, lvl, shiny, originalTrainer, uniqueID, nickname, myTag, m1, m2, m3, m4, gender, item, happy, hasRequest, sndCost $queryBody ORDER BY tradeUniqueID desc LIMIT $start, $limit";
$result = $db->prepare($query);
if ($searchType == 1) {
	$result->bind_param("iiiiii", $haveRequest, $allowHack, $whichPoke, $gender, $type, $adoptNow);
}else if ($searchType == 2) {
	$result->bind_param("s", $tradeIDInput);
}else if ($searchType == 3) {
	$result->bind_param("i", $trainerIDInput);
}else if ($searchType == 4) {
	//Nothing needed
	//$result->bind_param("iii", $whichPoke, $type, $gender);
}
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
            <li><a href="searchTrades.php?<?php echo $urlValidation ?>">Search Trades</a></li>
            <li class="active">Search Results</li>
            </ol>
        </div>
    </div>
    <? include 'shared/adTop.php'; ?>
    <div class="row">
        <div class = "col-xs-12">
          <div class="panel panel-primary">
            <div class="panel-heading">
              <h4 class="panel-title">Trade Results</h4>
            </div>
            <div class = "panel-body">
                <p>Here are the results of your pokémon search. Press on "Request Trade" to make your offer for this pokémon. You can also view that trade's request (if any) by pressing on "View Trade Request".
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
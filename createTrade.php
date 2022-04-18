<?php
session_start();
$whichProfile = $_REQUEST['whichProfile'];
$showTopAd = "yes";
$showSideAd = "yes";
$id = $_SESSION['myID'];
$currentSave = $_SESSION['currentSave3'];
$loggedIn = true;
$pageTitle = "Create Trade";
require '../moveList.php';
include 'shared/basic.php';
include 'shared/cookies.php';

$db = connect_To_Original_Database();

//This page has pickups so we want to make sure the current save is up to date
check_If_Saved_Outside($db, $profileInfo, $id, $currentSave);

$urlValidation = "whichProfile=".$whichProfile;

//Create Trade Code
$whichDB = $profileInfo[3]; //get Which DB the Pokemon are stored in for this profile

//Get the Total Count of Pokemon in this profile to make the pagination
$queryTotal = "SELECT COUNT(*) FROM sndgame_ptd3_basic.trainerPokemons$whichDB WHERE trainerID = ? AND whichProfile = ?";
$resultTotal = $db->prepare($queryTotal);
$resultTotal->bind_param("ii", $id, $whichProfile);
$resultTotal->execute();
$resultTotal->store_result();
$resultTotal->bind_result($totalPoke);
$resultTotal->fetch();
$resultTotal->free_result();
$resultTotal->close();

$limit = 100; //Limit of Pokemon Shown in a page
$page = mysql_escape_string($_GET['page']); //which page are we in?
$whichURL = "createTrade.php?"; //which url for pagination
$paginateArray = get_Pagination_Text($page, $totalPoke, $limit, $whichURL, $urlValidation);
$paginateText = $paginateArray[0];
$start = $paginateArray[1];
//

include 'shared/head.php';
?>
<script src="scripts/createTrade.js"></script>
<body>
<? include 'shared/navbar.php'; ?>
<div class="container-fluid">
	<div class = "row">
        <div class = "col-sm-12" style="padding-top: 70px">
        	<ol class="breadcrumb">
            <li><a href="main.php?<?php echo $urlValidation ?>">Home</a></li>
            <li class="active">Create Trade</li>
            </ol>
        </div>
    </div>
    <? include 'shared/adTop.php'; ?>
    <div class="row">
        <div class = "col-lg-9 col-md-8 col-sm-7 col-xs-12">
          <div class="panel panel-primary">
            <div class="panel-heading">
              <h4 class="panel-title">Create Trade</h4>
            </div>
            <div class = "panel-body">
                <p>Here is a list of your pokémon from this profile, click on "Trade" to create a new Trade.
    <p>NOTE: This will remove the pokémon from your profile. To get him back to your profile go back to the "Your Trade Request" page and call your pokémon back.</p>
            </div>
          </div>
      </div>
      <? include 'shared/adSide.php'; ?>
    </div>
    <?php
	?>
    <div class="row">
        <div class = "col-sm-12">
        	<div class="alert alert-info text-center" role="alert">
                Total Pokémon in this Profile: <span class="label label-success"><?php echo $totalPoke ?></span>
                <?php echo $paginateText ?>
           </div>
      </div>
     <div>
<?php
if ($totalPoke > 0) {
	$query = "SELECT num, lvl, shiny, originalOwner, uniqueID, nickname, myTag, m1, m2, m3, m4, gender, item, happy FROM sndgame_ptd3_basic.trainerPokemons".$whichDB." WHERE trainerID = ? AND whichProfile = ? ORDER BY num, lvl LIMIT $start, $limit";
	$result = $db->prepare($query);
	$result->bind_param("ii", $id, $whichProfile);
	$result->execute();
	$result->store_result();
	$hmp = $result->affected_rows;
	$result->bind_result($pokeNum, $pokeLevel,$pokeShiny, $originalOwner, $pokeID, $pokeNickname, $myTag, $move1, $move2, $move3, $move4, $pokeGender, $pokeItem, $pokeHoF);
	for ($i=1; $i<=$hmp; $i++) {
		$result->fetch();
		echo block_poke_create_trade($pokeID, $pokeNum, $pokeNickname, $move1, $move2, $move3, $move4, $pokeLevel, $pokeShiny,
						$pokeItem, $myTag, $pokeHoF, $pokeGender, $whichProfile, $whichDB, $urlValidation);
	}
	$result->free_result();
	$result->close();
}
$db->close();
?>
</div>
<? include 'shared/footer.php'; ?>
</body>
</html>
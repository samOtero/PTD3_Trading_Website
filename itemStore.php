<?php
session_start();
$whichProfile = $_REQUEST[ 'whichProfile' ];
$showTopAd = "yes";
$showSideAd = "yes";
$id = $_SESSION[ 'myID' ];
$currentSave = $_SESSION[ 'currentSave3' ];
$loggedIn = true;
$pageTitle = "Item Store";
include 'shared/basic.php';
include 'shared/legacyCoins.php';
include 'shared/sndCoins.php';
include 'shared/cookies.php';

$db = connect_To_Trading_Database();
$dbOriginal = connect_To_Original_Database();

$urlValidation = "whichProfile=" . $whichProfile;

//Get Legacy coin count
$legacyCount = getLegacyCoin($db, $id);
$sndCount = getSndCoin( $dbOriginal, $id );
$dbOriginal->close();



include 'shared/head.php';
?>
<!--<script src="scripts/myTrades.js"></script>-->
<body>
	<?php include 'shared/navbar.php'; ?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12" style="padding-top: 70px">
				<ol class="breadcrumb">
					<li><a href="main.php?<?php echo $urlValidation ?>">Home</a>
					</li>
					<li class="active">Item Store</li>
				</ol>
			</div>
		</div>
		<? include 'shared/adTop.php'; ?>
		<div class="row">
			<div class="col-lg-9 col-md-8 col-sm-7 col-xs-12">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h4 class="panel-title">Item Store</h4>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-xs-12 text-center">
								<p>You have (<?php echo $sndCount ?>) SnD Coins and (<?php echo $legacyCount ?>) Legacy coins to use in buying items. <a href="http://samdangames.blogspot.com/p/get-snd-coins.html">Click here to get more SnD Coins.</a>
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<? include 'shared/adSide.php'; ?>
		</div>
		
		<div class="row">
			<div class="col-xs-12">
				<?php echo itemBox(17, $urlValidation) ?>
				<?php echo itemBox(18, $urlValidation) ?>
				<?php echo itemBox(19, $urlValidation) ?>
				<?php echo itemBox(20, $urlValidation) ?>
				<?php echo itemBox(21, $urlValidation) ?>
				<?php echo itemBox(22, $urlValidation) ?>
				<?php echo itemBox(23, $urlValidation) ?>
				<?php echo itemBox(24, $urlValidation) ?>
				<?php echo itemBox(25, $urlValidation) ?>
				<?php echo itemBox(26, $urlValidation) ?>
				<?php echo itemBox(27, $urlValidation) ?>
				<?php echo itemBox(13, $urlValidation) ?>
				<?php echo itemBox(14, $urlValidation) ?>
			</div>
		</div>
	</div>
	</div>
	<?php include 'shared/footer.php';
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function itemBox($itemNum, $urlValidation) {
	$itemName = get_Item_Name($itemNum);
	$itemDescription = get_Item_Description($itemNum);
	?>
	<div class="itemblock panel-group col-lg-3 col-md-4 col-sm-6">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<img src="http://www.ptdtrading.com/trading_center/item/<?php echo $itemNum ?>.png">
				<span id="nickname<?php echo $id?>">
					<?php echo $itemName?>
				</span>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-xs-12 text-center">
						<p>
							<?php echo $itemDescription ?>
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 text-center">
						<p>
							<a role="button" class="btn btn-success" href="itemBuy.php?<?php echo $urlValidation ?>&type=snd&itemId=<?php echo $itemNum ?>">Buy (10) for 1 SnD Coin</a> <a role="button" class="btn btn-success" href="itemBuy.php?<?php echo $urlValidation ?>&type=legacy&itemId=<?php echo $itemNum ?>">Buy (1) for 1 Legacy Coin</a>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function get_Item_Description( $itemNum ) {
		if ( $itemNum == 17 ) {
			return "A cute doll that makes certain species of Pokémon evolve.";
		}
		if ( $itemNum == 18 ) {
			return "A peculiar stone that makes certain species of Pokémon evolve. It is colored orange.";
		}
		if ( $itemNum == 19 ) {
			return "A peculiar stone that makes certain species of Pokémon evolve. It is a clear, light blue.";
		}
		if ( $itemNum == 20 ) {
			return "A peculiar stone that makes certain species of Pokémon evolve. It has a thunderbolt pattern.";
		}
		if ( $itemNum == 21 ) {
			return "A peculiar stone that makes certain species of Pokémon evolve. It is as black as the night sky.";
		}
		if ( $itemNum == 22 ) {
			return "A peculiar stone that makes certain species of Pokémon evolve. It has a leaf pattern.";
		}
		if ( $itemNum == 23 ) {
			return "A peculiar stone that makes certain species of Pokémon evolve. It is as dark as dark can be.";
		}
		if ( $itemNum == 24 ) {
			return "A cloth imbued with horrifyingly strong spiritual energy. It is loved by a certain Pokémon.";
		}
		if ( $itemNum == 25 ) {
			return "An item to be held by a Pokémon. It is a special metallic film that ups the power of Steel-type moves.";
		}
		if ( $itemNum == 26 ) {
			return "A peculiar stone that makes certain species of Pokémon evolve. It sparkles like eyes.";
		}
		if ( $itemNum == 27 ) {
			return "A peculiar stone that makes certain species of Pokémon evolve. It is as red as the sun.";
		}
		if ( $itemNum == 28 ) {
			return "A spray-type medicine. It awakens a Pokémon from the clutches of sleep.";
		}
		if ( $itemNum == 13 ) {
			return "If held, prevents a Pokémon from evolving.";
		}
		if ( $itemNum == 14 ) {
			return "If held, prevents a Pokémon gaining experience.";
		}
		if ( $itemNum == 15 ) {
			return "If held, increases the chance of an egg from this Pokémon being Shiny.";
		}
		if ( $itemNum == 16 ) {
			return "If held, increases the chance of an egg from this Pokémon being Shadow.";
		}
		if ( $itemNum == 32 ) {
			return "An item to be held by a Pokémon. When the holder inflicts damage, the target may flinch.";
		}
		if ( $itemNum == 34 ) {
			return "A peculiar stone that makes certain species of Pokémon evolve. It shines with a dazzling light.";
		}
		if ( $itemNum == 34 ) {
			return "A peculiar stone that makes certain species of Pokémon evolve. It shines with a dazzling light.";
		}
		if ( $itemNum == 54 ) {
			return "An item to be held by a Pokémon. This exotic-smelling incense boots the power of Water-type moves.";
		}
		if ( $itemNum == 55 ) {
			return "An item to be held by a Pokémon. This incense has a curious aroma that boosts the power of Water-type moves.";
		}
		if ( $itemNum == 56 ) {
			return "An item to be held by a Pokémon. The beguiling aroma of this incense may cause attacks to miss its holder.";
		}
		if ( $itemNum == 57 ) {
			return "An item to be held by a Pokémon. This exotic-smelling incense makes the holder bloated and slow moving.";
		}
		if ( $itemNum == 58 ) {
			return "An item to be held by a Pokémon. It doubles any prize money received if the holding Pokémon joins a battle.";
		}
		if ( $itemNum == 59 ) {
			return "An item to be held by a Pokémon. This exotic-smelling incense boosts the power of Psychic-type moves.";
		}
		if ( $itemNum == 60 ) {
			return "An item to be held by a Pokémon. This exotic-smelling incense boosts the power of Rock-type moves.";
		}
		if ( $itemNum == 61 ) {
			return "An item to be held by a Pokémon. This exotic-smelling incense boosts the power of Grass-type moves.";
		}
		if ( $itemNum == 62 ) {
			return "A transparent device somehow filled with all sorts of data. It was produced by Silph Co.";
		}
		if ( $itemNum == 63 ) {
			return "A very tough and inflexible scale. Dragon-type Pokémon may be holding this item when caught.";
		}
		if ( $itemNum == 64 ) {
			return "A transparent device overflowing with dubious data. Its producer is unknown.";
		}
		if ( $itemNum == 65 ) {
			return "A box packed with a tremendous amount of electric energy. It's loved by a certain Pokémon.";
		}
		if ( $itemNum == 66 ) {
			return "A box packed with a tremendous amount of magma energy. It's loved by a certain Pokémon.";
		}
		if ( $itemNum == 67 ) {
			return "A protective item of some sort. It is extremely stiff and heavy. It's loved by a certain Pokémon.";
		}
		if ( $itemNum == 68 ) {
			return "A peculiar stone that can make certain species of Pokémon evolve. it's as round as a Pokémon Egg.";
		}
		if ( $itemNum == 69 ) {
			return "An item to be held by a Pokémon. This sharply hooked claw increases the holder's critical-hit ratio.";
		}
		if ( $itemNum == 70 ) {
			return "An item to be held by a Pokémon. When the holder successfully inflicts damage, the target may also flinch.";
		}
		if ( $itemNum == 77 ) {
			return "An item to be held by Clamperl. This fang gleams a sharp silver and raises the holder's Sp. Atk stat.";
		}
		if ( $itemNum == 78 ) {
			return "An item to be held by Clamperl. This scale shines with a faint pink and raises the holder's Sp. Def stat.";
		}
		if ( $itemNum == 79 ) {
			return "An item to be held by a Pokémon. It helps keep wild Pokémon away if the holder is the head of the party.";
		}
		return "None";
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////?>
</body>
</html>
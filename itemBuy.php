<?php
session_start();
$whichProfile = $_REQUEST[ 'whichProfile' ];
$showTopAd = "yes";
$showSideAd = "yes";
$id = $_SESSION[ 'myID' ];
$currentSave = $_SESSION[ 'currentSave3' ];
$loggedIn = true;
$pageTitle = "Item Store Buy";
include 'shared/basic.php';
include 'shared/legacyCoins.php';
include 'shared/sndCoins.php';
include 'shared/cookies.php';

$db = connect_To_Trading_Database();
$dbOriginal = connect_To_Original_Database();

//Update your currentSave to prevent cheating, will redirect to saved outside page
$currentSave = update_Current_Save($dbOriginal, $id, $currentSave);

$urlValidation = "whichProfile=" . $whichProfile;

//Get Legacy coin count
$legacyCount = getLegacyCoin( $db, $id );
$sndCount = getSndCoin( $dbOriginal, $id );

$itemNum = $_REQUEST['itemId'];
$currencyType = $_REQUEST['type'];
$canPurchase = true;
$validItem = true;
$cost = 1;
$quantity = 1;

//Make sure player is purchasing valid items
if ($itemNum != 17 && $itemNum != 18 && $itemNum != 19 && $itemNum != 20 && $itemNum != 21 && $itemNum != 22 && $itemNum != 23 && $itemNum != 24
    && $itemNum != 25 && $itemNum != 26 && $itemNum != 27 && $itemNum != 13 && $itemNum != 14) {
	$validItem = false;
}

if ($currencyType == "snd") {
	$quantity = 10;
	if ($sndCount < $cost) {
		$canPurchase = false;
	}
}else{
	if ($legacyCount < $cost) {
		$canPurchase = false;
	}
}

//Make actual purchase and deduct items
if ($canPurchase == true && $validItem == true) {
	$transactionFlag = true;
	$dbOriginal->autocommit(false);
	//Subtract coins
	if ($currencyType == "snd") {
		$transactionFlag = giveSndCoins($id, -$cost, $dbOriginal, $transactionFlag);
	}else{
		$db->autocommit(false);
		$transactionFlag = giveLegacyCoin($db, $id, -$cost, $transactionFlag);
	}
	if ( $transactionFlag == true ) {
		$query = "select whichItem, quantity from sndgame_ptd3_basic.items WHERE trainerID = ? AND whichProfile = ? AND whichItem = ?";
		$result = $dbOriginal->prepare( $query );
		$result->bind_param( "iii", $id, $whichProfile, $itemNum);
		$result->execute();
		$result->store_result();
		$result->bind_result( $myItemTemp, $myItemQuantity );
		$totalValues = $result->affected_rows;
		$result->fetch();
		$result->close();
		if ( $totalValues > 0 ) {
			$myItemQuantity += $quantity;
			$query = "UPDATE sndgame_ptd3_basic.items SET quantity = ? WHERE trainerID = ? AND whichProfile = ? AND whichItem = ?";
			$result = $dbOriginal->prepare( $query );
			$result->bind_param( "iiii", $myItemQuantity, $id, $whichProfile, $itemNum );
			if ( !$result->execute() ) {
				$transactionFlag = false;
			}
			$result->close();
		} else {
			$query = "INSERT INTO sndgame_ptd3_basic.items (whichItem, quantity, trainerID, whichProfile) VALUES (?, ?, ?, ?)";
			$result = $dbOriginal->prepare( $query );
			$result->bind_param( "iiii", $itemNum, $quantity, $id, $whichProfile );
			if ( !$result->execute() ) {
				$transactionFlag = false;
			}
			$result->close();
		}
		if ( $transactionFlag == true ) {
			$dbOriginal->commit();
			if ( $currencyType != "snd" ) {
				$db->commit();
				$db->autocommit( true );
				$db->close();
			}
		} else {
			$dbOriginal->rollback();
			if ($currencyType != "snd" ) {
				$db->rollback();
				$db->autocommit( true );
				$db->close();
			}
		}
		$dbOriginal->autocommit( true );
		$dbOriginal->close();
	}
}


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
					<li><a href="itemStore.php?<?php echo $urlValidation ?>">Item Store</a></li>
					<li class="active">Item Store Buy</li>
				</ol>
			</div>
		</div>
		<? include 'shared/adTop.php'; ?>
		<div class="row">
			<div class="col-lg-9 col-md-8 col-sm-7 col-xs-12">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h4 class="panel-title">Item Store Buy</h4>
					</div>
					<div class="panel-body">
					<?php if ($canPurchase == false) { ?>
						<div class="row">
							<div class="col-xs-12">
								<p>You don't have enough coins to make this purchase.</p>
							</div>
						</div>
					<?php }else if ($transactionFlag == false) { ?>
						<div class="row">
							<div class="col-xs-12">
								<p>An error has occured. Press back and try again.</p>
							</div>
						</div>
					<?php }else if ($validItem == false) { ?>
						<div class="row">
							<div class="col-xs-12">
								<p>Trying to purchase an invalid item. Sneaky you.</p>
							</div>
						</div>
					<?php }else{ ?>
						<div class="row">
							<div class="col-xs-12">
								<p>Congratulations! You have purchased (<?php echo $quantity?>) of the following item(s):
								</p>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								<?php echo itemBox($itemNum) ?>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								<p><strong>(Note: You can give this item to your pokemon inside of the game itself. Remember to avoid playing the game and the trading center at the same time.)</strong>
								</p>
							</div>
						</div>
					<?php } ?>
					</div>
				</div>
			</div>
			<? include 'shared/adSide.php'; ?>
		</div>
		
	</div>
	</div>
	<?php include 'shared/footer.php';
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function itemBox($itemNum) {
	$itemName = get_Item_Name($itemNum);
	$itemDescription = get_Item_Description($itemNum);
	?>
	<div class="itemblock panel-group col-lg-6">
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
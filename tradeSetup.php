<?php
session_start();
$whichProfile = $_REQUEST['whichProfile'];
$showTopAd = "yes";
$showSideAd = "yes";
$id = $_SESSION['myID'];
$currentSave = $_SESSION['currentSave3'];
$loggedIn = true;
$pageTitle = "Trade Setup";
require '../moveList.php';
include 'shared/basic.php';
include 'shared/cookies.php';

$db = connect_To_Original_Database();

$urlValidation = "whichProfile=".$whichProfile;

//Trade Setup Code
$whichDB = $profileInfo[3]; //get Which DB the Pokemon are stored in for this profile
$pokeId = $_REQUEST['pokeId'];
if (!isset($pokeId)) {
	redirect_To_NotAllowed();
}
$query = "SELECT num, lvl, shiny, originalOwner, uniqueID, nickname, myTag, m1, m2, m3, m4, gender, item, happy FROM sndgame_ptd3_basic.trainerPokemons".$whichDB." WHERE trainerID = ? AND whichProfile = ? AND uniqueID = ?";
$result = $db->prepare($query);
$result->bind_param("iii", $id, $whichProfile, $pokeId);
$result->execute();
$result->store_result();
$hmp = $result->affected_rows;
$result->bind_result($pokeNum, $pokeLevel,$pokeShiny, $originalOwner, $pokeID, $pokeNickname, $myTag, $move1, $move2, $move3, $move4, $pokeGender, $pokeItem, $pokeHoF);
if ($result->affected_rows == 0) {
	$result->free_result();
	$result->close();
	redirect_To_NotAllowed();
}else{
	$result->fetch();
}
$result->free_result();
$result->close();

include 'shared/head.php';
?>
<script src="scripts/tradeSetup.js"></script>
<? include 'shared/pokeDropDown.php'; ?>
<body>
<? include 'shared/navbar.php'; ?>
<div class="container-fluid">
	<div class = "row">
        <div class = "col-sm-12" style="padding-top: 70px">
        	<ol class="breadcrumb">
            <li><a href="main.php?<?php echo $urlValidation ?>">Home</a></li>
            <li><a href="createTrade.php?<?php echo $urlValidation ?>">Create Trade</a></li>
            <li class="active">Trade Setup</li>
            </ol>
        </div>
    </div>
    <? include 'shared/adTop.php'; ?>
    <div class="row">
    	<div class = "col-sm-12">
        <?php block_poke_trade_setup($pokeID, $pokeNum, $pokeNickname, $move1, $move2, $move3, $move4, $pokeLevel, $pokeShiny,
		$pokeItem, $myTag, $pokeHoF, $pokeGender); ?>
        </div>
    </div>
    <div class="row">
        <div class = "col-lg-9 col-md-8 col-sm-7 col-xs-12" >
          <div class="panel panel-primary">
            <div class="panel-heading">
              <h4 class="panel-title">Trade Setup</h4>
            </div>
            <div class = "panel-body">
                Create any request that you may have in exchange for this pokémon. Once you are done press the "Create Trade" button.
            </div>
            <form action="tradeMe.php?<?php echo $urlValidation ?>&Action=setup&pokeID=<?php echo $pokeID ?>" method="post" name="form1" id="form1">
            <ul class = "list-group">                
            	<!--CREATE REQUEST SECTION-->
                <li class="list-group-item">            
                    <style>
						.nofloat {
							float:none;
						}
					</style>
                    <?php for ($i=1, $b=3; $i<=3; $i++, $b--) {
                    		createRequest($i);
                    }?>
                    <h4>Request left: <span class="label label-info" id="requestCount">3</span></h4>
                    <p><button role="button" class="btn btn-info btn-sm" id="requestBtn" onClick="createNewRequest();return false;">Create New Request</button></p>
                    <script>
					//UPDATE REQUEST POKE INFO
					$("select").change(function() {
						if (jQuery.hasData(this) == false) {
							return;
						}
						var whichPoke = $(this).data("poke");
						var which = $(this).data("request");
						var name = "#poke"+whichPoke+"_"+which;
						var num = $(name).val();
						//SPECIFIC POKEMON RULES
//						if (num == 172) { //pichu
//							name = "#type"+whichPoke+"_"+which;
//							$(name).val(0);
//							$(name).prop("disabled", true);//disable type
//							name = "#gender"+whichPoke+"_"+which;
//							$(name).val(1);
//							$(name).prop("disabled", true);//disable gender
//						}else{
							name = "#type"+whichPoke+"_"+which;
							$(name).prop("disabled", false);//enable type
							name = "#gender"+whichPoke+"_"+which;
							$(name).prop("disabled", false);//enable type
						//}
						var name = "#type"+whichPoke+"_"+which;
						var shiny = $(name).val();
						var name = "#gender"+whichPoke+"_"+which;
						var gender = $(name).val();
						//var name = "#levelComparison"+whichPoke+"_"+which;
						var lvlComp = -1;//$(name).val();
						//var name = "#level"+whichPoke+"_"+which;
						var lvl = -1;//$(name).val();
						update_Poke_Request_Info(num, gender, lvlComp, lvl, shiny, whichPoke, which);
					});
					$(".pokeReset").click(function() {
						if (jQuery.hasData(this) == false) {
							return;
						}
						var whichPoke = $(this).data("poke");
						var which = $(this).data("request");
						var name = "#poke"+whichPoke+"_"+which;
						$(name).val("-1")
						var num = "-1";
						var name = "#type"+whichPoke+"_"+which;
						$(name).val("-1");
						$(name).prop("disabled", false);//enable type
						var shiny = "-1";
						var name = "#gender"+whichPoke+"_"+which;
						$(name).val("-1");
						$(name).prop("disabled", false);//enable gender
						var gender = "-1";
						var lvlComp = "-1";//$(name).val();
						var lvl = "-1";//$(name).val();
						update_Poke_Request_Info(num, gender, lvlComp, lvl, shiny, whichPoke, which);
					});
					</script>
                </li>
                <!--END CREATE REQUEST SECTION-->
     			<!--ADOPT NOW SECTION-->
                <li class="list-group-item">
                    <h4>
                    Adopt Now Price:
                    </h4>
                    <p>Set an adoption price in SnD coins for a trainer to adopt your pokémon.</p>
                    <div class="row">
                        <div class="col-sm-3">
                            <p>
                            <select class="form-control col-sm-3" name="adoptSel" id="adoptSel">
                                <option value="0">None</option>
                                <?php
								for ($i=1; $i<=20; $i++) {
									echo '<option value="'.$i.'">'.$i.'</option>';
								}
								?>
                            </select>
                            </p>
                        </div>
                    </div>
                </li>
                <!-- END ADOPT SECTION -->
                <!--ALLOW HACK SECTION-->
                <li class="list-group-item">
                    <h4>
                    Allow Hacked Offers:
                    </h4>
                    <div class="row">
                        <div class="col-sm-3">
                            <p>
                            <select class="form-control col-sm-3" name="hackSel" id="hackSel">
                                <option value="0">No</option>
								<option value="1">Yes</option>
                            </select>
                            </p>
                        </div>
                    </div>
                </li>
                <!-- END ALLOW HACK SECTION -->
                <!--TRADE SUMMARY SECTION-->
                <li class="list-group-item">
                    <h4>Create your Trade:</h4>
                    <p>If you make no Request, trainers can make offers to you but no trades will be done automatically.</p>
                    <button type="submit" class="btn btn-success">Create Trade</button>
                </li>
                <!--END TRADE SUMMARY SECTION-->
                </form>
            </ul>
          </div>
      	</div>
    <? include 'shared/adSide.php'; ?>
    </div>
</div>
<?php
function createRequest($which) {
	?>
	<div class="row">
        <div class="col-sm-6">
            <div class="panel panel-success" id="requestPanel<?php echo $which ?>" style="display:none">
                <div class="panel-heading">
                    <h4>Request #<?php echo $which ?></h4>
                </div>
                <div class="list-group">
                    <?php for ($i=1; $i<=6; $i++) {
						createRequestPokemon($which, $i);
					}
					?> 
                    <div class="list-group-item list-group-item-success">
                    <button type="button" role="button" class="btn btn-primary addPoke" data-pokeLeft="5" data-which="<?php echo $which ?>">Add another Pokémon <span class="badge" id="requestAddMore<?php echo $which?>">5</span></button>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
 <?php
}
function createRequestPokemon($which, $whichPoke) { 
?>
<div class="list-group-item list-group-item-success" id="pokeRequestTop<?php echo $whichPoke ?>_<?php echo $which?>" <?php if($whichPoke != 1) { ?> style="display:none" <?php } ?>>
			<div class="row" style="padding-bottom:5px"><div class="col-xs-12">
            Pokémon <?php echo $whichPoke ?>: <button type="button" class="btn btn-primary btn-xs" data-toggle="collapse" data-target="#pokeRequestItem<?php echo $which ?>_<?php echo $whichPoke?>"><span class="glyphicon  glyphicon-pencil"></span></button> <strong><button type="button" class="btn btn-danger btn-xs pokeReset" data-poke="<?php echo $whichPoke?>" data-request="<?php echo $which ?>"></strong><span class="glyphicon glyphicon-remove"></span></button>
            </div></div>
            <div class="row">
                <div class="pokeblock panel-group col-xs-12" id="pokeRequest<?php echo $whichPoke ?>_<?php echo $which?>" style="margin-bottom:0px; cursor:pointer;">
                    <div class = "panel panel-primary" id="pokePanel<?php echo $whichPoke ?>_<?php echo $which?>" data-toggle="collapse" data-target="#pokeRequestItem<?php echo $which ?>_<?php echo $whichPoke?>">
                        <div class = "panel-heading" id="panel<?php echo $whichPoke ?>_<?php echo $which?>"> 
                        
                        </div>       
                    </div>
                </div>
            </div>
 </div>
 <div id="pokeRequestItem<?php echo $which ?>_<?php echo $whichPoke?>" class="collapse">
     <div class="list-group-item list-group-item-success">
                Which Pokémon: <select id="poke<?php echo $whichPoke?>_<?php echo $which ?>" name="poke<?php echo $whichPoke?>_<?php echo $which ?>" data-poke="<?php echo $whichPoke?>" data-request="<?php echo $which ?>" class="form-control nofloat">
                <option value="-1">None</option>
                <option value="0">Any</option>
                <?php echo pokemon_Drop_Down_Options()?>
                </select><br/>
                Special Type: <select id="type<?php echo $whichPoke?>_<?php echo $which ?>" name="type<?php echo $whichPoke?>_<?php echo $which ?>" data-poke="<?php echo $whichPoke?>" data-request="<?php echo $which ?>" class="form-control nofloat">
                <?php echo special_Type_Drop_Down_Options()?>
                </select><br/>
                Gender: <select id="gender<?php echo $whichPoke?>_<?php echo $which ?>" name="gender<?php echo $whichPoke?>_<?php echo $which ?>" data-poke="<?php echo $whichPoke?>" data-request="<?php echo $which ?>" class="form-control nofloat">
                <?php echo gender_Options()?>
                </select><br/>
                <?php /*
             Level: <select id="levelComparison<?php echo $whichPoke?>_<?php echo $which ?>" name="levelComparison<?php echo $whichPoke?>_<?php echo $which ?>" data-poke="<?php echo $whichPoke?>" data-request="<?php echo $which ?>" class="form-control nofloat">
             <option value="1">=</option>
             <option value="2"><=</option>
             <option value="3">>=</option>
             <option value="4"><</option>
             <option value="5">></option>
             </select><br/>
             <select id="level<?php echo $whichPoke?>_<?php echo $which ?>" name="level<?php echo $whichPoke?>_<?php echo $which ?>" data-poke="<?php echo $whichPoke?>" data-request="<?php echo $which ?>" class="form-control nofloat">
             <option value="0">Any</option>
             <?php
			 	for($m=1; $m<=10; $m++) {
					echo '<option value="'.$m.'">'.$m.'</option>';
				}
			 ?>
             </select><br/> */?>
             <button type="button" role="button" class="btn btn-primary" data-toggle="collapse" data-target="#pokeRequestItem<?php echo $which ?>_<?php echo $whichPoke?>">Done with this Pokémon</button><br/>
     </div>
 </div>
 <script>
	update_Poke_Request_Info(-1, 1, 1, -1, 1, <?php echo $whichPoke?>, <?php echo $which?>);
</script>
<?php
}
?>
<?php include 'shared/footer.php'; ?>
</body>
</html>
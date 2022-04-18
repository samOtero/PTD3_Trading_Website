<?php
include 'shared/database.php';
include 'shared/basic2.php';

/////////////////////////////////////////////////////////////////////////////////////////////////
function block_poke_long($id, $num, $nickname, $m1, $m2, $m3, $m4, $level, $shiny, $item, $tag, $hof, $gender, $boxName="") {
	 
	 //get gender img if needed
	$genderName = get_Gender($gender);
	$genderIcon = "";
	if ($genderName != "none") {
		$genderIcon = '<img src = "http://www.ptdtrading.com/trading_center/images/'.$genderName.'.png" title="'.$genderName.'"/>';
	}
	//Hall of Fame Icon
	$hofIcon = '';
	if ($hof != 0) {
		$hofIcon = '<img src = "http://www.ptdtrading.com/images/ribbon_smaller.png" title="Hall of Fame Medal"/>';
	}
	
	//Panel Style based on Shiny
	$panelType = get_Panel_Name($shiny);
	
	//Extra Name depending on shiny
	$extraName = get_Abr_Shiny_Name($shiny);
	
	//Hacked Tag
	$hackedLabel = '';
	if ($tag == "h") {
		$hackedLabel = '<span class="label label-danger">H</span>';
	}
  ?>
  	<div class="pokeblock panel-group col-xs-12" id="pokeblock<?php echo $id ?>">
    <div class = "panel panel-primary <?php echo $panelType ?> ">
    	<div class = "panel-heading" id="panel<?php echo $id ?>" style="cursor: pointer"  data-toggle="collapse" data-target="#collapse<?php echo $id.$boxName ?>"> 
        	<img src="http://www.ptdtrading.com/games/ptd/small/<?php echo $num ?>_0.png"><span id="nickname<?php echo $id?>"><?php echo $nickname?></span><?php echo $genderIcon ?><?php echo $hofIcon ?>  <span class="label label-success"><?php echo $extraName ?></span> <?php echo $hackedLabel?> <span class="pull-right">Lvl <?php echo $level ?></span>
        </div>
        <div id="collapse<?php echo $id.$boxName ?>" class = "panel-body panel-collapse collapse">
            <div class = "row">
                <div class = "col-xs-12 text-center">
                    <h4>
                        	<p>
							<?php if ($m1 != 0) {?>
                            <span class="label label-primary"><?php echo get_Move_Name_By_ID($m1)?></span>
							<?php } 
							 if ($m2 != 0) {?>
                             <span class="label label-primary"><?php echo get_Move_Name_By_ID($m2)?></span>
                             <?php } ?>
                             </p>
                        	<p>
                            <?php if ($m3 != 0) {?>
                            <span class="label label-primary"><?php echo get_Move_Name_By_ID($m3)?></span>
                            <?php }
							if ($m4 != 0) {?>
                             <span class="label label-primary"><?php echo get_Move_Name_By_ID($m4)?></span>
                             <?php } ?>
                             </p>
                        <p><span class="label label-warning"><?php echo get_Item_Name($item)?></span></p>
                    </h4>
				</div>
            </div>
		</div>        
    </div>
  </div>
  <?php
    }
 /////////////////////////////////////////////////////////////////////////////////////////////////
function block_poke_my_trade($id, $num, $nickname, $m1, $m2, $m3, $m4, $level, $shiny, $item, $tag, $hof, $gender, $urlValidation, $requestNum, $whichProfile, $whichDB) {
	 
	 //get gender img if needed
	$genderName = get_Gender($gender);
	$genderIcon = "";
	if ($genderName != "none") {
		$genderIcon = '<img src = "http://www.ptdtrading.com/trading_center/images/'.$genderName.'.png" title="'.$genderName.'"/>';
	}
	//Hall of Fame Icon
	$hofIcon = '';
	if ($hof != 0) {
		$hofIcon = '<img src = "http://www.ptdtrading.com/images/ribbon_smaller.png" title="Hall of Fame Medal"/>';
	}
	
	//Panel Style based on Shiny
	$panelType = get_Panel_Name($shiny);
	
	//Extra Name depending on shiny
	$extraName = get_Abr_Shiny_Name($shiny);
	
	//Hacked Tag
	$hackedLabel = '';
	if ($tag == "h") {
		$hackedLabel = '<span class="label label-danger">H</span>';
	}
  ?>
  	<div class="pokeblock panel-group col-md-6" id="pokeblock<?php echo $id ?>">
		<div style="padding-bottom: 7px;padding-left: 5px">Request <span class="badge" style="background-color: #5cb85c;"><?php echo $requestNum ?></span></div>
    <div class = "panel panel-primary <?php echo $panelType ?> ">
    	<div class = "panel-heading" id="panel<?php echo $id ?>" style="cursor: pointer"  data-toggle="collapse" data-target="#collapse<?php echo $id.$boxName ?>"> 
        	<img src="http://www.ptdtrading.com/games/ptd/small/<?php echo $num ?>_0.png"><span id="nickname<?php echo $id?>"><?php echo $nickname?></span><?php echo $genderIcon ?><?php echo $hofIcon ?>  <span class="label label-success"><?php echo $extraName ?></span> <?php echo $hackedLabel?> <span class="pull-right">Lvl <?php echo $level ?></span>
        </div>
        <div id="collapse<?php echo $id ?>" class = "panel-body panel-collapse collapse">
            <div class = "row">
                <div class = "col-sm-6 text-center">
                    <h4>
                        	<p>
							<?php if ($m1 != 0) {?>
                            <span class="label label-primary"><?php echo get_Move_Name_By_ID($m1)?></span>
							<?php } 
							 if ($m2 != 0) {?>
                             <span class="label label-primary"><?php echo get_Move_Name_By_ID($m2)?></span>
                             <?php } ?>
                             </p>
                        	<p>
                            <?php if ($m3 != 0) {?>
                            <span class="label label-primary"><?php echo get_Move_Name_By_ID($m3)?></span>
                            <?php }
							if ($m4 != 0) {?>
                             <span class="label label-primary"><?php echo get_Move_Name_By_ID($m4)?></span>
                             <?php } ?>
                             </p>
                        <p><span class="label label-warning"><?php echo get_Item_Name($item)?></span></p>
                    </h4>
				</div>
				<div class = "col-sm-6 text-center" >
					<div style="margin-bottom: 10px"><h5>TradeID: <span class="label label-primary"><?php echo $id ?></span></h5></div>
                	<div class="btn-group-vertical btn-group-sm" role="group" aria-label="...">
						<a role="button" class="btn btn-success" id="viewRequestBtn<?php echo $id ?>" href="viewRequest.php?<?php echo $urlValidation ?>&pokeId=<?php echo $id ?>">View Request <span class="badge"><?php echo $requestNum ?></span></a>
                   		<button role="button" class="btn btn-danger" id="callbackBtn<?php echo $id ?>" data-toggle="collapse" data-target="#collapse2_<?php echo $id ?>">Call back to profile</button>
                    </div>
                </div>
                
            </div>
		</div> 
   		<!-- ABANDON PANEL -->
		<div id="collapse2_<?php echo $id ?>" class = "panel-body panel-collapse collapse">
			<div class = "row">
				<div id="callback_<?php echo $id ?>" class = "col-xs-12 text-center">
					Are you sure you want to call back?<br/>
					<button type="button" class="btn btn-success btn-md" data-toggle="collapse" data-target="#collapse2_<?php echo $id ?>">No</button>
					<button type="button" class="btn btn-danger btn-md" onClick="callBackPoke('<?php echo $id?>', '<?php echo $whichProfile?>', '<?php echo $whichDB?>');return false;">Yes</button>

				</div>
			</div>
		</div>
		<!-- END ABANDON PANEL -->       
    </div>
  </div>
  <?php
    }
 /////////////////////////////////////////////////////////////////////////////////////////////////
function block_poke_search_trade($id, $num, $nickname, $m1, $m2, $m3, $m4, $level, $shiny, $item, $tag, $hof, $gender, $urlValidation, $hasRequest, $sndCost) {
	 
	 //get gender img if needed
	$genderName = get_Gender($gender);
	$genderIcon = "";
	if ($genderName != "none") {
		$genderIcon = '<img src = "http://www.ptdtrading.com/trading_center/images/'.$genderName.'.png" title="'.$genderName.'"/>';
	}
	//Hall of Fame Icon
	$hofIcon = '';
	if ($hof != 0) {
		$hofIcon = '<img src = "http://www.ptdtrading.com/images/ribbon_smaller.png" title="Hall of Fame Medal"/>';
	}
	
	//Panel Style based on Shiny
	$panelType = get_Panel_Name($shiny);
	
	//Extra Name depending on shiny
	$extraName = get_Abr_Shiny_Name($shiny);
	
	//Hacked Tag
	$hackedLabel = '';
	if ($tag == "h") {
		$hackedLabel = '<span class="label label-danger">H</span>';
	}
  ?>
  	<div class="pokeblock panel-group col-lg-3 col-md-4 col-sm-6" id="pokeblock<?php echo $id ?>">
    <div class = "panel panel-primary <?php echo $panelType ?>">
    	<div class = "panel-heading" id="panel<?php echo $id ?>" style="cursor: pointer"> 
        <img src="http://www.ptdtrading.com/games/ptd/small/<?php echo $num ?>_0.png"><span id="nickname<?php echo $id?>"><?php echo $nickname?></span><?php echo $genderIcon ?><?php echo $hofIcon ?>  <span class="label label-success"><?php echo $extraName ?></span> <?php echo $hackedLabel?> <span class="pull-right">Lvl <?php echo $level ?></span>
        </div>
        <div id="collapse<?php echo $id ?>" class = "panel-body panel-collapse collapse">
            <div class = "row">
                <div class = "col-xs-6 text-center">
                    <h4>
                        	<p>
							<?php if ($m1 != 0) {?>
                            <span class="label label-primary"><?php echo get_Move_Name_By_ID($m1)?></span>
							<?php } 
							 if ($m2 != 0) {?>
                             <span class="label label-primary"><?php echo get_Move_Name_By_ID($m2)?></span>
                             <?php } ?>
                             </p>
                        	<p>
                            <?php if ($m3 != 0) {?>
                            <span class="label label-primary"><?php echo get_Move_Name_By_ID($m3)?></span>
                            <?php }
							if ($m4 != 0) {?>
                             <span class="label label-primary"><?php echo get_Move_Name_By_ID($m4)?></span>
                             <?php } ?>
                             </p>
                        <p><span class="label label-warning"><?php echo get_Item_Name($item)?></span></p>
                    </h4>
                </div>
                <div class = "col-xs-6 text-center" >
                	<div class="btn-group-vertical btn-group-sm" role="group" aria-label="...">
                    	<a id="tradeBtn<?php echo $id ?>" role="button" class="btn btn-success" href="requestTrade.php?<?php echo $urlValidation ?>&pokeId=<?php echo $id ?>">Request Trade</a>
                        <?php
						if ($hasRequest == 1) {
						?>
                        <button id="viewRequestBtn<?php echo $id ?>" data-toggle="collapse" data-target="#collapse3_<?php echo $id ?>" type="button" class="btn btn-primary">View Trade Request</button>
                        <?php
						}
						if ($sndCost > 0) {
						?>
						<a id="adoptBtn<?php echo $id ?>" role="button" class="btn btn-success" href="requestTrade.php?<?php echo $urlValidation ?>&pokeId=<?php echo $id ?>">Adopt Now <span class="badge"><?php echo $sndCost?></span></a>
						<?php
						}
						?>
                    </div>
                </div>
            </div>
        </div>
        
         <!-- TRADE REQUEST PANEL -->
        <div id="collapse3_<?php echo $id ?>" class = "panel-body panel-collapse collapse">
            <div class="row">
            	<div class="xs-col-12 text-center">Loading...</div>
            </div>
        </div>
        <!-- END TRADE REQUEST PANEL -->
        <script>
			$("#viewRequestBtn<?php echo $id ?>").click(function () {
				var colmain = $("#collapse3_<?php echo $id ?>");
				var isExpanded = colmain.attr("aria-expanded");
				if (isExpanded == "true") {
					colmain.collapse('hide');
				}else{
					colmain.collapse('show');
					$("#collapse2_<?php echo $id ?>").html('<div class="row"><div class="xs-col-12 text-center">Loading...</div></div>');
					loadTradeRequest("<?php echo $id ?>");
				}
			});
			$("#panel<?php echo $id ?>").click(function() {
				var colmain = $("#collapse<?php echo $id ?>");
				var isExpanded = colmain.attr("aria-expanded");
				if (isExpanded == "true") {
					colmain.collapse('hide');
					$("#collapse3_<?php echo $id ?>").collapse('hide');
				}else{
					colmain.collapse('show');
				}
			});
		</script>
        
    </div>
  </div>
  <?php
    }
 /////////////////////////////////////////////////////////////////////////////////////////////////
function block_poke_request_trade($id, $num, $nickname, $m1, $m2, $m3, $m4, $level, $shiny, $item, $tag, $hof, $gender, $boxName="") {
	 
	 //get gender img if needed
	$genderName = get_Gender($gender);
	$genderIcon = "";
	if ($genderName != "none") {
		$genderIcon = '<img src = "http://www.ptdtrading.com/trading_center/images/'.$genderName.'.png" title="'.$genderName.'"/>';
	}
	//Hall of Fame Icon
	$hofIcon = '';
	if ($hof != 0) {
		$hofIcon = '<img src = "http://www.ptdtrading.com/images/ribbon_smaller.png" title="Hall of Fame Medal"/>';
	}
	
	//Panel Style based on Shiny
	$panelType = get_Panel_Name($shiny);
	
	//Extra Name depending on shiny
	$extraName = get_Abr_Shiny_Name($shiny);
	
	//Hacked Tag
	$hackedLabel = '';
	if ($tag == "h") {
		$hackedLabel = '<span class="label label-danger">H</span>';
	}
  ?>
  	<div class="pokeblock panel-group col-lg-4 col-md-6" id="pokeblock<?php echo $id ?>">
  	Offer Me: <input type="checkbox" name="offer[]" value="<?php echo $id ?>"/>
    <div class = "panel panel-primary <?php echo $panelType ?>" data-toggle="collapse" data-target="#collapse<?php echo $id.$boxName ?>">
    	<div class = "panel-heading" id="panel<?php echo $id ?>" style="cursor: pointer"> 
        <img src="http://www.ptdtrading.com/games/ptd/small/<?php echo $num ?>_0.png"><span id="nickname<?php echo $id?>"><?php echo $nickname?></span><?php echo $genderIcon ?><?php echo $hofIcon ?>  <span class="label label-success"><?php echo $extraName ?></span> <?php echo $hackedLabel?> <span class="pull-right">Lvl <?php echo $level ?></span>
        </div>
        <div id="collapse<?php echo $id.$boxName ?>" class = "panel-body panel-collapse collapse">
            <div class = "row">
                <div class = "col-xs-12 text-center">
                    <h4>
                        	<p>
							<?php if ($m1 != 0) {?>
                            <span class="label label-primary"><?php echo get_Move_Name_By_ID($m1)?></span>
							<?php } 
							 if ($m2 != 0) {?>
                             <span class="label label-primary"><?php echo get_Move_Name_By_ID($m2)?></span>
                             <?php } ?>
                             </p>
                        	<p>
                            <?php if ($m3 != 0) {?>
                            <span class="label label-primary"><?php echo get_Move_Name_By_ID($m3)?></span>
                            <?php }
							if ($m4 != 0) {?>
                             <span class="label label-primary"><?php echo get_Move_Name_By_ID($m4)?></span>
                             <?php } ?>
                             </p>
                        <p><span class="label label-warning"><?php echo get_Item_Name($item)?></span></p>
                    </h4>
                </div>
            </div>
        </div>        
    </div>
  </div>
  <?php
    }
 /////////////////////////////////////////////////////////////////////////////////////////////////
function block_poke_elemental_lab($pokeFamilyList, $urlValidation) {
	 
	//Regular Panel Name
	$panelType = get_Panel_Name(0);
	
	//Extra Name depending on shiny
	$extraName = "";//get_Abr_Shiny_Name($shiny);
	$pokeNum = $pokeFamilyList[0];
	$totalTypes = count($pokeFamilyList[1]);
	$total = 18; //How many elementals you need
	
	$pokeFamilyGfx = "";
	$pokeCount = count($pokeFamilyList[2]);
	for ($i=0; $i<$pokeCount; $i++) {
		$pokeFamilyGfx .= "<img src=\"http://www.ptdtrading.com/games/ptd/small/".$pokeFamilyList[2][$i]."_0.png\">";
	}
	
	if ($totalTypes >= $total)
		$extraName = "Completed!";
	
	//If marked as redeemed then just mark it as completed
	if ($pokeFamilyList[3] == 1) {
		$extraName = "Redeemed";
		$panelType = get_Panel_Name(1);
	}
	
  ?>
  	<div class="pokeblock panel-group col-lg-3 col-md-4 col-sm-6" id="pokeblock<?php echo $pokeNum ?>">
    <div class = "panel panel-primary <?php echo $panelType ?>" data-toggle="collapse" data-target="#collapse<?php echo $pokeNum ?>">
    	<div class = "panel-heading" id="panel<?php echo $pokeNum ?>" style="cursor: pointer"> 
			<?php echo $pokeFamilyGfx?><span id="nickname<?php echo $pokeNum?>">Family</span> <span class="label label-success"><?php echo $extraName ?></span> <span class="pull-right"><?php echo "$totalTypes/$total" ?></span>
        </div>
        <div id="collapse<?php echo $pokeNum ?>" class = "panel-body panel-collapse collapse">
            <div class = "row">
                <div class = "col-xs-12 text-center">
                    <h4>
                    <?php
	  					for($i=0; $i<$totalTypes; $i++){
							echo "<span class=\"label label-success\">".get_Abr_Shiny_Name($pokeFamilyList[1][$i])."</span> ";
						}
						//Show missing elements
						show_Missing_Elements($totalTypes, $pokeFamilyList);
	  				?>
                    </h4>
                </div>
                <?php
                if ($extraName == "Completed!") { ?>
					<div class = "col-xs-12 text-center" >
                	<div class="btn-group-vertical btn-group-sm" role="group" aria-label="...">
                    	<button role="button" class="btn btn-success btnCompleted" data-family="<?php echo $pokeNum?>" data-urlvalidation="<?php echo $urlValidation?>">Collect Reward!</button>
                    </div>
                </div>
                <?php
				}
				?>
            </div>
        </div>        
    </div>
  </div>
  <?php
    }
 /////////////////////////////////////////////////////////////////////////////////////////////////
function show_Missing_Elements($totalTypes, $pokeFamilyList) {
	for($i=3; $i<=21; $i++) {
		if ($i != 20) { //20 is not used for types
			$haveElement = false;
			for($a=0; $a<$totalTypes; $a++) {
				$haveType = $pokeFamilyList[1][$a];
				if ($haveType == $i) {
					$haveElement = true;
					break;
				}
			}
			if ($haveElement == false) {
				echo "<span class=\"label label-danger\">".get_Abr_Shiny_Name($i)."</span> ";
			}
		}
	}
}
/////////////////////////////////////////////////////////////////////////////////////////////////
function block_poke_trade_setup($id, $num, $nickname, $m1, $m2, $m3, $m4, $level, $shiny, $item, $tag, $hof, $gender, $boxName="") {
	 
	 //get gender img if needed
	$genderName = get_Gender($gender);
	$genderIcon = "";
	if ($genderName != "none") {
		$genderIcon = '<img src = "http://www.ptdtrading.com/trading_center/images/'.$genderName.'.png" title="'.$genderName.'"/>';
	}
	//Hall of Fame Icon
	$hofIcon = '';
	if ($hof != 0) {
		$hofIcon = '<img src = "http://www.ptdtrading.com/images/ribbon_smaller.png" title="Hall of Fame Medal"/>';
	}
	
	//Panel Style based on Shiny
	$panelType = get_Panel_Name($shiny);
	
	//Extra Name depending on shiny
	$extraName = get_Abr_Shiny_Name($shiny);
	
	//Hacked Tag
	$hackedLabel = '';
	if ($tag == "h") {
		$hackedLabel = '<span class="label label-danger">H</span>';
	}
  ?>
  	<div class="pokeblock panel-group col-lg-3 col-md-4 col-sm-6" id="pokeblock<?php echo $id ?>">
    <div class = "panel panel-primary <?php echo $panelType ?>" data-toggle="collapse" data-target="#collapse<?php echo $id.$boxName ?>">
    	<div class = "panel-heading" id="panel<?php echo $id ?>" style="cursor: pointer"> 
        <img src="http://www.ptdtrading.com/games/ptd/small/<?php echo $num ?>_0.png"><span id="nickname<?php echo $id?>"><?php echo $nickname?></span><?php echo $genderIcon ?><?php echo $hofIcon ?>  <span class="label label-success"><?php echo $extraName ?></span> <?php echo $hackedLabel?> <span class="pull-right">Lvl <?php echo $level ?></span>
        </div>
        <div id="collapse<?php echo $id.$boxName ?>" class = "panel-body panel-collapse collapse">
            <div class = "row">
                <div class = "col-xs-12 text-center">
                    <h4>
                        	<p>
							<?php if ($m1 != 0) {?>
                            <span class="label label-primary"><?php echo get_Move_Name_By_ID($m1)?></span>
							<?php } 
							 if ($m2 != 0) {?>
                             <span class="label label-primary"><?php echo get_Move_Name_By_ID($m2)?></span>
                             <?php } ?>
                             </p>
                        	<p>
                            <?php if ($m3 != 0) {?>
                            <span class="label label-primary"><?php echo get_Move_Name_By_ID($m3)?></span>
                            <?php }
							if ($m4 != 0) {?>
                             <span class="label label-primary"><?php echo get_Move_Name_By_ID($m4)?></span>
                             <?php } ?>
                             </p>
                        <p><span class="label label-warning"><?php echo get_Item_Name($item)?></span></p>
                    </h4>
                </div>
            </div>
        </div>        
    </div>
  </div>
  <?php
    }
 /////////////////////////////////////////////////////////////////////////////////////////////////
function block_poke_pickup($id, $num, $nickname, $m1, $m2, $m3, $m4, $level, $shiny, $item, $tag, $hof, $gender, $whichProfile, $whichDB, $urlValidation) {
	 
	 //get gender img if needed
	$genderName = get_Gender($gender);
	$genderIcon = "";
	if ($genderName != "none") {
		$genderIcon = '<img src = "http://www.ptdtrading.com/trading_center/images/'.$genderName.'.png" title="'.$genderName.'"/>';
	}
	//Hall of Fame Icon
	$hofIcon = '';
	if ($hof != 0) {
		$hofIcon = '<img src = "http://www.ptdtrading.com/images/ribbon_smaller.png" title="Hall of Fame Medal"/>';
	}
	
	//Panel Style based on Shiny
	$panelType = get_Panel_Name($shiny);
	
	//Extra Name depending on shiny
	$extraName = get_Abr_Shiny_Name($shiny);
	
	//Hacked Tag
	$hackedLabel = '';
	if ($tag == "h") {
		$hackedLabel = '<span class="label label-danger">H</span>';
	}
  ?>
  	<div class="pokeblock panel-group col-lg-3 col-md-4 col-sm-6" id="pokeblock<?php echo $id ?>">
    <div class = "panel panel-primary <?php echo $panelType ?>">
    	<div class = "panel-heading" id="panel<?php echo $id ?>" style="cursor: pointer"> 
        <img src="http://www.ptdtrading.com/games/ptd/small/<?php echo $num ?>_0.png"><span id="nickname<?php echo $id?>"><?php echo $nickname?></span><?php echo $genderIcon ?><?php echo $hofIcon ?>  <span class="label label-success"><?php echo $extraName ?></span> <?php echo $hackedLabel?> <span class="pull-right">Lvl <?php echo $level ?></span>
        </div>
        <div id="collapse<?php echo $id ?>" class = "panel-body panel-collapse collapse">
            <div class = "row">
                <div class = "col-xs-6 text-center">
                    <h4>
                        	<p>
							<?php if ($m1 != 0) {?>
                            <span class="label label-primary"><?php echo get_Move_Name_By_ID($m1)?></span>
							<?php } 
							 if ($m2 != 0) {?>
                             <span class="label label-primary"><?php echo get_Move_Name_By_ID($m2)?></span>
                             <?php } ?>
                             </p>
                        	<p>
                            <?php if ($m3 != 0) {?>
                            <span class="label label-primary"><?php echo get_Move_Name_By_ID($m3)?></span>
                            <?php }
							if ($m4 != 0) {?>
                             <span class="label label-primary"><?php echo get_Move_Name_By_ID($m4)?></span>
                             <?php } ?>
                             </p>
                        <p><span class="label label-warning"><?php echo get_Item_Name($item)?></span></p>
                    </h4>
                </div>
                <div class = "col-xs-6 text-center" >
                	<div class="btn-group-vertical btn-group-sm" role="group" aria-label="...">
                    	<button id="profileBtn<?php echo $id ?>" role="button" class="btn btn-primary">Send To Profile</button>
                        <button id="abandonBtn<?php echo $id ?>" type="button" class="btn btn-danger">Abandon</button>
                    </div>
                </div>
            </div>
		</div>
        
        <!-- ABANDON PANEL -->
        <div id="collapse2_<?php echo $id ?>" class = "panel-body panel-collapse collapse">
            <div class = "row">
                <div id="abandonContent_<?php echo $id ?>" class = "col-xs-12 text-center">
                    Abandon this Pokémon forever?<br/>
                    <button type="button" class="btn btn-success btn-md" data-toggle="collapse" data-target="#collapse2_<?php echo $id ?>">No</button>
                    <button type="button" class="btn btn-danger btn-md" onClick="abandonFirstTryPickup('<?php echo $id?>');return false;">Yes</button>
                       
                </div>
            </div>
        </div>
        <!-- END ABANDON PANEL -->
        <!-- PICKUP PANEL -->
        <div id="collapse3_<?php echo $id ?>" class = "panel-body panel-collapse collapse">
            <div class = "row">
                <div id="pickupContent_<?php echo $id ?>" class = "col-xs-12 text-center">
                    Sending this Pokémon to your profile...<br/>                       
                </div>
            </div>
        </div>
        <!-- END PICKUP PANEL -->
        <script>
			$("#panel<?php echo $id ?>").click(function() {
				var colmain = $("#collapse<?php echo $id ?>");
				var isExpanded = colmain.attr("aria-expanded");
				if (isExpanded == "true") {
					colmain.collapse('hide');
				}else{
					colmain.collapse('show');
				}
			});
			$("#abandonBtn<?php echo $id ?>").click(function() {
				var colmain = $("#collapse2_<?php echo $id ?>");
				var isExpanded = colmain.attr("aria-expanded");
				if (isExpanded == "true") {
					colmain.collapse('hide');
				}else{
					resetAbandonPickup('<?php echo $id?>');
					colmain.collapse('show');
				}
			});
			$("#profileBtn<?php echo $id?>").click(function() {
				pickupPoke('<?php echo $id ?>', '<?php echo $whichProfile?>', '<?php echo $whichDB ?>');
			});
		</script>
        
    </div>
  </div>
  <?php
    }
 /////////////////////////////////////////////////////////////////////////////////////////////////
 function block_poke_create_trade($id, $num, $nickname, $m1, $m2, $m3, $m4, $level, $shiny, $item, $tag, $hof, $gender, $whichProfile, $whichDB, $urlValidation) {
	 
	 //get gender img if needed
	$genderName = get_Gender($gender);
	$genderIcon = "";
	if ($genderName != "none") {
		$genderIcon = '<img src = "http://www.ptdtrading.com/trading_center/images/'.$genderName.'.png" title="'.$genderName.'"/>';
	}
	//Hall of Fame Icon
	$hofIcon = '';
	if ($hof != 0) {
		$hofIcon = '<img src = "http://www.ptdtrading.com/images/ribbon_smaller.png" title="Hall of Fame Medal"/>';
	}
	
	//Panel Style based on Shiny
	$panelType = get_Panel_Name($shiny);
	
	//Extra Name depending on shiny
	$extraName = get_Abr_Shiny_Name($shiny);
	
	//Hacked Tag
	$hackedLabel = '';
	if ($tag == "h") {
		$hackedLabel = '<span class="label label-danger">H</span>';
	}
  ?>
  	<div class="pokeblock panel-group col-lg-3 col-md-4 col-sm-6" id="pokeblock<?php echo $id ?>">
    <div class = "panel panel-primary <?php echo $panelType ?>">
    	<div class = "panel-heading" id="panel<?php echo $id ?>" style="cursor: pointer"> 
        <img src="http://www.ptdtrading.com/games/ptd/small/<?php echo $num ?>_0.png"><span id="nickname<?php echo $id?>"><?php echo $nickname?></span><?php echo $genderIcon ?><?php echo $hofIcon ?>  <span class="label label-success"><?php echo $extraName ?></span> <?php echo $hackedLabel?> <span class="pull-right">Lvl <?php echo $level ?></span>
        </div>
        <div id="collapse<?php echo $id ?>" class = "panel-body panel-collapse collapse">
            <div class = "row">
                <div class = "col-xs-7 text-center">
                    <h4>
                        	<p>
							<?php if ($m1 != 0) {?>
                            <span class="label label-primary"><?php echo get_Move_Name_By_ID($m1)?></span>
							<?php } 
							 if ($m2 != 0) {?>
                             <span class="label label-primary"><?php echo get_Move_Name_By_ID($m2)?></span>
                             <?php } ?>
                             </p>
                        	<p>
                            <?php if ($m3 != 0) {?>
                            <span class="label label-primary"><?php echo get_Move_Name_By_ID($m3)?></span>
                            <?php }
							if ($m4 != 0) {?>
                             <span class="label label-primary"><?php echo get_Move_Name_By_ID($m4)?></span>
                             <?php } ?>
                             </p>
                        <p><span class="label label-warning"><?php echo get_Item_Name($item)?></span></p>
                    </h4>
                </div>
                <div class = "col-xs-5 text-center" >
                	<div class="btn-group-vertical btn-group-sm" role="group" aria-label="...">
                    	<a id="tradeBtn<?php echo $id ?>" role="button" class="btn btn-primary" href="tradeSetup.php?<?php echo $urlValidation ?>&pokeId=<?php echo $id ?>">Trade</a>
                        <button id="nicknameBtn<?php echo $id ?>" data-toggle="collapse" data-target="#collapse3_<?php echo $id ?>" type="button" class="btn btn-primary">Nickname</button>
                        <button id="abandonBtn<?php echo $id ?>" type="button" class="btn btn-danger">Abandon</button>
                    </div>
                </div>
            </div>
        </div>
        
         <!-- NICKNAME PANEL -->
        <div id="collapse3_<?php echo $id ?>" class = "panel-body panel-collapse collapse">
            <div class = "row">
                <div id="nicknameContent_<?php echo $id ?>" class = "col-xs-12 text-center">
                <span id="nicknameError<?php echo $id ?>"></span>
                    <div class="input-group">
                    
                    	<input id="nicknameInput<?php echo $id ?>"type="text" class="form-control" placeholder="New Nickname...">
                        <span class="input-group-btn">
                        	<button id="nicknameChange<?php echo $id ?>" class="btn btn-success" type="button">Change</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <!-- END NICKNAME PANEL -->
        
        <!-- ABANDON PANEL -->
        <div id="collapse2_<?php echo $id ?>" class = "panel-body panel-collapse collapse">
            <div class = "row">
                <div id="abandonContent_<?php echo $id ?>" class = "col-xs-12 text-center">
                    Abandon this Pokémon forever?<br/>
                    <button type="button" class="btn btn-success btn-md" data-toggle="collapse" data-target="#collapse2_<?php echo $id ?>">No</button>
                    <button type="button" class="btn btn-danger btn-md" onClick="abandonFirstTry('<?php echo $id?>', '<?php echo $whichProfile?>', '<?php echo $whichDB?>');return false;">Yes</button>
                       
                </div>
            </div>
        </div>
        <!-- END ABANDON PANEL -->
        <script>
			$("#panel<?php echo $id ?>").click(function() {
				var colmain = $("#collapse<?php echo $id ?>");
				var isExpanded = colmain.attr("aria-expanded");
				if (isExpanded == "true") {
					colmain.collapse('hide');
					$("#collapse3_<?php echo $id ?>").collapse('hide');
				}else{
					colmain.collapse('show');
				}
			});
			$("#abandonBtn<?php echo $id ?>").click(function() {
				var colmain = $("#collapse2_<?php echo $id ?>");
				var isExpanded = colmain.attr("aria-expanded");
				if (isExpanded == "true") {
					colmain.collapse('hide');
				}else{
					resetAbandon('<?php echo $id?>', '<?php echo $whichProfile?>', '<?php echo $whichDB?>');
					colmain.collapse('show');
				}
			});
			$("#nicknameChange<?php echo $id ?>").click(function() {
				var newNickname = $("#nicknameInput<?php echo $id ?>").val();
				if ($.trim(newNickname).length != 0) {
					changeNickname('<?php echo $id?>', newNickname, '<?php echo $whichDB?>');
				}else{
					$("#nicknameError<?php echo $id ?>").html("Enter a new nickname.");
				}
			});
		</script>
        
    </div>
  </div>
  <?php
    }
 /////////////////////////////////////////////////////////////////////////////////////////////////
 function get_Pagination_Text($currentPage, $totalItems, $itemPerPage, $whichURL, $urlValidation) {
	 if ($currentPage) {
		$start = ($currentPage -1) * $itemPerPage;
	}else{
		$currentPage = 1;
		$start = 0;
	}
	if ($start > 0) {
		if ($totalItems < $start) {
			$start = 0;
			$currentPage = 1;
		}
	}
	if ($currentPage == 0)
		$currentPage = 1;
		
	$paginateText = '';
	$lastpage = ceil($totalItems/$itemPerPage);
	
	if ($totalItems > 0 && $lastpage > 1) {
		//Set up right more and left more
		$leftMore = $currentPage - 10;
		if ($leftMore < 1) {
			$leftMore = 1;
		}
		$rightMore = $currentPage + 10;
		if ($rightMore > $lastpage) {
			$rightMore = $lastpage;
		}
		$paginateText = '<nav aria-label="Page navigation"><ul class="pagination" style="margin:0px">';
		if ($currentPage > 1) {
			$paginateText .= '<li><a href="'.$whichURL.'page='.($currentPage -1).'&'.$urlValidation.'" aria-label="Previous"><span aria-hidden="true"><</span></a></li>';
			$paginateText .= '<li><a href="'.$whichURL.'page='.($leftMore).'&'.$urlValidation.'" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
		}else{
			$paginateText .= '<li class="disabled"><a href="#" aria-label="Previous"><span aria-hidden="true"><</span></a></li>';
			$paginateText .= '<li class="disabled"><a href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
		}
		//Get our pagination limits
		$totalOnEachSide = 9;
		$startPage = $currentPage - $totalOnEachSide;
		$endPage = $currentPage + $totalOnEachSide;
		//Add extra to other side if needed
		if ($startPage < 1) {
			$endPage += -$startPage + 1;
		}
		
		if ($endPage > $lastpage) {
			$startPage -= ($endPage - $lastpage)-1;
		}
		//Set limits
		if ($startPage < 1) {
			$startPage = 1;
		}
		
		if ($endPage > $lastpage) {
			$endPage = $lastpage;
		}
		for ($counter = $startPage; $counter <= $endPage; $counter++) {
			if ($counter == $currentPage) {
				$paginateText .= '<li class="active"><a href="'.$whichURL.'page='.$counter.'&'.$urlValidation.'">'.$counter.'</a></li>';
			}else{
				$paginateText .= '<li><a href="'.$whichURL.'page='.$counter.'&'.$urlValidation.'">'.$counter.'</a></li>';
			}
			$didFirst = true;
		}
		if ($currentPage < $lastpage) {
			$paginateText .= '<li><a href="'.$whichURL.'page='.($rightMore).'&'.$urlValidation.'" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
			$paginateText .= '<li><a href="'.$whichURL.'page='.($currentPage +1).'&'.$urlValidation.'" aria-label="Next"><span aria-hidden="true">></span></a></li>';
		}else{
			$paginateText .= '<li class="disabled"><a href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
			$paginateText .= '<li class="disabled"><a href="#" aria-label="Next"><span aria-hidden="true">></span></a></li>';
		}
		$paginateText .= '</ul></nav>';
	}
	return array($paginateText, $start);
}
/////////////////////////////////////////////////////////////////////////////////////////////////
function get_Basic_Profile_Info_PTD3($id, $whichProfile) {
 	$db = connect_To_Original_Database();
	$query = "select Version, Nickname, Money, whichDB from sndgame_ptd3_basic.trainerProfiles WHERE trainerID = ? AND whichProfile = ?";
	$result = $db->prepare($query);
	$result->bind_param("ii", $id, $whichProfile);
	$result->execute();
	$result->store_result();
	$result->bind_result($myVersion, $myNickname, $myMoney, $myDB);
	$totalProfiles = $result->affected_rows;
	if ($totalProfiles == 1) {
		$result->fetch();
		//$myAvatarName = get_Avatar_Name($myGender, $myAvatar);
		$myVersionName = get_Version_Name($myVersion);
		$result->free_result();
		$result->close();
		$db->close();
		//$myBadge2 = get_Story_Badge($id, $whichProfile);
		return array($myVersionName, $myNickname, $myMoney, $myDB);
	}
	$result->free_result();
	$result->close();
	$db->close();
	return NULL;	
 }
 /////////////////////////////////////////////////////////////////////////////////////////////////
 function get_Version_Name($myVersion) {
	 $versionName = "";
	if ($myVersion == 1) {
		$versionName = "Alpha";
	}else if ($myVersion == 2) {
		$versionName = "Omega";
	}
	return $versionName;
 }
 /////////////////////////////////////////////////////////////////////////////////////////////////
 function get_Current_Save_Status($id, $currentSave, $db) { //Connect to original user database and confirm that their currentSave has not changed.
	$reason = "good";
	$query = "select trainerID from sndgame_ptd3_basic.currentSave WHERE trainerID = ? AND currentSave = ?";
	$result = $db->prepare($query);
	$result->bind_param("is", $id, $currentSave);
	$result->execute();
	$result->store_result();
	$result->bind_result($temp);			
	if ($result->affected_rows) {
		$result->fetch();		
	}else{
		$reason = "savedOutside";
	}
	$result->free_result();
	$result->close();
	return $reason;
}
 /////////////////////////////////////////////////////////////////////////////////////////////////
 function check_If_Saved_Outside($dbGame, $profileInfo, $id, $currentSave) {
 	$reason = get_Current_Save_Status($id, $currentSave, $dbGame);
	if (is_null($profileInfo) || $reason == "savedOutside") {
		$dbGame->close();
		//redirect to saved outside page
		redirect_To_SavedOutside();
	}
 }
 /////////////////////////////////////////////////////////////////////////////////////////////////
 function redirect_To_SavedOutside() {
 	header("Location: http://www.ptdtrading.com/ptd3/savedOutside.php");
	exit;
 }
/////////////////////////////////////////////////////////////////////////////////////////////////
function redirect_To_NotAllowed() {
 	header("Location: http://www.ptdtrading.com/ptd3/notAllowed.php");
	exit;
 }
/////////////////////////////////////////////////////////////////////////////////////////////////
function redirect_To_Error() {
 	header("Location: http://www.ptdtrading.com/ptd3/error.php");
	exit;
 }
/////////////////////////////////////////////////////////////////////////////////////////////////
function update_Current_Save($db, $id, $currentSave) {
	$newCurrentSave = uniqid(true);
	$query = "UPDATE sndgame_ptd3_basic.currentSave SET currentSave = ? WHERE trainerID = ? AND currentSave = ?";
	$result = $db->prepare($query);
	$result->bind_param("sis", $newCurrentSave, $id, $currentSave);
	$result->execute();
	if ($result->sqlstate=="00000") {
		$currentSave = $newCurrentSave;
		$_SESSION['currentSave3'] = $currentSave;		
	}else{
		//redirect to saved outside page
		redirect_To_SavedOutside();
	}
	$result->close();
	return $currentSave;
}
/////////////////////////////////////////////////////////////////////////////////////////////////
  function get_Item_Name($itemNum) {
	 if ($itemNum == 18) {
	 	return "Fire Stone";
	 }
	 if ($itemNum == 19) {
	 	return "Water Stone";
	 }
	 if ($itemNum == 20) {
	 	return "Thunder Stone";
	 }
	 if ($itemNum == 21) {
	 	return "Moon Stone";
	 }
	 if ($itemNum == 22) {
	 	return "Leaf Stone";
	 }
	 if ($itemNum == 23) {
	 	return "Dusk Stone";
	 }
	 if ($itemNum == 24) {
	 	return "Reaper Cloth";
	 }
	 if ($itemNum == 25) {
	 	return "Metal Coat";
	 }
	  if ($itemNum == 26) {
	 	return "Dawn Stone";
	 }
	 if ($itemNum == 27) {
	 	return "Sun Stone";
	 }
	 if ($itemNum == 17) {
	 	return "Friendship Doll";
	 }
	 if ($itemNum == 11) {
	 	return "Oran Berry";
	 }
	 if ($itemNum == 12) {
	 	return "Chilan Berry";
	 }
	 if ($itemNum == 13) {
	 	return "Everstone";
	 }
	 if ($itemNum == 14) {
	 	return "Neeverstone";
	 }
	 if ($itemNum == 15) {
	 	return "Gold Incense";
	 }
	 if ($itemNum == 16) {
	 	return "Dark Incense";
	 }
	 if ($itemNum == 32) {
	 	return "King's Rock";
	 }
	 if ($itemNum == 34) {
	 	return "Shiny Stone";
	 }
	 if ($itemNum == 35) {
	 	return "Silk Scarf";
	 }
	 if ($itemNum == 36) {
	 	return "Wide Lens";
	 }
	 if ($itemNum == 37) {
	 	return "Zoom Lens";
	 }
	 if ($itemNum == 38) {
	 	return "Smoke Ball";
	 }
	 if ($itemNum == 39) {
	 	return "Charcoal";
	 }
	 if ($itemNum == 40) {
	 	return "Miracle Seed";
	 }
	 if ($itemNum == 41) {
	 	return "Mystic Water";
	 }
	 if ($itemNum == 42) {
	 	return "Yellow Flute";
	 }
	 if ($itemNum == 43) {
	 	return "Metronome";
	 }
	 if ($itemNum == 53) {
	 	return "Manectite";
	 }
	 if ($itemNum == 54) {
	 	return "Wave Incense";
	 }
	 if ($itemNum == 55) {
	 	return "Sea Incense";
	 }
	 if ($itemNum == 56) {
	 	return "Lax Incense";
	 }
	 if ($itemNum == 57) {
	 	return "Full Incense";
	 }
	 if ($itemNum == 58) {
	 	return "Luck Incense";
	 }
	 if ($itemNum == 59) {
	 	return "Odd Incense";
	 }
	 if ($itemNum == 60) {
	 	return "Rock Incense";
	 }
	 if ($itemNum == 61) {
	 	return "Rose Incense";
	 }
	 if ($itemNum == 62) {
	 	return "Up-grade";
	 }
	 if ($itemNum == 63) {
	 	return "Dragon Scale";
	 }
	 if ($itemNum == 64) {
	 	return "Dubious Disc";
	 }
	 if ($itemNum == 65) {
	 	return "Electirizer";
	 }
	 if ($itemNum == 66) {
	 	return "Magmarizer";
	 }
	 if ($itemNum == 67) {
	 	return "Protector";
	 }
	 if ($itemNum == 68) {
	 	return "Oval Stone";
	 }
	 if ($itemNum == 69) {
	 	return "Razor Claw";
	 }
	 if ($itemNum == 70) {
	 	return "Razor Fang";
	 }
	 if ($itemNum == 71) {
	 	return "Aerodactylite";
	 }
	 if ($itemNum == 72) {
	 	return "Blastoisinite";
	 }
	 if ($itemNum == 73) {
	 	return "Ampharosite";
	 }
	 if ($itemNum == 74) {
	 	return "Venusaurite";
	 }
	 if ($itemNum == 75) {
	 	return "Blue Orb";
	 }
	 if ($itemNum == 76) {
	 	return "Red Orb";
	 }
	 if ($itemNum == 77) {
	 	return "Deep Sea Tooth";
	 }
	 if ($itemNum == 78) {
	 	return "Deep Sea Scale";
	 }
	 if ($itemNum == 79) {
	 	return "Pure Incense";
	 }
	 if ($itemNum == 80) {
	 	return "Beedrillite";
	 }
	 if ($itemNum == 81) {
	 	return "Mewtwonite X";
	 }
	 if ($itemNum == 82) {
	 	return "Charizardite X";
	 }
	 if ($itemNum == 83) {
	 	return "Garchompite";
	 }
	 if ($itemNum == 84) {
	 	return "Prison Bottle";
	 }
	 if ($itemNum == 85) {
	 	return "Meteorite";
	 }
 	return "None";
 }
 /////////////////////////////////////////////////////////////////////////////////////////////////
 ?>
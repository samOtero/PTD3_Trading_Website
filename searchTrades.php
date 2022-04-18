<?php
session_start();
$whichProfile = $_REQUEST['whichProfile'];
$showTopAd = "yes";
$showSideAd = "yes";
$id = $_SESSION['myID'];
$currentSave = $_SESSION['currentSave3'];
$loggedIn = true;
$pageTitle = "Search Trades";
include 'shared/basic.php';
include 'shared/cookies.php';

//$db = connect_To_Original_Database();

//Don't care if they saved outside here
//check_If_Saved_Outside($db, $profileInfo, $id, $currentSave);

$urlValidation = "whichProfile=".$whichProfile;

include 'shared/head.php';
?>
<? include 'shared/pokeDropDown.php'; ?>
<body>
<? include 'shared/navbar.php'; ?>
<div class="container-fluid">
	<div class = "row">
        <div class = "col-sm-12" style="padding-top: 70px">
        	<ol class="breadcrumb">
            <li><a href="main.php?<?php echo $urlValidation ?>">Home</a></li>
            <li class="active">Search Trades</li>
            </ol>
        </div>
    </div>
    <? include 'shared/adTop.php'; ?>
    <div class="row">
        <div class = "col-lg-9 col-md-8 col-sm-7 col-xs-12" >
          <div class="panel panel-primary">
            <div class="panel-heading">
              <h4 class="panel-title">Search Trades</h4>
            </div>
            <div class = "panel-body">
                Search for Pokémon other trainers have put up for trade.
            </div>
            <ul class = "list-group">                
            	<!--POKEMON SECTION-->
                <li class="list-group-item">            
                    <style>
						.nofloat {
							float:none;
						}
					</style>
                    <ul class="nav nav-pills nav-justified">
                    	<li class="active"><a data-toggle="tab" href="#pokemon">By Pokemon</a></li>
                        <li><a data-toggle="tab" href="#tradeID">By TradeID</a></li>
                        <li><a data-toggle="tab" href="#trainerID">By TrainerID</a></li>
                        <li><a data-toggle="tab" href="#trainerRequest">By Trainer Request</a></li>
                    </ul>
                    <div class="tab-content">
                    	<div id="pokemon" class="tab-pane fade in active">
                        <form action="searchResults.php?searchType=1&<?php echo $urlValidation ?>" method="post" name="form1" id="form1">
                        	<h4 style="padding-top:10px;">Pokémon:</h4>
                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <p>
                                    <select class="form-control col-sm-3" name="pokeList" id="pokeList">
                                        <option value="0">Any</option>
                                        <?php echo pokemon_Drop_Down_Options()?>
                                    </select>
                                    </p>
                                </div>
                            </div>
                            <h4 style="padding-top:10px;">
                            Special Type:
                            </h4>
                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <p>
                                    <select class="form-control col-sm-3" name="type" id="type">
                                        <?php echo special_Type_Drop_Down_Options()?>
                                    </select>
                                    </p>
                                </div>
                            </div>
                            <h4 style="padding-top:10px;">
                            Gender:
                            </h4>
                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <p>
                                    <select class="form-control col-sm-3" name="gender" id="gender">
                                        <?php echo gender_Options()?>
                                    </select>
                                    </p>
                                </div>
                            </div>
                            <h4 style="padding-top:10px;">
                            Have Request?:
                            </h4>
                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <p>
                                    <select class="form-control col-sm-3" name="haveRequest" id="haveRequest">
                                        <option value="-1">Show All</option>
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                    </p>
                                </div>
                            </div>
                            <h4 style="padding-top:10px;">
                            Have Adopt Now?:
                            </h4>
                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <p>
                                    <select class="form-control col-sm-3" name="adoptNow" id="adoptNow">
                                        <option value="-1">Show All</option>
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                        <?php
										for ($i=1; $i<=20; $i++) {
											echo '<option value="'.$i.'">'.$i.'</option>';
										}
										?>
                                    </select>
                                    </p>
                                </div>
                            </div>
                            <h4 style="padding-top:10px;">
                            Allows Hacked Offers?:
                            </h4>
                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <p>
                                    <select class="form-control col-sm-3" name="hackSel" id="hackSel">
                                        <option value="-1">Show All</option>
                                        <option value="0">No</option>
                                        <option value="-2">Yes</option>
                                    </select>
                                    </p>
                                </div>
                            </div>
                            <div style="padding-top:10px;">
                            <button type="submit" class="btn btn-success">Search</button>
                            </div>
                        </form>
                        </div>
                        <div id="tradeID" class="tab-pane fade">
                        	<form action="searchResults.php?searchType=2&<?php echo $urlValidation ?>" method="post" name="form2" id="form2">
                                <h4 style="padding-top:10px;">Trade ID:</h4>
                                <p>
                                Search by a specific Trade ID
                                </p>
                                <div class="row">
                                    <div class="col-lg-4 col-md-6">
                                        <p>
                                        <div class="input-group">
                                            <input id="tradeIDInput" name="tradeIDInput" type="text" class="form-control" placeholder="Enter Trade ID...">
                                            <span class="input-group-btn">
                                                <button id="searchTradeID" class="btn btn-success" type="submit">Search</button>
                                            </span>
                                        </div>
                                        </p>
                                    </div>
                                </div>
                        	</form>
                        </div>
                        <div id="trainerID" class="tab-pane fade">
                        	<form action="searchResults.php?searchType=3&<?php echo $urlValidation ?>" method="post" name="form2" id="form2">
                                <h4 style="padding-top:10px;">Trainer ID:</h4>
                                <p>
                                Search by a specific Trainer ID.</br><span style="font-weight:bold">(Note: You can find your Trainer ID in the Top Menu Bar.)</span>
                                </p>
                                <div class="row">
                                    <div class="col-lg-4 col-md-6">
                                        <p>
                                        <div class="input-group">
                                            <input id="trainerIDInput" name="trainerIDInput" type="text" class="form-control" placeholder="Enter Trainer ID...">
                                            <span class="input-group-btn">
                                                <button id="searchTrainerID" class="btn btn-success" type="submit">Search</button>
                                            </span>
                                        </div>
                                        </p>
                                    </div>
                                </div>
                        	</form>
                        </div>
                        <div id="trainerRequest" class="tab-pane fade">
                        	<form action="searchResults.php?searchType=4&<?php echo $urlValidation ?>" method="post" name="form1" id="form1">
                        	<h4 style="padding-top:10px;">Pokémon:</h4>
                            <p>Search for which pokémon other trainers are requesting.</p>
                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <p>
                                    <select class="form-control col-sm-3" name="pokeList" id="pokeList">
                                        <option value="0">Any</option>
                                        <?php echo pokemon_Drop_Down_Options()?>
                                    </select>
                                    </p>
                                </div>
                            </div>
                            <h4 style="padding-top:10px;">
                            Special Type:
                            </h4>
                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <p>
                                    <select class="form-control col-sm-3" name="type" id="type">
                                        <?php echo special_Type_Drop_Down_Options()?>
                                    </select>
                                    </p>
                                </div>
                            </div>
                            <h4 style="padding-top:10px;">
                            Gender:
                            </h4>
                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <p>
                                    <select class="form-control col-sm-3" name="gender" id="gender">
                                        <?php echo gender_Options()?>
                                    </select>
                                    </p>
                                </div>
                            </div>
                            <div style="padding-top:10px;">
                            <button type="submit" class="btn btn-success">Search</button>
                            </div>
                        </form>
                        </div>
                    </div>               
                </li>
                <!--END POKEMON SECTION-->
            </ul>
          </div>
      	</div>
      	<? include 'shared/adSide.php'; ?>
    </div>
</div>
<?php include 'shared/footer.php'; ?>
</body>
</html>
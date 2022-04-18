<?php
session_start();
$whichProfile = $_REQUEST['whichProfile'];
$showTopAd = "no";
$showSideAd = "no";
$id = $_SESSION['myID'];
$currentSave = $_SESSION['currentSave3'];
$loggedIn = true;
$pageTitle = "Saved Outside";
include 'shared/basic.php';
include 'shared/cookies.php';

include 'shared/head.php';


?>

<body>
<? include 'shared/navbar.php'; ?>
<div class="container-fluid">
<div class="row">
    <div class = "col-sm-12"  style="padding-top: 70px">
      <div class="panel panel-primary">
        <div class="panel-heading">
          <h4 class="panel-title"> Oops! There is a problem!</h4>
        </div>
        <div class = "panel-body">
        	<p>It seems you have saved outside of the Trading Center. <a href="http://www.ptdtrading.com/trading.php">Please go back and log in again</a>.</p>
        </div>
      </div>
  </div>
</div>
</div>
<? include 'shared/footer.php'; ?>
</body>
</html>
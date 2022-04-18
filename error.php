<?php
session_start();
$whichProfile = $_REQUEST['whichProfile'];
$showTopAd = "no";
$showSideAd = "no";
$id = $_SESSION['myID'];
$currentSave = $_SESSION['currentSave3'];
$loggedIn = true;
$pageTitle = "Error!";
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
        	<p>It seems that was an error in our server. Please try again.</p>
        </div>
      </div>
  </div>
</div>
</div>
<? include 'shared/footer.php'; ?>
</body>
</html>
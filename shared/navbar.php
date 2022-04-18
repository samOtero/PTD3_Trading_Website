<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
      <a class="navbar-brand" href="http://www.ptdtrading.com/trading_account.php?Action=logged">PTD 3: Trading Center</a> </div>
    <div id="navbar" class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
        <li><a href="http://samdangames.blogspot.com/">Blog</a></li>
        <li><a href="main.php?<?php echo $urlValidation?>">Home</a></li>
        <li class=""dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Trading <span class="caret"></span></a>
          <ul class="dropdown-menu">
           <li><a href="myTrades.php?<?php echo $urlValidation?>">My Trades</a></li>
            <li><a href="createTrade.php?<?php echo $urlValidation?>">Create Trade</a></li>
            <li><a href="searchTrades.php?<?php echo $urlValidation?>">Search Trades</a></li>
            <!--<li><a href="#">Latest Trades</a></li>-->
          </ul>
        </li>
        <li><a href="mysteryGift.php?<?php echo $urlValidation?>">Mystery Gift</a></li>
      </ul>
      <p class="navbar-text navbar-right"><img src="http://www.ptdtrading.com/ptd3/images/noah_1.png"> <?php echo $_COOKIE["nickname3"] ?> - Trainer ID: <?php echo $id ?></p>
    </div>
    <!--/.nav-collapse --> 
  </div>
</nav>
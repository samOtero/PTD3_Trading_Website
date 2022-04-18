<?php
include '../shared/database.php';
$db = connect_To_Trading_Database();
$query = "select num, shiny from ptdtrad_ptd2_trading.ptd_3_mysterygift WHERE date <= '".date("Y-m-d")."' order by date desc limit 1";
$result = $db->prepare($query);
$result->execute();
$result->store_result();
$result->bind_result($pokeNum, $pokeShiny);
$result->fetch();
$result->close();
echo 'rnd=1&rslt=1&mgn='.$pokeNum.'&mgs='.$pokeShiny;
//echo 'main_shell.swf?version='.$ver;
 ?>
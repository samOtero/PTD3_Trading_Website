<?php
function connect_To_Original_Database() {
 	$db = new mysqli('', '', '', 'sndgame_ptd3_basic');
 
 	if (mysqli_connect_errno()) {
		echo 'Result=Failure&Reason=DatabaseConnection&Extra'.mysqli_connect_errno();
	 	exit;
 	}
	return $db;
 }
/////////////////////////////////////////////////////////////////////////////////////////////////
function connect_To_Trading_Database() {
 	$db = new mysqli('', '', '', 'ptdtrad_ptd2_trading');
 	if (mysqli_connect_errno()) {
		echo 'Result=Failure&Reason=DatabaseConnection&Extra='.mysqli_connect_errno();
	 	exit;
 	}
	return $db;
 }
 /////////////////////////////////////////////////////////////////////////////////////////////////
 ?>
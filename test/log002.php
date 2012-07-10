<?php
	include("../php/net/mysql.php");
	include("../php/util/logger.php");
	
	$mysql = new mysql("localhost", "root", "1234", "logs");
	$log = new logger($mysql, LOGLEVEL_INFO, strtoupper(date("My")));
	
	$mysql->logger($log);
	$mysql->query("SELECT * FROOM `accounts`");
	
	echo("Testing logging MySQL query error");
?>
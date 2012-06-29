<?php
	include("../php/net/mysql.php");
	include("../php/net/session.php");
	include("../php/util/logger.php");
	
	$mysql_log = new mysql("localhost", "root", "1234", "logs");
	$mysql = new mysql("localhost", "root", "1234", "test");
	
	$log = new logger($mysql_log, LOGLEVEL_INFO, strtoupper(date("My")));
	$session = new session("test", $mysql, "SELECT * FROM `accounts` WHERE `uname` = '%s' AND `pword` = '%s'", $log);
	
	$session->user("daniel", "123");
	
	echo("Testing logging session authentication to separate database");
?>
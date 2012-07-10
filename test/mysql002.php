<?php
	include("../php/util/timestamp.php");
	include("../php/util/toolbox.php");
	include("../php/net/mysql.php");
	
	$code = toolbox::randStr(10);
	$timestamp = new timestamp("LAST_DB_CHANGE");
	$mysql = new mysql("localhost", "root", "1234", "test", $timestamp);
	$query = $mysql->query("INSERT INTO `codes` (`code`) VALUES ('".$code."');");
	
	if (!$query['error']) {
		echo("Generated code: ".$code."<br />");
		echo("Stored in: `test`.`codes` @ `localhost` (MySQL)<br /><br />");
		echo("Last database change: ".date("Y-m-d H:i:s", $timestamp->time())."<br />");
		echo("Stored in: ".$timestamp->file()." (file)");
	} else echo($query['error']);
?>
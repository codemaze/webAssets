<?php
	include("../php/net/mysql.php");
	
	$host = "localhost";
	$user = "root";
	$pass = "1234";
	$db = "test";
	$table = "codes";
	
	echo("<b>PHP</b><br />");
	
	// --- PHP --------------------------------------------------------------------------------------------------------------------------------
	if (@mysql_connect($host, $user, $pass)) {
		if (mysql_select_db($db)) {
			if ($query = mysql_query("SELECT * FROM `".$table."`")) while ($record = mysql_fetch_assoc($query)) echo($record['code']."<br />");
			else echo(mysql_error()."<br />");
		} else echo("No database selected<br />");
	} else echo("No valid connection<br />");
	// ----------------------------------------------------------------------------------------------------------------------------------------
	
	echo("<br /><b>phpAssets</b><br />");
	
	// --- webAssets --------------------------------------------------------------------------------------------------------------------------
	$mysql = new mysql($host, $user, $pass, $db);
	$query = $mysql->query("SELECT * FROM `".$table."`");
	if (!$query['error']) foreach ($query['records'] as $record) echo($record['code']."<br />");
	else echo($query['error']."<br />");
	// ----------------------------------------------------------------------------------------------------------------------------------------
	
	echo("Source code comparison between webAssets- and PHP MySQL API");
?>
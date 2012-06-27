<?php
	include("../php/net/mysql.php");
	include("../php/net/session.php");
	include("../php/util/logger.php");
	
	$mysql_log = new mysql("localhost", "root", "1234", "logs");
	$mysql = new mysql("localhost", "root", "1234", "test");
	
	$logger = new logger($mysql_log, LOGLEVEL_INFO, strtoupper(date("My")));
	$session = new session("test", $mysql, "SELECT * FROM `accounts` WHERE `uname` = '%s' AND `pword` = '%s'");
	
	$session->logger($logger);
	
	if ($session->user()) echo("Welcome, ".$session->getUserProperty("uname")."!");
	else if (isset($_GET['uname']) && isset($_GET['pword']) && $session->user($_GET['uname'], $_GET['pword'])) header("Location: session001.php");
	else {
?>
<form method = "GET" action = "session001.php">
	<input type = "text" name = "uname" />
	<input type = "password" name ="pword" />
	<input type = "submit" value = "Logga in" />
</form>
<?php
	}
?>
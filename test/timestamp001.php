<?php
	include("../php/util/timestamp.php");
	
	$timestamp = new timestamp("TIMESTAMP001");
	
	$timestamp->touch();
	echo("Timestamp: ".date("Y-m-d H:i:s", $timestamp->time())."<br />");
	echo("Stored in: ".$timestamp->file()." (file)<br />");
	
	echo("<br />Testing timestamp");
?>
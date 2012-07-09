<?php
	include("../php/util/config.php");
	
	$config = new config("test");
	
	$config->read();
	
	echo($config->get("mysql.host"));
?>
<?php
	include("../php/util/config.php");
	
	$config = new config("test");
	
	if ($config->saved()) $config->read();
	else {
		$config->add("mysql.host", "localhost");
		$config->add("mysql.port", "3306");
		$config->add("mysql.uname", "root");
		$config->add("mysql.pword", "1234");
		$config->add("mysql.db", "test");
		$config->save();
	}
	
	echo($config->get("mysql.host"));
?>
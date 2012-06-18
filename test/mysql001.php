<?php
    include("../php/net/mysql.php");
    
    $mysql = new mysql("localhost", "root", "1234");
    
    if ($mysql->connection()) echo("MySQL connection works!");
    else echo("MySQL connection failed!");
	
	echo("Testing MySQL connection");
?>
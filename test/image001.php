<?php
	include("../php/misc/image.php");
	
	$image = new image("image001.jpg");
	
	$image->resize(1000, 1000);
	$image->output();
?>
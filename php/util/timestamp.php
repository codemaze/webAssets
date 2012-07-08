<?php
	class timestamp {
		public static $className = __CLASS__;
		
		private $file;
		
		public function timestamp($file) { $this->file = $file; }
		public function file($file = NULL) {return($this->file = (is_string($file)) ? $file : $this->file); }
		public function time() { return(($t = @file_get_contents($this->file)) ? $t : NULL); }
		public function touch($time = NULL) {
			$time = (!empty($time)) ? $time : time();
			
			@file_put_contents($this->file, $time);
			return($time);
		}
	}
	
	$path = pathinfo($_SERVER['SCRIPT_NAME']);
	
	if ($path['filename'] == timestamp::$className) echo(($copyright = @file_get_contents("../../COPYRIGHT")) ? $copyright : timestamp::$className." © ".date("Y"));
?>
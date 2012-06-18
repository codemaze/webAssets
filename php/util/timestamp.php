<?php
	class timestamp {
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
?>
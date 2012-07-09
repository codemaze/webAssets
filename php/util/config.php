<?php
	class config {
		private $name;
		private $properties;
		
		public function config($name) {
			$this->name = $name;
		}
		
		public function add($property, $value) {
			$added = false;
			
			if (is_string($property) || is_int($property)) {
				$this->properties[$property] = $value;
				$added = true;
			}
			
			return($added);
		}
		
		public function get($property) {
			$prop = NULL;
			
			if (isset($this->properties[$property])) $prop = $this->properties[$property];
			
			return($prop);
		}
		
		public function write($name = NULL) {
			$name = (empty($filename)) ? $this->name : $name;
			
			file_put_contents($name.".php", "<?php\n");
			
			foreach ($this->properties as $property => $value) file_put_contents($name.".php", "\t$".$name."['".$property."']"." = \"".$value."\";\n", FILE_APPEND);
			
			file_put_contents($name.".php", "?>", FILE_APPEND);
		}
		
		public function read($name = NULL) {
			$name = (empty($filename)) ? $this->name : $name;
			
			@include($name.".php");
			
			$this->properties = (isset($$name)) ? $$name : array();
		}
	}
?>
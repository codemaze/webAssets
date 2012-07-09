<?php
	class config {
		private $name;
		private $properties;
		
		public function config($name) {
			$this->name($name);
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
		
		public function name($name = NULL) { return($this->name = (is_string($name)) ? $name : $this->name); }
		
		public function read() {
			$name = $this->name();
			
			@include($name.".php");
			
			$this->properties = (isset($$name)) ? $$name : array();
		}
		
		public function save() {
			file_put_contents($this->name().".php", "<?php\n\t// Generated using webAssets @ ".date("Y-m-d H:i:s")."\n\t\n");
			
			foreach ($this->properties as $property => $value) file_put_contents($this->name().".php", "\t$".$this->name()."['".$property."']"." = \"".$value."\";\n", FILE_APPEND);
			
			file_put_contents($this->name().".php", "?>", FILE_APPEND);
		}
		
		public function saved() { return(file_exists($this->name().".php")); }
	}
?>
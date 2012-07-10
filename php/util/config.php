<?php
	class config {
		private $name;
		private $path;
		private $properties;
		
		public function config($name, $path = "./") {
			$this->name($name);
			$this->path($path);
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
		public function path($path = NULL) { return($this->path = (is_string($path)) ? $path : $this->path); }
		
		public function read() {
			$name = $this->name();
			
			@include($this->path().$name.".php");
			
			$this->properties = (isset($$name)) ? $$name : array();
		}
		
		public function save() {
			file_put_contents($this->path().$this->name().".php", "<?php\n\t// Generated using webAssets @ ".date("Y-m-d H:i:s")."\n\t\n");
			
			foreach ($this->properties as $property => $value) file_put_contents($this->path().$this->name().".php", "\t$".$this->name()."['".$property."']"." = \"".$value."\";\n", FILE_APPEND);
			
			file_put_contents($this->path().$this->name().".php", "?>", FILE_APPEND);
		}
		
		public function saved() { return(file_exists($this->path().$this->name().".php")); }
	}
?>
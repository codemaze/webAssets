<?php
	define("LOGLEVEL_DEBUG", 0);
	define("LOGLEVEL_INFO", 1);
	define("LOGLEVEL_NOTICE", 2);
	define("LOGLEVEL_WARNING", 3);
	define("LOGLEVEL_ERROR", 4);
	
	class logger {
		public static $className = __CLASS__;
		
		private $fields = array("time", "module", "level", "msg");
		private $loglevels = array("DEBUG" => LOGLEVEL_DEBUG, "INFO" => LOGLEVEL_INFO, "NOTICE" => LOGLEVEL_NOTICE, "WARNING" => LOGLEVEL_WARNING, "ERROR" => LOGLEVEL_ERROR);
		private $level;
		private $mysqlTable;
		private $output;
		
		public function logger($output = NULL, $level = LOGLEVEL_INFO, $mysqlTable = NULL) {
			$this->level($level);
			$this->output($output);
			$this->mysqlTable($mysqlTable);
		}
		
		private function isLogLevel($level) { return(in_array($level, $this->loglevels, true)); }
		public function level($level = NULL) { return($this->level = ($this->isLogLevel($level)) ? $level : $this->level); }
		
		public function mysqlTable($mysqlTable = NULL) {
			if (is_a($this->output, "mysql") && $this->output->connection() && is_string($mysqlTable)) {
				$query = $this->output->query("SHOW TABLES;");
				$array = array();
				
				foreach ($query['records'] as $record) $array[] = $record['Tables_in_'.$this->output->db()];
				
				if (in_array($mysqlTable, $array)) {
					$query = $this->output->query("SHOW COLUMNS FROM `".$mysqlTable."`;");
					$array = array();
					
					foreach ($query['records'] as $record) if ($record['Field'] != "id") $array[] = $record['Field'];
					
					$this->mysqlTable = ($array == $this->fields) ? $mysqlTable : false;
				} else {
					$set = array_keys($this->loglevels);
					$fields = NULL;
					$levels = NULL;
					
					for ($i = 1; $i <= count($set); $i++) $levels .= ($i != count($set)) ? "'".$set[($i - 1)]."'," : "'".$set[($i - 1)]."'";
					
					foreach ($this->fields as $field) {
						switch ($field) {
							case "time": $fields .= "`time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,"; break;
							case "module": $fields .= "`module` varchar(20) DEFAULT 'SYSTEM',"; break;
							case "level": $fields .= "`level` enum(".$levels.") DEFAULT '".reset($set)."',"; break;
							case "msg": $fields .= "`msg` text,"; break;
						}
					}
					
					$query = $this->output->query("CREATE TABLE `".$mysqlTable."` (`id` int(11) NOT NULL AUTO_INCREMENT,".$fields."PRIMARY KEY (`id`)) Engine=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");
					$this->mysqlTable = (!$query['error'])  ? $mysqlTable : false;
				}
			}
			
			return($this->mysqlTable);
		}
		
		public function msg($msg, $level = NULL, $module = NULL) {
			$level = ($this->isLogLevel($level)) ? $level : $this->level();
			$module = (!empty($module)) ? strtoupper($module) : NULL;
			
			if ($level >= $this->level()) {
				$level = ($l = array_search($level, $this->loglevels)) ? $l : key($this->loglevels);
				
				if (is_string($this->mysqlTable)) $this->msgMysqlTable($msg, $level, $module);
				else $this->msgFile($msg, $level, $module);
			}
		}
		
		private function msgMysqlTable($msg, $level, $module) {
			$msg = mysql_real_escape_string($msg, $this->output->connection());
			$fields = NULL;
			$values = NULL;
			
			for ($i = 1; $i <= count($this->fields); $i++) {
				$field =$this->fields[($i - 1)];
				
				if ($field != "time") {
					$comma = ($i != count($this->fields)) ? ',' : NULL;
					
					switch ($field) {
						case "module": $values .= "'".$module."'".$comma; break;
						case "level": $values .= "'".$level."'".$comma; break;
						case "msg": $values .= "'".$msg."'".$comma; break;
					}
					
					$fields .= '`'.$field.'`'.$comma;
				}
			}
			
			if (!empty($fields)) $this->output->query("INSERT INTO `".$this->mysqlTable."` (".$fields.") VALUES (".$values.");", false);
		}
		
		private function msgFile($msg, $level, $module) {
			$filename = (is_string($this->output)) ? $this->output : strtoupper(date("My")).".log";
			$fields = NULL;
			$header = NULL;
			
			for ($i = 1; $i <= count($this->fields); $i++) $fields .= ($i != count($this->fields)) ? $this->fields[($i - 1)]."," : $this->fields[($i - 1)]."\n";
			
			if ($file = @fopen($filename, "r+")) $header = fgets($file);
			else {
				file_put_contents($filename, $fields);
				
				if ($file = @fopen($filename, "r+")) $header = fgets($file);
			}
			
			if ($header == $fields) {
				fseek($file, 0, SEEK_END);
				fwrite($file, date("Y-m-d H:i:s").",".$level.",".$msg."\n");
				fclose($file);
			}
		}
		
		public function output($output = NULL) { return($this->output = (!empty($output)) ? $output : $this->output); }
	}
	
	$path = pathinfo($_SERVER['SCRIPT_NAME']);
	
	if ($path['filename'] == logger::$className) echo(($copyright = @file_get_contents("../../COPYRIGHT")) ? $copyright : logger::$className." © ".date("Y"));
?>
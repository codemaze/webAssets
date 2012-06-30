<?php
	class mysql {
		private $module = "MYSQL";
		private $AFR;
		private $charset;
		private $connection;
		private $db;
		private $errors;
		private $logger;
		private $queries;
		private $timestamp;
		
		public function mysql($host = NULL, $uname = NULL, $pword = NULL, $db = NULL, $timestamp = NULL, $port = NULL, $logger = NULL, $AFR = false, $charset = "utf8", $persistent = false) {
			$this->autoFreeResources($AFR);
			$this->connect($host, $uname, $pword, $db, $port, $charset, $persistent);
			$this->errors = array();
			$this->queries = array();
			$this->timestamp($timestamp);
		}
		
		public function autoFreeResources($AFR = NULL) { return($this->AFR = (is_bool($AFR)) ? $AFR : $this->AFR); }
		
		public function charset($charset = NULL, $link = NULL) {
			$link = (is_resource($link)) ? $link : $this->connection();
			$success = false;
			
			if (@mysql_ping($link)) $success = mysql_set_charset($charset, $link);
			if ($success && $link == $this->connection()) $this->charset = $charset;
			
			return((empty($charset)) ? $this->charset : $success);
		}
		
		public function close($link = NULL) {
			$link = (is_resource($link)) ? $link : $this->connection();
			$success = false;
			
			if (@mysql_ping($link)) $success = mysql_close($link);
			if ($link == $this->connection()) $this->connection = false;
			
			return($success);
		}
		
		public function connect($host, $uname = NULL, $pword = NULL, $db = NULL, $port = NULL, $charset = NULL, $persistent = false) {
			$port = (!empty($port)) ? ":".$port : ":3306";
			
			if ($this->connection = ($persistent) ? @mysql_pconnect($host.$port, $uname, $pword) : @mysql_connect($host.$port, $uname, $pword)) {
				$this->charset($charset);
				$this->db($db);
			}
			
			return($this->connection());
		}
		
		public function connection($link = NULL) { return($this->connection = (is_resource($link) && @mysql_ping($link)) ? $link : $this->connection); }
		
		public function db($db = NULL, $link = NULL) {
			$link = (is_resource($link)) ? $link : $this->connection();
			$success = false;
			
			if (@mysql_ping($link) && is_string($db)) $success = mysql_select_db($db, $link);
			if ($success && $link == $this->connection()) $this->db = $db;
			
			return((empty($db)) ? $this->db : $success);
		}
		
		public function getErrors($lastError = false) { return(($lastError) ? end($this->errors) : $this->errors); }
		public function getQueries($lastQuery = false) { return(($lastQuery) ? end($this->queries) : $this->queries); }
		public function logger($logger = NULL) { return($this->logger = (is_a($logger, "logger")) ? $logger : $this->logger); }
		
		public function query($query, $log = true, $freeResource = false, $link = NULL) {
			$link = (is_resource($link)) ? $link : $this->connection();
			$array = array('query' => $query, 'resource' => false, 'records' => array(), 'error' => false);
			
			if (@mysql_ping($link)) {
				$this->db($this->db());
				
				if ($array['resource'] = mysql_query($query, $link)) {
					if (is_resource($array['resource'])) while ($row = mysql_fetch_array($array['resource'])) $array['records'][] = $row;
					else if ($array['resource'] && is_a($this->timestamp, "timestamp")) $this->timestamp->touch();
					if ($freeResource || $this->AFR) mysql_free_result($array['resource']);
					
					$this->queries[] = $query;
				} else $this->errors[] = $array['error'] = mysql_error($link);
			} else $this->errors[] = $array['error'] = "No valid connection";
			
			if (is_a($this->logger, "logger")) {
				if (!empty($array['error'])) $this->logger->msg($array['error'], LOGLEVEL_ERROR, $this->module);
				if ($array['resource'] && $log) $this->logger->msg($query, LOGLEVEL_INFO, $this->module);
			}
			
			return($array);
		}
		
		public function timestamp($timestamp = NULL) { return($this->timestamp = (is_a($timestamp, "timestamp")) ? $timestamp : $this->timestamp); }
	}
?>
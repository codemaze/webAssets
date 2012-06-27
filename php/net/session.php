<?php
	class session {
		private $module = "SESSION";
		private $idleMax;
		private $logger;
		private $loginQuery;
		private $mysql;
		private $name;
		private $user;
		
		public function session($name = NULL, $mysql = NULL, $loginQuery = NULL, $logger = NULL, $idleMax = 0) {
			$this->idleMax($idleMax);
			$this->logger($logger);
			$this->loginQuery($loginQuery);
			$this->mysql($mysql);
			$this->name($name);
			$this->start();
		}
		
		public function getUserProperty($property) { return((isset($this->user[$property])) ? $this->user[$property] : NULL); }
		
		public function idleCheck() {
			$overLimit = false;
			$time = time();
			$last_request = $_SESSION['last_request'] = (isset($_SESSION['last_request'])) ? $_SESSION['last_request'] : $time;
			$idleTime = $time - $last_request;
			
			if (!empty($this->idleMax) && $idleTime >= ($this->idleMax*60)) $overLimit = true;
			
			return(array("over_limit" => $overLimit, "time" => ($idleTime/60)));
		}
		
		public function idleMax($min = NULL) { return($this->idleMax = (is_int($min)) ? $min : $this->idleMax); }
		public function logger($logger = NULL) { return($this->logger = (is_a($logger, "logger")) ? $logger : $this->logger); }
		public function loginQuery($loginQuery = NULL) {  return($this->loginQuery = (is_string($loginQuery)) ? $loginQuery : $this->loginQuery); }
		public function mysql($mysql = NULL) { return($this->mysql = (is_a($mysql, "mysql")) ? $mysql : $this->mysql); }
		
		public function name($name = NULL) {
			$this->name = (!empty($name)) ? $name : session_name();
			
			return(session_name($name));
		}
		
		public function start() {
			$start = session_start();
			$this->user = (isset($_SESSION['user'])) ? $_SESSION['user'] : array();
			
			return($start);
		}
		
		public function stop() {
			$this->user = $_SESSION = array();
			
			if (isset($_COOKIE[$this->name])) {
				$params = session_get_cookie_params();
				
				setcookie($this->name, '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
			}
			
			return(session_destroy());
		}
		
		public function user($uname = NULL, $pword = NULL) {
			$success = false;
			
			if (!empty($uname) && !empty($pword) && is_a($this->mysql, "mysql")) {
				$query = $this->mysql->query(sprintf($this->loginQuery, @mysql_real_escape_string($uname), @mysql_real_escape_string($pword)));
				
				if (!empty($query['records'])) {
					$this->user = $_SESSION['user'] = $query['records'][0];
					$success = true;
				}
			} else if (!empty($this->user)) $success = true;
			
			if (!empty($uname) && $success && is_a($this->logger, "logger")) $this->logger->msg($uname." successfully logged in to the system", LOGLEVEL_NOTICE, $this->module);
			
			return($success);
		}
	}
?>
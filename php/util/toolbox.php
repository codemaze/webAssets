<?php
	class toolbox {
		public static $className = __CLASS__;
		
		public static function randBool() { return((mt_rand(0, 1) == 1) ? true : false); }
		
		public static function randChr($letter = true, $capital = false) {
			$chr = NULL;
			
			if ($letter) $chr = ($capital) ? chr(mt_rand(65, 90)) : chr(mt_rand(97, 122));
			else $chr = mt_rand(0, 9);
			
			return($chr);
		}
		
		public static function randStr($length = 6, $numbers = true, $letters = true, $capitals = true, $capitalsOnly = false) {
			$str = NULL;
			
			for ($i = 0; $i < $length; $i++) {
				if ($numbers) {
					if ($letters) {
						if ($capitals) {
							if ($capitalsOnly) $str .= toolbox::randChr(toolbox::randBool(), true);
							else $str .= toolbox::randChr(toolbox::randBool(), toolbox::randBool());
						} else $str .= toolbox::randChr(toolbox::randBool());
					} else $str .= toolbox::randChr(false);
				} else if ($letters) {
					if ($numbers) {
						if ($capitals) {
							if ($capitalsOnly) $str .= toolbox::randChr(toolbox::randBool(), true);
							else $str .= toolbox::randChr(toolbox::randBool(), toolbox::randBool());
						} else $str .= toolbox::randChr(false);
					} else {
						if ($capitals) {
							if ($capitalsOnly) $str .= toolbox::randChr(true, true);
							else $str .= toolbox::randChr(true, toolbox::randBool());
						} else $str .= toolbox::randChr(true);
					}
				}
			}
			
			return($str);
		}
		
		public static function reload($page = '.') {
			header("Location: ".$page);
			exit();
		}
		
		public static function urlVars($url, $vars, $del = false) {
			parse_str((isset($url['query'])) ? $url['query'] : NULL, $url_query);
			
			var_dump($url_query);
			
			$url = parse_url($url);
			$query = (isset($url['query']) && !$del) ? array_merge($url_query, $vars) : $url_query;
			$new_url = NULL;
			
			if ($del) foreach ($vars as $var) unset($query[$var]);
			
			foreach ($url as $element => $value) {
				switch ($element) {
					case "scheme": $url[$element] = $value."://"; break;
					case "user": $url[$element] = (isset($url['pass'])) ? $value : $value.'@'; break;
					case "pass": $url[$element] = ':'.$value.'@'; break;
					case "query": $url[$element] = '?'.http_build_query($query); break;
					case "fragment": $url[$element] = '#'.$value; break;
				}
				
				$new_url .= $url[$element];
			}
			
			return($new_url);
		}
	}
	
	$path = pathinfo($_SERVER['SCRIPT_NAME']);
	
	if ($path['filename'] == toolbox::$className) echo(($copyright = @file_get_contents("../../COPYRIGHT")) ? $copyright : toolbox::$className." © ".date("Y"));
?>
<?php
	class image {
		public static $className = __CLASS__;
		
		private $data;
		private $type;
		private $x;
		private $y;
		
		public function image($file, $output = false, $setHeader = true, $destroy = true) {
			if ($this->type = @exif_imagetype($file)) {
				switch ($this->type) {
					case IMAGETYPE_GIF: $this->data = $file = imagecreatefromgif($file); break;
					case IMAGETYPE_JPEG: $this->data = $file = imagecreatefromjpeg($file); break;
					case IMAGETYPE_PNG: $this->data = $file = imagecreatefrompng($file); break;
				}
			}
			
			$this->x = (is_resource($file)) ? $this->x($file) : false;
			$this->y = (is_resource($file)) ? $this->y($file) : false;
			
			if ($output) $this->output($setHeader, $destroy);
		}
		
		public function boundaries($x = NULL, $y = NULL, $keepAspectRatio = true, $image = NULL) {
			$image = (is_resource($image)) ? $image : $this->data;
			
			if (is_int($x) && $this->x($image) > $x) $image = $this->resize($x, NULL, $keepAspectRatio, $image);
			if (is_int($y) && $this->y($image) > $y) $image = $this->resize(NULL, $y, $keepAspectRatio, $image);
			
			return($image);
		}
		
		public function destroy($image = NULL) { (is_resource($image)) ? imagedestroy($image) : imagedestroy($this->data); }
		public function data($data = NULL) { return($this->data = (is_resource($data)) ? $data : $this->data); }
		public function type($type = NULL) { return($this->type = (is_int($type)) ? $type : $this->type); }
		
		public function output($file = NULL, $setHeader = true, $destroy = true) {
			if (empty($file) && $setHeader && !headers_sent()) header("Content-Type: ".image_type_to_mime_type($this->type));
			if (!empty($file)) $file = (strrpos($file, '.')) ? $file : $file.image_type_to_extension($this->type);
			
			switch ($this->type) {
				case IMAGETYPE_GIF: imagegif($this->data, $file); break;
				case IMAGETYPE_JPEG: imagejpeg($this->data, $file); break;
				case IMAGETYPE_PNG: imagepng($this->data, $file); break;
			}
			
			if ($destroy) $this->destroy();
		}
		
		public function resize($x = NULL, $y = NULL, $keepAspectRatio = true, $image = NULL) {
			$image = (is_resource($image)) ? $image : $this->data;
			$imageX = $this->x($image);
			$imageY = $this->y($image);
			$xChange = ($keepAspectRatio && is_int($y)) ? ($imageX * ($y/$imageY)) : $imageX;
			$yChange = ($keepAspectRatio && is_int($x)) ? ($imageY * ($x/$imageX)) : $imageY;
			$x = (is_int($x)) ? $x : $xChange;
			$y = (is_int($y)) ? $y : $yChange;
			$tmpim = imagecreatetruecolor($x, $y);
			
			imagealphablending($tmpim, false);
			imagesavealpha($tmpim, true);
			imagecolorallocatealpha($tmpim, 255, 255, 255, 127);
			imagecopyresampled($tmpim, $image, 0, 0, 0, 0, $x, $y, $imageX, $imageY);
			
			if ($image == $this->data) {
				$this->data($tmpim);
				$this->x = $x;
				$this->y = $y;
			}
			
			return($tmpim);
		}
		
		public function x($image = NULL) { return((is_resource($image)) ? imagesx($image) : $this->x); }
		public function y($image = NULL) { return((is_resource($image)) ? imagesy($image) : $this->y); }
	}
	
	$path = pathinfo($_SERVER['SCRIPT_NAME']);
	
	if ($path['filename'] == image::$className) echo(($copyright = @file_get_contents("../../COPYRIGHT")) ? $copyright : image::$className." © ".date("Y"));
?>
<?php
	/**
	* @author Simon Jarvis
	* @copyright 2006 Simon Jarvis
	* @version 08/11/06
	* @link http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
	* @license:
	* This program is free software; you can redistribute it and/or
	* modify it under the terms of the GNU General Public License
	* as published by the Free Software Foundation; either version 2
	* of the License, or (at your option) any later version.
	*
	* This program is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	* GNU General Public License for more details:
	* http://www.gnu.org/licenses/gpl.html
	*
	* @var resource $image [handle for image]
	* @var int $image_type [Constant representing image type - JPEG, etc]
	* @var string $fname   [filename]
	*/

	namespace core;
	class SimpleImage {

		public $image;
		public $image_type;
		public $fname;

		public function __construct($filename) {
			$this->fname = $filename;
			$image_info = getimagesize($filename);
			$this->image_type = $image_info[2];

			if( $this->image_type == IMAGETYPE_JPEG ) {
				$this->image = imagecreatefromjpeg($filename);
			}
			elseif( $this->image_type == IMAGETYPE_GIF ) {
				$this->image = imagecreatefromgif($filename);
			}
			elseif( $this->image_type == IMAGETYPE_PNG ) {
				$this->image = imagecreatefrompng($filename);
			}
		}

		/**
		 * Convert $image to a base64 encoded data-uri
		 *
		 * @return string [base64 encoded data-uri]
		 */

		public function img_data_uri(){
			ob_start();
			switch($this->image_type){
				case IMAGETYPE_PNG: {
					$type = 'png';
					imagepng($this->image);
				} break;
				case IMAGETYPE_GIF: {
					$type = 'gif';
					imagegif($this->image);
				} break;
				case IMAGETYPE_JPEG: {
					$type = 'jpeg';
					imagejpeg($this->image);
				} break;
			}
			$contents = ob_get_clean();
			return "data:image/$type;base64," . base64_encode($contents);
		}

		/**
		 * Save image to file in format given by $image_type
		 *
		 * @param  string  $filename    [name of file]
		 * @param  int  $image_type     [from PHP constant]
		 * @param  int  $compression    [JPEG compression]
		 * @param  int  $permissions    [Unix file permissions]
		 *
		 * @return void
		 */

		public function save($filename, $image_type = IMAGETYPE_JPEG, $compression = 90, $permissions=null) {
			if( $image_type == IMAGETYPE_JPEG ) {
				imagejpeg($this->image, $filename, $compression);
			}
			elseif( $image_type == IMAGETYPE_GIF ) {
				imagegif($this->image, $filename);
			}
			elseif( $image_type == IMAGETYPE_PNG ) {
				imagepng($this->image, $filename);
			}
			if(isset($permissions)) {
				chmod($filename, $permissions);
			}
		}

		/**
		 * Convert image to type given by $image_type and output
		 *
		 * @param  int $image_type [from PHP constant]
		 *
		 * @return void
		 */

		public function output($image_type=IMAGETYPE_JPEG) {
			if( $image_type == IMAGETYPE_JPEG ) {
				imagejpeg($this->image);
			}
			elseif( $image_type == IMAGETYPE_GIF ) {
				imagegif($this->image);
				}
			elseif( $image_type == IMAGETYPE_PNG ) {
				imagepng($this->image);
			}
		}

		/**
		 * Returns the width of the image
		 *
		 * @param void
		 *
		 * @return int
		 */

		public function getWidth() {
			return imagesx($this->image);
		}

		/**
		 * Returns the height of the image
		 *
		 * @param void
		 *
		 * @return int
		 */

		public function getHeight() {
			return imagesy($this->image);
		}

		/**
		 * Scale an image up to $max in one dimension.
		 *
		 * The image will resize if any dimension is less than $max
		 *
		 * @param  integer $min       [minimum dimension in pixels]
		 * @param  boolean  $overwrite [overwrite the original file]
		 *
		 * @return void
		 */

		public function min_dim($min = 0, $overwrite = false){
			$width = $this->getWidth();
			$height = $this->getHeight();
			if(($width < $min) && ($height < $min)){
				($width >= $height) ? $this->resizeToWidth($min) : $this->resizeToHeight($min);
				if($overwrite){
					$this->save($this->fname);
				}
			}
		}

		/**
		 * Scale an image up to $max in one dimension.
		 *
		 * The image will resize if any dimension is less than $max
		 *
		 * @param  integer $max       [Maximum dimension in pixels]
		 * @param  boolean  $overwrite [overwrite the original file]
		 *
		 * @return void
		 */

		public function max_dim($max = 0, $overwrite = false){
			$width = $this->getWidth();
			$height = $this->getHeight();
			if(($width > $max) || ($height > $max)){
				($width >= $height) ? $this->resizeToWidth($max) : $this->resizeToHeight($max);
				if($overwrite){
					$this->save($this->fname);
				}
			}
		}

		/**
		 * Resize to a fixed height. width adjusts accordingly
		 *
		 * @param int $height [new height in pixels]
		 *
		 * @return void
		 */

		public function resizeToHeight($height) {
			$ratio = $height / $this->getHeight();
			$width = $this->getWidth() * $ratio;
			$this->resize($width,$height);
		}

		/**
		 * Resize to a fixed width. Height adjusts accordingly
		 *
		 * @param int $width [new width in pixels]
		 *
		 * @return void
		 */

		public function resizeToWidth($width) {
			$ratio = $width / $this->getWidth();
			$height = $this->getheight() * $ratio;
			$this->resize($width,$height);
		}

		/**
		 * Increase/Decrease size proportionally
		 *
		 * @param  int $scale [scale by this factor]
		 *
		 * @return void
		 */

		public function scale($scale) {
			$width = $this->getWidth() * $scale/100;
			$height = $this->getheight() * $scale/100;
			$this->resize($width,$height);
		}

		/**
		 * Copy and resize image to give width & height
		 * Updates $image to the copy at new dimensions
		 *
		 * @link http://php.net/manual/en/function.imagecopyresampled.php
		 * @param  int $width  [width in pixels]
		 * @param  int $height [height in pixels]
		 *
		 * @return void
		 */

		public function resize($width, $height) {
			$new_image = imagecreatetruecolor($width, $height);
			imagecopyresampled(
				$new_image,
				$this->image,
				0,
				0,
				0,
				0,
				$width,
				$height,
				$this->getWidth(),
				$this->getHeight()
			);
			$this->image = $new_image;
		}
	}
?>

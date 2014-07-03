<?php
	/**
	 * @author Chris Zuber <shgysk8zer0@gmail.com>
	 * @copyright 2014, Chris Zuber
	 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3 (GPL-3.0)
	 * @package core_shared
	 * @version 2014-04-19
	 */

	class magic_cache {
		protected $file, $ext, $type, $size, $etag, $mod_time, $gz, $status;

		public function __construct($file){
			(!defined('BASE')) ? $this->file = dirname(__FILE__) . "/$file" : $this->file = BASE . "$file";
			header("File_Requested: {$this->file}");
			if(file_exists($this->file)){
				$this->etag = md5_file($this->file);
				$this->mod_time = filemtime($this->file);
				$this->size = filesize($this->file);
				$this->fname = preg_replace('/^.+\//', '', $this->file);
				$this->ext = preg_replace('/^.+\./', '', $this->fname);
				$this->type_by_extension();
				$this->cache_control();
				$this->make_headers();
				echo file_get_contents($this->file);
			}
			else{
				$this->status = 404;
				$this->http_status();
			}
		}

		protected function make_headers(){
			$gzip = array('svgz', 'cssz', 'jsz');
			$this->status = 200;
			$this->http_status();
			header("Content-Type: {$this->type}");
			header("Content-Length: {$this->size}");
			if(in_array($this->ext, $gzip)){
				header('Content-Encoding: gzip');
			}
			//set last-modified header
			header("Last-Modified: " . gmdate("D, d M Y H:i:s T", $this->mod_time));
			//set etag-header
			header("Etag: {$this->etag}");
			//make sure caching is turned on
			header('Cache-Control: public');
		}

		protected function cache_control(){
			//$ifModifiedSince = (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false);
			//get the HTTP_IF_NONE_MATCH header if set (etag: unique file hash)
			$etagHeader = (isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);

			//check if page has changed. If not, send 304 and exit
			if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $this->mod_time || $etagHeader == $this->etag){
				$this->status = 304;
				$this->http_status();
			}
		}

		protected function type_by_extension(){ //Because PHP does a poor job of setting MIME-Type
			$gzip = array('svgz', 'cssz', 'jsz');
			switch($this->ext){ //Start by matching file extensions
				case 'svg': case 'svgz': $type = 'image/svg+xml'; break;
				case 'woff': $type = 'application/font-woff'; break;
				case 'ttf': $type = 'application/x-font-ttf'; break;
				case 'otf': $type = 'application/x-font-opentype'; break;
				case 'sql': $type = 'text/x-sql'; break;
				case 'appcache': $type = 'text/cache-manifest'; break;
				case 'mml': $type = 'application/xhtml+xml'; break;
				case 'ogv': $type = 'video/ogg'; break;
				case 'webm': $type = 'video/webm'; break;
				case 'ogg': case 'oga': case 'opus': $type = 'audio/ogg'; break;
				case 'flac': $type = 'audio/flac'; break;
				case 'mp4': case 'mpeg': case 'mpg': case 'mpe': case 'mp4'; $type = 'video/mp4'; break;
				case 'm4a': $type = 'audio/mp4'; break;
				case 'css': case 'cssz': $type = 'text/css'; break;
				case 'js': case 'jsz': $type = 'text/javascript'; break;
				default: $type = mime_content_type($this->file); //If not found, try the file's default
			}
			$this->type = $type;
		}

		protected function http_status(){
			http_status_code($this->status);
			if(!preg_match('/^2[\d]{2}$/', $this->status)) {
				exit();
			}
		}
	}
?>

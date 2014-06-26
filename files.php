<?php class magic_cache{
	protected $file;
	protected $ext;
	protected $type;
	protected $size;
	protected $etag;
	protected $mod_time;
	protected $gz;
	protected $status;
	
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
			//print_r($this);//($this->type === 'text/cache-manifest') ? echo $this->type : echo file_get_contents($file);
			echo file_get_contents($this->file);
		}
		else{
			$this->status = 404;
			$this->http_status();//header('HTTP/1.1 404 Not Found');
		}
	}
	
	protected function make_headers(){
		$gzip = array('svgz', 'cssz', 'jsz');
		$this->status = 200;
		$this->http_status();//header('HTTP/1.1 200 OK');
		//header("Content-Type: {$this->type}");
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
		/*switch($this->type){
			case 'text/cache-manifest': header('Cache-Control: No-Cache'); break;
			default: header('Cache-Control: public');
		}*/
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
			/*header("HTTP/1.1 304 Not Modified");
			exit;*/
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
		//Description of Status Codes may be found here http://www.w3schools.com/tags/ref_httpmessages.asp
		switch($this->status){
			case 100: $desc = 'Continue'; break;
			case 101: $desc = 'Switching Protocols'; break;
			case 103: $desc = 'Checkpoint'; break;
			case 200: $desc = 'OK'; break;
			case 201: $desc = 'Created'; break;
			case 202: $desc = 'Accepted'; break;
			case 203: $desc = 'Non-Authoritative Information'; break;
			case 204: $desc = 'No Content'; break;
			case 205: $desc = 'Reset Content'; break;
			case 206: $desc = 'Partial Content'; break;
			case 300: $desc = 'Multiple Choices'; break;
			case 301: $desc = 'Moved Permanently'; break;
			case 302: $desc = 'Found'; break;
			case 303: $desc = 'See Other'; break;
			case 304: $desc = 'Not Modified'; break;
			case 306: $desc = 'Switch Proxy'; break;
			case 307: $desc = 'Temporary Redirect'; break;
			case 308: $desc = 'Resume Incomplete'; break;
			case 400: $desc = 'Bad Request'; break;
			case 401: $desc = 'Unauthorized'; break;
			case 402: $desc = 'Payment Required'; break;
			case 403: $desc = 'Forbidden'; break;
			case 404: $desc = 'Not Found'; break;
			case 405: $desc = 'Method Not Allowed'; break;
			case 406: $desc = 'Not Acceptable'; break;
			case 407: $desc = 'Proxy Authentication Required'; break;
			case 408: $desc = 'Request Timeout'; break;
			case 409: $desc = 'Conflict'; break;
			case 410: $desc = 'Gone'; break;
			case 411: $desc = 'Length Required'; break;
			case 412: $desc = 'Precondition Failed'; break;
			case 413: $desc = 'Request Entity Too Large'; break;
			case 414: $desc = 'Request-URI Too Long'; break;
			case 415: $desc = 'Unsupported Media Type'; break;
			case 416: $desc = 'Requested Range Not Satisfiable'; break;
			case 417: $desc = 'Expectation Failed'; break;
			case 500: $desc = 'Internal Server Error'; break;
			case 501: $desc = 'Not Implemented'; break;
			case 502: $desc = 'Bad Gateway'; break;
			case 503: $desc = 'Service Unavailable'; break;
			case 504: $desc = 'Gateway Timeout'; break;
			case 505: $desc = 'HTTP Version Not Supported'; break;
			case 511: $desc = 'Network Authentication Required'; break;
			default: header("HTTP/1.1 500 Internal Server Error");
			exit;
		}
		header("HTTP/1.1 {$this->status} {$desc}");
		if(!preg_match('/^2[\d]{2}$/', $this->status)) { //If it it is not 2**, exit.
		//This is the case for not modified, not found, or other errors
			exit;
		}
		return;
	}
}
	(isset($_REQUEST['file'])) ? new magic_cache($_REQUEST['file']) : header('HTTP/1.1 404 Not Found');
?>

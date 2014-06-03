<?php
	class template extends regexp{
		/**
		 * @author Chris Zuber <shgysk8zer0@gmail.com>
		 * @copyright 2014, Chris Zuber
		 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3 (GPL-3.0)
		 * @package core_shared
		 * @version 2014-06-01
		 */
		
		private static $instance = [];
		private $template, $path;

		public static function load($tpl) {
			/**
			 * Static load function avoids creating multiple instances
			 * It checks if an instance has been created and returns that or a new instance
			 * 
			 * Can be called on multiple files (one at a time) to load
			 * multiple files. It stores them as an array, with the file as the
			 * array key and the instance as the value
			 *
			 * @params void
			 * @return template object/class
			 * @example $template = template::load($template_file)
			 */

			if(!array_key_exists($tpl, self::$instance)) {
				self::$instance[$tpl] = new self($tpl);
			}
			return self::$instance[$tpl];
		}

		public function __construct($tpl) {
			/**
			 * Reads the template specified by $tpl
			 * Reads the file from BASE . "/components/templates/{$tpl}.tpl"
			 * Will exit if file cannot be read (either DNE or denied by permissions)
			 * 
			 * @param string $tpl
			 * @return void
			 * @usage $template = new template($template_file)
			 */
			
			$this->path = BASE . "/components/templates/{$tpl}.tpl";
			if(file_exists($this->path)) {
				parent::__construct(file_get_contents($this->path));
			}
			else {
				exit("Attempted to load a template that cannot be read. {$tpl} cannot be read")
			}
		}

		public function __set($replace, $with) {
			/**
			 * Unlike most magic setters, this does not work with variables
			 * Instead, it sets up a Regular Expression string replacement.
			 * 
			 * Templates are expected to use placeholders of the format %[A-Z_]+%,
			 * So $template->url = $url will replace all occurances of %URL% with $url.
			 * All placeholders should be enclosed in '%' and should be all upper case.
			 * 
			 * @param string $replace
			 * @param string $with
			 * @return void
			 * @usage $template->url = $url
			 */
			
			$this->replace('%' . trim(strtoupper($replace)) . '%')->with($with);
		}

		public function set($arr) {
			/**
			 * Loops through $arr using, replacing array_key with array_value in $template
			 * See __set() documentation for description of template formatting.
			 * 
			 * @param array $arr
			 * @return self
			 * @usage $template->set([$placeholder => $replacement][, ...])
			 */
			
			foreach($arr as $replace => $with) {
				$this->replace('%' . trim(strtoupper($replace)) . '%')->with($with);
			}
			return $this;
		}

		public function out($print = false) {
			/**
			 * Executes the Regular Expression replacement without updating
			 * the source (original template content).
			 * 
			 * Will either return the result (default), or will
			 * echo it (if $print evaluates as true)
			 * 
			 * @param boolean $print
			 * @return string or void
			 * @usage $conntent = $template->out([false]);
			 * or to print the results $template->out(true)
			 */
			
			$html = $this->execute(false);
			$this->pattern = $this->replacement = [];
			if($print){
				echo $html;
			}
			else {
				return $html;
			}
		}
	}
?>

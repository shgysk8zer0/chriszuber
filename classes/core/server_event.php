<?php
	/**
	 * Class to allow continuous updates from server using Server Sent Events
	 *
	 * @author Chris Zuber <shgysk8zer0@gmail.com>
	 * @package core_shared
	 * @uses json_response
	 * @version 2014-08-18
	 * @link https://developer.mozilla.org/en-US/docs/Server-sent_events/Using_server-sent_events
	 * @copyright 2014, Chris Zuber
	 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3 (GPL-3.0)
	 * This program is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU General Public License
	 * as published by the Free Software Foundation, either version 3
	 * of the License, or (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 *
	 * @var server_event $instance
	 * @example
	 * $event = new server_event(); $n = 42;
	 * while($n--) {
	 * 	$event->notify(
	 * 	'This is an example of a server event',
	 * 	'It functions the same has json_response, but can send multiple messages'
	 * )->html(
	 * 	'main',
	 * 	'This is the ' . 43 - $n .'th message'
	 * )->send()->wait(1)
	 * }
	 * $event->close();
	 */

	namespace core;
	class server_event extends json_response {
		private static $instance = null;

	/**
	 * Static method to load class
	 * @param array $data
	 * @return NULL
	 */

		public static function load(array $data = null) {
			if(is_null(self::$instance)) {
				self::$instance = new self($data);
			}
			return self::$instance;
		}

		/**
		 * Constructor for class. Class method to set headers
		 * and initialize first (optional) set of data.
		 *
		 * Inherits its methods from json_response, so do parent::__construct()
		 *
		 * @param array $data (optional array of data to be initialized with)
		 * @example $event = new server_event(['html' => ['main' => 'It Works!']]...)
		 */

		public function __construct(array $data = null) {
			$this->set_headers();
			parent::__construct();

			if(isset($data)) {
				$this->response = $data;
			}
		}

		/**
		 * Sends everything with content-type of text/event-stream,
		 * Echos json_encode($this->response)
		 * An optional $key argument can be used to only
		 * send a subset of $this->response
		 *
		 * @param string $key
		 * @example $event->send() or $event->send('notify')
		 */

		public function send($key = null) {
			echo 'event: ping' . PHP_EOL;

			if(count($this->response)) {
				if(is_string($key)) {
					echo 'data: ' . json_encode([$key => $this->response[$key]]) . PHP_EOL . PHP_EOL;
				}
				else {
					echo 'data: ' . json_encode($this->response) . PHP_EOL . PHP_EOL;
				}
				$this->response = [];
			}

			ob_flush();
			flush();
			return $this;
		}

		/**
		 * Sets headers required to be handled as a server event.
		 * @param void
		 * @return server_event
		 */

		private function set_headers() {
			header('Content-Type: text/event-stream');
			header_remove('X-Powered-By');
			header_remove('Expires');
			header_remove('Pragma');
			header_remove('X-Frame-Options');
			header_remove('Server');
			return $this;
		}

		/**
		 * Set delay between events and flush out
		 * previous response.
		 *
		 * @param int $delay
		 * @return server_event
		 */

		public function wait($delay = 1) {
			sleep((int)$delay);
			return $this;
		}

		/**
		 * Same as the send() method, except this
		 * method indicates that it is the final event.
		 *
		 * The handler in handleJSON will terminate the serverEvent
		 * after receiving an event of type 'close'
		 *
		 * @param $key
		 * @example $event->close() or $event->close('notify')
		 */

		public function close($key = null) {
			echo 'event: close' . PHP_EOL;

			if(!empty($this->response)) {
				if(is_string($key)) {
					echo 'data: ' . json_encode([$key => $this->response[$key]]) . PHP_EOL . PHP_EOL;
				}
				else {
					echo 'data: ' . json_encode($this->response) . PHP_EOL . PHP_EOL;
				}
				$this->response = [];
			}
			else {
				echo 'data: "{}"' . PHP_EOL . PHP_EOL;
			}

			ob_flush();
			flush();
			return $this;
		}
	}
?>

<?php 
class myLog extends myClass {
	function __construct ($properties = array ()) {
		if(!array_key_exists('path', $properties)) {
			$properties['path'] = 'log-'.date('Y-m-d').'.txt';
		}
		parent::__construct($properties);

		$this->start();
	}

	public function stop() {

	}

	public function start() {
		$this->properties['log_file_path'] = $this->properties['log_dir'].$this->properties['path'];
	}

	public function message($message = "") {
		$this->_print($message, '@INFORMATION');
	}

	public function warning($message = "") {
		$this->_print($message, '@WARNING');
	}

	public function error($message = "") {
		$this->_print($message, '@ERROR');
	}

	public function _print($message = "", $before_message = "") {
		$message = '['.date('H:i:s').']['.$before_message.'] '.$this->printTrackInformation().' #'.$message.'#'."\n";
		file_put_contents($this->properties['log_file_path'], $message, FILE_APPEND);
	}

	public function getTrackInformation() {
		$track_information = debug_backtrace();
		foreach($track_information as $track_information_item) {
			if(!array_key_exists('file', $track_information_item) || $track_information_item['file'] != __FILE__) {
				return $track_information_item;
			}
		}

		return false;
	}

	public function printTrackInformation() {
		$track_information = $this->getTrackInformation();
		if($track_information) {
			$return = 'Line: '.$track_information['line'].' in file '.$track_information['file'];
		} else {
			$return = 'Non backrack';
		}

		return $return;
	}
}

return 'myLog';
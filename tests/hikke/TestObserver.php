<?php

namespace hikke;

class TestObserver implements \SplObserver
{
	static $logs;
	
	private $name;
	
	function __construct($name) {
		$this->name = $name;
	}
	
	function __toString() {
		return $this->name;
	}
	
	static function reset() {
		self::$logs = "";
	}
	
	function update(\SplSubject $event, $supp = null) {
		self::$logs .= "$this->name: $event";
		if ($supp && $supp != $event) {
			self::$logs .= ":$supp";
		}
		self::$logs .= "\n";
	}
	
	function callMe(\stdClass $arg) {
		$arg->data .= __METHOD__."\n";
	}
}

<?php

namespace hikke;

require_once __DIR__."/../../vendor/autoload.php";
require_once __DIR__."/TestObserver.php";

class EventTest extends \PHPUnit_Framework_TestCase
{
	function setUp() {
		TestObserver::reset();
	}
	
	function testBasic() {
		$first = new Event("first", 1);
		$second = new Event("second", 2);
		
		$this->assertEquals(-1, $first->compareTo($second));
		$this->assertEquals(1, $second->compareTo($first));
		
		$this->assertEquals("first", $first->getName());
		$this->assertEquals("second", $second->getName());
	}
	
	function testAttachDetach() {
		$event = new Event("event");
		$observer = new TestObserver("observer");
		$this->assertEquals($event, $event->attach($observer));
		$this->assertTrue($event->detach($observer));
		$this->assertFalse($event->detach($observer));
	}
	
	function testNotify() {
		$event = new Event("event");
		$event->attach(new TestObserver("first"));
		$event->attach(new TestObserver("second"));
		$event->attach(new TestObserver("third"));
		$event->notify();
		$this->assertEquals(
			"first: event\n".
			"second: event\n".
			"third: event\n", TestObserver::$logs);
	}
	
	function testPrioritizedNotify() {
		$event = new Event("event");
		$event->attach(new TestObserver("first"),3);
		$event->attach(new TestObserver("second"),2);
		$event->attach(new TestObserver("third"),1);
		$event->notify();
		$this->assertEquals(
			"third: event\n".
			"second: event\n".
			"first: event\n", TestObserver::$logs);
	}
}

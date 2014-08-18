<?php

namespace hikke;

require_once __DIR__."/../../vendor/autoload.php";
require_once __DIR__."/TestObserver.php";

class ProxyTest extends \PHPUnit_Framework_TestCase
{
	function setUp() {
		TestObserver::reset();
	}
	
	function testBasic() {
		$proxy = new Event\Proxy(["first","second"]);
		$this->assertInstanceOf("hikke\\Event", $proxy->first);
		$this->assertInstanceOf("hikke\\Event", $proxy->second);
	}
	
	function testAutoInsert() {
		$proxy = new Event\Proxy;
		$this->assertFalse(isset($proxy->event));
		$this->assertInstanceOf("hikke\\Event", $proxy->event);
		$this->assertTrue(isset($proxy->event));
		unset($proxy->event);
		$this->assertFalse(isset($proxy->event));
	}
	
	function testExplicitInsert() {
		$proxy = new Event\Proxy;
		$this->assertFalse(isset($proxy->event));
		$proxy->event = 1.23;
		$this->assertTrue(isset($proxy->event));
		$this->assertInstanceOf("hikke\Event", $proxy->event);
		$this->assertEquals(1.23, round($proxy->event->getPriority(),2));
	}
	
	function testProxy() {
		$proxy = new Event\Proxy(["one", "two", "three"]);
		$observer = new TestObserver("observer");
		$proxy->attach($observer);
		$proxy->notify();
		$this->assertEquals(
			"observer: one\n".
			"observer: two\n".
			"observer: three\n",
			TestObserver::$logs);
	}
	
	function testProxyProxy() {
		$proxied = new Event\Proxy(["one", "two", "three"]);
		$observer = new TestObserver("observer");
		$proxied->attach($observer);
		$proxy = new Event\Proxy;
		$proxy->attach($proxied);
		$proxy->notify();
		$this->assertEquals(
			"observer: default:one\n".
			"observer: default:two\n".
			"observer: default:three\n",
			TestObserver::$logs);
	}
	
	function testProxyCall() {
		$proxy = new Event\Proxy;
		$observer = new TestObserver("call");
		$proxy->attach($observer);
		$arg = (object) ["data" => null];
		$proxy->callMe($arg);
		$this->assertEquals("hikke\\TestObserver::callMe\n", $arg->data);
	}
	
	function testIterator() {
		$proxy = new Event\Proxy;
		$proxy->attach(new TestObserver("o1"));
		$proxy->attach(new TestObserver("o2"));
		$proxy->attach(new TestObserver("o3"));
		$string = "";
		foreach ($proxy as $event) {
			$string .= $event;
			foreach ($event as $observer) {
				$string .= ":$observer";
			}
		}
		$this->assertEquals("default:o1:o2:o3", $string);
	}
	
	function testCountAndDetach() {
		$rcount = function($proxy) {
			return array_sum(array_map(function($e) {
				return count($e);
			}, iterator_to_array($proxy)));
		};
		$proxy = new Event\Proxy(["e1","e2"]);
		$observer1 = new TestObserver("o1");
		$proxy->attach($observer1);
		$this->assertEquals(2, count($proxy));
		$observer2 = new TestObserver("o2");
		$proxy->attach($observer2);
		$this->assertEquals(4, $rcount($proxy));
		$proxy->detach($observer1);
		$this->assertEquals(2, $rcount($proxy));
		$proxy->detach($observer2, "e2");
		$this->assertEquals(1, $rcount($proxy));
	}
	
	function testAssignTwice() {
		$proxy = new Event\Proxy;
		$proxy->ev0 = new Event("ev0", 0);
		$this->setExpectedException("UnexpectedValueException", "The event name 'ev0' is already in use");
		$proxy->ev0 = 0;
	}
	
	function testAssignDifferent() {
		$proxy = new Event\Proxy;
		$this->setExpectedException("UnexpectedValueException", "The event names differ: 'ev3' <> 'ev4'");
		$proxy->ev3 = new Event("ev4");
	}
}

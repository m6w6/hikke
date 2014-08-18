<?php

/**
 * Hikke
 * 
 * @author Michael Wallner <mike@php.net>
 */
namespace hikke\Event;

use hikke\Event;
use hikke\Event\Storage;

/**
 * Proxy
 * 
 * @package hikke\Event
 */
class Proxy implements \SplSubject, \SplObserver, \IteratorAggregate, \Countable
{
	/**
	 * @var array
	 */
	private $events = array();
	
	/**
	 * @var \hikke\Event\Storage
	 */
	private $storage;
	
	public function __construct(array $events = ["default"]) {
		$this->storage = new Storage;
		foreach ((array) $events as $priority => $event) {
			$this->insert($event, $priority);
		}
	}
	
	/**
	 * Notify all attached observers passing along $origin
	 * @param \SplSubject $origin
	 */
	public function update(\SplSubject $origin) {
		$this->notify($origin);
	}
	
	/**
	 * Apply a cvallback ot a specific or all events
	 * @param string $event
	 * @param callable $apply
	 */
	public function apply($event, callable $apply) {
		if (strlen($event)) {
			$apply($this->events[$event]);
		} else {
			foreach ($this->storage as $ev) {
				$apply($ev);
			}
		}
	}
	
	/**
	 * Notify all attached observers to a specific or all events passing alomg $origin
	 * @param object $origin
	 * @param string $event
	 */
	public function notify($origin = null, $event = null) {
		$this->apply($event, function($ev) use($origin) {
			$ev->notify($origin);
		});
	}
	
	/**
	 * Attach an observer to a specfiv or all events
	 * @param \SplObserver $observer
	 * @param string $event
	 * @param int|float $priority
	 * @return \hikke\Event\Proxy self
	 */
	public function attach(\SplObserver $observer, $event = null, $priority = 0) {
		$this->apply($event, function($ev) use($observer, $priority) {
			$ev->attach($observer, $priority);
		});
		return $this;
	}
	
	/**
	 * Detach an observer from all or a specific event
	 * @param \SplObserver $observer
	 * @param string $event
	 * @return \hikke\Event\Proxy self
	 */
	public function detach(\SplObserver $observer, $event = null) {
		$this->apply($event, function($ev) use($observer) {
			$ev->detach($observer);
		});
		return $this;
	}
	
	/**
	 * Insert a new event type
	 * @param string $name
	 * @param int|float $priority
	 */
	private function insert($name, $priority = 0) {
		if ($priority instanceof Event) {
			/* assignement in the form:
			 * $proxy->foo = new Event("foo", 123);
			 */
			$event = $priority;
			$priority = $event->getPriority();
			
			/* sanity check */
			if ($name !== $event->getName()) {
				throw new \UnexpectedValueException(
					sprintf("The event names differ: '%s' <> '%s'",
						$name, $event->getName()));
			}
		} elseif (isset($this->events[$name])) {
			throw new \UnexpectedValueException(
				sprintf("The event name '%s' is already in use", $name));
		} else {
			$event = new Event($name);
		}
		
		$bucket = $this->storage->insert($event, $priority);
		$event->setPriority($bucket->getPriority());
		$this->events[$name] = $event;
	}
	
	/**
	 * @ignore
	 */
	public function __call($method, $args) {
		$observers = new \SplObjectStorage;
		$this->apply(null, function($ev) use($observers) {
			foreach ($ev as $observer) {
				if (!$observers->contains($observer)) {
					$observers->attach($observer);
				}
			}
		});
		foreach ($observers as $observer) {
			if (is_callable(array($observer, $method))) {
				call_user_func_array(array($observer, $method), $args);
			}
		}
	}
	
	/**
	 * @ignore
	 */
	public function __get($event) {
		if (!isset($this->events[$event])) {
			$this->insert($event);
		}
		return $this->events[$event];
	}
	
	/**
	 * @ignore
	 */
	public function __set($event, $priority) {
		$this->insert($event, $priority);
	}
	
	/**
	 * @ignore
	 */
	public function __isset($event) {
		return isset($this->events[$event]);
	}
	
	/**
	 * @ignore
	 */
	public function __unset($event) {
		if (isset($this->events[$event])) {
			$this->storage->delete($this->events[$event]);
			unset($this->events[$event]);
		}
	}
	
	/**
	 * @ignore
	 */
	public function getIterator() {
		return $this->storage;
	}
	
	/**
	 * @ignore
	 */
	public function count() {
		return count($this->storage);
	}
}

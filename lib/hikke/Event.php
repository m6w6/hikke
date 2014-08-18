<?php

/**
 * Hikke
 * 
 * @author Michael Wallner <mike@php.net>
 */
namespace hikke;

use hikke\Event\Priority;
use hikke\Event\Storage;

/**
 * Event
 * 
 * @package hikke\Event
 */
class Event extends Priority implements \SplSubject, \IteratorAggregate, \Countable
{
	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * @var float
	 */
	private $priority;
	
	/**
	 * @var hikke\Event\Storage
	 */
	private $storage;
	
	/**
	 * Create a new event
	 * @param string $name custom event name
	 * @param int|float $priority
	 */
	public function __construct($name, $priority = 0) {
		$this->name = $name;
		$this->priority = $priority;
		$this->storage = new Storage;
	}

	/**
	 * Returns the event name
	 * @return string
	 */
	public function __toString() {
		return (string) $this->name;
	}
	
	/**
	 * Get priority of this event
	 * @return float
	 */
	public function getPriority() {
		return $this->priority;
	}
	
	/**
	 * Get the event name
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Update priority of this event
	 * @param float $priority
	 */
	public function setPriority($priority) {
		$this->priority = $priority;
	}
	
	/**
	 * Attach an event observer
	 * @param \SplObserver $observer
	 * @param float $priority
	 * @return \hikke\Event
	 */
	public function attach(\SplObserver $observer, $priority = 0) {
		$this->storage->insert($observer, $priority);
		return $this;
	}
	
	/**
	 * Detach an already attached event observer
	 * @param \SplObserver $observer
	 * @return bool whether the observer was attached
	 */
	public function detach(\SplObserver $observer) {
		return $this->storage->delete($observer);
	}
	
	/**
	 * Notify attached observers
	 * @param \SplSubject $origin
	 */
	public function notify(\SplSubject $origin = null) {
		foreach ($this->storage as $observer) {
			$observer->update($origin ?: $this, $this);
		}
	}
	
	/**
	 * @ignore
	 */
	public function count() {
		return count($this->storage);
	}
	
	/**
	 * @ignore
	 */
	public function getIterator() {
		return $this->storage;
	}
}

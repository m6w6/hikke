<?php

/**
 * Hikke
 * 
 * @author Michael Wallner <mike@php.net>
 */
namespace hikke\Event;

use hikke\Event\Bucket;

/**
 * Storage
 * 
 * @package hikke\Event
 */
class Storage implements \Iterator, \Countable
{
	/**
	 * @var int
	 */
	private $sequence = 1;
	
	/**
	 * @var array
	 */
	private $buckets = array();
	
	/**
	 * @var array
	 */
	private $iterator;
	
	/**
	 * @param object $object
	 * @param int|float $priority
	 * @return \hikke\Event\Bucket
	 */
	public function insert($object, $priority = 0) {
		$priority += $this->sequence++ / 1000;
		$bucket = new Bucket($object, $priority);
		$this->buckets[$bucket->getHash()] = $bucket;
		return $bucket;
	}
	
	/**
	 * @param object $object
	 * @return bool whether the storage container the object
	 */
	public function delete($object) {
		$hash = spl_object_hash($object);
		if (($found = isset($this->buckets[$hash]))) {
			unset($this->buckets[$hash]);
		}
		return $found;
	}
	
	/**
	 * @ignore
	 */
	public function count() {
		return count($this->buckets);
	}
	
	/**
	 * @ignore
	 */
	public function rewind() {
		$this->iterator = $this->buckets;
		uasort($this->iterator, ["hikke\\Event\\Priority", "compare"]);
	}
	
	/**
	 * @ignore
	 */
	public function valid() {
		return NULL !== key($this->iterator);
	}
	
	/**
	 * @ignore
	 */
	public function next() {
		next($this->iterator);
	}
	
	/**
	 * @ignore
	 */
	public function key() {
		return current($this->iterator)->getPriority();
	}
	
	/**
	 * @ignore
	 */
	public function current() {
		return current($this->iterator)->getObject();
	}
}

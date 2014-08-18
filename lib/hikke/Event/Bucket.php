<?php

/**
 * Hikke
 * 
 * @author Michael Wallner <mike@php.net>
 */
namespace hikke\Event;

use hikke\Event\Priority;

/**
 * Bucket
 * 
 * @package hikke\Event
 */
class Bucket extends Priority
{
	/**
	 * @var object
	 */
	private $object;
	
	/**
	 * @var float
	 */
	private $priority;
	
	/**
	 * @var string
	 */
	private $hash;
	
	/**
	 * Create a new storage bucket
	 * @param object $object
	 * @param float $priority
	 */
	public function __construct($object, $priority) {
		$this->object = $object;
		$this->priority = $priority;
	}
	
	/**
	 * Get the priority of the object in the bucket
	 * @return float
	 */
	public function getPriority() {
		return $this->priority;
	}
	
	/**
	 * Get the object contained in this bucket
	 * @return object
	 */
	public function getObject() {
		return $this->object;
	}
	
	/**
	 * Get the hash of the object contained in this bucket
	 * @return string
	 */
	public function getHash() {
		if (!isset($this->hash)) {
			$this->hash = spl_object_hash($this->object);
		}
		return $this->hash;
	}
}

<?php

/**
 * Hikke
 * 
 * @author Michael Wallner <mike@php.net>
 */
namespace hikke\Event;

/**
 * Prioritized
 * 
 * @package hikke\Event
 */
interface Prioritized
{
	/**
	 * Get the object's priority, the closer the value to 0 the higher the priority
	 * @return int|float
	 */
	public function getPriority();
}

<?php

/**
 * Hikke
 * 
 * @author Michael Wallner <mike@php.net>
 */
namespace hikke\Event;

/**
 * Priority
 * 
 * @package hikke\Event
 */
abstract class Priority implements Prioritized
{
	/**
	 * Compare an instance of a prioritzed object ot another
	 * @param \hikke\Event\Prioritized $other
	 * @return int
	 */
	public function compareTo(Prioritized $other) {
		return static::compare($this, $other);
	}
	
	/**
	 * Comparator
	 * @param \hikke\Event\Prioritized $a
	 * @param \hikke\Event\Prioritized $b
	 * @return int
	 */
	public static function compare(Prioritized $a, Prioritized $b) {
		if ($a->getPriority() < $b->getPriority()) {
			return -1;
		} elseif ($b->getPriority() < $a->getPriority()) {
			return 1;
		} else {
			// @codeCoverageIgnoreStart
			return 0;
			// @codeCoverageIgnoreEnd
		}
	}
	
}

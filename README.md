# hikke\Event

Prioritized event observers. [![Build Status](https://travis-ci.org/m6w6/hikke.svg)](https://travis-ci.org/m6w6/hikke)

Example:

```php
<?php

use hikke\Event;

class Observer implements \SplObserver {
	private $name;
	function __construct($name) {
		$this->name = $name;
	}
	function update(\SplSubject $e) {
		echo "Observer '{$this->name}' notified by '$e' ({$e->getPriority()})\n";
	}
	function proxiedMethodCall($arg) {
		$this->name .= $arg;
	}
}

$event = new Event("my_event");
$event->attach(new Observer("o1"), 1);
$event->attach(new Observer("o2"), 2);
$event->notify();

?>
```

Output:

```
Observer 'o1' notified by 'my_event' (0)
Observer 'o2' notified by 'my_event' (0)
```

Another example:

```php
<?php

$proxy = new Event\Proxy;
$proxy->ev1 = 0;
$proxy->ev2 = 1;
$proxy->attach(new Observer("o1"), null, 1);
$proxy->attach(new Observer("o2"), null, 0);
$proxy->attach(new Observer("o3"), "ev2");
$proxy->ev3->attach(new Observer("o2"));

$proxy->proxiedMethodCall("-proxy");
$proxy->notify();
?>
```

Output:

```
Observer 'o2-proxy' notified by 'default' (0.001)
Observer 'o1-proxy' notified by 'default' (0.001)
Observer 'o2-proxy' notified by 'ev1' (0.002)
Observer 'o1-proxy' notified by 'ev1' (0.002)
Observer 'o2-proxy' notified by 'ev3' (0.004)
Observer 'o2-proxy' notified by 'ev2' (1.003)
Observer 'o3-proxy' notified by 'ev2' (1.003)
Observer 'o1-proxy' notified by 'ev2' (1.003)
```

<?php

require_once('singleton_trait.php');

class EventManager {
    use Singleton;

    protected static $events;

    protected function _init() {
        static::$events = array();
    }

    public function isEventRegistered($eventName) {
        return isset(static::$events[$eventName]);
    }

    protected function & getEventObservers($eventName) {
        if ($this->isEventRegistered($eventName)) {
            return static::$events[$eventName];
        }
        $rv = NULL; // need this 'cause we return by ref
        return $rv;
  
    }

    public function unregisterEvent($eventName) {
        unset(static::$events[$eventName]);
    }

    public function registerEvent($eventName) {
        if (!$this->isEventRegistered($eventName)) {
            static::$events[$eventName] = array();
        }
    }

    public function triggerEvent($eventName, $eventData) {
        if ($this->isEventRegistered($eventName)) {
             $observers = & $this->getEventObservers($eventName);
             foreach ($observers as $callback) {
                 $eventData = call_user_func($callback, $eventData);
             }
             return $eventData;
        } else {
             trigger_error('attempt to trigger nonregistered event', E_USER_ERROR);
        }
    }

    public function subscribeObserver($eventName, $callback) {
        if (!$this->isEventRegistered($eventName)) {
             // warning only, subscribing will automatically register one
             trigger_error('attempt to subscribe to nonregistered event', E_USER_WARNING);
        }
        // return the index so the observer can be unsubscribed
        $observers = & $this->getEventObservers($eventName);
        return array_push($observers, $callback);
    }

    public function unsubscribeObserver($eventName, $index) {
        if ($this->isEventRegistered($eventName)) { // defensive
            $observers = & $this->getEventObservers($eventName);
            if (isset($observers[$index])) {
                $callback = $observers[$index];
                unset($observers[$index]);
            } else {
                $callback = NULL; // already unsubscribed
            }
            // sometimes we might need that callback which was subscribed (i.e. for introspection/debug)
            return $callback;
        } else {
             trigger_error('attempt to unsubscribe from nonregistered event', E_USER_ERROR);
        }
    }

    public function __call($methodName, $args) {
        if (0 === strpos($methodName, "on")) {
            $eventName = strtolower(substr($methodName, 2));
            return $this->subscribeObserver($eventName, $args[0]);
        } elseif (0 === strpos($methodName, "event")) {
            $eventName = strtolower(substr($methodName, 5));
            return $this->triggerEvent($eventName, $args[0]);
        } else {
            trigger_error("unknown method " . $methodName, E_USER_ERROR);
        }
    }
};

?>


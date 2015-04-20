<?php

require_once 'event_manager.php';
require_once 'singleton_trait.php';

class ObserverManager {
    use Singleton;

    protected static $observers = array();

    protected function _init($conn) {
        $this->initObserversFromDb($conn);
    }

    protected function initObserversFromDb($conn) {
        $evManager = EventManager::getInstance();

        $query = $conn->prepare(
            "SELECT o_singleton_classname, o_methodname, o_eventname FROM event_manager_observers"
        );
        $query->execute();
        $result = $query->get_result();

        while ($row = $result->fetch_row()) {
            list($singletonClassname, $methodName, $eventName) = $row;
            if ($singletonClassname) { // singleton callback
                $callback = function($eventData) use ($singletonClassname, $methodName) {
                    require('observers/' . strtolower($singletonClassname) . '.php');
                    return call_user_func(array($singletonClassname, $methodName), $eventData);
                };
            } else { // simple method callback
                $callback = function($eventData) use ($methodName) {
                    require('observers/' . strtolower($methodName) . '.php');
                    return call_user_func($methodName, $eventData);
                };
            }
            $evManager->registerEvent($eventName);
            $evManager->subscribeObserver($eventName, $callback);
        }
    }

/*** TODO: out of task scope but should be implemented in ideal world
    public function addObserverToDb($dbh, $singletonName, $methodName) {
    }

    public function removeObserverFromDb($dbh, $singletonName, $methodName) {
    }

***/
};

?>


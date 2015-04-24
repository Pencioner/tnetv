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
                $callback = function($data) use ($singletonClassname, $methodName) {
                    if (!isset(static::$observers["S" . $singletonClassname . $methodName])) {
                        set_include_path(get_include_path() . PATH_SEPARATOR . './test'); // FIXME TODO workaround for tests, do it another way
                        require('observers/' . strtolower($singletonClassname) . '.php');
                        static::$observers["S" . $singletonClassname . $methodName] = true;
                    }
                    $instance = $singletonClassname::getInstance();
                    return call_user_func(array($instance, $methodName), $data);
                };
            } else { // simple method callback
                $callback = function($data) use ($methodName) {
                    if (!isset(static::$observers["M" . $methodName])) {
                        set_include_path(get_include_path() . PATH_SEPARATOR . './test'); // FIXME TODO workaround for tests, do it another way
                        require('observers/' . strtolower($methodName) . '.php');
                        static::$observers["M" . $methodName] = true;
                    }
                    return call_user_func($methodName, $data);
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

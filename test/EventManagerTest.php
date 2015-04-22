<?php

require './server_root/event_manager.php';

class EventManagerTest extends PHPUnit_Framework_TestCase {
    protected static $reflection;

    protected function getReflection() {
        if (!isset(static::$reflection)) {
            static::$reflection = new ReflectionClass("EventManager");
        }
        return static::$reflection;
    }

    protected function getProperty($propName) {
        $property = $this->getReflection()->getProperty($propName);
        $property->setAccessible(true);
        return $property;
    }

    protected function reflectionGetMethod($methodName) {
        $method = $this->getReflection()->getMethod($methodName);
        $method->setAccessible(true);
        return $method;
    }

    protected function reflectionCallMethod($methodName, $instance, $args) {
        return $this->reflectionGetMethod($methodName)->invokeArgs($instance, $args);
    }

    public function __call($methodName, $args) {
        if (0 === strpos($methodName, "call")) {
            $methodName = strtolower(substr($methodName, 4));
            $instance = array_shift($args);
            return $this->reflectionCallMethod($methodName, $instance, $args);
        } else {
            trigger_error("unknown method " . $methodName, E_USER_ERROR);
        }
    }


    public function testIsSingleton() {
        $this->assertSame(EventManager::getInstance(), EventManager::getInstance());
    }

    public function testHaveEmptyEventsListAtStart() {
        $this->assertEmpty(PHPUnit_Framework_Assert::readAttribute(EventManager::getInstance(), "events"));
    }

    public function testRegisterEvent() {
        EventManager::getInstance()->registerEvent("test");
        $this->assertTrue(EventManager::getInstance()->isEventRegistered("test"));
        $observers = $this->callGetEventObservers(EventManager::getInstance(), "test");
        $this->assertNotNull($observers);
        $this->assertEmpty($observers);
    }

    public function testUnregisterEvent() {
        EventManager::getInstance()->registerEvent("test");
        EventManager::getInstance()->unregisterEvent("test");
        $this->assertFalse(EventManager::getInstance()->isEventRegistered("test"));
        $observers = $this->callGetEventObservers(EventManager::getInstance(), "test");
        $this->assertNull($observers);
    }

    public function testSubscribeObserver() {
    }

    public function testUnsubscribeObserver() {
    }

    public function testTriggerEvent() {
    }
}

?>


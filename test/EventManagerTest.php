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
        $this->assertNotNull(PHPUnit_Framework_Assert::readAttribute(EventManager::getInstance(), "events"));
        $this->assertEmpty(PHPUnit_Framework_Assert::readAttribute(EventManager::getInstance(), "events"));
    }

    public function testRegisterEvent() {
        EventManager::getInstance()->registerEvent("Test");
        $this->assertTrue(EventManager::getInstance()->isEventRegistered("Test"));
        $observers = $this->callGetEventObservers(EventManager::getInstance(), "Test");
        $this->assertNotNull($observers);
        $this->assertEmpty($observers);
    }

    public function testUnregisterEvent() {
        EventManager::getInstance()->registerEvent("Test");
        EventManager::getInstance()->unregisterEvent("Test");
        $this->assertFalse(EventManager::getInstance()->isEventRegistered("Test"));
        $observers = $this->callGetEventObservers(EventManager::getInstance(), "Test");
        $this->assertNull($observers);
    }

    protected static $someValue;
    protected static $someStrlen;
    protected static $observerIdxVal;
    protected static $observerIdxLen;
    public function testSubscribeObserver() {
        EventManager::getInstance()->registerEvent("Test");
        static::$observerIdxVal = EventManager::getInstance()->subscribeObserver(
            "Test",
            function($val) { static::$someValue = $val; return $val; }
        );
        $observers = $this->callGetEventObservers(EventManager::getInstance(), "Test");
        $this->assertNotEmpty($observers);
        $this->assertTrue(1 == count($observers));
        $this->assertInstanceOf('Closure', $observers[static::$observerIdxVal]);
        // also tests __call() magic
        static::$observerIdxLen = EventManager::getInstance()->onTest(
            function($val) { static::$someStrlen = strlen($val); return $val; }
        );
        // reread observers as our testclass __call() code doesn't cope with reference passing
        $observers = $this->callGetEventObservers(EventManager::getInstance(), "Test");
        $this->assertTrue(2 == count($observers));
        $this->assertInstanceOf('Closure', $observers[static::$observerIdxLen]);
    }

    public function testTriggerEvent() {
        EventManager::getInstance()->triggerEvent("Test", "hahaha");
        $this->assertTrue(static::$someValue == "hahaha");
        $this->assertTrue(static::$someStrlen == strlen("hahaha"));

        EventManager::getInstance()->triggerEvent("Test", "huh");
        $this->assertTrue(static::$someValue == "huh");
        $this->assertTrue(static::$someStrlen == strlen("huh"));
    }

    public function testUnsubscribeObserver() {
        EventManager::getInstance()->triggerEvent("Test", "unsub");
        $this->assertTrue(static::$someValue == "unsub");
        EventManager::getInstance()->unsubscribeObserver("Test", static::$observerIdxVal);

        // also tests __call() magic
        EventManager::getInstance()->eventTest("done");
        $this->assertTrue(static::$someValue == "unsub");
        $this->assertTrue(static::$someStrlen == strlen("done"));
        EventManager::getInstance()->unsubscribeObserver("Test", static::$observerIdxLen);
        EventManager::getInstance()->eventTest("OK");
        $this->assertFalse(static::$someValue == "OK");
        $this->assertFalse(static::$someStrlen == strlen("OK"));
    }

    // TODO: more specific test of __call() maybe...
}

?>


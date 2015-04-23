<?php

require './server_root/observers_manager.php';

class ResultMock {
    public function __construct() {
        $this->fetched = 0;
        $this->data = array(
            array('TestSingletonObserver', 'action', 'ObserverTest'),
            array(NULL, 'testMethodObserver', 'ObserverTest'),
            array(NULL, 'testSecondEventObserver', 'ObserverTest2')
        );
    }

    public function fetch_row() {
        if ($this->fetched < count($this->data)) {
            ++$this->fetched;
            return $this->data[-1 + $this->fetched];
        }
        return NULL;
    }
}

class ObserverManagerTest extends PHPUnit_Framework_TestCase {

     public function testMock() {
         $mysqli = $this->getMock('mysqli');
     }

    public function __construct() {
        $this->mockConn = $this->getMockBuilder('mysqli')
                               ->setMethods(array('prepare'))
                               ->disableOriginalConstructor()->getMock();
        $this->mockQuery = $this->getMockBuilder('mysqli_stmt')
                                ->setMethods(array('execute', 'get_result'))
                                ->disableOriginalConstructor()->getMock();
        $this->mockResult = new ResultMock;
    }

    public function testInit() {
        $this->mockConn->expects($this->once())
                       ->method('prepare')
                       ->will($this->returnValue($this->mockQuery));

        $this->mockQuery->expects($this->once())
                        ->method('execute');

        $this->mockQuery->expects($this->once())
                        ->method('get_result')
                        ->will($this->returnValue($this->mockResult));

        ObserverManager::getInstance($this->mockConn);
        $this->assertTrue(EventManager::getInstance()->isEventRegistered('ObserverTest'));
        $this->assertTrue(EventManager::getInstance()->isEventRegistered('ObserverTest2'));
    }

    public function testObserversAreWorking() {
        $this->assertSame(42, EventManager::getInstance()->triggerEvent("ObserverTest2", 42));
        $this->assertSame(EventManager::getInstance()->eventObserverTest("somestring"), "sOMESTRING");
    }

    public function testIsSingleton() {
        $this->assertSame(ObserverManager::getInstance(), ObserverManager::getInstance());
    }
};

?>


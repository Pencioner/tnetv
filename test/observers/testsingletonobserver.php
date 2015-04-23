<?php
    require_once 'server_root/singleton_trait.php';

    class TestSingletonObserver {
        use Singleton;

        public function action($data) {
            return strtoupper($data);
        }
    }
?>

<?php
trait Singleton
{
    protected static $instance;
    final public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static;
            $init_args = func_get_args();
            call_user_func_array(array(static::$instance, '_init'), $init_args);
        }

        return static::$instance;
    }

    final private function __construct() {}
    final private function __wakeup() {}
    final private function __clone() {}

    protected function _init() {}
};
?>


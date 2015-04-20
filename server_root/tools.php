<?php
    function filterReqVars($reqType, $varsPrefix) {
        global ${"_$reqType"};
        $reqVars = ${"_$reqType"}; # $reqType = "GET"  ==>  $reqVars = $_GET  etc...
        foreach ($reqVars as $name => $value) {
            if (0 == strpos($name, $varsPrefix)) {
                global $$name;
                $$name = $value;
            }
        }
    }

    function filterGetVars($varsPrefix) {
        filterReqVars("GET", $varsPrefix);
    }
    function filterPostVars($varsPrefix) {
        filterReqVars("POST", $varsPrefix);
    }
?>


<?php

function smilieReplace($text) {
    // FIXME: prototyping, move this replacements set to database table or external config file
    $replacements = array(
        ':)' => '<img src="/img/funny.gif" alt="funny" />',
        ':(' => '<img src="/img/sad.gif" alt="sad"  />'
    );

    return strtr($text, $replacements);
}

?>


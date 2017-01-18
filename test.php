<?php

$text = "/var/lib/asterisk/sounds/files/tm30/Incorrect Prompt";
$string = preg_replace('/\s+/', '_', $text);
var_dump($string);

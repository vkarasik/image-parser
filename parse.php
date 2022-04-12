<?php

// Run via CLI `php parse.php <resoursename> <category>`
// <resoursename> is required e.g: defender for parser_defender.php
// <category> is optional e.g: https://defender.ru/catalog/2-0-speaker-systems

if (isset($_SERVER['argv'][1])) {

    $resource = 'parser_' . $_SERVER['argv'][1] . '.php';
    $parsers = scandir('parsers');

    if (in_array($resource, $parsers)) {
        include 'parsers' . '/' . $resource;
    } else {
        echo 'Error! Please, check resource argument!';
    }
} else {
    echo 'Error! Please, enter valid resource argument!';
}

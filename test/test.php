<?php
require_once __DIR__ . '/../vendor/autoload.php';


$s = "2019-02-22T15:37:11.823517Z";
$d = new \DateTime($s, new \DateTimeZone('UTC'));
var_dump($s);
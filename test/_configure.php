<?php

$appKeyFilename = __DIR__ . '/asset/app_key.json';
$devKeyFilename = __DIR__ . '/asset/device_key.json';

if (file_exists($appKeyFilename)) {
    define('testAppKeyJsonString', file_get_contents($appKeyFilename));
} else {
    throw new \Exception('Missing app_key.json');
}

if (file_exists($devKeyFilename)) {
    define('testDeviceJsonString', file_get_contents($devKeyFilename));
} else {
    throw new \Error('Missing device_key.json');
}


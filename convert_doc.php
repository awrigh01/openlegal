<?php
require __DIR__ . '/vendor/autoload.php';
use \CloudConvert\Api;
$api = new Api("klT25-PsgkIZBCFb-Gu7qAlY4WIDteIU7l9_1aoXwwyZvH9BYCj0cRqlY0PtPIIVMP-wP1ZMxqTpEKDYyPik9g");

$process = $api->createProcess([
    'inputformat' => 'docx',
    'outputformat' => 'txt',
]);

$api->convert([
        'inputformat' => 'docx',
        'outputformat' => 'txt',
        'input' => 'upload',
        'file' => fopen('test.docx', 'r'),
    ])
    ->wait()
    ->download('test.txt');

echo "Conversion was started in background :-)";

?>
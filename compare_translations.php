<?php

$en = include 'resources/lang/en/msg.php';
$ar = include 'resources/lang/ar/msg.php';
$fr = include 'resources/lang/fr/msg.php';
$ru = include 'resources/lang/ru/msg.php';

$languages = ['ar' => $ar, 'fr' => $fr, 'ru' => $ru];

foreach ($languages as $lang => $trans) {
    $missing = array_diff_key($en, $trans);
    echo "Missing in $lang:\n";
    foreach ($missing as $key => $value) {
        echo "  '$key' => '$value',\n";
    }
    echo "\n";
}

<?php

require_once './Controllers/ApurationController.php';


$t1 = time();

echo "\n";
echo "\e[1;37;42m ################ Started in " . date('d/m/Y H:i:s', $t1) . "\e[0m";
echo "\n\n";

$participants = $argv[1] ?? 4;
$limitApurations = $argv[2] ?? 100;

$ap = new ApurationController($participants, true);

$idApuration = $ap->runApuration($participants, $limitApurations);
$ap->showResultsByIdApuration($idApuration);

$t2 = time();

echo "\n";
echo "\e[1;37;42m ################ Finished in " . date('d/m/Y H:i:s', $t2) . ' ' . ($t2 - $t1) . ' seconds' . "\e[0m";
echo "\n";
echo "\n";

<?php

require_once './Controllers/ExecutionController.php';


$t1 = time();

echo "\n";
echo "\e[1;37;42m ################ Started in " . date('d/m/Y H:i:s', $t1) . "\e[0m";
echo "\n\n";

$participants = $argv[1] ?? 4;
$limitApurations = $argv[2] ?? 100;

$exec = new ExecutionController($participants, $limitApurations);

$idExecution = $exec->runApurations();
$exec->showResultsByIdExecution($idExecution);

$t2 = time();

echo "\n";
echo "\e[1;37;42m ################ Finished in " . date('d/m/Y H:i:s', $t2) . ' ' . gmdate("H:i:s", ($t2 - $t1))  . " duration \e[0m";
echo "\n";
echo "\n";

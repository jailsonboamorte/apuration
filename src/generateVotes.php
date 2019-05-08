<?php

require_once './Models/Vote.php';


$t1 = time();

echo "\n";
echo "\e[1;37;46m ################ Started in " . date('d/m/Y H:i:s', $t1) . "\e[0m";
echo "\n\n";

$length = $argv[1] ?? 4;

$vote = new Vote($length, 'votes');

$vote->generateAndSaveVotes();

$t2 = time();

echo "\n";
echo "\e[1;37;46m ################ Finished in " . date('d/m/Y H:i:s', $t2) . ' ' . ($t2 - $t1) . " seconds. \e[0m";
echo "\n";
echo "\n";

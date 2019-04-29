<?php

require_once './Vote.php';


$t1 = time();

echo "\n";
echo "################ Started in " . date('d/m/Y H:i:s', $t1);
echo "\n\n";

$length = $argv[1] ?? 4;

$vote = new Vote($length, 'votes');

$vote->generateAndSaveVotes();

$t2 = time();

echo "\n";
echo "################ Finished in " . date('d/m/Y H:i:s', $t2) . ' ' . ($t2 - $t1) . ' seconds';
echo "\n";
echo "\n";

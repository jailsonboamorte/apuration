<?php

require_once './Controllers/CombinationController.php';


$t1 = time();

echo "\n";
echo "\e[1;37;42m ################ Started in " . date('d/m/Y H:i:s', $t1) . "\e[0m";
echo "\n\n";

$length = $argv[1] ?? 4;

$comb = new CombinationController($length);

$qtd = $comb->generateAndSaveCombinations($length);

$t2 = time();

echo "\n";
echo "\e[1;37;42m ################ Finished in " . date('d/m/Y H:i:s', $t2) . ' ' . ($t2 - $t1) . ' seconds' . "\e[0m";
echo "\n";
echo "qtd : " . $qtd . "\n";
echo "\n";

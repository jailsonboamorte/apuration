<?php

require_once './Apuration.php';


$t1 = time();

echo "\n";
echo "################ Started in " . date('d/m/Y H:i:s', $t1);
echo "\n\n";

$e = $argv[1] ?? 4;
$b = $argv[2] ?? 3;

$ap = new Apuration($e);

$t2 = time();

$combinations = explode('*', $ap->enumarate($e, $b));
echo "\n";
echo "################ End Gereation " . date('d/m/Y H:i:s', $t2) . ' ' . ($t2 - $t1) . ' seconds';
echo "\n";
echo "\n";
echo "qtd : " . count($combinations) . "\n";

//$result = $ap->storageCombinations($combinations);
//print_r($result);

$t3 = time();
echo "\n";
echo "\n";
echo "################ Finished in " . date('d/m/Y H:i:s', $t3) . ' - ' . ($t3 - $t1) . ' seconds';
echo "\n";
echo "\n";

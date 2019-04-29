<?php

require_once './Apuration.php';

$ap = new Apuration();

$t1 = time();

echo "\n";
echo "################ Started in :" . date('d/m/Y H:i:s', $t1);
echo "\n\n";

$e = $argv[1] ?? 4;
$b = $argv[2] ?? 3;


$t2 = time();
echo "\n";
echo "################ End Gereation:" . date('d/m/Y H:i:s', $t2) . ' ' . ($t2 - $t1) . ' seconds';
echo "\n";

$combinations = explode('*', $ap->enumarate($e, $b));
//echo "\n\n" . count($combinations);
//print_r($combinations);

$ap->storageCombinations($combinations);

$t3 = time();
echo "\n";
echo "\n";
echo "################ Finished in:" . date('d/m/Y H:i:s', $t3) . ' - ' . ($t3 - $t1) . ' seconds';
echo "\n";
echo "\n";

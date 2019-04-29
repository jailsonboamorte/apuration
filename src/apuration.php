<?php

require_once './Apuration.php';


$t1 = time();

echo "\n";
echo "################ Started in " . date('d/m/Y H:i:s', $t1);
echo "\n\n";

$length = $argv[1] ?? 4;
$votes = $argv[2] ?? 100;

$ap = new Apuration($length, true);

$apurations = $ap->getApuration($length, $votes);
//print_r($apurations);

foreach ($apurations as $k => $apuration) {

  $proportion = $apuration['proportion'];
  $combination = $apuration['combination'];

  echo "[$k]\n";
//  echo implode('-', array_keys($apuration['votes'])) . "\n";
//  echo implode('-', $apuration['votes']) . "\n\n";
  echo "----------Proportion: \n";
  echo "A: {$proportion['A']['sum']} - {$proportion['A']['percentage']}% / B: {$proportion['B']['sum']} - {$proportion['B']['percentage']}% Total: {$proportion['total']}\n ";
  echo "\n---------Combination: \n";
  echo "A: {$combination['A']['sum']} - {$combination['A']['percentage']}% / B: {$combination['B']['sum']} - {$combination['B']['percentage']}% Total: {$combination['total']} \n";
  echo "\n\n===================================================================\n\n";
}
$t2 = time();

echo "\n";
echo "################ Finished in " . date('d/m/Y H:i:s', $t2) . ' ' . ($t2 - $t1) . ' seconds';
echo "\n";
echo "\n";

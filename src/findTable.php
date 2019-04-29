<?php

require 'vendor/autoload.php';
include_once './mongodbConnection.php';


$t1 = time();

$v = $argv[1] ?? 2;

$lines = $mongodbConn->truetable->lines;
echo "\n\n";
echo "################ Started in :" . date('d/m/Y H:i:s', $t1);
echo "\n\n";
$possibilities = pow(2, $v);
echo "Possibilities: " . $possibilities;
echo "\n";

$fields = [];
for ($i = 0; $i < $v; $i++) {
  $fields[$i] = 1;
}
$count = $lines->count(['_id' => ['$lte' => $possibilities], 'sum' => 3], ['projection' => $fields]);
echo "Count: " . $count;
echo "\n";
$tableLines = $lines->find(['_id' => ['$lte' => $possibilities], 'sum' => 3], ['projection' => $fields]);
foreach ($tableLines as $lines) {
  $_id = $lines['_id'];
  unset($lines['_id']);
//  print_r($lines);
//  echo "\n";
//  echo implode('-', array_reverse((array) $lines));
//  echo "\n";
}

$t2 = time();
echo "\n";
echo "################ Finished in:" . date('d/m/Y H:i:s', $t2);
echo "\n";
echo "################ Duraction: " . ($t2 - $t1) . ' seconds';
echo "\n";
?>
<?php

$t1 = time();

echo "\n\n";
echo "################ Started in :" . date('d/m/Y H:i:s', $t1);
echo "\n\n";

$votesCount = 4;
$possibilities = pow(2, $votesCount);

for ($i = 0; $i < $possibilities; $i++) {
  $binare = str_pad(decbin($i), $votesCount, 0, STR_PAD_LEFT);
  $arrayBinare = str_split($binare);
  $posVote = str_pad($i + 1, 5, 0, STR_PAD_LEFT);
  $c = "[" . $posVote . ']  ' . implode('-', $arrayBinare);
  if (array_sum($arrayBinare) == 3) {
    echo "\e[0;30;47m$c\e[0m\n";
    continue;
  }
  echo "$c \n";
}

$t2 = time();
echo "\n";
echo "################ Finished in:" . date('d/m/Y H:i:s', $t2);
echo "\n";
echo "################ Duraction: " . ($t2 - $t1) . ' seconds';
echo "\n";

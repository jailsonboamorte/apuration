<?php

//include_once './mongodbConnection.php';

$t1 = time();
$options = ['A', 'B'];
for ($i = 1; $i < 100; $i++) {
  $dataVotes[] = $options[rand(0, 1)];
}

$v = $argv[1];

echo "\n\n";
echo "################ Started in :" . date('d/m/Y H:i:s', $t1);
echo "\n\n";
$votes = array_slice($dataVotes, 0, $v ?? 3);
print_r($votes);
//unset($votes[5]);

$votesCount = count($votes);
$possibilities = pow(2, $votesCount);

//$votes[4] = 'A';
$proportion = array_count_values(array_slice($votes, 0, $votesCount));
$proportion['A'] = $proportion['A'] ?? 0;
$proportion['B'] = $proportion['B'] ?? 0;
//print_r($proportion);

echo "\n";
echo "\n";
echo implode(' , ', $votes);
echo "\n";

echo "\n";
echo "Numbers of vote: " . $votesCount . ' possibilities: ' . $possibilities . "\n\n";
echo "\n";



$votesMap = ['A' => 0, 'B' => 0];
$combinations = [];
for ($i = 0; $i < $possibilities; $i++) {
  $binare = str_pad(decbin($i), $votesCount, 0, STR_PAD_LEFT);
  $arrayBinare = str_split($binare);
  $posVote = str_pad($i + 1, 5, 0, STR_PAD_LEFT);
  $c = '[' . $posVote . ']' . implode('-', $arrayBinare);
  if (array_sum($arrayBinare) == 3) {
//    echo "\e[0;30;47m$c\e[0m\n";
    $combinations[] = $c;
    continue;
  }
////  echo "$c \n";
//  continue;
//
////  echo "\n";
////  print_r($arrayBinare);
////  print_r(array_filter($arrayBinare));
//  $arrayBinareFilter = array_filter($arrayBinare);
////  echo "\n";
////  echo implode(' , ', array_keys(array_filter($arrayBinare)));
////  echo "\n";
////  echo "\n";
//
//  $votesGroup = array_intersect_key($votes, $arrayBinareFilter);
//  $countVotes = array_count_values($votesGroup);
////  echo "\n";
////  echo "\n";
////  print_r($countVotes);
//  $maxs = array_keys($countVotes, max($countVotes));
////  echo "\n";
////  print_r($maxs);
//  echo "\n"
//  . "\n [" . $posVote . ']  ' . $binare . ' ' . implode('-', $arrayBinare)
//  . ' -> ' . implode(' , ', $votesGroup)
//  . " - Winner: " . $maxs[0];
//  $votesMap[$maxs[0]] += 1;

  if ($i > 1073741824) {
    break;
  }
}
print_r($combinations);

//echo "\n";
//echo "\n";
//echo "Result by proportion: \n";
//$sumPro = array_sum($proportion);
//$proportion['A'] .= ' -> ' . round($proportion['A'] / $sumPro * 100, 2) . '%';
//$proportion['B'] .= ' -> ' . round($proportion['B'] / $sumPro * 100, 2) . '%';
//print_r($proportion);
//
//echo "\n";
//echo "----------------------------##################---------------------------";
//echo "\n\n";
//echo "Result by Map Combination: \n";
//$sumM = array_sum($votesMap);
//$votesMap['A'] .= ' -> ' . round($votesMap['A'] / $sumM * 100, 2) . '%';
//$votesMap['B'] .= ' -> ' . round($votesMap['B'] / $sumM * 100, 2) . '%';
//print_r($votesMap);
$t2 = time();
echo "\n";
echo "################ Finished in:" . date('d/m/Y H:i:s', $t2);
echo "\n";
echo "################ Duraction: " . ($t2 - $t1) . ' seconds';
echo "\n";
?>
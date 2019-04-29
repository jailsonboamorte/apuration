<?php

$t1 = time();
$length = $argv[1];
$string = str_pad(111, $length, 0, STR_PAD_LEFT);

echo "\n\n";
echo "################ Started in :" . date('d/m/Y H:i:s', $t1);
echo "\n\n";

function numbersAreCloses(array $arrayPos): array
{

  $difP3P2 = $arrayPos['pos3'] - $arrayPos['pos2'];
  $difP2P1 = $arrayPos['pos2'] - $arrayPos['pos1'];
  $return['areCloses32'] = ($difP3P2 == 1 ? true : false);
  $return['areCloses31'] = ($difP3P2 == 1 && $difP2P1 == 1) ? true : false;
  return $return;
}

function completeNumber(string $number, int $length, int $strPad, $direction = STR_PAD_LEFT): string
{
  return str_pad($number, $length, $strPad, $direction);
}

function getPositions(string $string): array
{
  $arrayFisrt = str_split($string);
//  print_r($arrayFisrt);
  $arrayKeysPos = array_keys(array_filter($arrayFisrt));
  $arrayPos = array_combine(['pos1', 'pos2', 'pos3'], $arrayKeysPos);
//  print_r($arrayPos);
  return $arrayPos;
}

//$string = '001101';
$combinations[] = $string;

$arrayPos = getPositions($string);
$arrayClose = numbersAreCloses($arrayPos);
$pos1 = $arrayPos['pos1'];
$pos2 = $arrayPos['pos2'];
$pos3 = $arrayPos['pos3'];
$condExit = $pos1 != 0 || $pos2 != 1 || $pos3 != 2;

while ($condExit) {

  $arrayPos = getPositions($string);
  $arrayClose = numbersAreCloses($arrayPos);
  $pos1 = $arrayPos['pos1'];
  $pos2 = $arrayPos['pos2'];
  $pos3 = $arrayPos['pos3'];
//  echo "###### pos1: $pos1 pos2: $pos2 pos3: $pos3 string: $string \n";

  $part1 = completeNumber(1, $pos1 + 1, 0);
  if ($arrayClose['areCloses31']) {
    $pos1--;
    $pos2 = $length - 2; # before last position
    $pos3 = $length - 1; # last position
    $part1 = completeNumber(1, ($pos1 + 1), 0);
    $part2 = completeNumber(1, ($pos2 + 1) - ($pos1 + 1), 0);
    $part3 = completeNumber(1, ($length - $pos3), 0, STR_PAD_RIGHT);
    $string = $part1 . $part2 . $part3;
//    $string = $part3;
//    echo "### 31 pos1: $pos1 pos2: $pos2 pos3: $pos3 string: $string \n";
  } elseif ($arrayClose['areCloses32']) {
    $pos2--;
    $pos3 = $length - 1; # set last position
    $part1 = completeNumber(1, $pos1 + 1, 0);
    $part2 = completeNumber(1, ($pos2 + 1) - ($pos1 + 1), 0);
    $part3 = completeNumber(1, ($pos3 + 1) - ($pos2 + 1), 0);
    $string = $part1 . $part2 . $part3;
//    echo "### 32 pos1: $pos1 pos2: $pos2 pos3: $pos3 string: $string \n";
  } else {
    $pos3--;
    $part1 = completeNumber(1, $pos1 + 1, 0);
    $part2 = completeNumber(1, ($pos2 + 1) - ($pos1 + 1), 0);
    $part3 = completeNumber(1, ($length - $pos3), 0, STR_PAD_RIGHT);
    $lenCom = strlen($part1 . $part2 . $part3);
    $dif = $length - strlen($part1 . $part2 . $part3);
    $complement = $dif <= 0 ? '' : completeNumber(0, $dif, 0);
    $string = $part1 . $part2 . $complement . $part3;
//    echo "### RE pos1: $pos1 pos2: $pos2 pos3: $pos3 string: $string \n";
  }
  $combinations[] = $string;

  $condExit = $pos1 != 0 || $pos2 != 1 || $pos3 != 2;
}

echo 'qtd: ' . count($combinations) . "\n\n";

$t2 = time();
echo "\n";
echo "################ Finished in:" . date('d/m/Y H:i:s', $t2);
echo "\n";
echo "################ Duraction: " . ($t2 - $t1) . ' seconds';
echo "\n";
?>
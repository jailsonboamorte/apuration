<?php

$t1 = time();

echo "\n\n";
echo "################ Started in :" . date('d/m/Y H:i:s', $t1);
echo "\n\n";

$e = $argv[1] ?? 4;
$b = $argv[2] ?? 3;

function appendValueForEachElement($value, $array)
{

  foreach ($array as $k => $arr) {
    $return[] = $value;
    if (is_array($arr)) {

      $it = new RecursiveIteratorIterator(new RecursiveArrayIterator($arr));
      foreach ($it as $v) {
        $return[] = $v;
      }
    } else {
      $return[] = $arr;
    }
  }
  return $return;
}

function appendValueforEachString($value, $string)
{
  $array = explode('*', $string);
  foreach ($array as $arr) {
    $return[] = $value . $arr;
  }

  return implode('*', $return ?? []);
}

function unionArray($array1, $array2)
{
//  print_r($array);
  $return[] = array_values($array1);
  $return[] = array_values($array2);

//  print_r($return);
  return $return;
}

function enumarate($len, $rem)
{
  if ($rem == 0) {
    echo "###########################: " . $len . ' - ' . $rem . "\n";
    $r = array_fill(0, $len, 0);
    print_r($r);
    return $r;
  }
  if ($len < $rem) {
    echo "###########################: " . $len . ' - ' . $rem . "\n";
    $r = [];
    return $r;
  }
  if ($len == $rem) {
    echo "###########################: " . $len . ' - ' . $rem . "\n";
    $r = array_fill(0, $rem, 1);
    print_r($r) . "\n";
    return $r;
  }
  $le = appendValueForEachElement(0, enumarate($len - 1, $rem));
//  print_r($le);
//  $ri = appendArray(1, enumarate($len - 1, $rem - 1));
  $ri = appendValueForEachElement(1, enumarate($len - 1, $rem - 1));
//  print_r($ri);
//  $r = unionArray($le, $ri);
  echo "###########################: " . $len . ' - ' . $rem . "\n";
  $r = unionArray($le, $ri);
  print_r($r);
  return $r;
}

$combinations = enumarate($e, $b);
//echo "\n\n" . count($combinations);
print_r($combinations);

$t2 = time();
echo "\n";
echo "################ Finished in:" . date('d/m/Y H:i:s', $t2);
echo "\n";
echo "################ Duraction: " . ($t2 - $t1) . ' seconds';
echo "\n";

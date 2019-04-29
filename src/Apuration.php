<?php

require 'vendor/autoload.php';
require_once './Mongo.php';
require_once './Vote.php';
require_once './Combination.php';

class Apuration
{

  private $length;
  private $combination;

  /**
   * 
   * @param int $length
   * @param bool $combination 
   */
  public function __construct(int $length, bool $combination = false)
  {
    $this->length = $length;
    if ($combination) {
      $combination = new Combination($length);
      $options = ['projection' => ['_id' => 0, '0' => 1, '1' => 1, '2' => 1]];
      $filter = [];
      $this->combination = $combination->getCombinations($filter, $options);
    }
  }

  /**
   * 
   * @param int $value
   * @param string $string
   * @return string
   */
  private function appendValueforEachString(int $value, string $string): string
  {
    $array = explode('*', $string);
    foreach ($array as $combination) {
      $append = $value . $combination;
      if (strlen($append) == $this->length) {
//        echo implode('-', str_split($append)) . "\n";
      }
      $return[] = $append;
    }
    return implode('*', $return ?? []);
  }

  /**
   * 
   * @param int $len
   * @param int $rem
   * @return string
   */
  function enumarate(int $len, int $rem): string
  {
    if ($rem == 0) {
      $r = str_pad('', $len, 0);
      return $r;
    }
    if ($len < $rem) {
      $r = '';
      return $r;
    }
    if ($len == $rem) {
      $r = str_pad('', $rem, 1);
      return $r;
    }
    $le = $this->appendValueforEachString(0, $this->enumarate($len - 1, $rem));
    $ri = $this->appendValueforEachString(1, $this->enumarate($len - 1, $rem - 1));
    $r = $le . '*' . $ri;
    return $r;
  }

  /**
   * 
   * @param array $combinations
   * @return array
   */
  public function storageCombinations(array $combinations): array
  {
    $return = ['updated' => 0, 'inserted' => 0, 'total' => 0, 'fail' => 0];
    foreach ($combinations as $value) {
      $res = $this->saveCombination($value);
      isset($res['updated']) ? $return['updated'] += $res['updated'] : null;
      isset($res['inserted']) ? $return['inserted'] += $res['inserted'] : null;
      isset($res['fail']) ? $return['fail'][] = $res['fail'] : null;
      $return['total'] += 1;
    }
    return $return;
  }

  private function saveCombination(string $combination): array
  {
    $collection = 'combinations_for_' . $this->length . '_elements';
    $return = [];
    $mongo = new Mongo();
    $arrayBinare = array_filter(str_split($combination));
    $_id = $combination;
    $dec = bindec($combination);
    $hasLine = $mongo->findOne($collection, ['_id' => $_id], ['_id']);
    $data = [
      '_id' => $_id,
      'decimal' => $dec,
      'sum' => array_sum($arrayBinare),
      ] + array_keys($arrayBinare);
    if ($hasLine) { # there is on base
      $updateResult = $mongo->updateOne($collection, ['_id' => $_id], ['$set' => $data]);
      $updateResult->getMatchedCount() == 1 ? $return['updated'] = 1 : $return['fail'] = $_id;
    } else { # new register
      $insertOneResult = $mongo->saveOne($collection, $data);
      $insertOneResult->getInsertedCount() == 1 ? $return['inserted'] = 1 : $return['fail'] = $_id;
    }
    return $return;
  }

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
    $arrayKeysPos = array_keys(array_filter($arrayFisrt));
    $arrayPos = array_combine(['pos1', 'pos2', 'pos3'], $arrayKeysPos);
    return $arrayPos;
  }

  public function generateAndSaveCombinations(int $length): int
  {
    $string = str_pad(111, $length, 0, STR_PAD_LEFT);
    $this->saveCombination($string);

    $arrayPos = $this->getPositions($string);
    $arrayClose = $this->numbersAreCloses($arrayPos);
    $pos1 = $arrayPos['pos1'];
    $pos2 = $arrayPos['pos2'];
    $pos3 = $arrayPos['pos3'];
    $condExit = $pos1 != 0 || $pos2 != 1 || $pos3 != 2;

    $i = 1;
    while ($condExit) {

      $arrayPos = $this->getPositions($string);
      $arrayClose = $this->numbersAreCloses($arrayPos);
      $pos1 = $arrayPos['pos1'];
      $pos2 = $arrayPos['pos2'];
      $pos3 = $arrayPos['pos3'];
//  echo "###### pos1: $pos1 pos2: $pos2 pos3: $pos3 string: $string \n";

      $part1 = $this->completeNumber(1, $pos1 + 1, 0);
      if ($arrayClose['areCloses31']) {
        $pos1--;
        $pos2 = $length - 2; # before last position
        $pos3 = $length - 1; # last position
        $part1 = $this->completeNumber(1, ($pos1 + 1), 0);
        $part2 = $this->completeNumber(1, ($pos2 + 1) - ($pos1 + 1), 0);
        $part3 = $this->completeNumber(1, ($length - $pos3), 0, STR_PAD_RIGHT);
        $string = $part1 . $part2 . $part3;
//    $string = $part3;
//    echo "### 31 pos1: $pos1 pos2: $pos2 pos3: $pos3 string: $string \n";
      } elseif ($arrayClose['areCloses32']) {
        $pos2--;
        $pos3 = $length - 1; # set last position
        $part1 = $this->completeNumber(1, $pos1 + 1, 0);
        $part2 = $this->completeNumber(1, ($pos2 + 1) - ($pos1 + 1), 0);
        $part3 = $this->completeNumber(1, ($pos3 + 1) - ($pos2 + 1), 0);
        $string = $part1 . $part2 . $part3;
//    echo "### 32 pos1: $pos1 pos2: $pos2 pos3: $pos3 string: $string \n";
      } else {
        $pos3--;
        $part1 = $this->completeNumber(1, $pos1 + 1, 0);
        $part2 = $this->completeNumber(1, ($pos2 + 1) - ($pos1 + 1), 0);
        $part3 = $this->completeNumber(1, ($length - $pos3), 0, STR_PAD_RIGHT);
        $dif = $length - strlen($part1 . $part2 . $part3);
        $complement = $dif <= 0 ? '' : $this->completeNumber(0, $dif, 0);
        $string = $part1 . $part2 . $complement . $part3;
//    echo "### RE pos1: $pos1 pos2: $pos2 pos3: $pos3 string: $string \n";
      }
      $this->saveCombination($string);
      $i++;
      $condExit = $pos1 != 0 || $pos2 != 1 || $pos3 != 2;
    }
    return $i;
  }

  /**
   * 
   * @param int $length
   * @param int $max
   */
  public function getApuration(int $length, int $max = 3): array
  {
    $apuration = [];

    $vote = new Vote($length);
    $options = ['limit' => $max, 'projection' => ['_id' => 0, 'proportion_votes' => 1, 'votes' => 1]];
    $filter = [];
    $cursor = $vote->getVotes($filter, $options);
    $i = 0;
    foreach ($cursor as $register) {
      $votes = (array) $register['votes'];
      $apuration[$i]['votes'] = $votes;
      $apuration[$i]['proportion']['A'] = ['sum' => $register['proportion_votes']['A'], 'percentage' => round($register['proportion_votes']['A'] / $length * 100, 2)];
      $apuration[$i]['proportion']['B'] = ['sum' => $register['proportion_votes']['B'], 'percentage' => round($register['proportion_votes']['B'] / $length * 100, 2)];
      $apuration[$i]['proportion']['total'] = $length;


      $apurationCombination = $this->processApurationForCombination($votes);
      $apuration[$i]['combination']['A'] = ['sum' => $apurationCombination['A'], 'percentage' => round($apurationCombination['A'] / $apurationCombination['sum_combinations'] * 100, 2)];
      $apuration[$i]['combination']['B'] = ['sum' => $apurationCombination['B'], 'percentage' => round($apurationCombination['B'] / $apurationCombination['sum_combinations'] * 100, 2)];
      $apuration[$i]['combination']['total'] = $apurationCombination['sum_combinations'];

      $i++;
    }
    return $apuration;
  }

  private function processApurationForCombination(array $votes): array
  {

    $apuration = ['A' => 0, 'B' => 0];
    foreach ($this->combination as $combination) {
      $apurationCombi = [$votes[$combination[0]], $votes[$combination[1]], $votes[$combination[2]]];
      $optionWinner = key((array_count_values($apurationCombi)));
      $apuration[$optionWinner] ++;
    }
    $apuration['sum_combinations'] = $apuration['A'] + $apuration['B'];
    return $apuration;
  }

}

?>
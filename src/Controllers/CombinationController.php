<?php

require 'vendor/autoload.php';
require_once './Models/Combination.php';


class CombinationController
{

  private $participants;
  private $combination;

  /**
   * 
   * @param int $participants
   * @param bool $combination 
   */
  public function __construct(int $participants)
  {

    $this->participants = $participants;
    $this->combination = new Combination($this->participants);
  }

  private function saveCombination(string $combination): array
  {
    $collection = 'combinations_for_' . $this->participants . '_participants';
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
      ] + array_keys($arrayBinare) + array_combine(['position_0', 'position_1', 'position_2'], array_keys($arrayBinare));
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

  function completeNumber(string $number, int $participants, int $strPad, $direction = STR_PAD_LEFT): string
  {
    return str_pad($number, $participants, $strPad, $direction);
  }

  function getPositions(string $string): array
  {
    $arrayFisrt = str_split($string);
    $arrayKeysPos = array_keys(array_filter($arrayFisrt));
    $arrayPos = array_combine(['pos1', 'pos2', 'pos3'], $arrayKeysPos);
    return $arrayPos;
  }

  public function generateAndSaveCombinations(int $participants): int
  {
    $string = str_pad(111, $participants, 0, STR_PAD_LEFT);
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
        $pos2 = $participants - 2; # before last position
        $pos3 = $participants - 1; # last position
        $part1 = $this->completeNumber(1, ($pos1 + 1), 0);
        $part2 = $this->completeNumber(1, ($pos2 + 1) - ($pos1 + 1), 0);
        $part3 = $this->completeNumber(1, ($participants - $pos3), 0, STR_PAD_RIGHT);
        $string = $part1 . $part2 . $part3;
//    $string = $part3;
//    echo "### 31 pos1: $pos1 pos2: $pos2 pos3: $pos3 string: $string \n";
      } elseif ($arrayClose['areCloses32']) {
        $pos2--;
        $pos3 = $participants - 1; # set last position
        $part1 = $this->completeNumber(1, $pos1 + 1, 0);
        $part2 = $this->completeNumber(1, ($pos2 + 1) - ($pos1 + 1), 0);
        $part3 = $this->completeNumber(1, ($pos3 + 1) - ($pos2 + 1), 0);
        $string = $part1 . $part2 . $part3;
//    echo "### 32 pos1: $pos1 pos2: $pos2 pos3: $pos3 string: $string \n";
      } else {
        $pos3--;
        $part1 = $this->completeNumber(1, $pos1 + 1, 0);
        $part2 = $this->completeNumber(1, ($pos2 + 1) - ($pos1 + 1), 0);
        $part3 = $this->completeNumber(1, ($participants - $pos3), 0, STR_PAD_RIGHT);
        $dif = $participants - strlen($part1 . $part2 . $part3);
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

}

?>
<?php

require 'vendor/autoload.php';
//require_once './Mongo.php';
require_once './Models/Vote.php';
require_once './Models/Combination.php';
require_once './Models/Apuration.php';
require_once './Models/Result.php';
require_once './Models/MapVotesCombination.php';

class ApurationController
{

  private $participants;
  private $combination;
  private $MapVotesCombination;

  /**
   * 
   * @param int $participants
   * @param bool $combination 
   */
  public function __construct(int $participants, bool $combination = false)
  {

    $this->participants = $participants;
    $this->MapVotesCombination = new MapVotesCombination($participants);

    if ($combination) {
      $combination = new Combination($participants);
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
      if (strlen($append) == $this->participants) {
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

  /**
   * 
   * @param int $participants
   * @param int $max
   */
  public function runApuration(int $participants, int $max = 3)
  {
    $vote = new Vote($participants);
    $apuration = new Apuration($participants, $max);
    $result = new Result();
    $combination = new Combination($participants);
    $dataApuration = ['number_participants' => $participants, 'limit_of_apuration' => $max];
    $idApuration = $apuration->add($dataApuration);
//    echo $idApuration;
    $options = ['limit' => $max, 'projection' => ['_id' => 1, 'proportion_votes' => 1, 'votes' => 1]];
    $filter = [];
    $cursor = $vote->get($filter, $options);
//    print_r($cursor);
    $tableVote = $vote->getCollection();
    $tableCombination = $combination->getCollection();
    $tableMapVotesCombinations = $this->MapVotesCombination->getCollection();
    foreach ($cursor as $register) {
      $command = "generateMapVotesCombinations('$tableVote','{$register['_id']}', '$idApuration','$tableCombination','$tableMapVotesCombinations');";
      $result = $this->MapVotesCombination->execute($command);
//      print_r($result->toArray()[0]['ok']);
      if ($result->toArray()[0]['ok']) {
//      echo $reduce . "\n";
        $resultReduce = $this->MapVotesCombination->mapReduce($tableMapVotesCombinations, "function () {emit(this.winner, 1);}", "function (key, values) {return Array.sum(values)}", "reduce_$tableMapVotesCombinations");
        //print_r($resultReduce);
      }
      //echo $command . "\n";
//      $votes = (array) $register['votes'];
//      $this->generateMapVotessCombination($votes, $idApuration);
//      $resultCombination = $this->processApurationForCombination($votes);
//        $dataResult = [
//          'apuration_id' => $idApuration,
//          'votes' => $votes,
//          'proportion' => [
//            'A' => ['sum' => $register['proportion_votes']['A'], 'percentage' => round($register['proportion_votes']['A'] / $participants * 100, 2)],
//            'B' => ['sum' => $register['proportion_votes']['B'], 'percentage' => round($register['proportion_votes']['B'] / $participants * 100, 2)],
//            'total' => $participants
//          ],
//          'combination' => [
//            'A' => ['sum' => $resultCombination['A'], 'percentage' => round($resultCombination['A'] / $resultCombination['sum_combinations'] * 100, 2)],
//            'B' => ['sum' => $resultCombination['B'], 'percentage' => round($resultCombination['B'] / $resultCombination['sum_combinations'] * 100, 2)],
//            'total' => $resultCombination['sum_combinations']
//          ]
//        ];
//      $result->add($dataResult);
    }
    return $idApuration;
  }

  function showResultsByIdApuration($idApuration)
  {
    $result = new Result();
    $results = $result->get(['apuration_id' => $idApuration]);
    foreach ($results as $k => $result) {

      $proportion = $result['proportion'];
      $combination = $result['combination'];

//  echo implode('-', array_keys((array) $result['votes'])) . "\n";
//  echo implode('-', (array) $result['votes']) . "\n\n";

      $winnerP = ($proportion['A']['percentage'] > $proportion['B']['percentage']) ? 'A' : 'B';
      $winnerP = ($proportion['B']['percentage'] == $proportion['A']['percentage']) ? 'ND' : $winnerP;

      $winnerC = ($combination['A']['percentage'] > $combination['B']['percentage']) ? 'A' : 'B';
      $winnerC = ($combination['B']['percentage'] == $combination['A']['percentage']) ? 'ND' : $winnerC;

      $count = '[' . ($k + 1) . ']';
      $l = "Proportion Total: {$proportion['total']}" . str_pad(' ', 60, ' ', STR_PAD_BOTH) . "Combination Total: {$combination['total']}";
      $lenght = 120;

      $color1 = $color2 = "";
      if ($winnerC != $winnerP) {
        $color1 = "\e[1;37;46m";
        $color2 = "\e[0m";
      }

      echo $count . str_pad($l, $lenght, ' ', STR_PAD_BOTH) . "\n";
      $l = "A: {$proportion['A']['percentage']}% - {$proportion['A']['sum']}   / B: {$proportion['B']['percentage']}% - {$proportion['B']['sum']} [$winnerP]"
        . str_pad(' ', 40, ' ', STR_PAD_BOTH)
        . "[$winnerC] A: {$combination['A']['percentage']}% - {$combination['A']['sum']} / B: {$combination['B']['percentage']}% - {$combination['B']['sum']}";
      echo $color1 . str_pad($l, $lenght, ' ', STR_PAD_BOTH) . $color2 . "\n";

      echo "\n" . str_pad('-', $lenght, '-', STR_PAD_BOTH) . "\n";
    }
  }

//  private function processApurationForCombination(array $votes): array
//  {
//
//    $apuration = ['A' => 0, 'B' => 0];
//    foreach ($this->combination as $combination) {
//      $apurationCombi = [$votes[$combination[0]], $votes[$combination[1]], $votes[$combination[2]]];
//      $optionWinner = key((array_count_values($apurationCombi)));
//      $apuration[$optionWinner] ++;
//    }
//    $apuration['sum_combinations'] = $apuration['A'] + $apuration['B'];
//    return $apuration;
//  }
//  private function generateMapVotessCombination(array $votes, $idApuration)
//  {
//
//    $dataMap['apuration_id'] = $idApuration;
//    foreach ($this->combination as $combination) {
//
//      $dataMap['apuration_id'] = $idApuration;
//      $dataMap['votes'] = [$votes[$combination[0]], $votes[$combination[1]], $votes[$combination[2]]];
//      $dataMap['sum_votes'] = array_count_values($dataMap['votes']);
//      $dataMap['qty_votes_winner'] = max($dataMap['sum_votes']);
//      $dataMap['winner'] = array_flip($dataMap['sum_votes'])[$dataMap['qty_votes_winner']];
////      print_r($dataMap);
//      $this->MapVotesCombination->add($dataMap);
//    }
////    $apuration['sum_combinations'] = $apuration['A'] + $apuration['B'];
//  }
}

?>
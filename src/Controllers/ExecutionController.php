<?php

require 'vendor/autoload.php';
require_once './Models/Vote.php';
require_once './Models/Combination.php';
require_once './Models/Apuration.php';
require_once './Models/Result.php';
require_once './Models/MapVotesCombination.php';
require_once './Models/Execution.php';

class ExecutionController
{

  private $participants;
  private $vote;
  private $apuration;
  private $combination;
  private $mapVotesCombination;
  private $limitApurations;

  /**
   * 
   * @param int $participants
   * @param bool $combination 
   */
  public function __construct(int $participants, int $limitApurations)
  {

    $this->participants = $participants;
    $this->limitApurations = $limitApurations;
    $this->mapVotesCombination = new MapVotesCombination($participants);
    $this->vote = new Vote($this->participants);
    $this->apuration = new Apuration($this->participants, $limitApurations);
    $this->combination = new Combination($this->participants);
  }

  /**
   * 
   * @param int $participants number of votes 
   * @param int $max number of votes group
   */
  public function runApurations()
  {
    $t1 = time();
    $execution = new Execution($this->participants, $this->limitApurations);
    $dataExecution = ['number_participants' => $this->participants, 'limit_of_apuration' => $this->limitApurations];
    $idExecution = (string) $execution->add($dataExecution);

    $this->processApurations($idExecution, $this->limitApurations);

    $t2 = time();
    $execution->edit(['_id' => $idExecution], ['duration' => ($t2 - $t1)]);
    return $idExecution;
  }

  private function processApurations(string $idExecution)
  {


    for ($i = 0; $i <= $this->limitApurations; $i++) {

      $idVotes = $this->vote->generateAndSaveVotes();
      $optionsVotes = ['projection' => ['_id' => 1, 'proportion_votes' => 1, 'votes' => 1]];
      $filter = ['_id' => $idVotes];
      $cursorVotes = $this->vote->get($filter, $optionsVotes);
      $register = $cursorVotes->toArray();
//      print_r($register[0]);
      $tableVote = $this->vote->getCollection();
      $tableCombination = $this->combination->getCollection();
      $tableMapVotesCombinations = $this->mapVotesCombination->getCollection();


      $t1 = time();

      $dataApuration = ['execution_id' => $idExecution, 'number_participants' => $this->participants, 'votes' => $register[0]['votes']];
      $idApuration = $this->apuration->add($dataApuration);

      $command = "generateMapVotesCombinations('$tableVote','{$register[0]['_id']}', '$idApuration','$tableCombination','$tableMapVotesCombinations');";
      $resultMap = $this->mapVotesCombination->execute($command);

      if ($resultMap->toArray()[0]['ok']) {

        $map = "function () {emit(this.winner, 1);}";
        $reduce = "function (key, values) {return Array.sum(values)}";
        $optionsReduce = ['query' => ['apuration_id' => (string) $idApuration]];
        $getReduce = $this->mapVotesCombination->mapReduce($tableMapVotesCombinations, $map, $reduce, ['inline' => 1], $optionsReduce);

        foreach ($getReduce as $option) {
          $resultReduce[$option['_id']] = $option['value'];
        }

        $resultReduce['A'] = $resultReduce['A'] ?? 0;
        $resultReduce['B'] = $resultReduce['B'] ?? 0;

        $register['proportion_votes']['A'] = $register['proportion_votes']['A'] ?? 0;
        $register['proportion_votes']['B'] = $register['proportion_votes']['B'] ?? 0;

        $t2 = time();
        $dataApuration = [
          'proportion' => [
            'A' => ['sum' => $register['proportion_votes']['A'], 'percentage' => round($register['proportion_votes']['A'] / $this->participants * 100, 2)],
            'B' => ['sum' => $register['proportion_votes']['B'], 'percentage' => round($register['proportion_votes']['B'] / $this->participants * 100, 2)],
            'total' => $this->participants
          ],
          'combination' => [
            'A' => ['sum' => $resultReduce['A'], 'percentage' => round($resultReduce['A'] / ($resultReduce['A'] + $resultReduce['B']) * 100, 2)],
            'B' => ['sum' => $resultReduce['B'], 'percentage' => round($resultReduce['B'] / ($resultReduce['A'] + $resultReduce['B']) * 100, 2)],
            'total' => $resultReduce['A'] + $resultReduce['B']
          ],
          'duration' => ($t2 - $t1)
        ];
      }

      $this->apuration->edit(['_id' => $idApuration], $dataApuration);
    }
  }

  function showResultsByIdExecution($idExecution)
  {
    $results = $this->apuration->get(['execution_id' => $idExecution], []);
    foreach ($results as $k => $result) {

      $proportion = $result['proportion'];
      $combination = $result['combination'];

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

}

?>
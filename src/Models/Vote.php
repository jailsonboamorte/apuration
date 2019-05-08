<?php

require 'vendor/autoload.php';
require_once 'Mongo.php';

class Vote
{

  private $lenght;
  private $options;
  private $colletion;

  /**
   * 
   * @param int $lenght
   */
  public function __construct(int $lenght)
  {
    $this->lenght = $lenght;
    $this->colletion = $lenght . '_votes';
    $this->options = ['A' => 'A', 'B' => 'B'];
  }

  /**
   * 
   * @param array $votes
   * @return bool
   */
  private function saveVotes(array $votes): bool
  {
    $mongo = new Mongo();

    $data = ['created' => date('Y-m-d H:i:s'), 'proportion_votes' => array_count_values($votes), 'votes' => $votes];
    foreach ($votes as $key => $value) {
      $data[$key] = $key;
      $data['vote_' . $key] = $value;
    }
    
    $insertOneResult = $mongo->saveOne($this->colletion, $data);
    return $insertOneResult->getInsertedCount() == 1 ? true : false;
  }

  /**
   * 
   */
  public function generateAndSaveVotes()
  {
    $votes = [];
    for ($i = 0; $i < $this->lenght; $i++) {
      $votes[] = array_rand($this->options);
    }
    $this->saveVotes($votes);
  }

  public function getVotes($filter, $options)
  {
    $mongo = new Mongo();
    return $mongo->find($this->colletion, $filter, $options);
  }

}

?>
<?php

require 'vendor/autoload.php';
require_once 'Mongo.php';
require_once 'Model.php';

class Vote extends Model
{

  private $lenght;
  private $options;
  public $colletion;

  /**
   * 
   * @param int $lenght
   */
  public function __construct(int $lenght)
  {
    $this->lenght = $lenght;
    $this->colletion = $lenght . '_votes';
    $this->options = ['A' => 'A', 'B' => 'B'];
    parent::__construct($this->colletion);
  }

  /**
   * 
   * @param array $votes
   * @return bool
   */
  private function saveVotes(array $votes): bool
  {

    $data = ['created' => date('Y-m-d H:i:s'), 'proportion_votes' => array_count_values($votes), 'votes' => $votes];
    foreach ($votes as $key => $value) {
      $data[$key] = $key;
      $data['vote_' . $key] = $value;
    }
    
    return $this->save($data);
    
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

  public function get($filter, $options)
  {
    return $this->find($filter, $options);
  }

}

?>
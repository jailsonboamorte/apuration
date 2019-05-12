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
   */
  public function generateAndSaveVotes()
  {
    for ($i = 0; $i < $this->lenght; $i++) {
      $data[] = array_rand($this->options);
    }
    $data = ['proportion_votes' => array_count_values($data), 'votes' => $data];
    return $this->save($data);
  }

  public function get($filter, $options)
  {
    return $this->find($filter, $options);
  }

}

?>
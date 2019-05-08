<?php

require 'vendor/autoload.php';
require_once 'Model.php';

class MapVotesCombination extends Model
{

  /**
   * 
   * @param int $lenght
   */
  public function __construct(int $lenght)
  {
    $colletion = 'map_votes_combinations_' . $lenght . '_participants';
    parent::__construct($colletion);
  }

  /**
   * 
   * @param array $data
   * @return type
   */
  function add(array $data)
  {
    return $this->save($data);
  }

  public function get($filter, $options)
  {
    return $this->find($filter, $options);
  }

}

?>
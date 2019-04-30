<?php

require 'vendor/autoload.php';
require_once 'Model.php';

class Result extends Model
{

  /**
   * 
   * @param int $participants
   * @param int $limitApuration
   */
  public function __construct()
  {
    $colletion = 'results';
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

  public function get($filter, $options=[])
  {
    return $this->find($filter, $options);
  }

}

?>
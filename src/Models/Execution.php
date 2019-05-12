<?php

require 'vendor/autoload.php';
require_once 'Model.php';

class Execution extends Model
{

  /**
   * 
   * @param int $participants
   * @param int $limitApuration
   */
  public function __construct(int $participants, int $limitApuration)
  {
    $colletion = 'execution_' . $participants . 'x' . $limitApuration;
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

  function edit(array $filter, array $data)
  {
    return $this->updatOne($filter, $data);
  }

  public function get($filter, $options)
  {
    return $this->find($filter, $options);
  }

}

?>
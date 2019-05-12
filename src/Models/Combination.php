<?php

require 'vendor/autoload.php';
require_once 'Model.php';

class Combination extends Model
{

  /**
   * 
   * @param int $lenght
   */
  public function __construct(int $lenght)
  {
    $colletion = 'combinations_for_' . $lenght . '_participants';
    parent::__construct($colletion);
  }

  public function getCombinations($filter, $options)
  {
    return iterator_to_array($this->find($filter, $options));
  }

  function add(array $data)
  {
    return $this->save($data);
  }

}

?>
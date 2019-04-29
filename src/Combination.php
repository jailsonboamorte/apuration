<?php

require 'vendor/autoload.php';
require_once './Mongo.php';

class Combination
{

  private $lenght;
  private $colletion;

  /**
   * 
   * @param int $lenght
   */
  public function __construct(int $lenght)
  {
    $this->lenght = $lenght;
    $this->colletion = 'combinations_for_' . $lenght . '_elements';
  }

  public function getCombinations($filter, $options)
  {
    $mongo = new Mongo();
    return iterator_to_array($mongo->find($this->colletion, $filter, $options));
  }

}

?>
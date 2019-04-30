<?php

require 'vendor/autoload.php';
require_once 'Mongo.php';

class Model
{

  public $colletion;

  /**
   * 
   * @param string $colletion
   */
  public function __construct(string $colletion)
  {
    $this->colletion = $colletion;
  }

  function save(array $data)
  {
    $mongo = new Mongo();

    $data['created'] = date('Y-m-d H:i:s');
    $insertOneResult = $mongo->saveOne($this->colletion, $data);
    return $insertOneResult->getInsertedCount() == 1 ? $insertOneResult->getInsertedId() : false;
  }

  public function find($filter, $options)
  {
    $mongo = new Mongo();
    return $mongo->find($this->colletion, $filter, $options);
  }

}

?>
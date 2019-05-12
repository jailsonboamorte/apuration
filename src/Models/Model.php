<?php

require 'vendor/autoload.php';
require_once 'Mongo.php';

class Model
{

  public $colletion;
  public $mongo;

  /**
   * 
   * @param string $colletion
   */
  public function __construct(string $colletion)
  {
    $this->colletion = $colletion;
    $this->mongo = new Mongo();
  }

  public function getCollection()
  {
    return $this->colletion;
  }

  function save(array $data)
  {

    $data['created'] = date('Y-m-d H:i:s');
    $insertOneResult = $this->mongo->saveOne($this->colletion, $data);
    return $insertOneResult->getInsertedCount() == 1 ? $insertOneResult->getInsertedId() : false;
  }

  /**
   * 
   * @param array $filter
   * @param array $data
   * @return bool
   */
  function updatOne(array $filter, array $data): bool
  {
    $data['updated'] = date('Y-m-d H:i:s');
    $updateOneResult = $this->mongo->updateOne($this->colletion, $filter, ['$set' => $data]);
    return $updateOneResult->getModifiedCount() == 1 ? true : false;
  }

  public function find($filter, $options)
  {
    return $this->mongo->find($this->colletion, $filter, $options);
  }

  public function execute($command)
  {
    return $this->mongo->command($command);
  }

  public function mapReduce($collection, $map, $reduce, $out, $options)
  {
    return $this->mongo->mapReduce($collection, $map, $reduce, $out, $options);
  }

}

?>
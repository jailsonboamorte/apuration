<?php

require 'vendor/autoload.php';

class Mongo
{

  private $connection;

  public function __construct()
  {
    $this->connection = new MongoDB\Client("mongodb://mongodb:27017/truetable");
  }

  public function getConnection()
  {
    return $this->connection;
  }

  public function saveOne(string $colletion, array $data)
  {
    $colletionMg = $this->getConnection()->truetable->$colletion;
    return $colletionMg->insertOne($data);
  }

  public function updateOne(string $colletion, array $filter, array $data)
  {
    $colletionMg = $this->getConnection()->truetable->$colletion;
    return $colletionMg->updateOne($filter, $data);
  }

  public function findOne(string $colletion, array $filter, array $data)
  {
    $colletionMg = $this->connection->truetable->$colletion;
    return $colletionMg->findOne($filter, $data);
  }

  public function find(string $colletion, array $filter = [], array $data = [])
  {
    $colletionMg = $this->connection->truetable->$colletion;
    return $colletionMg->find($filter, $data);
  }

  public function command($command)
  {
    return $this->connection->truetable->command(['eval' => $command]);
  }

  public function mapReduce($collection, $map, $reduce, $out, $options)
  {
    $map = new MongoDB\BSON\Javascript($map);
    $reduce = new MongoDB\BSON\Javascript($reduce);
    return $this->connection->truetable->$collection->mapReduce($map, $reduce, $out, $options);
  }

}

?>
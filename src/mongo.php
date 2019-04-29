<?php

include_once './mongodbConnection.php';

$bulk = new MongoDB\Driver\BulkWrite();
$bulk->insert(['name' => 'John Doe']);

$writeConcern = new MongoDB\Driver\writeConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
$result = $mongodbConn->executeBulkWrite('test.mycollection', $bulk);

var_dump($result);

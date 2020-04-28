<?php

namespace Classes\DataBase;

use mysqli;
use Exception;
use Classes\Util;
/* -------------------------------------------------------------------------- */
/*                      Class for the DataBase connection                     */
/* -------------------------------------------------------------------------- */

class DataBase
{


  private $host = __HOST;
  private $username = __USERNAME;
  private $password = __PASSWORD;
  private $dbName = __DB_NAME;


  protected function connect()
  {
    $db = new mysqli($this->host, $this->username, $this->password, $this->dbName);
    if ($db->connect_errno) {
      echo "Fallo al conectar a MySQL: (" . $db->connect_errno . ") " . $db->connect_error;
    }
    $db->set_charset("utf8");

    return $db;
  }

  // update tables 
  protected function updateTable($table, $pk, $wherePk, $propsArray)
  {
    $query = "UPDATE {$table} SET ";

    $count = 0;
    $paramsArray = [];
    // Remove primary key from the array to update (pk is not supose to update)
    unset($propsArray[$pk]);
    foreach ($propsArray as $key => $value) {
      $paramsArray[] = $value;
      $coma = ($count > 0 ? ',' : '');
      $query .= "$coma $key = ?";
      $count++;
    }
    $query .= " WHERE {$pk} = '" . $wherePk . "'";
    $db = $this->connect();
    $stmt = $db->prepare($query);
    $bind =  str_repeat('s', count($paramsArray));
    // php 5 version
    $refs = array();
    foreach ($paramsArray as $key => $value) {
      $refs[$key] = &$paramsArray[$key];
    }
    call_user_func_array(array($stmt, "bind_param"), array_merge([$bind], $refs));
    // php 7 version
    // $stmt->bind_param($bind, ...$paramsArray);
    $stmt->execute();
  }

  // Insert row into tables 
  protected function insertTable($table, $propsArray)
  {
    $query = "INSERT INTO {$table}";

    $count = 0;
    $valuesArray = [];
    $columns = '';
    $values = '';

    foreach ($propsArray as $key => $value) {
      $valuesArray[] = $value;
      $coma = ($count > 0 ? ',' : '');
      $columns .= "$coma $key";
      $values .= "$coma ?";
      $count++;
    }

    $query .= "({$columns}) VALUES ($values)";

    $this->insertQuery([$query], $valuesArray);
  }

  protected function insertQuery($query, $valuesArray, $insertId = false)
  {
    $db = $this->connect();
    // multiple inserts
    if ($this->isMultiArray($valuesArray)) {

      foreach ($valuesArray as $key => $array) {        
        $stmt = $db->prepare($query[$key]);
        $bind =  str_repeat('s', count($array));
        // php 5 version
        $refs = [];
        foreach ($array as $key => $value) {
          $refs[$key] = &$array[$key];
        }
        call_user_func_array(array($stmt, "bind_param"), array_merge([$bind], $refs));
        // // php 7 version
        // $stmt->bind_param($bind, ...$array);
        $stmt->execute();
      }
    } else {
      $stmt = $db->prepare($query[0]);
      $bind =  str_repeat('s', count($valuesArray));
      // php 5 version
      $refs = [];
      foreach ($valuesArray as $key => $value) {
        $refs[$key] = &$valuesArray[$key];
      }
      call_user_func_array(array($stmt, "bind_param"), array_merge([$bind], $refs));
      // php 7 version
      // $stmt->bind_param($bind, ...$valuesArray);
      if ($stmt->execute()) {
        echo 'funciono';
        if ($insertId === true) {
          return $stmt->insert_id;
        }
        return true;
      } else {
        throw new Exception($stmt->error);   
      }
    }
  }

  // select just one row
  protected function selectOne($query, $whereArray = [])
  {

    $result = $this->selectFromDB($query, $whereArray);
    if ($result->num_rows > 0) {

      $obj = $result->fetch_assoc();
      return (object) $obj;
    } else {
      return false;
    }
  }
  // select multiple rows
  protected function selectAll($query, $whereArray = [])
  {
    $result = $this->selectFromDB($query, $whereArray);
    if ($result->num_rows > 0) {
      $obj = $result->fetch_all(MYSQLI_ASSOC);
      return Util::toObject($obj);
    }
    return false;
  }
  // global select
  private function selectFromDB($query, $whereArray)
  {
    $db = $this->connect();
    $stmt = $db->prepare($query);

    if (count($whereArray) > 0) {

      $bind = str_repeat('s', count($whereArray));
      // php 5 version
      $refs = array();
      foreach ($whereArray as $key => $value) {
        $refs[$key] = &$whereArray[$key];
      }
      // var_dump($refs); 
      call_user_func_array(array($stmt, "bind_param"), array_merge([$bind], $refs));
      // php 7 version
      // $stmt->bind_param($bind, ...$whereArray);

    }
    if(!$stmt->execute()){
      throw new Exception($stmt->error);   
    }
    
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
  }
  // check if the array given is a assosiative array
  protected function isMultiArray($array)
  {
    $rv = array_filter($array, 'is_array');
    if (count($rv) > 0) return true;
    return false;
  }
}

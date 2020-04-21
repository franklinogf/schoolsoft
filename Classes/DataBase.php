<?php
namespace Classes;

use mysqli;
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
    foreach($paramsArray as $key => $value) {
            $refs[$key] = &$paramsArray[$key];
    }         
    call_user_func_array(array($stmt, "bind_param"), array_merge([$bind],$refs));
    // php 7 version
    // $stmt->bind_param($bind, ...$paramsArray);
    $stmt->execute();
  }

  protected function select($query,$whereArray = []){   
   
    $result = $this->selectFromDB($query,$whereArray);
    if($result->num_rows > 0){    
     
      $obj = $result->fetch_assoc();     
      return (object) $obj;   
    }else{
      return false;
    }    

  }
  protected function selectAll($query,$whereArray = []){

    $result = $this->selectFromDB($query,$whereArray);
    if($result->num_rows > 0){
    $obj = $result->fetch_all(MYSQLI_ASSOC);
    return Util::toObject($obj);
    }
    return false;
   

  }

  private function selectFromDB($query,$whereArray){
    $db = $this->connect();
    $stmt = $db->prepare($query);
    if(count($whereArray) > 0){
      $bind = str_repeat('s',count($whereArray));
      // php 5 version
      $refs = array();
      foreach($whereArray as $key => $value) {
              $refs[$key] = &$whereArray[$key];
      }   
    call_user_func_array(array($stmt, "bind_param"), array_merge([$bind],$refs));
    // php 7 version
    // $stmt->bind_param($bind, ...$whereArray);
     
    }
    $stmt->execute();
    $result = $stmt->get_result();   
    $stmt->close();
    return $result;
  }
}

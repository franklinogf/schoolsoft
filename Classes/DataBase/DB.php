<?php

namespace Classes\DataBase;


/* -------------------------------------------------------------------------- */
/*                      Class for the DataBase queries                        */
/* -------------------------------------------------------------------------- */

class DB extends DataBase
{
  private static $instance = null;
  private static $table = '';
  private static $columns = '*';
  private static $query = '';
  private static $orderBy = null;
  private static $findby = null;
  private static $whereCols = [];
  private static $whereValues = [];
  private static $whereOperators = [];
  private static $innerJoinTable = [];
  private static $innerJoinCol1 = [];
  private static $innerJoinCol2 = [];
  private static $innerJoinOperator = [];


  public static function table($table)
  {
    if (self::$instance === null) {
      self::$instance = new self;
    }
    self::$table = trim($table);

    return self::$instance;
  }

  public function select($columns)
  {
    self::$columns = trim($columns);

    return self::$instance;
  }

  public function orderBy($col, $mode = 'DESC')
  {

    self::$orderBy = ' ORDER BY ' . trim($col) . ' ' . trim($mode);
    return self::$instance;
  }

  public function find($value, $col = 'id')
  {

    self::$findby = ' WHERE ' . trim($col) . ' = ?';
    self::$query = 'SELECT ' . self::$columns . ' FROM ' . self::$table . self::$findby;
    $obj = $this->selectOne(self::$query, [trim($value)]);
    return $obj;
  }


  public function where($w1, $w2 = false, $w3 = false)
  {
    if ($w2) {
      self::$whereCols[] = trim($w1);
      self::$whereValues[] = ($w3 ? trim($w3) : trim($w2));
      self::$whereOperators[] = ($w3 ? trim($w2) : '=');
    } else if (!$w2) {
      if ($this->isMultiArray($w1)) {
        foreach ($w1 as $w) {
          self::$whereCols[] = trim($w[0]);
          self::$whereValues[] = (isset($w[2]) ? trim($w[2]) : trim($w[1]));
          self::$whereOperators[] = (isset($w[2]) ? trim($w[1]) : '=');
        }
      } else {
        self::$whereCols[] = trim($w1[0]);
        self::$whereValues[] = (isset($w1[2]) ? trim($w1[2]) : trim($w1[1]));
        self::$whereOperators[] = (isset($w1[2]) ? trim($w1[1]) : '=');
      }
    }
    return self::$instance;
  }

  public function join($tableToJoin, $table1Col, $operator, $table2Col)
  {
    self::$innerJoinTable[] = $tableToJoin;
    self::$innerJoinCol1[] = $table1Col;
    self::$innerJoinCol2[] = $table2Col;
    self::$innerJoinOperator[] = $operator;
    return self::$instance;
  }

  public function get()
  {
    $this->buildSelectQuery();
    $obj = $this->selectAll(self::$query, self::$whereValues);   
    $this->closeDB();
    return $obj;
  }

  public function first()
  {
    $this->buildSelectQuery('limit 1');
    $obj = $this->selectOne(self::$query, self::$whereValues);
    $this->closeDB();
    return $obj;
  }

  private function buildSelectQuery($other = '')
  {
    $other = trim($other);

    $where = '';    
    $count = 0;
    if (count(self::$whereCols) > 0) {
      foreach (self::$whereCols  as $i => $col) {
        $where .= ($count > 0 ? ' AND' : ' WHERE');
        $where .= ' ' . $col . ' ' . self::$whereOperators[$i] . ' ?';
        $count++;
      }
    }

    $join = '';    
    if(count(self::$innerJoinTable) > 0){
      foreach (self::$innerJoinTable  as $i => $table) {       
        $join .= ' INNER JOIN ' . $table . ' ON ' . self::$innerJoinCol1[$i] .' '. self::$innerJoinOperator[$i] . ' ' . self::$innerJoinCol2[$i];        
      }
    }

    echo self::$query = 'SELECT ' . self::$columns . ' FROM ' . self::$table . $join . $where . self::$orderBy . ' ' . $other;
  
  }

  private function isMultiArray($array)
  {
    $rv = array_filter($array, 'is_array');
    if (count($rv) > 0) return true;
    return false;
  }

  private function closeDB(){
    self::$instance = null;
    self::$table = '';
    self::$columns = '*';
    self::$query = '';
    self::$orderBy = null;
    self::$findby = null;
    self::$whereCols = [];
    self::$whereValues = [];
    self::$whereOperators = [];
    self::$innerJoinTable = [];
    self::$innerJoinCol1 = [];
    self::$innerJoinCol2 = [];
    self::$innerJoinOperator = [];

  }
}

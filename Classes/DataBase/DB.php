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
  private static $where = [];
  private static $whereCols = [];
  private static $whereValues = [];
  private static $whereOperators = [];
  private static $orWhereCols = [];
  private static $orWhereValues = [];
  private static $orWhereOperators = [];
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

  public function orderBy($col, $mode = null)
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

  public function orWhere($w1, $w2 = false, $w3 = false)
  {
    if ($w2) {
      self::$orWhereCols[] = trim($w1);
      self::$orWhereValues[] = ($w3 ? trim($w3) : trim($w2));
      self::$orWhereOperators[] = ($w3 ? trim($w2) : '=');
    } else if (!$w2) {
      if ($this->isMultiArray($w1)) {
        foreach ($w1 as $w) {
          self::$orWhereCols[] = trim($w[0]);
          self::$orWhereValues[] = (isset($w[2]) ? trim($w[2]) : trim($w[1]));
          self::$orWhereOperators[] = (isset($w[2]) ? trim($w[1]) : '=');
        }
      } else {
        self::$orWhereCols[] = trim($w1[0]);
        self::$orWhereValues[] = (isset($w1[2]) ? trim($w1[2]) : trim($w1[1]));
        self::$orWhereOperators[] = (isset($w1[2]) ? trim($w1[1]) : '=');
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
    $obj = $this->selectAll(self::$query, self::$where);
    $this->closeDB();
    return $obj;
  }

  public function first()
  {
    $this->buildSelectQuery('limit 1');
    $obj = $this->selectOne(self::$query, self::$where);
    $this->closeDB();
    return $obj;
  }

  public function insert($insertArray,$getId = false)
  {
    $valuesArray = [];
    $query = [];
    if ($this->isMultiArray($insertArray)) {
      foreach ($insertArray as $array) {
        $count = 0;
        $valuesArray[] = $array;
        $values = '';
        $columns = '';
        foreach ($array as $key => $value) {
          $coma = ($count > 0 ? ',' : '');
          $columns .= "{$coma}{$key}";
          $values .= "{$coma}?";
          $count++;
        }
        $query[] = 'INSERT INTO ' . self::$table . "($columns) VALUES ($values)";
      }
    } else {
      $count = 0;
      $columns = '';
      $values = '';
      $query = [];
      foreach ($insertArray as $key => $value) {
        $valuesArray[] = $value;
        $coma = ($count > 0 ? ',' : '');
        $columns .= "{$coma}{$key}";
        $values .= "{$coma}?";
        $count++;
      }
      $query[] = 'INSERT INTO ' . self::$table . "($columns) VALUES ($values)";
    }

    return $this->insertQuery($query,$valuesArray,$getId);
  }

  public function insertGetId($insertArray){
    return $this->insert($insertArray,true);
  }

  private function buildSelectQuery($other = '')
  {
    // for the limit or other
    $other = trim($other);

    $where = '';
    if (count(self::$whereCols) > 0) {
      foreach (self::$whereCols  as $i => $col) {
        $where .= ($i > 0 ? ' AND' : ' WHERE');
        $where .= ' ' . $col . ' ' . self::$whereOperators[$i] . ' ?';
      }
    }

    if (count(self::$orWhereCols) > 0) {
      foreach (self::$orWhereCols  as $i => $col) {
        $where .= ' OR ' . $col . ' ' . self::$orWhereOperators[$i] . ' ?';
      }
    }

    self::$where = array_merge(self::$whereValues, self::$orWhereValues);

    $join = '';
    if (count(self::$innerJoinTable) > 0) {
      foreach (self::$innerJoinTable  as $i => $table) {
        $join .= ' INNER JOIN ' . $table . ' ON ' . self::$innerJoinCol1[$i] . ' ' . self::$innerJoinOperator[$i] . ' ' . self::$innerJoinCol2[$i];
      }
    }

    self::$query = 'SELECT ' . self::$columns . ' FROM ' . self::$table . $join . $where . self::$orderBy . ' ' . $other;
  }

 

  private function closeDB()
  {
    self::$instance = null;
    self::$table = '';
    self::$columns = '*';
    self::$query = '';
    self::$orderBy = null;
    self::$findby = null;
    self::$where = [];
    self::$whereCols = [];
    self::$whereValues = [];
    self::$whereOperators = [];
    self::$orWhereCols = [];
    self::$orWhereValues = [];
    self::$orWhereOperators = [];
    self::$innerJoinTable = [];
    self::$innerJoinCol1 = [];
    self::$innerJoinCol2 = [];
    self::$innerJoinOperator = [];
  }
}

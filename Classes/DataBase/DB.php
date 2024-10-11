<?php

namespace Classes\DataBase;

use Classes\Session;
use Classes\Util;

/* -------------------------------------------------------------------------- */
/*                      Class for the DataBase queries                        */
/* -------------------------------------------------------------------------- */

class DB extends DataBase
{
  private static $instance = null;
  private static $table = '';
  private static $columns = '*';
  private static $query = '';
  private static $groupBy = null;
  private static $orderBy = null;
  private static $where = [];
  private static $whereCols = [];
  private static $whereValues = [];
  private static $whereOperators = [];
  private static $orWhereCols = [];
  private static $orWhereValues = [];
  private static $orWhereOperators = [];
  private static $whereRaw = "";
  private static $whereRawValues = [];
  private static $innerJoinTable = [];
  private static $innerJoinCol1 = [];
  private static $innerJoinCol2 = [];
  private static $innerJoinOperator = [];
  private static $cosey = true;
  public static function admin($schoolId = __SCHOOL_ACRONYM)
  {
    self::$instance ??= new self;
    parent::$admin = true;
    self::table('schools');
    self::$whereCols[] = 'id';
    self::$whereValues[] = $schoolId;
    self::$whereOperators[] = '=';


    return self::$instance;
  }
  /* -------------------- Get the next autoincrement number ------------------- */
  // table that has autoincrement
  public static function getNextAutoIncrementIdFromTable($table)
  {
    $nextId = self::table('')->selectOne("SHOW TABLE STATUS LIKE '$table'");
    return $nextId->Auto_increment;
  }
  //table that doesn't have autoincrement but has an id
  public static function getNextIdFromTable($table, $col = 'id')
  {
    $nextId = self::table($table)->select("MAX($col) as $col")->first();
    return (int) $nextId->{$col} + 1;
  }
  /* ---------------------- Method to create a new table ---------------------- */

  public function create($columns, $others = '')
  {
    $tableName = self::$table;
    $query = "CREATE TABLE IF NOT EXISTS {$tableName} ({$columns}) {$others};";
    return $this->query($query);
  }

  public function alter($query)
  {
    $tableName = self::$table;
    $q = "ALTER TABLE IF EXISTS {$tableName} {$query}";
    return $this->query($q);
  }

  /* -------------------------------- Raw Query ------------------------------- */
  public function query($query)
  {
    return $this->normalQuery($query);
  }

  /* ---------------------------- select the table ---------------------------- */

  public static function table($table, $cosey = true)
  {
    self::$instance ??= new self;
    self::$table = '`' . trim($table) . '`';
    self::$cosey = $cosey;

    return self::$instance;
  }

  /* ---------------- Select columns of the table defaults ALL ---------------- */

  public function select($columns)
  {
    self::$columns = trim($columns);

    return $this;
  }

  /* --------------------- Insert one row or multiple rows -------------------- */

  public function insert($insertArray, $getId = false)
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
          $columns .= "{$coma} `{$key}`";
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
        $columns .= "{$coma} `{$key}`";
        $values .= "{$coma}?";
        $count++;
      }
      $query[] = 'INSERT INTO ' . self::$table . "($columns) VALUES ($values)";
    }

    return $this->insertQuery($query, $valuesArray, $getId);
  }

  /* ------------------ insert a row and get the inserted id ------------------ */

  public function insertGetId($insertArray)
  {
    return $this->insert($insertArray, true);
  }

  public function update($updateArray)
  {
    $count = 0;
    $set = '';
    $valuesArray = [];
    foreach ($updateArray as $key => $value) {
      $valuesArray[] = $value;
      $coma = ($count > 0 ? ',' : '');
      $set .= "$coma `{$key}` = ?";
      $count++;
    }
    $where = $this->buildWhere();

    $query = 'UPDATE ' . self::$table . ' SET' . $set . ' ' . $where;
    $values = array_merge($valuesArray, self::$whereValues);
    $result = $this->updateQuery($query, $values);
    $this->closeDB();
    if (isset($result['error'])) {
      return true;
    }
    return false;
  }
  public function delete()
  {

    $where = $this->buildWhere();
    $query = 'DELETE FROM ' . self::$table . ' ' . $where;
    $this->updateQuery($query, self::$whereValues);
    $this->closeDB();
  }

  /* ------------------------- find by the primary key ------------------------ */

  public function find($value, $col = 'id')
  {

    $this->where($col, $value);
    $this->buildSelectQuery();
    $obj = $this->selectOne(self::$query, self::$where);
    $this->closeDB();
    return $obj;
  }

  /* ------------------------------ where clause ------------------------------ */

  public function where($w1, $w2 = false, $w3 = false)
  {
    if ($w2) {
      self::$whereCols[] = trim($w1);
      self::$whereValues[] = $w3 ? trim($w3) : trim($w2);
      self::$whereOperators[] = $w3 ? trim($w2) : '=';
    } else if (!$w2) {
      if (parent::isMultiArray($w1)) {
        foreach ($w1 as $w) {
          self::$whereCols[] = trim($w[0]);
          self::$whereValues[] = isset($w[2]) ? trim($w[2]) : trim($w[1]);
          self::$whereOperators[] = isset($w[2]) ? trim($w[1]) : '=';
        }
      } else {
        self::$whereCols[] = trim($w1[0]);
        self::$whereValues[] = isset($w1[2]) ? trim($w1[2]) : trim($w1[1]);
        self::$whereOperators[] = isset($w1[2]) ? trim($w1[1]) : '=';
      }
    }

    return $this;
  }

  /* ----------------------------- OR WHERE clause ---------------------------- */

  public function orWhere($w1, $w2 = false, $w3 = false)
  {
    if ($w2) {
      self::$orWhereCols[] = trim($w1);
      self::$orWhereValues[] = ($w3 ? trim($w3) : trim($w2));
      self::$orWhereOperators[] = ($w3 ? trim($w2) : '=');
    } else if (!$w2) {
      if (parent::isMultiArray($w1)) {
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


    return $this;
  }

  /* -------------------------------- Where Raw ------------------------------- */
  public function whereRaw($query, $values = [])
  {
    self::$whereRaw = $query;
    self::$whereRawValues = $values;
    return $this;
  }

  /* ------------------------------- Inner join ------------------------------- */

  public function join($tableToJoin, $table1Col, $operator, $table2Col)
  {
    self::$innerJoinTable[] = $tableToJoin;
    self::$innerJoinCol1[] = $table1Col;
    self::$innerJoinCol2[] = $table2Col;
    self::$innerJoinOperator[] = $operator;
    return $this;
  }

  /* ----------------------------- Order by filter ---------------------------- */

  public function orderBy($col, $mode = null)
  {

    self::$orderBy = ' ORDER BY ' . trim($col) . ' ' . trim($mode);
    return $this;
  }

  public function cosey($active = false)
  {

    self::$cosey = $active;
    return $this;
  }

  /* ----------------------------- group by filter ---------------------------- */

  public function groupBy($col)
  {

    self::$groupBy = ' GROUP BY ' . trim($col);
    return $this;
  }

  /* ----------------------------- echo the query ----------------------------- */
  public function display($exit = false)
  {
    $this->buildSelectQuery();
    echo self::$query . "<br/> ";
    var_dump(self::$where);
    echo "<hr/>";
    if ($exit)
      exit;
  }

  /* ---------------------------- get multiple rows --------------------------- */

  public function get()
  {
    $this->buildSelectQuery();
    $obj = $this->selectAll(self::$query, self::$where);
    // echo self::$query. "<br/> ";
    // var_dump(self::$where);
    // echo "<hr/>";
    $this->closeDB();
    return $obj;
  }

  /* ------------------------------- get one row ------------------------------ */

  public function first()
  {
    $this->buildSelectQuery('limit 1');

    $obj = $this->selectOne(self::$query, self::$where);
    $this->closeDB();
    return $obj;
  }

  private function buildWhere()
  {
    $where = '';
    if (count(self::$whereCols) > 0) {
      foreach (self::$whereCols as $i => $col) {
        $where .= ($i > 0 ? ' AND' : ' WHERE');
        $where .= ' ' . $col . ' ' . self::$whereOperators[$i] . ' ?';
      }
    }

    if (count(self::$orWhereCols) > 0) {
      foreach (self::$orWhereCols as $i => $col) {
        $where .= ' OR ' . $col . ' ' . self::$orWhereOperators[$i] . ' ?';
      }
    }

    if (self::$whereRaw !== "") {
      $where .= $where === '' ? ' WHERE ' : ' ';
      $where .= self::$whereRaw;
    }

    self::$where = array_merge(self::$whereValues, self::$orWhereValues, self::$whereRawValues);
    if (__COSEY) {
      $obj = $this->selectOne("SELECT escuela,centro FROM profesor WHERE id = ?", [Session::id()]);
      if (!$obj) {
        $obj = $this->selectOne("SELECT escuela,centro FROM year WHERE mt = ?", [Session::id()]);
      }
      if (self::$cosey) {
        return $where . " AND " . self::$table . ".escuela = '$obj->escuela' AND " . self::$table . ". centro = '$obj->centro'";
      }
    }
    return $where;
  }

  /* ----------------- Build the query of the select statement ---------------- */

  private function buildSelectQuery($other = '')
  {
    // for the limit or other
    $other = trim($other);

    $where = $this->buildWhere();

    $join = '';
    if (count(self::$innerJoinTable) > 0) {
      foreach (self::$innerJoinTable as $i => $table) {
        $join .= ' INNER JOIN ' . $table . ' ON ' . self::$innerJoinCol1[$i] . ' ' . self::$innerJoinOperator[$i] . ' ' . self::$innerJoinCol2[$i];
      }
    }

    self::$query = 'SELECT ' . self::$columns . ' FROM ' . self::$table . $join . $where . self::$groupBy . self::$orderBy . ' ' . $other;
  }

  /* ---------------- restore the DB class to the initial state --------------- */

  private function closeDB()
  {
    self::$instance = null;
    self::$table = '';
    self::$columns = '*';
    self::$query = '';
    self::$groupBy = null;
    self::$orderBy = null;
    self::$where = [];
    self::$whereCols = [];
    self::$whereValues = [];
    self::$whereOperators = [];
    self::$orWhereCols = [];
    self::$orWhereValues = [];
    self::$orWhereOperators = [];
    self::$whereRaw = "";
    self::$whereRawValues = [];
    self::$innerJoinTable = [];
    self::$innerJoinCol1 = [];
    self::$innerJoinCol2 = [];
    self::$innerJoinOperator = [];
    parent::$admin = false;
  }

  /* -------------------------------- var_dump -------------------------------- */
  public function dump()
  {
    $this->buildSelectQuery();
    $result = $this->selectFromDB(self::$query, self::$where);
    if ($result->num_rows == 1) {
      $obj = $result->fetch_assoc();
      Util::dump($obj);
    } else if ($result->num_rows > 1) {
      $obj = $result->fetch_all(MYSQLI_ASSOC);
      Util::dump($obj);
    }
  }
}

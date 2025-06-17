<?php

namespace Classes\DataBase;

use Classes\Session;
use Classes\Util;
use Classes\DataBase\DataBase;
use PDO;

/* -------------------------------------------------------------------------- */
/*                      Class for the DataBase queries                        */
/* -------------------------------------------------------------------------- */

/**
 * Database query builder class with fluent interface
 */
class DB extends DataBase
{
  /**
   * SQL operator constants
   */
  public const string EQUALS = '=';
  public const string NOT_EQUALS = '!=';
  public const string GREATER_THAN = '>';
  public const string LESS_THAN = '<';
  public const string GREATER_EQUALS = '>=';
  public const string LESS_EQUALS = '<=';
  public const string LIKE = 'LIKE';

  /**
   * @var self|null Singleton instance
   */
  private static $instance = null;

  /**
   * @var string Current table name
   */
  private static $table = '';

  /**
   * @var string Columns to select
   */
  private static $columns = '*';

  /**
   * @var string Current SQL query
   */
  private static $query = '';

  /**
   * @var string|null Group by clause
   */
  private static $groupBy = null;

  /**
   * @var string|null Order by clause
   */
  private static $orderBy = null;

  /**
   * @var array Parameter values for prepared statements
   */
  private static $where = [];

  /**
   * @var array Where clause column names
   */
  private static $whereCols = [];

  /**
   * @var array Where clause values
   */
  private static $whereValues = [];

  /**
   * @var array Where clause operators
   */
  private static $whereOperators = [];

  /**
   * @var array OR Where clause column names
   */
  private static $orWhereCols = [];

  /**
   * @var array OR Where clause values
   */
  private static $orWhereValues = [];

  /**
   * @var array OR Where clause operators
   */
  private static $orWhereOperators = [];

  /**
   * @var string Raw where clause
   */
  private static $whereRaw = "";

  /**
   * @var array Raw where clause values
   */
  private static $whereRawValues = [];

  /**
   * @var array Inner join table names
   */
  private static $innerJoinTable = [];

  /**
   * @var array Inner join column 1 names
   */
  private static $innerJoinCol1 = [];

  /**
   * @var array Inner join column 2 names
   */
  private static $innerJoinCol2 = [];

  /**
   * @var array Inner join operators
   */
  private static $innerJoinOperator = [];

  /**
   * @var bool Cosey flag
   */
  private static $cosey = true;

  /**
   * Set admin mode and select school by ID
   *
   * @param string $schoolId
   * @return self
   */
  public static function admin($schoolId = __SCHOOL_ACRONYM): self
  {
    self::$instance ??= new self;
    parent::$admin = true;
    self::table('schools');
    self::$whereCols[] = 'id';
    self::$whereValues[] = $schoolId;
    self::$whereOperators[] = self::EQUALS;

    return self::$instance;
  }

  /**
   * Get the next auto-increment ID from a table
   *
   * @param string $table
   * @return int
   */
  public static function getNextAutoIncrementIdFromTable(string $table): int
  {
    $nextId = self::table('')->selectOne("SHOW TABLE STATUS LIKE '$table'");
    return $nextId->Auto_increment;
  }

  /**
   * Get the next ID from a table that doesn't have auto-increment
   *
   * @param string $table
   * @param string $col
   * @return int
   */
  public static function getNextIdFromTable(string $table, string $col = 'id'): int
  {
    $nextId = self::table($table)->select("MAX($col) as $col")->first();
    return (int) $nextId->{$col} + 1;
  }

  /**
   * Create a new table
   *
   * @param string $columns
   * @param string $others
   * @return bool
   */
  public function create(string $columns, string $others = ''): bool
  {
    $tableName = self::$table;
    $query = "CREATE TABLE IF NOT EXISTS {$tableName} ({$columns}) {$others};";
    return $this->query($query);
  }

  /**
   * Alter an existing table
   *
   * @param string $query
   * @return bool
   */
  public function alter(string $query): bool
  {
    $tableName = self::$table;
    $q = "ALTER TABLE IF EXISTS {$tableName} {$query}";
    return $this->query($q);
  }

  /**
   * Execute a raw SQL query
   *
   * @param string $query
   * @return mixed
   */
  public function query(string $query): bool|\PDOStatement
  {
    return $this->normalQuery($query);
  }

  /**
   * Select a table
   *
   * @param string $table
   * @param bool $cosey
   * @return self
   */
  public static function table(string $table, bool $cosey = true): self
  {
    self::$instance ??= new self;
    self::$table = '`' . trim($table) . '`';
    self::$cosey = $cosey;

    return self::$instance;
  }

  /**
   * Select columns of the table (defaults to ALL)
   *
   * @param string $columns
   * @return self
   */
  public function select(string $columns): self
  {
    self::$columns = trim($columns);

    return $this;
  }

  /**
   * Insert one row or multiple rows
   *
   * @param array $insertArray
   * @param bool $getId
   * @return mixed
   */
  public function insert(array $insertArray, bool $getId = false)
  {
    $valuesArray = [];
    $query = [];
    if ($this->isMultiArray($insertArray)) {
      foreach ($insertArray as $array) {
        $count = 0;
        $valuesArray[] = array_values($array);
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
      $paramValues = [];
      foreach ($insertArray as $key => $value) {
        $paramValues[] = $value;
        $coma = ($count > 0 ? ',' : '');
        $columns .= "{$coma} `{$key}`";
        $values .= "{$coma}?";
        $count++;
      }
      $query[] = 'INSERT INTO ' . self::$table . "($columns) VALUES ($values)";
      $valuesArray[] = $paramValues;
    }

    return $this->insertQuery($query, $valuesArray, $getId);
  }

  /**
   * Insert a row and get the inserted ID
   *
   * @param array $insertArray
   * @return int
   */
  public function insertGetId(array $insertArray): int
  {
    return $this->insert($insertArray, true);
  }

  /**
   * Update rows in the table
   *
   * @param array $updateArray
   * @return bool
   */
  public function update(array $updateArray): bool
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

    return $result !== false && !isset($result['error']);
  }

  /**
   * Delete rows from the table
   *
   * @return void
   */
  public function delete(): void
  {
    $where = $this->buildWhere();
    $query = 'DELETE FROM ' . self::$table . ' ' . $where;
    $this->updateQuery($query, self::$whereValues);
    $this->closeDB();
  }

  /**
   * Find a row by the primary key
   *
   * @param mixed $value
   * @param string $col
   * @return object|null
   */
  public function find($value, string $col = 'id'): ?object
  {
    $this->where($col, $value);
    $this->buildSelectQuery();
    $obj = $this->selectOne(self::$query, self::$where);
    $this->closeDB();
    return $obj;
  }

  /**
   * Add a where clause
   *
   * @param mixed $w1
   * @param mixed $w2
   * @param mixed $w3
   * @return self
   */
  public function where($w1, $w2 = false, $w3 = false): self
  {
    if ($w2) {
      if (strpos($w1, '.') !== false) {
        [$table, $col] = explode('.', $w1);
        self::$whereCols[] = trim("`$table`.`$col`");
      } else {
        self::$whereCols[] = trim("`$w1`");
      }
      self::$whereValues[] = $w3 ? trim($w3) : trim($w2);
      self::$whereOperators[] = $w3 ? trim($w2) : self::EQUALS;
    } else if (!$w2) {
      if (parent::isMultiArray($w1)) {
        foreach ($w1 as $w) {
          if (strpos($w[0], '.') !== false) {
            [$table, $col] = explode('.', $w[0]);
            self::$whereCols[] = trim("`$table`.`$col`");
          } else {
            self::$whereCols[] = trim("`$w[0]`");
          }

          self::$whereValues[] = isset($w[2]) ? trim($w[2]) : trim($w[1]);
          self::$whereOperators[] = isset($w[2]) ? trim($w[1]) : self::EQUALS;
        }
      } else {

        if (strpos($w1[0], '.') !== false) {
          [$table, $col] = explode('.', $w1[0]);
          self::$whereCols[] = trim("`$table`.`$col`");
        } else {
          self::$whereCols[] = trim("`$w1[0]`");
        }
        self::$whereValues[] = isset($w1[2]) ? trim($w1[2]) : trim($w1[1]);
        self::$whereOperators[] = isset($w1[2]) ? trim($w1[1]) : self::EQUALS;
      }
    }

    return $this;
  }

  /**
   * Add an OR where clause
   *
   * @param mixed $w1
   * @param mixed $w2
   * @param mixed $w3
   * @return self
   */
  public function orWhere($w1, $w2 = false, $w3 = false): self
  {
    if ($w2) {
      self::$orWhereCols[] = trim($w1);
      self::$orWhereValues[] = ($w3 ? trim($w3) : trim($w2));
      self::$orWhereOperators[] = ($w3 ? trim($w2) : self::EQUALS);
    } else if (!$w2) {
      if (parent::isMultiArray($w1)) {
        foreach ($w1 as $w) {
          self::$orWhereCols[] = trim($w[0]);
          self::$orWhereValues[] = (isset($w[2]) ? trim($w[2]) : trim($w[1]));
          self::$orWhereOperators[] = (isset($w[2]) ? trim($w[1]) : self::EQUALS);
        }
      } else {
        self::$orWhereCols[] = trim($w1[0]);
        self::$orWhereValues[] = (isset($w1[2]) ? trim($w1[2]) : trim($w1[1]));
        self::$orWhereOperators[] = (isset($w1[2]) ? trim($w1[1]) : self::EQUALS);
      }
    }

    return $this;
  }

  /**
   * Add a raw where clause
   *
   * @param string $query
   * @param array $values
   * @return self
   */
  public function whereRaw(string $query, array $values = []): self
  {
    self::$whereRaw = $query;
    self::$whereRawValues = $values;
    return $this;
  }

  /**
   * Add an inner join clause
   *
   * @param string $tableToJoin
   * @param string $table1Col
   * @param string $operator
   * @param string $table2Col
   * @return self
   */
  public function join(string $tableToJoin, string $table1Col, string $operator, string $table2Col): self
  {
    self::$innerJoinTable[] = $tableToJoin;
    self::$innerJoinCol1[] = $table1Col;
    self::$innerJoinCol2[] = $table2Col;
    self::$innerJoinOperator[] = $operator;
    return $this;
  }

  /**
   * Add an order by clause
   *
   * @param string $by
   * @param string|null $mode
   * @return self
   */
  public function orderBy(string $by, ?string $mode = null): self
  {
    $mode = $mode ? strtoupper($mode) : null;
    self::$orderBy = ' ORDER BY ' . trim($by) . ' ' . $mode;
    return $this;
  }

  /**
   * Set the cosey flag
   *
   * @param bool $active
   * @return self
   */
  public function cosey(bool $active = false): self
  {
    self::$cosey = $active;
    return $this;
  }

  /**
   * Add a group by clause
   *
   * @param string $col
   * @return self
   */
  public function groupBy(string $col): self
  {
    self::$groupBy = ' GROUP BY ' . trim($col);
    return $this;
  }

  /**
   * Display the current query
   *
   * @param bool $exit
   * @return void
   */
  public function display(bool $exit = false): void
  {
    $this->buildSelectQuery();
    echo self::$query . "<br/> ";
    var_dump(self::$where);
    echo "<hr/>";
    $this->closeDB();
    if ($exit) {
      exit;
    }
  }

  /**
   * Get multiple rows
   *
   * @return array
   */
  public function get(): array
  {
    $this->buildSelectQuery();
    $obj = $this->selectAll(self::$query, self::$where);
    $this->closeDB();
    return $obj;
  }

  /**
   * Get the first row
   *
   * @return object|bool
   */
  public function first(): bool|object
  {
    $this->buildSelectQuery('limit 1');
    $obj = $this->selectOne(self::$query, self::$where);
    $this->closeDB();
    return $obj;
  }

  /**
   * Build the where clause
   *
   * @return string
   */
  private function buildWhere(): string
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

  /**
   * Build the select query
   *
   * @param string $other
   * @return void
   */
  private function buildSelectQuery(string $other = ''): void
  {
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

  /**
   * Restore the DB class to the initial state
   *
   * @return void
   */
  private function closeDB(): void
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
}

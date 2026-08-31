<?php

namespace Classes\DataBase;

use Classes\Session;
use Exception;
use PDO;
use PDOException;

/* -------------------------------------------------------------------------- */
/*                      Class for the DataBase connection                     */
/* -------------------------------------------------------------------------- */

class DataBase
{
    protected static $admin = false;
    private string $host = __HOST;
    private string $username = __USERNAME;
    private string $password = __PASSWORD;
    private string $dbName = __DB_NAME;
    private string $adminHost = __ADMIN_HOST;
    private string $adminUsername = __ADMIN_USERNAME;
    private string $adminPassword = __ADMIN_PASSWORD;
    private string $adminDbName = __ADMIN_DB_NAME;

    // Conexión singleton para reutilización
    private static ?PDO $connection = null;

    protected function connect()
    {
        // Reutilizar conexión existente si está disponible
        if (self::$connection !== null) {
            return self::$connection;
        }

        try {
            if (self::$admin) {
                $dsn = "mysql:host={$this->adminHost};port=3306;dbname={$this->adminDbName};charset=utf8";
                $db = new PDO($dsn, $this->adminUsername, $this->adminPassword);
            } else {
                $dsn = "mysql:host={$this->host};port=3306;dbname={$this->dbName};charset=utf8";
                $db = new PDO($dsn, $this->username, $this->password, [
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]);
            }

            // // Configurar PDO para que lance excepciones en caso de error
            // $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // // Devolver resultados como objetos
            // $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            // // Emular sentencias preparadas para mayor compatibilidad
            // $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);


            self::$connection = $db;
            return $db;
        } catch (PDOException $e) {
            throw new Exception("Fallo al conectar a MySQL: " . $e->getMessage());
        }
    }

    // Método para cerrar la conexión
    public function closeConnection()
    {
        self::$connection = null;
    }


    protected function normalQuery($query)
    {
        $db = $this->connect();
        try {
            $stmt = $db->query($query);
            return $stmt;
        } catch (PDOException $e) {
            $this->exception($e->getMessage(), $query);
            return false;
        }
    }

    protected function deleteTable($table, $pk, $wherePk)
    {
        $query = "DELETE FROM {$table} WHERE {$pk} = ?";
        return $this->deleteQuery($query, [$wherePk]);
    }

    protected function deleteQuery($query, $valuesArray = [])
    {
        // Verifica la sesión antes de ejecutar operaciones destructivas
        if (!Session::is_logged(false)) {
            return $this->exception("Usuario no autenticado", $query);
        }

        try {
            $db = $this->connect();
            $stmt = $db->prepare($query);

            if ($stmt->execute($valuesArray)) {
                return $stmt->rowCount() > 0;
            }

            return false;
        } catch (PDOException $e) {
            return $this->exception($e->getMessage(), $query, $valuesArray);
        }
    }

    // update tables
    protected function updateTable($table, $pk, $wherePk, $propsArray)
    {
        // Elimina la clave primaria del arreglo de actualización
        unset($propsArray[$pk]);

        if (empty($propsArray)) {
            return $this->exception("No hay campos para actualizar", "updateTable", ["table" => $table, "pk" => $pk]);
        }

        $query = "UPDATE {$table} SET ";
        $count = 0;
        $valuesArray = [];

        foreach ($propsArray as $key => $value) {
            $valuesArray[] = $value;
            $coma = ($count > 0 ? ',' : '');
            $query .= "$coma $key = ?";
            $count++;
        }

        $query .= " WHERE {$pk} = ?";
        $valuesArray[] = $wherePk; // Agrega la clave primaria al final

        return $this->updateQuery($query, $valuesArray);
    }

    protected function updateQuery($query, $valuesArray)
    {
        try {
            $db = $this->connect();
            $stmt = $db->prepare($query);

            if (!Session::is_logged(false)) {
                return $this->exception("Usuario no autenticado", $query);
            }

            return $stmt->execute($valuesArray);
        } catch (PDOException $e) {
            return $this->exception($e->getMessage(), $query, $valuesArray);
        }
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

        return $this->insertQuery([$query], [$valuesArray]);
    }

    protected function insertQuery($query, $valuesArray, $insertId = false)
    {
        $db = $this->connect();

        if (!Session::is_logged(false)) {
            return $this->exception("Usuario no autenticado", implode("; ", $query));
        }

        try {
            // multiple inserts
            if ($this->isMultiArray($valuesArray)) {
                foreach ($valuesArray as $key => $array) {
                    $stmt = $db->prepare($query[$key]);
                    $stmt->execute($array);
                }
                return true;
            } else {
                $stmt = $db->prepare($query[0]);
                $stmt->execute($valuesArray[0]);

                if ($insertId === true) {
                    return $db->lastInsertId();
                }
                return true;
            }
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    // select just one row
    protected function selectOne($query, $whereArray = [])
    {
        $result = $this->selectFromDB($query, $whereArray);
        if ($result) {
            return $result->fetch();
        }
        return false;
    }

    // select multiple rows
    protected function selectAll($query, $whereArray = [])
    {
        $result = $this->selectFromDB($query, $whereArray);
        if ($result) {
            return $result->fetchAll();
        }
        return [];
    }

    // global select
    protected function selectFromDB($query, $whereArray)
    {
        try {
            $db = $this->connect();
            $stmt = $db->prepare($query);
            $stmt->execute($whereArray);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Error con el query $query (" . $e->getMessage() . ")");
        }
    }

    // check if the array given is a associative array
    protected static function isMultiArray($array)
    {
        $rv = array_filter($array, 'is_array');
        if (count($rv) > 0) {
            return true;
        }
        return false;
    }

    private function exception($message, $query, $values = null)
    {
        // Registrar el error en algún lugar (log)
        error_log("Database error: $message, Query: $query");
        return ["error" => true, "message" => $message, "query" => $query, "values" => $values];
    }

    // Destructor 
    public function __destruct()
    {
        $this->closeConnection();
    }
}

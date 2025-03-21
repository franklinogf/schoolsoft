<?php

namespace Classes\DataBase;

use Classes\Session;
use Classes\Util;
use Exception;
use mysqli;

/* -------------------------------------------------------------------------- */
/*                      Class for the DataBase connection                     */
/* -------------------------------------------------------------------------- */

class DataBase
{
    protected static $admin = false;
    private $host = __HOST;
    private $username = __USERNAME;
    private $password = __PASSWORD;
    private $dbName = __DB_NAME;
    private $adminHost = __ADMIN_HOST;
    private $adminUsername = __ADMIN_USERNAME;
    private $adminPassword = __ADMIN_PASSWORD;
    private $adminDbName = __ADMIN_DB_NAME;

    // Conexión singleton para reutilización
    private static $connection = null;

    protected function connect()
    {
        // Reutilizar conexión existente si está disponible
        if (self::$connection !== null) {
            return self::$connection;
        }

        if (self::$admin) {
            $db = new mysqli($this->adminHost, $this->adminUsername, $this->adminPassword, $this->adminDbName);
        } else {
            $db = new mysqli($this->host, $this->username, $this->password, $this->dbName);
        }

        if ($db->connect_errno) {
            throw new Exception("Fallo al conectar a MySQL: (" . $db->connect_errno . ") " . $db->connect_error);
        }
        $db->set_charset("utf8");

        self::$connection = $db;
        return $db;
    }

    // Método para cerrar la conexión
    public function closeConnection()
    {
        if (self::$connection !== null) {
            self::$connection->close();
            self::$connection = null;
        }
    }

    // Método para iniciar una transacción
    protected function beginTransaction()
    {
        $db = $this->connect();
        $db->autocommit(false);
        return $db->begin_transaction();
    }

    // Método para confirmar una transacción
    protected function commit()
    {
        if (self::$connection !== null) {
            return self::$connection->commit();
        }
        return false;
    }

    // Método para revertir una transacción
    protected function rollback()
    {
        if (self::$connection !== null) {
            return self::$connection->rollback();
        }
        return false;
    }

    protected function normalQuery($query)
    {
        $db = $this->connect();
        try {
            // Usa consulta preparada en lugar de query() directo
            $stmt = $db->prepare($query);
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $db->error);
            }

            if (!$stmt->execute()) {
                throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            // Mejorado el manejo de excepciones
            return $this->exception($e->getMessage(), $query);
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

        $db = $this->connect();
        if (!$stmt = $db->prepare($query)) {
            return $this->exception($db->error, $query);
        }

        if (count($valuesArray) > 0) {
            $bind = str_repeat('s', count($valuesArray));
            $stmt->bind_param($bind, ...$valuesArray);
        }

        if ($stmt->execute()) {
            $affectedRows = $stmt->affected_rows;
            $stmt->close();
            return $affectedRows > 0;
        }

        $error = $stmt->error;
        $stmt->close();
        return $this->exception($error, $query, $valuesArray);
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
        $db = $this->connect();
        $stmt = $db->prepare($query);
        if (!$stmt) {
            return $this->exception($db->error, $query, $valuesArray);
        }
        $bind = str_repeat('s', count($valuesArray));

        $stmt->bind_param($bind, ...$valuesArray);
        if (!$stmt->execute()) {
            return $this->exception("Error executing query", $query, $valuesArray);
        }
        return true;
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
                if (!$stmt = $db->prepare($query[$key])) {
                    echo "error " . $db->error . "<br/>";
                }
                $bind = str_repeat('s', count($array));
                // php 5 version
                // $refs = [];
                // foreach ($array as $key => $value) {
                //     $refs[$key] = &$array[$key];
                // }
                // call_user_func_array(array($stmt, "bind_param"), array_merge([$bind], $refs));
                // // php 7 version
                $stmt->bind_param($bind, ...$array);
                if (Session::is_logged(false)) {
                    $stmt->execute();
                }
            }
        } else {
            $stmt = $db->prepare($query[0]);
            $bind = str_repeat('s', count($valuesArray));
            // php 5 version
            // $refs = [];
            // foreach ($valuesArray as $key => $value) {
            //     $refs[$key] = &$valuesArray[$key];
            // }
            // call_user_func_array(array($stmt, "bind_param"), array_merge([$bind], $refs));
            // php 7 version
            $stmt->bind_param($bind, ...$valuesArray);
        }
        if (Session::is_logged(false)) {
            if ($stmt->execute()) {
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
        $obj = $result->fetch_all(MYSQLI_ASSOC);
        return Util::toObject($obj);
    }
    // global select
    protected function selectFromDB($query, $whereArray)
    {

        $db = $this->connect();
        if (!$stmt = $db->prepare($query)) {
            // var_dump($whereArray);
            throw new Exception("Error con el query $query ($db->error)");
        }

        if (count($whereArray) > 0) {

            $bind = str_repeat('s', count($whereArray));
            // php 5 version
            // $refs = array();
            // foreach ($whereArray as $key => $value) {
            //     $refs[$key] = &$whereArray[$key];
            // }
            // var_dump($refs);
            // call_user_func_array(array($stmt, "bind_param"), array_merge([$bind], $refs));
            // php 7 version
            $stmt->bind_param($bind, ...$whereArray);
        }
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }
    // check if the array given is a associative  array
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

    // Añadir un método para destructor que cierre la conexión
    public function __destruct()
    {
        $this->closeConnection();
    }
}

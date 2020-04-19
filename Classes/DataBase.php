<?php
namespace Classes;

use mysqli;
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
}

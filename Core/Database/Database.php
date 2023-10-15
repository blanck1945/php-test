<?php

namespace Core\Database;

use Core\Autoload\AutoLoad;

class Database
{

    private string $db_host;
    private string $db_name;

    private string $db_user;
    private string $db_password;
    private string $db_port;

    public function __construct(private string $db_type)
    {
        AutoLoad::loadAutoload();
        $this->db_type = $db_type;
        $this->db_host = $_ENV['DB_HOST'];
        $this->db_port = $_ENV['DB_PORT'];
        $this->db_name = $_ENV['DB_NAME'];
        $this->db_user = $_ENV['DB_USER'];
        $this->db_password = $_ENV['DB_PASSWORD'];
    }

    public function connect_db()
    {
        $connection_string = "host=$this->db_host port=$this->db_port dbname=$this->db_name user=$this->db_user password=$this->db_password";

        try {
            return pg_connect($connection_string);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function execute_query($query)
    {
        $connection = $this->connect_db();

        try {
            $result = pg_query($connection, $query);

            return $result;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param string $tableName
     * @param array $columns
     * @param array $values
     * @return mixed
     */
    function insert($tableName, $columns, $values)
    {
        $query = "INSERT INTO $tableName $columns VALUES $values";
    }

    public function create($table, $data)
    {


        $connection = $this->connect_db();

        try {
            $sql_query = "INSERT INTO $table (
                name,
                description,
                price,
                quantity
            ) VALUES (
                '$data->',
                '$data->description',
                $data->price,
                $data->quantity
            )";
            $result = pg_query($connection, $sql_query);

            return $result;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}

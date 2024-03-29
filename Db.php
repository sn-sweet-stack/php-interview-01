<?php
namespace App;

use function getenv;
use function strtolower;
use function str_starts_with;

class Db
{
    private $host;
    private $port;
    private $username;
    private $password;
    private $database;
    private $connection;

    public function __construct() {
        $this->host     = getenv('DB_HOST');
        $this->port     = getenv('DB_PORT');
        $this->username = getenv('DB_USERNAME');
        $this->password = getenv('DB_PASSWORD');
        $this->database = getenv('DB_DATABASE');

        // Establish a connection to the database
        $this->connect();
    }

    private function connect() {
        $this->connection = new \mysqli($this->host, $this->username, $this->password, $this->database, $this->port);

        // Check connection
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    public function query($sql, $params = []) {
        // Prepare the SQL statement
        $stmt = $this->connection->prepare($sql);

        // Bind parameters if any
        if (!empty($params)) {
            $types = '';
            $values = [];
            foreach ($params as $param) {
                // Determine the type of the parameter
                if (is_int($param)) {
                    $types .= 'i'; // integer
                } elseif (is_float($param)) {
                    $types .= 'd'; // double
                } else {
                    $types .= 's'; // string
                }

                $values[] = $param;
            }

            // Bind parameters
            $stmt->bind_param($types, ...$values);
        }

        // Execute the statement
        $stmt->execute();

        // Get the result (if it's a SELECT query)
        $result = str_starts_with(strtolower($sql), 'select')
            ? $stmt->get_result()
            : true;

        // Check if the query was successful
        if (!$result) {
            die("Query failed: " . $this->connection->error);
        }

        // Close the statement
        $stmt->close();

        return $result;
    }


    public function closeConnection()
    {
        // Close the database connection
        $this->connection->close();
    }
}


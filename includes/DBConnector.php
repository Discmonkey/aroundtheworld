<?php

/**
 * User: maxg
 * Date: 3/25/17
 * Time: 4:01 PM
 * This is a simple database connection class. It supports parameterized queries and inserts. 
 */

function ref_values($arr){
    # fixes error with passing by reference in mysqli
    $refs = array();
    foreach($arr as $key => $value)
        $refs[$key] = &$arr[$key];
    return $refs;
}


class DBConnector
{
    // simple wrapper class for the msqli connection
    // parameterizes queries and provides a simple interface for reading or inserting
    private $connection = null;

    function __construct()
    {
        $this->connection = new mysqli("airports.c74vpprqwl8a.us-east-1.rds.amazonaws.com", "maxgrinchenko", "ASDF;lkj4231", "gotravel");
    }

    private function prepare_param_statements(array &$params) {
        // private function for preparing array of params to be bound to the query, defaults to string
        $type_string = "";
        foreach ($params as $param) {
            switch (gettype($param)) {
                case 'integer':
                    $type_string .= 'i';
                    break;
                case 'double':
                    $type_string .= 'd';
                    break;
                default:
                    $type_string .= 's';
            }
        }

        array_unshift($params, $type_string);
    }

    function query(string $query, array $params): array {
        // prepares query and queries the database

        $stmt = $this->connection->prepare($query);

        if (count($params) > 0) {
            $this->prepare_param_statements($params);
            call_user_func_array(array($stmt, 'bind_param'), ref_Values($params));
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $results = [];

        if ($result)
            while ($row = $result->fetch_assoc()) {
                $results []= $row;
            }

        return $results;

    }

    function insert(string $query, array $params): int {
        $stmt = $this->connection->prepare($query);


        if (count($params) > 0) {
            $this->prepare_param_statements($params);
            call_user_func_array(array($stmt, 'bind_param'), ref_values($params));
        }

        $stmt->execute();

        return $stmt->affected_rows;
    }

}
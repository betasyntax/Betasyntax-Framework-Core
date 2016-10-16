<?php namespace Betasyntax\Orm;

use PDO;

class PdoValueBinder {

    protected $pdoTypes = array(
        'boolean' => PDO::PARAM_BOOL,
        'integer' => PDO::PARAM_INT,
        'double'  => PDO::PARAM_INT,
        'string'  => PDO::PARAM_STR,
        'NULL'    => PDO::PARAM_NULL
    );

    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }
    public function type($value) {
        if (is_string($value)) {
            // return $this->db->quote($value);
            return $value;
        } elseif (is_null($value)) {
            return NULL;
        } elseif (is_numeric($value)) {
            return $value;
        } else {
        }
    }
}
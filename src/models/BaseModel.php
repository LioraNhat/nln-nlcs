<?php

namespace App\Core;

use PDO;

abstract class BaseModel {
    
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
}
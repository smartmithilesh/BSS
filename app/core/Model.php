<?php
class Model {
    protected $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    protected function beginTransaction() { $this->db->beginTransaction(); }
    protected function commit()           { $this->db->commit(); }
    protected function rollBack()         { $this->db->rollBack(); }
}

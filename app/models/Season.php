<?php
class Season extends Model {
    public function getAll() {
        return $this->db->query("SELECT * FROM seasons ORDER BY start_year DESC")->fetchAll();
    }
    public function getActive() {
        $s = $this->db->query("SELECT * FROM seasons WHERE is_active=1 LIMIT 1")->fetch();
        return $s ?: null;
    }
    public function find($id) {
        $st = $this->db->prepare("SELECT * FROM seasons WHERE id=?");
        $st->execute([$id]); return $st->fetch();
    }
    public function create($d) {
        $this->db->prepare("INSERT INTO seasons(name,start_year,end_year,is_active)VALUES(?,?,?,?)")
            ->execute([$d['name'],$d['start_year'],$d['end_year'],0]);
    }
    public function update($id,$d) {
        $this->db->prepare("UPDATE seasons SET name=?,start_year=?,end_year=? WHERE id=?")
            ->execute([$d['name'],$d['start_year'],$d['end_year'],$id]);
    }
    public function setActive($id) {
        $this->db->exec("UPDATE seasons SET is_active=0");
        $this->db->prepare("UPDATE seasons SET is_active=1 WHERE id=?")->execute([$id]);
    }
    public function delete($id) {
        $this->db->prepare("DELETE FROM seasons WHERE id=?")->execute([$id]);
    }
}

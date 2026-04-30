<?php
class Publication extends Model {
    public function getAll() {
        return $this->db->query("SELECT * FROM publications ORDER BY name")->fetchAll();
    }
    public function find($id) {
        $st=$this->db->prepare("SELECT * FROM publications WHERE id=?"); $st->execute([$id]); return $st->fetch();
    }
    public function create($d) {
        $this->db->prepare("INSERT INTO publications(name)VALUES(?)")->execute([$d['name']]);
    }
    public function update($id,$d) {
        $this->db->prepare("UPDATE publications SET name=? WHERE id=?")->execute([$d['name'],$id]);
    }
    public function delete($id) {
        $this->db->prepare("DELETE FROM publications WHERE id=?")->execute([$id]);
    }
}

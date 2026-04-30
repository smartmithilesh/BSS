<?php
class ClassModel extends Model {
    public function getAll() {
        return $this->db->query("SELECT * FROM classes ORDER BY sort_order,name")->fetchAll();
    }
    public function find($id) {
        $st=$this->db->prepare("SELECT * FROM classes WHERE id=?"); $st->execute([$id]); return $st->fetch();
    }
    public function create($d) {
        $this->db->prepare("INSERT INTO classes(name,sort_order)VALUES(?,?)")->execute([$d['name'],$d['sort_order']??0]);
    }
    public function update($id,$d) {
        $this->db->prepare("UPDATE classes SET name=?,sort_order=? WHERE id=?")->execute([$d['name'],$d['sort_order']??0,$id]);
    }
    public function delete($id) {
        $this->db->prepare("DELETE FROM classes WHERE id=?")->execute([$id]);
    }
}

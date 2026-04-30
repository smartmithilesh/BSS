<?php
class School extends Model {
    public function getAll() {
        return $this->db->query("SELECT * FROM schools ORDER BY name")->fetchAll();
    }
    public function find($id) {
        $st=$this->db->prepare("SELECT * FROM schools WHERE id=?"); $st->execute([$id]); return $st->fetch();
    }
    public function create($d) {
        $this->db->prepare("INSERT INTO schools(name,contact_person,phone,email,address)VALUES(?,?,?,?,?)")
            ->execute([$d['name'],$d['contact_person']??'',$d['phone']??'',$d['email']??'',$d['address']??'']);
    }
    public function update($id,$d) {
        $this->db->prepare("UPDATE schools SET name=?,contact_person=?,phone=?,email=?,address=? WHERE id=?")
            ->execute([$d['name'],$d['contact_person']??'',$d['phone']??'',$d['email']??'',$d['address']??'',$id]);
    }
    public function delete($id) {
        $this->db->prepare("DELETE FROM schools WHERE id=?")->execute([$id]);
    }
}

<?php
class Department extends Model {
    public function getAll($activeOnly=false) {
        $sql="SELECT * FROM departments";
        if($activeOnly) $sql.=" WHERE is_active=1";
        $sql.=" ORDER BY name";
        return $this->db->query($sql)->fetchAll();
    }

    public function find($id) {
        $st=$this->db->prepare("SELECT * FROM departments WHERE id=?");
        $st->execute([$id]); return $st->fetch();
    }

    public function create($d) {
        $this->db->prepare("INSERT INTO departments(name,description,is_active)VALUES(?,?,?)")
            ->execute([$d['name'],$d['description']??'',isset($d['is_active'])?1:0]);
    }

    public function update($id,$d) {
        $this->db->prepare("UPDATE departments SET name=?,description=?,is_active=? WHERE id=?")
            ->execute([$d['name'],$d['description']??'',isset($d['is_active'])?1:0,$id]);
    }

    public function delete($id) {
        $this->db->prepare("DELETE FROM departments WHERE id=?")->execute([$id]);
    }
}

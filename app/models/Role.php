<?php
class Role extends Model {
    public function getAll($activeOnly=false) {
        $sql="SELECT r.*,d.name AS department_name FROM roles r LEFT JOIN departments d ON d.id=r.department_id";
        if($activeOnly) $sql.=" WHERE r.is_active=1";
        $sql.=" ORDER BY d.name,r.name";
        return $this->db->query($sql)->fetchAll();
    }
    public function find($id) {
        $st=$this->db->prepare("SELECT * FROM roles WHERE id=?");
        $st->execute([$id]); return $st->fetch();
    }
    public function create($d) {
        $this->db->prepare("INSERT INTO roles(department_id,name,slug,description,is_active)VALUES(?,?,?,?,?)")
            ->execute([$d['department_id']?:null,$d['name'],$this->slug($d['slug']??$d['name']),$d['description']??'',isset($d['is_active'])?1:0]);
    }
    public function update($id,$d) {
        $this->db->prepare("UPDATE roles SET department_id=?,name=?,slug=?,description=?,is_active=? WHERE id=?")
            ->execute([$d['department_id']?:null,$d['name'],$this->slug($d['slug']??$d['name']),$d['description']??'',isset($d['is_active'])?1:0,$id]);
    }
    public function delete($id) {
        $this->db->prepare("DELETE FROM roles WHERE id=?")->execute([$id]);
    }
    private function slug($value) {
        $slug=strtolower(trim((string)$value));
        $slug=preg_replace('/[^a-z0-9]+/','-',$slug);
        return trim($slug,'-') ?: 'role';
    }
}

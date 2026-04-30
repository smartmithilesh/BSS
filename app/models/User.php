<?php
class User extends Model {
    public function findByEmail($email) {
        $st=$this->db->prepare("SELECT u.*,COALESCE(r.slug,u.role) AS role,r.name AS role_name,d.name AS department_name FROM users u LEFT JOIN departments d ON d.id=u.department_id LEFT JOIN roles r ON r.id=u.role_id WHERE u.email=?");
        $st->execute([$email]); return $st->fetch();
    }
    public function find($id) {
        $st=$this->db->prepare("SELECT * FROM users WHERE id=?");
        $st->execute([$id]); return $st->fetch();
    }
    public function getAll() {
        return $this->db->query("SELECT u.id,u.name,u.email,u.department_id,u.role_id,COALESCE(r.slug,u.role) AS role,r.name AS role_name,u.created_at,d.name AS department_name FROM users u LEFT JOIN departments d ON d.id=u.department_id LEFT JOIN roles r ON r.id=u.role_id ORDER BY u.name")->fetchAll();
    }
    public function create($d) {
        [$roleId,$roleSlug]=$this->resolveRole($d);
        $this->db->prepare("INSERT INTO users(name,email,password,department_id,role_id,role)VALUES(?,?,?,?,?,?)")
            ->execute([$d['name'],$d['email'],password_hash($d['password'],PASSWORD_DEFAULT),$d['department_id']?:null,$roleId,$roleSlug]);
    }
    public function update($id,$d) {
        [$roleId,$roleSlug]=$this->resolveRole($d);
        $this->db->prepare("UPDATE users SET name=?,email=?,department_id=?,role_id=?,role=? WHERE id=?")
            ->execute([$d['name'],$d['email'],$d['department_id']?:null,$roleId,$roleSlug,$id]);
        if(!empty($d['password'])) $this->updatePassword($id,$d['password']);
    }
    public function updateProfile($id,$d) {
        $this->db->prepare("UPDATE users SET name=?,email=? WHERE id=?")
            ->execute([$d['name'],$d['email'],$id]);
        if(!empty($d['password'])) $this->updatePassword($id,$d['password']);
    }
    public function updatePassword($id,$password) {
        $this->db->prepare("UPDATE users SET password=? WHERE id=?")->execute([password_hash($password,PASSWORD_DEFAULT),$id]);
    }
    public function delete($id) {
        $this->db->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
    }
    private function resolveRole($d) {
        $roleId=!empty($d['role_id'])?(int)$d['role_id']:null;
        if($roleId) {
            $role=(new Role())->find($roleId);
            if($role) return [$roleId,$role['slug']];
        }
        return [null,$d['role']??'staff'];
    }
}

<?php
class Company extends Model {
    public function getAll() {
        return $this->db->query("SELECT * FROM companies ORDER BY name")->fetchAll();
    }
    public function find($id) {
        $st=$this->db->prepare("SELECT * FROM companies WHERE id=?"); $st->execute([$id]); return $st->fetch();
    }
    public function create($d) {
        $this->db->prepare("INSERT INTO companies(name,contact_person,phone,email,address)VALUES(?,?,?,?,?)")
            ->execute([$d['name'],$d['contact_person']??'',$d['phone']??'',$d['email']??'',$d['address']??'']);
    }
    public function update($id,$d) {
        $this->db->prepare("UPDATE companies SET name=?,contact_person=?,phone=?,email=?,address=? WHERE id=?")
            ->execute([$d['name'],$d['contact_person']??'',$d['phone']??'',$d['email']??'',$d['address']??'',$id]);
    }
    public function delete($id) {
        $this->db->prepare("DELETE FROM companies WHERE id=?")->execute([$id]);
    }
    // Ledger summary per season
    public function getLedger($seasonId) {
        $sql = "
            SELECT c.id, c.name,
                COALESCE(SUM(pi2.amount),0)     AS total_purchases,
                COALESCE(cp.total_paid,0)       AS total_paid,
                COALESCE(cr.total_returned,0)   AS total_returned
            FROM companies c
            LEFT JOIN purchases p  ON p.company_id=c.id AND p.season_id=?
            LEFT JOIN purchase_items pi2 ON pi2.purchase_id=p.id
            LEFT JOIN (
                SELECT company_id, SUM(amount) AS total_paid
                FROM company_payments WHERE season_id=? GROUP BY company_id
            ) cp ON cp.company_id=c.id
            LEFT JOIN (
                SELECT company_id, SUM(total_amount) AS total_returned
                FROM company_returns WHERE season_id=? GROUP BY company_id
            ) cr ON cr.company_id=c.id
            GROUP BY c.id ORDER BY c.name
        ";
        $st=$this->db->prepare($sql); $st->execute([$seasonId,$seasonId,$seasonId]);
        return $st->fetchAll();
    }
}

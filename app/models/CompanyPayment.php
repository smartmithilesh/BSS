<?php
class CompanyPayment extends Model {
    public function getAll($filters=[], $limit=null, $offset=0) {
        $sql="SELECT cp.*,c.name AS company_name,s.name AS season_name
              FROM company_payments cp JOIN companies c ON c.id=cp.company_id JOIN seasons s ON s.id=cp.season_id
              WHERE 1=1"; $params=[];
        if(!empty($filters['season_id']))  { $sql.=" AND cp.season_id=?";  $params[]=$filters['season_id']; }
        if(!empty($filters['company_id'])) { $sql.=" AND cp.company_id=?"; $params[]=$filters['company_id']; }
        $sql.=" ORDER BY cp.id DESC";
        if($limit!==null) { $limit=(int)$limit; $offset=(int)$offset; $sql.=" LIMIT $limit OFFSET $offset"; }
        $st=$this->db->prepare($sql); $st->execute($params); return $st->fetchAll();
    }
    public function count($filters=[]) {
        $sql="SELECT COUNT(*) FROM company_payments cp WHERE 1=1"; $params=[];
        if(!empty($filters['season_id']))  { $sql.=" AND cp.season_id=?";  $params[]=$filters['season_id']; }
        if(!empty($filters['company_id'])) { $sql.=" AND cp.company_id=?"; $params[]=$filters['company_id']; }
        $st=$this->db->prepare($sql); $st->execute($params); return (int)$st->fetchColumn();
    }
    public function find($id) {
        $st=$this->db->prepare("SELECT cp.*,c.name AS company_name,s.name AS season_name FROM company_payments cp JOIN companies c ON c.id=cp.company_id JOIN seasons s ON s.id=cp.season_id WHERE cp.id=?");
        $st->execute([$id]); return $st->fetch();
    }
    public function create($d) {
        $this->db->prepare("INSERT INTO company_payments(season_id,company_id,payment_date,amount,payment_mode,reference_no,notes)VALUES(?,?,?,?,?,?,?)")
            ->execute([$d['season_id'],$d['company_id'],$d['payment_date'],$d['amount'],$d['payment_mode'],$d['reference_no']??'',$d['notes']??'']);
    }
    public function delete($id) {
        $this->db->prepare("DELETE FROM company_payments WHERE id=?")->execute([$id]);
    }
    // Outstanding per company per season
    public function getOutstanding($seasonId) {
        $st=$this->db->prepare("
            SELECT c.id,c.name,
                COALESCE(SUM(DISTINCT p.net_amount),0) AS total_purchase,
                COALESCE((SELECT SUM(amount) FROM company_payments WHERE company_id=c.id AND season_id=?),0) AS total_paid,
                COALESCE((SELECT SUM(total_amount) FROM company_returns WHERE company_id=c.id AND season_id=?),0) AS total_returned
            FROM companies c
            LEFT JOIN purchases p ON p.company_id=c.id AND p.season_id=?
            GROUP BY c.id ORDER BY c.name");
        $st->execute([$seasonId,$seasonId,$seasonId]); return $st->fetchAll();
    }
}

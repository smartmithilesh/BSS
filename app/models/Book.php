<?php
class Book extends Model {
    public function getAll($filters=[], $limit=null, $offset=0) {
        $sql = "SELECT b.*,cl.name AS class_name,p.name AS publication_name,co.name AS company_name
                FROM books b
                JOIN classes cl ON cl.id=b.class_id
                JOIN publications p ON p.id=b.publication_id
                JOIN companies co ON co.id=b.company_id
                WHERE 1=1";
        $params=[];
        if(!empty($filters['name']))          { $sql.=" AND b.name LIKE ?";           $params[]='%'.$filters['name'].'%'; }
        if(!empty($filters['class_id']))      { $sql.=" AND b.class_id=?";            $params[]=$filters['class_id']; }
        if(!empty($filters['company_id']))    { $sql.=" AND b.company_id=?";          $params[]=$filters['company_id']; }
        if(isset($filters['is_active']) && $filters['is_active']!=='') {
            $sql.=" AND b.is_active=?"; $params[]=$filters['is_active'];
        }
        $sql.=" ORDER BY cl.sort_order,b.name";
        if($limit!==null) { $limit=(int)$limit; $offset=(int)$offset; $sql.=" LIMIT $limit OFFSET $offset"; }
        $st=$this->db->prepare($sql); $st->execute($params); return $st->fetchAll();
    }

    public function countAll($filters=[]) {
        $sql="SELECT COUNT(*) FROM books b WHERE 1=1"; $params=[];
        if(!empty($filters['name']))          { $sql.=" AND b.name LIKE ?"; $params[]='%'.$filters['name'].'%'; }
        if(!empty($filters['class_id']))      { $sql.=" AND b.class_id=?"; $params[]=$filters['class_id']; }
        if(!empty($filters['company_id']))    { $sql.=" AND b.company_id=?"; $params[]=$filters['company_id']; }
        if(isset($filters['is_active']) && $filters['is_active']!=='') {
            $sql.=" AND b.is_active=?"; $params[]=$filters['is_active'];
        }
        $st=$this->db->prepare($sql); $st->execute($params); return (int)$st->fetchColumn();
    }

    public function find($id) {
        $st=$this->db->prepare("SELECT * FROM books WHERE id=?"); $st->execute([$id]); return $st->fetch();
    }

    public function create($d) {
        $this->db->prepare("INSERT INTO books(name,class_id,publication_id,company_id,mrp,purchase_rate,sale_rate,discount_pct,is_active)VALUES(?,?,?,?,?,?,?,?,?)")
            ->execute([$d['name'],$d['class_id'],$d['publication_id'],$d['company_id'],
                       $d['mrp']??0,$d['purchase_rate']??0,$d['sale_rate']??0,$d['discount_pct']??0,
                       isset($d['is_active'])?1:0]);
    }

    public function update($id,$d) {
        $this->db->prepare("UPDATE books SET name=?,class_id=?,publication_id=?,company_id=?,mrp=?,purchase_rate=?,sale_rate=?,discount_pct=?,is_active=? WHERE id=?")
            ->execute([$d['name'],$d['class_id'],$d['publication_id'],$d['company_id'],
                       $d['mrp']??0,$d['purchase_rate']??0,$d['sale_rate']??0,$d['discount_pct']??0,
                       isset($d['is_active'])?1:0,$id]);
    }

    public function hasPurchaseHistory($id) {
        $st=$this->db->prepare("SELECT COUNT(*) FROM purchase_items WHERE book_id=?");
        $st->execute([$id]);
        return (int)$st->fetchColumn()>0;
    }

    public function delete($id) {
        $this->db->prepare("DELETE FROM books WHERE id=?")->execute([$id]);
    }

    // For purchase form – by company and class
    public function getByCompanyAndClass($companyId,$classId) {
        $st=$this->db->prepare("SELECT b.id,b.name,b.mrp,b.purchase_rate,b.sale_rate,cl.name AS class_name FROM books b JOIN classes cl ON cl.id=b.class_id WHERE b.company_id=? AND b.class_id=? AND b.is_active=1 ORDER BY b.name");
        $st->execute([$companyId,$classId]); return $st->fetchAll();
    }

    // For school sale form – by class with current stock
    public function getByClassWithStock($classId,$seasonId) {
        $st=$this->db->prepare("
            SELECT b.id,b.name,b.mrp,b.sale_rate,b.discount_pct,cl.name AS class_name,
                   COALESCE(s.qty,0) AS available_qty
            FROM books b
            JOIN classes cl ON cl.id=b.class_id
            LEFT JOIN stocks s ON s.book_id=b.id AND s.season_id=?
            WHERE b.class_id=? AND b.is_active=1
            ORDER BY b.name");
        $st->execute([$seasonId,$classId]); return $st->fetchAll();
    }
}

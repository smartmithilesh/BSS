<?php
class Purchase extends Model {

    public function getAll($filters=[], $limit=null, $offset=0) {
        $sql="SELECT p.*,c.name AS company_name,s.name AS season_name
              FROM purchases p
              JOIN companies c ON c.id=p.company_id
              JOIN seasons s ON s.id=p.season_id WHERE 1=1";
        $params=[];
        if(!empty($filters['season_id']))  { $sql.=" AND p.season_id=?";           $params[]=$filters['season_id']; }
        if(!empty($filters['company_id'])) { $sql.=" AND p.company_id=?";          $params[]=$filters['company_id']; }
        if(!empty($filters['invoice_no'])) { $sql.=" AND p.invoice_no LIKE ?";     $params[]='%'.$filters['invoice_no'].'%'; }
        if(!empty($filters['from_date']))  { $sql.=" AND p.purchase_date>=?";      $params[]=$filters['from_date']; }
        if(!empty($filters['to_date']))    { $sql.=" AND p.purchase_date<=?";      $params[]=$filters['to_date']; }
        $sql.=" ORDER BY p.id DESC";
        if($limit!==null) { $limit=(int)$limit; $offset=(int)$offset; $sql.=" LIMIT $limit OFFSET $offset"; }
        $st=$this->db->prepare($sql); $st->execute($params); return $st->fetchAll();
    }

    public function count($filters=[]) {
        $sql="SELECT COUNT(*) FROM purchases p WHERE 1=1"; $params=[];
        if(!empty($filters['season_id']))  { $sql.=" AND p.season_id=?";       $params[]=$filters['season_id']; }
        if(!empty($filters['company_id'])) { $sql.=" AND p.company_id=?";      $params[]=$filters['company_id']; }
        if(!empty($filters['invoice_no'])) { $sql.=" AND p.invoice_no LIKE ?"; $params[]='%'.$filters['invoice_no'].'%'; }
        if(!empty($filters['from_date']))  { $sql.=" AND p.purchase_date>=?";  $params[]=$filters['from_date']; }
        if(!empty($filters['to_date']))    { $sql.=" AND p.purchase_date<=?";  $params[]=$filters['to_date']; }
        $st=$this->db->prepare($sql); $st->execute($params); return (int)$st->fetchColumn();
    }

    public function find($id) {
        $st=$this->db->prepare("
            SELECT p.*,c.name AS company_name,c.address AS company_address,c.phone AS company_phone,
                   s.name AS season_name
            FROM purchases p
            JOIN companies c ON c.id=p.company_id
            JOIN seasons s ON s.id=p.season_id
            WHERE p.id=?");
        $st->execute([$id]); return $st->fetch();
    }

    public function getItems($purchaseId) {
        $st=$this->db->prepare("
            SELECT pi.*,b.name AS book_name,cl.name AS class_name
            FROM purchase_items pi
            JOIN books b ON b.id=pi.book_id
            JOIN classes cl ON cl.id=b.class_id
            WHERE pi.purchase_id=?
            ORDER BY cl.sort_order,b.name");
        $st->execute([$purchaseId]); return $st->fetchAll();
    }

    public function create($data) {
        $this->beginTransaction();
        try {
            $gross=$discount=$net=0;
            $items=[];
            foreach(($data['items']??[]) as $item) {
                $qty=(int)($item['qty']??0); $rate=(float)($item['rate']??0);
                $disc=(float)($item['discount_pct']??0);
                if($qty<=0||$rate<=0) continue;
                $rg=$qty*$rate; $rd=$rg*($disc/100); $ra=$rg-$rd;
                $gross+=$rg; $discount+=$rd; $net+=$ra;
                $items[]=['book_id'=>(int)$item['book_id'],'qty'=>$qty,'rate'=>$rate,'discount_pct'=>$disc,'amount'=>$ra];
            }
            if(empty($items)) throw new Exception('No valid items');

            $prefix = AppSettings::get('purchase_prefix','PUR');
            $invoiceNo = !empty($data['invoice_no']) ? $data['invoice_no'] : $prefix.'-'.date('Ymd-His');

            $st=$this->db->prepare("INSERT INTO purchases(season_id,company_id,invoice_no,purchase_date,gross_amount,discount_amount,net_amount,notes)VALUES(?,?,?,?,?,?,?,?)");
            $st->execute([$data['season_id'],$data['company_id'],$invoiceNo,$data['purchase_date'],$gross,$discount,$net,$data['notes']??'']);
            $purchaseId=(int)$this->db->lastInsertId();

            foreach($items as $it) {
                $this->db->prepare("INSERT INTO purchase_items(purchase_id,book_id,qty,rate,discount_pct,amount)VALUES(?,?,?,?,?,?)")
                    ->execute([$purchaseId,$it['book_id'],$it['qty'],$it['rate'],$it['discount_pct'],$it['amount']]);
                $this->db->prepare("INSERT INTO stocks(season_id,book_id,qty)VALUES(?,?,?) ON DUPLICATE KEY UPDATE qty=qty+VALUES(qty)")
                    ->execute([$data['season_id'],$it['book_id'],$it['qty']]);
            }
            $this->commit();
            return $purchaseId;
        } catch(Exception $e) { $this->rollBack(); throw $e; }
    }

    public function delete($id) {
        // Get items to restore stock
        $purchase = $this->find($id);
        $items    = $this->getItems($id);
        $this->beginTransaction();
        try {
            foreach($items as $it) {
                $this->db->prepare("UPDATE stocks SET qty=qty-? WHERE season_id=? AND book_id=?")
                    ->execute([$it['qty'],$purchase['season_id'],$it['book_id']]);
            }
            $this->db->prepare("DELETE FROM purchases WHERE id=?")->execute([$id]);
            $this->commit();
        } catch(Exception $e) { $this->rollBack(); throw $e; }
    }
}

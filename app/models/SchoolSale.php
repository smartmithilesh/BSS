<?php
class SchoolSale extends Model {

    public function getAll($filters=[], $limit=null, $offset=0) {
        $sql="SELECT ss.*,sc.name AS school_name,se.name AS season_name,
                     (ss.net_amount - ss.paid_amount) AS outstanding
              FROM school_sales ss
              JOIN schools sc ON sc.id=ss.school_id
              JOIN seasons se ON se.id=ss.season_id
              WHERE 1=1";
        $params=[];
        if(!empty($filters['season_id']))  { $sql.=" AND ss.season_id=?";         $params[]=$filters['season_id']; }
        if(!empty($filters['school_id']))  { $sql.=" AND ss.school_id=?";         $params[]=$filters['school_id']; }
        if(!empty($filters['invoice_no'])) { $sql.=" AND ss.invoice_no LIKE ?";   $params[]='%'.$filters['invoice_no'].'%'; }
        if(!empty($filters['from_date']))  { $sql.=" AND ss.sale_date>=?";        $params[]=$filters['from_date']; }
        if(!empty($filters['to_date']))    { $sql.=" AND ss.sale_date<=?";        $params[]=$filters['to_date']; }
        $sql.=" ORDER BY ss.id DESC";
        if($limit!==null) { $limit=(int)$limit; $offset=(int)$offset; $sql.=" LIMIT $limit OFFSET $offset"; }
        $st=$this->db->prepare($sql); $st->execute($params); return $st->fetchAll();
    }

    public function count($filters=[]) {
        $sql="SELECT COUNT(*) FROM school_sales ss WHERE 1=1"; $params=[];
        if(!empty($filters['season_id']))  { $sql.=" AND ss.season_id=?";       $params[]=$filters['season_id']; }
        if(!empty($filters['school_id']))  { $sql.=" AND ss.school_id=?";       $params[]=$filters['school_id']; }
        if(!empty($filters['invoice_no'])) { $sql.=" AND ss.invoice_no LIKE ?"; $params[]='%'.$filters['invoice_no'].'%'; }
        if(!empty($filters['from_date']))  { $sql.=" AND ss.sale_date>=?";      $params[]=$filters['from_date']; }
        if(!empty($filters['to_date']))    { $sql.=" AND ss.sale_date<=?";      $params[]=$filters['to_date']; }
        $st=$this->db->prepare($sql); $st->execute($params); return (int)$st->fetchColumn();
    }

    public function find($id) {
        $st=$this->db->prepare("
            SELECT ss.*,sc.name AS school_name,sc.address AS school_address,sc.phone AS school_phone,
                   se.name AS season_name,
                   (ss.net_amount - ss.paid_amount) AS outstanding
            FROM school_sales ss
            JOIN schools sc ON sc.id=ss.school_id
            JOIN seasons se ON se.id=ss.season_id
            WHERE ss.id=?");
        $st->execute([$id]); return $st->fetch();
    }

    public function getItems($saleId) {
        $st=$this->db->prepare("
            SELECT si.*,b.name AS book_name,cl.name AS class_name
            FROM school_sale_items si
            JOIN books b ON b.id=si.book_id
            JOIN classes cl ON cl.id=b.class_id
            WHERE si.sale_id=?
            ORDER BY cl.sort_order,b.name");
        $st->execute([$saleId]); return $st->fetchAll();
    }

    public function getPayments($saleId) {
        $st=$this->db->prepare("SELECT * FROM school_payments WHERE sale_id=? ORDER BY payment_date");
        $st->execute([$saleId]); return $st->fetchAll();
    }

    public function getAllPayments($filters=[], $limit=null, $offset=0) {
        $sql="SELECT sp.*,ss.invoice_no,sc.name AS school_name,se.name AS season_name
              FROM school_payments sp
              JOIN school_sales ss ON ss.id=sp.sale_id
              JOIN schools sc ON sc.id=sp.school_id
              JOIN seasons se ON se.id=sp.season_id
              WHERE 1=1";
        $params=[];
        if(!empty($filters['season_id'])) { $sql.=" AND sp.season_id=?"; $params[]=$filters['season_id']; }
        if(!empty($filters['school_id'])) { $sql.=" AND sp.school_id=?"; $params[]=$filters['school_id']; }
        if(!empty($filters['from_date'])) { $sql.=" AND sp.payment_date>=?"; $params[]=$filters['from_date']; }
        if(!empty($filters['to_date']))   { $sql.=" AND sp.payment_date<=?"; $params[]=$filters['to_date']; }
        $sql.=" ORDER BY sp.payment_date DESC,sp.id DESC";
        if($limit!==null) { $limit=(int)$limit; $offset=(int)$offset; $sql.=" LIMIT $limit OFFSET $offset"; }
        $st=$this->db->prepare($sql); $st->execute($params); return $st->fetchAll();
    }

    public function countPayments($filters=[]) {
        $sql="SELECT COUNT(*) FROM school_payments sp WHERE 1=1";
        $params=[];
        if(!empty($filters['season_id'])) { $sql.=" AND sp.season_id=?"; $params[]=$filters['season_id']; }
        if(!empty($filters['school_id'])) { $sql.=" AND sp.school_id=?"; $params[]=$filters['school_id']; }
        if(!empty($filters['from_date'])) { $sql.=" AND sp.payment_date>=?"; $params[]=$filters['from_date']; }
        if(!empty($filters['to_date']))   { $sql.=" AND sp.payment_date<=?"; $params[]=$filters['to_date']; }
        $st=$this->db->prepare($sql); $st->execute($params); return (int)$st->fetchColumn();
    }

    public function getOutstanding($filters=[]) {
        $sql="SELECT ss.*,sc.name AS school_name,se.name AS season_name,
                     (ss.net_amount-ss.paid_amount) AS outstanding
              FROM school_sales ss
              JOIN schools sc ON sc.id=ss.school_id
              JOIN seasons se ON se.id=ss.season_id
              WHERE (ss.net_amount-ss.paid_amount)>0.01";
        $params=[];
        if(!empty($filters['season_id'])) { $sql.=" AND ss.season_id=?"; $params[]=$filters['season_id']; }
        if(!empty($filters['school_id'])) { $sql.=" AND ss.school_id=?"; $params[]=$filters['school_id']; }
        $sql.=" ORDER BY sc.name,ss.sale_date,ss.id";
        $st=$this->db->prepare($sql); $st->execute($params); return $st->fetchAll();
    }

    private function nextInvoiceNo($seasonId) {
        $st=$this->db->prepare("SELECT COUNT(*)+1 FROM school_sales WHERE season_id=?");
        $st->execute([$seasonId]); $n=(int)$st->fetchColumn();
        return AppSettings::get('sale_prefix','INV').'-'.$seasonId.'-'.str_pad($n,4,'0',STR_PAD_LEFT);
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

                // stock check
                $st=$this->db->prepare("SELECT qty,b.name FROM stocks s JOIN books b ON b.id=s.book_id WHERE s.season_id=? AND s.book_id=? FOR UPDATE");
                $st->execute([$data['season_id'],$item['book_id']]);
                $row=$st->fetch();
                $avail=(int)($row['qty']??0);
                if($avail<$qty) throw new Exception('Insufficient stock for "'.(htmlspecialchars($row['name']??'Book')).'" (Available: '.$avail.')');

                $rg=$qty*$rate; $rd=$rg*($disc/100); $ra=$rg-$rd;
                $gross+=$rg; $discount+=$rd; $net+=$ra;
                $items[]=['book_id'=>(int)$item['book_id'],'qty'=>$qty,'rate'=>$rate,'discount_pct'=>$disc,'amount'=>$ra];
            }
            if(empty($items)) throw new Exception('No valid items provided');

            $invoiceNo = !empty($data['invoice_no']) ? $data['invoice_no'] : $this->nextInvoiceNo($data['season_id']);

            $this->db->prepare("INSERT INTO school_sales(season_id,school_id,invoice_no,sale_date,gross_amount,discount_amount,net_amount,paid_amount,notes)VALUES(?,?,?,?,?,?,?,0,?)")
                ->execute([$data['season_id'],$data['school_id'],$invoiceNo,$data['sale_date'],$gross,$discount,$net,$data['notes']??'']);
            $saleId=(int)$this->db->lastInsertId();

            foreach($items as $it) {
                $this->db->prepare("INSERT INTO school_sale_items(sale_id,book_id,qty,rate,discount_pct,amount)VALUES(?,?,?,?,?,?)")
                    ->execute([$saleId,$it['book_id'],$it['qty'],$it['rate'],$it['discount_pct'],$it['amount']]);
                $this->db->prepare("UPDATE stocks SET qty=qty-? WHERE season_id=? AND book_id=?")
                    ->execute([$it['qty'],$data['season_id'],$it['book_id']]);
            }
            $this->commit();
            return $saleId;
        } catch(Exception $e) { $this->rollBack(); throw $e; }
    }

    public function receivePayment($data) {
        $this->beginTransaction();
        try {
            $st=$this->db->prepare("SELECT net_amount,paid_amount,school_id,season_id FROM school_sales WHERE id=? FOR UPDATE");
            $st->execute([$data['sale_id']]);
            $sale=$st->fetch();
            if(!$sale) throw new Exception('Sale invoice not found');
            $amount=(float)($data['amount']??0);
            $outstanding=(float)$sale['net_amount']-(float)$sale['paid_amount'];
            if($amount<=0||$amount>$outstanding+0.01) throw new Exception('Invalid payment amount.');

            $this->db->prepare("INSERT INTO school_payments(sale_id,school_id,season_id,payment_date,amount,payment_mode,reference_no,notes)VALUES(?,?,?,?,?,?,?,?)")
                ->execute([$data['sale_id'],$sale['school_id'],$sale['season_id'],$data['payment_date'],$amount,$data['payment_mode'],$data['reference_no']??'',$data['notes']??'']);
            $this->db->prepare("UPDATE school_sales SET paid_amount=paid_amount+? WHERE id=?")
                ->execute([$amount,$data['sale_id']]);
            $this->commit();
        } catch(Exception $e) { $this->rollBack(); throw $e; }
    }

    public function deletePayment($id) {
        $this->beginTransaction();
        try {
            $st=$this->db->prepare("SELECT sale_id,amount FROM school_payments WHERE id=? FOR UPDATE");
            $st->execute([$id]);
            $payment=$st->fetch();
            if(!$payment) throw new Exception('Payment not found.');
            $this->db->prepare("DELETE FROM school_payments WHERE id=?")->execute([$id]);
            $this->db->prepare("UPDATE school_sales SET paid_amount=GREATEST(0,paid_amount-?) WHERE id=?")
                ->execute([$payment['amount'],$payment['sale_id']]);
            $this->commit();
        } catch(Exception $e) { $this->rollBack(); throw $e; }
    }
}

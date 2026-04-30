<?php
class CompanyReturn extends Model {
    public function getAll($seasonId=null) {
        $sql="SELECT cr.*,c.name AS company_name,s.name AS season_name
              FROM company_returns cr JOIN companies c ON c.id=cr.company_id JOIN seasons s ON s.id=cr.season_id
              WHERE 1=1";
        $params=[];
        if($seasonId) { $sql.=" AND cr.season_id=?"; $params[]=$seasonId; }
        $sql.=" ORDER BY cr.id DESC";
        $st=$this->db->prepare($sql); $st->execute($params); return $st->fetchAll();
    }
    public function find($id) {
        $st=$this->db->prepare("SELECT cr.*,c.name AS company_name,c.address AS company_address,s.name AS season_name FROM company_returns cr JOIN companies c ON c.id=cr.company_id JOIN seasons s ON s.id=cr.season_id WHERE cr.id=?");
        $st->execute([$id]); return $st->fetch();
    }
    public function getItems($returnId) {
        $st=$this->db->prepare("SELECT ri.*,b.name AS book_name,cl.name AS class_name FROM company_return_items ri JOIN books b ON b.id=ri.book_id JOIN classes cl ON cl.id=b.class_id WHERE ri.return_id=? ORDER BY cl.sort_order,b.name");
        $st->execute([$returnId]); return $st->fetchAll();
    }
    public function create($data) {
        $this->beginTransaction();
        try {
            $total=0; $items=[];
            foreach(($data['items']??[]) as $it) {
                $qty=(int)($it['qty']??0); $rate=(float)($it['rate']??0);
                if($qty<=0||$rate<=0) continue;
                $amount=$qty*$rate; $total+=$amount;
                $items[]=['book_id'=>(int)$it['book_id'],'qty'=>$qty,'rate'=>$rate,'amount'=>$amount];
            }
            if(empty($items)) throw new Exception('No valid items');
            $refNo=!empty($data['reference_no'])?$data['reference_no']:'RET-'.date('Ymd-His');
            $this->db->prepare("INSERT INTO company_returns(season_id,company_id,return_date,reference_no,total_amount,notes)VALUES(?,?,?,?,?,?)")
                ->execute([$data['season_id'],$data['company_id'],$data['return_date'],$refNo,$total,$data['notes']??'']);
            $retId=(int)$this->db->lastInsertId();
            foreach($items as $it) {
                $this->db->prepare("INSERT INTO company_return_items(return_id,book_id,qty,rate,amount)VALUES(?,?,?,?,?)")
                    ->execute([$retId,$it['book_id'],$it['qty'],$it['rate'],$it['amount']]);
                // Reduce stock when returning to company
                $this->db->prepare("UPDATE stocks SET qty=qty-? WHERE season_id=? AND book_id=?")
                    ->execute([$it['qty'],$data['season_id'],$it['book_id']]);
            }
            $this->commit(); return $retId;
        } catch(Exception $e) { $this->rollBack(); throw $e; }
    }
}

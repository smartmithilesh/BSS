<?php
class Report extends Model {
    public function financialSummary($filters=[]) {
        $where=[];
        $params=[];
        if(!empty($filters['season_id'])) {
            $where[]='season_id=?';
            $params[]=$filters['season_id'];
        }

        $purchaseWhere=$where;
        $purchaseParams=$params;
        if(!empty($filters['from_date'])) { $purchaseWhere[]='purchase_date>=?'; $purchaseParams[]=$filters['from_date']; }
        if(!empty($filters['to_date'])) { $purchaseWhere[]='purchase_date<=?'; $purchaseParams[]=$filters['to_date']; }

        $saleWhere=$where;
        $saleParams=$params;
        if(!empty($filters['from_date'])) { $saleWhere[]='sale_date>=?'; $saleParams[]=$filters['from_date']; }
        if(!empty($filters['to_date'])) { $saleWhere[]='sale_date<=?'; $saleParams[]=$filters['to_date']; }

        $companyPayWhere=$where;
        $companyPayParams=$params;
        if(!empty($filters['from_date'])) { $companyPayWhere[]='payment_date>=?'; $companyPayParams[]=$filters['from_date']; }
        if(!empty($filters['to_date'])) { $companyPayWhere[]='payment_date<=?'; $companyPayParams[]=$filters['to_date']; }

        $schoolPayWhere=$companyPayWhere;
        $schoolPayParams=$companyPayParams;

        $purchaseAmount=$this->sum('purchases','net_amount',$purchaseWhere,$purchaseParams);
        $salesAmount=$this->sum('school_sales','net_amount',$saleWhere,$saleParams);
        $companyPaid=$this->sum('company_payments','amount',$companyPayWhere,$companyPayParams);
        $schoolReceived=$this->sum('school_payments','amount',$schoolPayWhere,$schoolPayParams);

        return [
            'purchase_amount'=>$purchaseAmount,
            'company_paid'=>$companyPaid,
            'company_balance'=>$purchaseAmount-$companyPaid,
            'sales_amount'=>$salesAmount,
            'school_received'=>$schoolReceived,
            'school_balance'=>$salesAmount-$schoolReceived,
            'profit_loss'=>$salesAmount-$purchaseAmount,
            'cash_flow'=>$schoolReceived-$companyPaid,
        ];
    }

    public function monthlySummary($seasonId=0) {
        $months=[];
        $base=date('Y-m-01');
        for($i=5;$i>=0;$i--) {
            $key=date('Y-m',strtotime($base.' -'.$i.' months'));
            $months[$key]=[
                'label'=>date('M Y',strtotime($key.'-01')),
                'purchase_amount'=>0,
                'sales_amount'=>0,
                'company_paid'=>0,
                'school_received'=>0,
            ];
        }
        $this->fillMonthly($months,'purchases','purchase_date','net_amount','purchase_amount',$seasonId);
        $this->fillMonthly($months,'school_sales','sale_date','net_amount','sales_amount',$seasonId);
        $this->fillMonthly($months,'company_payments','payment_date','amount','company_paid',$seasonId);
        $this->fillMonthly($months,'school_payments','payment_date','amount','school_received',$seasonId);
        return array_values($months);
    }

    private function sum($table,$column,$where,$params) {
        $sql="SELECT COALESCE(SUM($column),0) FROM $table";
        if($where) $sql.=' WHERE '.implode(' AND ',$where);
        $st=$this->db->prepare($sql);
        $st->execute($params);
        return (float)$st->fetchColumn();
    }

    private function fillMonthly(&$months,$table,$dateColumn,$amountColumn,$targetKey,$seasonId) {
        $start=array_key_first($months).'-01';
        $where=["$dateColumn>=?"];
        $params=[$start];
        if($seasonId) {
            $where[]='season_id=?';
            $params[]=$seasonId;
        }
        $sql="SELECT DATE_FORMAT($dateColumn,'%Y-%m') AS month_key, COALESCE(SUM($amountColumn),0) AS total
              FROM $table WHERE ".implode(' AND ',$where)." GROUP BY month_key";
        $st=$this->db->prepare($sql);
        $st->execute($params);
        foreach($st->fetchAll() as $row) {
            if(isset($months[$row['month_key']])) $months[$row['month_key']][$targetKey]=(float)$row['total'];
        }
    }
}

<?php
class ReportController extends Controller {
    public function profitLoss() {
        $this->authCheck();
        $active=(new Season())->getActive();
        $filters=[
            'season_id'=>$_GET['season_id']??($active['id']??''),
            'from_date'=>$_GET['from_date']??'',
            'to_date'=>$_GET['to_date']??'',
        ];
        $report=new Report();
        $this->render('reports/profit_loss',[
            'pageTitle'=>'Profit & Loss Report',
            'filters'=>$filters,
            'seasons'=>(new Season())->getAll(),
            'summary'=>$report->financialSummary($filters),
            'monthly'=>$report->monthlySummary((int)($filters['season_id']??0)),
        ]);
    }
}

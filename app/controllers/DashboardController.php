<?php
class DashboardController extends Controller {
    public function index() {
        $this->authCheck();
        $season = (new Season())->getActive();
        $sid = $season['id']??0;

        $db = Database::connect();
        $companies  = (int)$db->query("SELECT COUNT(*) FROM companies")->fetchColumn();
        $schools    = (int)$db->query("SELECT COUNT(*) FROM schools")->fetchColumn();
        $books      = (int)$db->query("SELECT COUNT(*) FROM books WHERE is_active=1")->fetchColumn();
        $totalSales = $totalPurchase = $totalCollected = $totalPaid = 0;
        $report = new Report();
        $financeSummary = $report->financialSummary(['season_id'=>$sid]);
        $financeMonthly = $report->monthlySummary($sid);

        if($sid) {
            $st=$db->prepare("SELECT COALESCE(SUM(net_amount),0) FROM school_sales WHERE season_id=?"); $st->execute([$sid]); $totalSales=(float)$st->fetchColumn();
            $st=$db->prepare("SELECT COALESCE(SUM(net_amount),0) FROM purchases WHERE season_id=?"); $st->execute([$sid]); $totalPurchase=(float)$st->fetchColumn();
            $st=$db->prepare("SELECT COALESCE(SUM(paid_amount),0) FROM school_sales WHERE season_id=?"); $st->execute([$sid]); $totalCollected=(float)$st->fetchColumn();
            $st=$db->prepare("SELECT COALESCE(SUM(amount),0) FROM company_payments WHERE season_id=?"); $st->execute([$sid]); $totalPaid=(float)$st->fetchColumn();
        } else {
            $totalSales=$totalPurchase=$totalCollected=$totalPaid=0;
        }

        // Recent sales
        $recentSales=[];
        if($sid) {
            $st=$db->prepare("SELECT ss.*,sc.name AS school_name FROM school_sales ss JOIN schools sc ON sc.id=ss.school_id WHERE ss.season_id=? ORDER BY ss.id DESC LIMIT 5");
            $st->execute([$sid]); $recentSales=$st->fetchAll();
        }

        $this->render('dashboard/index',[
            'pageTitle'      => 'Dashboard',
            'season'         => $season,
            'companies'      => $companies,
            'schools'        => $schools,
            'books'          => $books,
            'totalSales'     => $totalSales,
            'totalPurchase'  => $totalPurchase,
            'totalCollected' => $totalCollected,
            'totalPaid'      => $totalPaid,
            'recentSales'    => $recentSales,
            'financeSummary' => $financeSummary,
            'financeMonthly' => $financeMonthly,
        ]);
    }
}

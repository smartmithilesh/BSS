<?php
class CompanyPaymentController extends Controller {
    public function index() {
        $this->authCheck();
        $filters=['season_id'=>$_GET['season_id']??'','company_id'=>$_GET['company_id']??''];
        $page=max(1,(int)($_GET['page']??1)); $limit=15; $offset=($page-1)*$limit;
        $m=new CompanyPayment();
        $this->render('company_payment/index',[
            'pageTitle'  => 'Company Payments',
            'payments'   => $m->getAll($filters,$limit,$offset),
            'total'      => $m->count($filters),
            'filters'    => $filters, 'page'=>$page,'limit'=>$limit,
            'seasons'    => (new Season())->getAll(),
            'companies'  => (new Company())->getAll(),
        ]);
    }
    public function create() {
        $this->authCheck();
        $this->render('company_payment/form',['pageTitle'=>'Pay Company','seasons'=>(new Season())->getAll(),'companies'=>(new Company())->getAll(),'activeSeason'=>(new Season())->getActive()]);
    }
    public function store() {
        $this->authCheck();
        (new CompanyPayment())->create($_POST);
        $this->flash('success','Payment recorded.');
        $this->redirect('?controller=companyPayment&action=index');
    }
    public function outstanding() {
        $this->authCheck();
        $seasonId=(int)($_GET['season_id']??((new Season())->getActive()['id']??0));
        $this->render('company_payment/outstanding',[
            'pageTitle' => 'Company Outstanding',
            'rows'      => (new CompanyPayment())->getOutstanding($seasonId),
            'seasons'   => (new Season())->getAll(),
            'seasonId'  => $seasonId,
        ]);
    }
    public function delete() {
        $this->authCheck();
        (new CompanyPayment())->delete((int)($_GET['id']??0));
        $this->redirect('?controller=companyPayment&action=index');
    }
}

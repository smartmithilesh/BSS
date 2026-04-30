<?php
class CompanyReturnController extends Controller {
    public function index() {
        $this->authCheck();
        $seasonId=$_GET['season_id']??'';
        $this->render('company_return/index',[
            'pageTitle' => 'Company Returns',
            'returns'   => (new CompanyReturn())->getAll($seasonId?:null),
            'seasons'   => (new Season())->getAll(),
            'seasonId'  => $seasonId,
        ]);
    }
    public function create() {
        $this->authCheck();
        $this->render('company_return/form',[
            'pageTitle'    => 'Return to Company',
            'seasons'      => (new Season())->getAll(),
            'companies'    => (new Company())->getAll(),
            'classes'      => (new ClassModel())->getAll(),
            'activeSeason' => (new Season())->getActive(),
        ]);
    }
    public function store() {
        $this->authCheck();
        try {
            $id=(new CompanyReturn())->create($_POST);
            $this->flash('success','Return saved.');
            $this->redirect('?controller=companyReturn&action=view&id='.$id);
        } catch(Exception $e) {
            $this->flash('error',$e->getMessage());
            $this->redirect('?controller=companyReturn&action=create');
        }
    }
    public function view() {
        $this->authCheck();
        $id=(int)($_GET['id']??0);
        $m=new CompanyReturn();
        $ret=$m->find($id); if(!$ret) $this->redirect('?controller=companyReturn&action=index');
        $this->render('company_return/view',['pageTitle'=>'Return Details','ret'=>$ret,'items'=>$m->getItems($id)]);
    }
    // AJAX: books by company + class with stock
    public function getBooks() {
        header('Content-Type: application/json');
        $companyId=(int)($_GET['company_id']??0); $classId=(int)($_GET['class_id']??0); $seasonId=(int)($_GET['season_id']??0);
        if(!$companyId||!$classId||!$seasonId) { echo json_encode([]); exit; }
        $st=Database::connect()->prepare("
            SELECT b.id,b.name,b.purchase_rate AS rate,COALESCE(s.qty,0) AS available_qty
            FROM books b LEFT JOIN stocks s ON s.book_id=b.id AND s.season_id=?
            WHERE b.company_id=? AND b.class_id=? AND b.is_active=1 ORDER BY b.name");
        $st->execute([$seasonId,$companyId,$classId]);
        echo json_encode($st->fetchAll()); exit;
    }
}

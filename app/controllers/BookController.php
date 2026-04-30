<?php
class BookController extends Controller {
    public function index() {
        $this->authCheck();
        $filters=['name'=>$_GET['name']??'','class_id'=>$_GET['class_id']??'','company_id'=>$_GET['company_id']??'','is_active'=>$_GET['is_active']??''];
        $page=max(1,(int)($_GET['page']??1)); $limit=20; $offset=($page-1)*$limit;
        $m=new Book();
        $this->render('book/index',[
            'pageTitle' => 'Books',
            'books'     => $m->getAll($filters,$limit,$offset),
            'total'     => $m->countAll($filters),
            'filters'   => $filters,
            'page'      => $page,
            'limit'     => $limit,
            'classes'   => (new ClassModel())->getAll(),
            'companies' => (new Company())->getAll(),
        ]);
    }
    public function form() {
        $this->authCheck();
        $id=(int)($_GET['id']??0);
        $this->render('book/form',[
            'pageTitle'    => $id?'Edit Book':'New Book',
            'book'         => $id?(new Book())->find($id):null,
            'classes'      => (new ClassModel())->getAll(),
            'publications' => (new Publication())->getAll(),
            'companies'    => (new Company())->getAll(),
        ]);
    }
    public function save() {
        $this->authCheck();
        $id=(int)($_POST['id']??0);
        $m=new Book(); $id?$m->update($id,$_POST):$m->create($_POST);
        $this->flash('success','Book saved.');
        $this->redirect('?controller=book&action=index');
    }
    public function delete() {
        $this->authCheck();
        $id=(int)($_GET['id']??0);
        $m=new Book();
        if($m->hasPurchaseHistory($id)) {
            $this->flash('error','This book has already been purchased, so you cannot delete it.');
            $this->redirect('?controller=book&action=index');
        }
        try {
            $m->delete($id);
            $this->flash('success','Book deleted.');
        } catch(Exception $e) {
            $this->flash('error','This book is already used in records, so you cannot delete it.');
        }
        $this->redirect('?controller=book&action=index');
    }
}

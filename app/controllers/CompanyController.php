<?php
class CompanyController extends Controller {
    public function index() { $this->authCheck(); $this->render('company/index',['pageTitle'=>'Companies','companies'=>(new Company())->getAll()]); }
    public function form()  { $this->authCheck(); $id=(int)($_GET['id']??0); $this->render('company/form',['pageTitle'=>$id?'Edit Company':'New Company','company'=>$id?(new Company())->find($id):null]); }
    public function save()  { $this->authCheck(); $id=(int)($_POST['id']??0); $m=new Company(); $id?$m->update($id,$_POST):$m->create($_POST); $this->flash('success','Company saved.'); $this->redirect('?controller=company&action=index'); }
    public function delete(){ $this->authCheck(); (new Company())->delete((int)($_GET['id']??0)); $this->redirect('?controller=company&action=index'); }
}

<?php
class PublicationController extends Controller {
    public function index() { $this->authCheck(); $this->render('publication/index',['pageTitle'=>'Publications','publications'=>(new Publication())->getAll()]); }
    public function form()  { $this->authCheck(); $id=(int)($_GET['id']??0); $this->render('publication/form',['pageTitle'=>$id?'Edit Publication':'New Publication','publication'=>$id?(new Publication())->find($id):null]); }
    public function save()  { $this->authCheck(); $id=(int)($_POST['id']??0); $m=new Publication(); $id?$m->update($id,$_POST):$m->create($_POST); $this->flash('success','Publication saved.'); $this->redirect('?controller=publication&action=index'); }
    public function delete(){ $this->authCheck(); (new Publication())->delete((int)($_GET['id']??0)); $this->redirect('?controller=publication&action=index'); }
}

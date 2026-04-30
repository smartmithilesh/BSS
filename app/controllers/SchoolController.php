<?php
class SchoolController extends Controller {
    public function index() { $this->authCheck(); $this->render('school/index',['pageTitle'=>'Schools','schools'=>(new School())->getAll()]); }
    public function form()  { $this->authCheck(); $id=(int)($_GET['id']??0); $this->render('school/form',['pageTitle'=>$id?'Edit School':'New School','school'=>$id?(new School())->find($id):null]); }
    public function save()  { $this->authCheck(); $id=(int)($_POST['id']??0); $m=new School(); $id?$m->update($id,$_POST):$m->create($_POST); $this->flash('success','School saved.'); $this->redirect('?controller=school&action=index'); }
    public function delete(){ $this->authCheck(); (new School())->delete((int)($_GET['id']??0)); $this->redirect('?controller=school&action=index'); }
}

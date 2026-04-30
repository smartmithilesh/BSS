<?php
class ClassController extends Controller {
    public function index() { $this->authCheck(); $this->render('class/index',['pageTitle'=>'Classes','classes'=>(new ClassModel())->getAll()]); }
    public function form()  { $this->authCheck(); $id=(int)($_GET['id']??0); $this->render('class/form',['pageTitle'=>$id?'Edit Class':'New Class','cls'=>$id?(new ClassModel())->find($id):null]); }
    public function save()  { $this->authCheck(); $id=(int)($_POST['id']??0); $m=new ClassModel(); $id?$m->update($id,$_POST):$m->create($_POST); $this->flash('success','Class saved.'); $this->redirect('?controller=class&action=index'); }
    public function delete(){ $this->authCheck(); (new ClassModel())->delete((int)($_GET['id']??0)); $this->redirect('?controller=class&action=index'); }
}

<?php
class DepartmentController extends Controller {
    public function index() {
        $this->authCheck();
        $this->render('department/index',['pageTitle'=>'Departments','departments'=>(new Department())->getAll()]);
    }
    public function form() {
        $this->authCheck();
        $id=(int)($_GET['id']??0);
        $this->render('department/form',['pageTitle'=>$id?'Edit Department':'New Department','department'=>$id?(new Department())->find($id):null]);
    }
    public function save() {
        $this->authCheck();
        try {
            $id=(int)($_POST['id']??0);
            $m=new Department();
            $id?$m->update($id,$_POST):$m->create($_POST);
            $this->flash('success','Department saved.');
        } catch(Exception $e) {
            $this->flash('error',$e->getMessage());
        }
        $this->redirect('?controller=department&action=index');
    }
    public function delete() {
        $this->authCheck();
        try {
            (new Department())->delete((int)($_GET['id']??0));
            $this->flash('success','Department deleted.');
        } catch(Exception $e) {
            $this->flash('error','Cannot delete department while users are linked to it.');
        }
        $this->redirect('?controller=department&action=index');
    }
}

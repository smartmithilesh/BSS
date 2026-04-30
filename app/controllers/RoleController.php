<?php
class RoleController extends Controller {
    public function index() {
        $this->authCheck();
        $this->render('role/index',['pageTitle'=>'Roles','roles'=>(new Role())->getAll()]);
    }
    public function form() {
        $this->authCheck();
        $id=(int)($_GET['id']??0);
        $this->render('role/form',[
            'pageTitle'=>$id?'Edit Role':'New Role',
            'role'=>$id?(new Role())->find($id):null,
            'departments'=>(new Department())->getAll(true),
        ]);
    }
    public function save() {
        $this->authCheck();
        try {
            $id=(int)($_POST['id']??0);
            $m=new Role();
            $id?$m->update($id,$_POST):$m->create($_POST);
            $this->flash('success','Role saved.');
        } catch(Exception $e) {
            $this->flash('error',$e->getMessage());
        }
        $this->redirect('?controller=role&action=index');
    }
    public function delete() {
        $this->authCheck();
        try {
            (new Role())->delete((int)($_GET['id']??0));
            $this->flash('success','Role deleted.');
        } catch(Exception $e) {
            $this->flash('error','Cannot delete role while users are linked to it.');
        }
        $this->redirect('?controller=role&action=index');
    }
}

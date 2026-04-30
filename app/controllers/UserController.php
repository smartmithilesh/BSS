<?php
class UserController extends Controller {
    public function index() {
        $this->authCheck();
        $this->render('user/index',['pageTitle'=>'Users','users'=>(new User())->getAll()]);
    }
    public function form() {
        $this->authCheck();
        $id=(int)($_GET['id']??0);
        $this->render('user/form',[
            'pageTitle'=>$id?'Edit User':'New User',
            'user'=>$id?(new User())->find($id):null,
            'departments'=>(new Department())->getAll(true),
            'roles'=>(new Role())->getAll(true),
        ]);
    }
    public function profile() {
        $this->authCheck();
        $id=(int)($_SESSION['user']['id']??0);
        $this->render('user/profile',[
            'pageTitle'=>'Edit Profile',
            'user'=>(new User())->find($id),
        ]);
    }
    public function saveProfile() {
        $this->authCheck();
        if($_SERVER['REQUEST_METHOD']!=='POST') $this->redirect('?controller=user&action=profile');
        $id=(int)($_SESSION['user']['id']??0);
        try {
            (new User())->updateProfile($id,$_POST);
            $_SESSION['user']['name']=$_POST['name']??$_SESSION['user']['name'];
            $_SESSION['user']['email']=$_POST['email']??$_SESSION['user']['email'];
            $this->flash('success','Profile updated.');
        } catch(Exception $e) {
            $this->flash('error',$e->getMessage());
        }
        $this->redirect('?controller=user&action=profile');
    }
    public function save() {
        $this->authCheck();
        try {
            $id=(int)($_POST['id']??0);
            if(!$id && empty($_POST['password'])) throw new Exception('Password is required for new users.');
            $m=new User();
            $id?$m->update($id,$_POST):$m->create($_POST);
            $this->flash('success','User saved.');
        } catch(Exception $e) {
            $this->flash('error',$e->getMessage());
        }
        $this->redirect('?controller=user&action=index');
    }
    public function delete() {
        $this->authCheck();
        $id=(int)($_GET['id']??0);
        if($id==(int)($_SESSION['user']['id']??0)) {
            $this->flash('error','You cannot delete your own user account.');
            $this->redirect('?controller=user&action=index');
        }
        try {
            (new User())->delete($id);
            $this->flash('success','User deleted.');
        } catch(Exception $e) {
            $this->flash('error',$e->getMessage());
        }
        $this->redirect('?controller=user&action=index');
    }
}

<?php
class SeasonController extends Controller {
    public function index() {
        $this->authCheck();
        $this->render('season/index',['pageTitle'=>'Seasons','seasons'=>(new Season())->getAll()]);
    }
    public function form() {
        $this->authCheck();
        $id=(int)($_GET['id']??0);
        $season=$id?(new Season())->find($id):null;
        $this->render('season/form',['pageTitle'=>$id?'Edit Season':'New Season','season'=>$season]);
    }
    public function save() {
        $this->authCheck();
        $id=(int)($_POST['id']??0);
        $m=new Season();
        $id ? $m->update($id,$_POST) : $m->create($_POST);
        $this->flash('success','Season saved.');
        $this->redirect('?controller=season&action=index');
    }
    public function setActive() {
        $this->authCheck();
        (new Season())->setActive((int)($_GET['id']??0));
        $this->redirect('?controller=season&action=index');
    }
    public function delete() {
        $this->authCheck();
        (new Season())->delete((int)($_GET['id']??0));
        $this->redirect('?controller=season&action=index');
    }
}

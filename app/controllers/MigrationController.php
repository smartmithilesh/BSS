<?php
class MigrationController extends Controller {
    public function index() {
        $this->authCheck();
        $this->itDepartmentCheck();
        $runner=new MigrationRunner();
        $this->render('migration/index',['pageTitle'=>'Database Updates','migrations'=>$runner->all()]);
    }
    public function run() {
        $this->authCheck();
        $this->itDepartmentCheck();
        try {
            $count=(new MigrationRunner())->runPending();
            $this->flash('success',$count.' update(s) applied.');
        } catch(Exception $e) {
            $this->flash('error',$e->getMessage());
        }
        $this->redirect('?controller=migration&action=index');
    }
}

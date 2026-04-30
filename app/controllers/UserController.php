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
            $m=new User();
            $current=$m->find($id);
            $data=$_POST;
            if(!empty($_FILES['profile_image']['name'])) {
                $data['profile_image']=$this->uploadProfileImage($_FILES['profile_image']);
                $old=$current['profile_image']??'';
                if($old) {
                    $oldPath=__DIR__.'/../../public/'.ltrim($old,'/');
                    if(is_file($oldPath)) @unlink($oldPath);
                }
            }
            $m->updateProfile($id,$data);
            $_SESSION['user']['name']=$_POST['name']??$_SESSION['user']['name'];
            $_SESSION['user']['email']=$_POST['email']??$_SESSION['user']['email'];
            if(isset($data['profile_image'])) $_SESSION['user']['profile_image']=$data['profile_image'];
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

    private function uploadProfileImage($file) {
        $error=(int)($file['error']??UPLOAD_ERR_NO_FILE);
        if($error!==UPLOAD_ERR_OK) {
            $messages=[
                UPLOAD_ERR_INI_SIZE=>'Profile image is bigger than the server upload limit.',
                UPLOAD_ERR_FORM_SIZE=>'Profile image is too large.',
                UPLOAD_ERR_PARTIAL=>'Profile image uploaded only partially. Please try again.',
                UPLOAD_ERR_NO_FILE=>'Please choose a profile image.',
                UPLOAD_ERR_NO_TMP_DIR=>'Server upload temp folder is missing.',
                UPLOAD_ERR_CANT_WRITE=>'Server could not write the uploaded file.',
                UPLOAD_ERR_EXTENSION=>'A PHP extension blocked the image upload.',
            ];
            throw new Exception($messages[$error]??'Profile image upload failed.');
        }
        if(empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) throw new Exception('Profile image upload failed. Please try again.');
        $ext=strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));
        if(!in_array($ext,['jpg','jpeg','png','gif','webp'])) throw new Exception('Profile image must be an image file.');
        if(($file['size']??0)>2*1024*1024) throw new Exception('Profile image must be 2 MB or smaller.');
        if(!@getimagesize($file['tmp_name'])) throw new Exception('Invalid profile image.');
        $dir=__DIR__.'/../../public/assets/uploads/users';
        if(!is_dir($dir) && !mkdir($dir,0777,true)) throw new Exception('Unable to create profile image upload folder.');
        if(!is_writable($dir)) throw new Exception('Profile image upload folder is not writable.');
        $name='user-'.(int)($_SESSION['user']['id']??0).'-'.date('YmdHis').'.'.$ext;
        if(!move_uploaded_file($file['tmp_name'],$dir.'/'.$name)) throw new Exception('Unable to upload profile image.');
        return 'assets/uploads/users/'.$name;
    }
}

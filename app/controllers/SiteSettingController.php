<?php
class SiteSettingController extends Controller {
    public function index() {
        $this->authCheck();
        $this->superAdminCheck();
        $this->render('site_setting/form',[
            'pageTitle'=>'Site Settings',
            'settings'=>(new SiteSetting())->getAllAssoc(),
        ]);
    }

    public function save() {
        $this->authCheck();
        $this->superAdminCheck();
        try {
            $data=$_POST;
            if(!empty($_FILES['brand_logo']['name']) && is_uploaded_file($_FILES['brand_logo']['tmp_name'])) {
                $ext=strtolower(pathinfo($_FILES['brand_logo']['name'],PATHINFO_EXTENSION));
                if(!in_array($ext,['jpg','jpeg','png','gif','webp'])) throw new Exception('Logo must be an image file.');
                $dir=__DIR__.'/../../public/assets/uploads/site';
                if(!is_dir($dir)) mkdir($dir,0775,true);
                $name='brand-logo-'.date('YmdHis').'.'.$ext;
                if(!move_uploaded_file($_FILES['brand_logo']['tmp_name'],$dir.'/'.$name)) throw new Exception('Unable to upload logo.');
                $data['brand_logo']='assets/uploads/site/'.$name;
            }
            (new SiteSetting())->saveAll($data);
            $this->flash('success','Site settings saved.');
        } catch(Exception $e) {
            $this->flash('error',$e->getMessage());
        }
        $this->redirect('?controller=siteSetting&action=index');
    }
}

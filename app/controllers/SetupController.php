<?php
class SetupController extends Controller {
    public function index() {
        if(file_exists(__DIR__.'/../config/installed.php')) $this->redirect('?controller=dashboard&action=index');
        $error=$_SESSION['setup_error']??''; unset($_SESSION['setup_error']);
        require __DIR__.'/../views/setup/index.php';
    }

    public function save() {
        if(file_exists(__DIR__.'/../config/installed.php')) $this->redirect('?controller=dashboard&action=index');
        try {
            $host=trim($_POST['db_host']??'localhost');
            $name=trim($_POST['db_name']??'');
            $user=trim($_POST['db_user']??'');
            $pass=(string)($_POST['db_pass']??'');
            $appName=trim($_POST['app_name']??'Bharat Book Depot');
            $timezone=trim($_POST['timezone']??'Asia/Kolkata');
            $adminName=trim($_POST['admin_name']??'Admin');
            $adminEmail=trim($_POST['admin_email']??'');
            $adminPass=(string)($_POST['admin_password']??'');
            if($name===''||$user===''||$adminEmail===''||$adminPass==='') throw new Exception('Please fill all required fields.');

            $pdo=new PDO('mysql:host='.$host.';charset=utf8mb4',$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `".str_replace('`','``',$name)."` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `".str_replace('`','``',$name)."`");
            $this->runSchema($pdo);

            $hash=password_hash($adminPass,PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET name=?,email=?,password=?,department_id=1,role_id=1,role='superadmin' WHERE id=1")
                ->execute([$adminName,$adminEmail,$hash]);
            $pdo->prepare("INSERT INTO site_settings(setting_key,setting_value)VALUES('shop_name',?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)")
                ->execute([$appName]);
            $pdo->prepare("INSERT INTO site_settings(setting_key,setting_value)VALUES('timezone',?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)")
                ->execute([$timezone]);

            $config=$this->configFile($host,$name,$user,$pass,$appName,$timezone);
            if(!file_put_contents(__DIR__.'/../config/config.local.php',$config)) throw new Exception('Unable to write app/config/config.local.php.');
            if(!file_put_contents(__DIR__.'/../config/installed.php',"<?php\nreturn ['installed_at'=>'".date('c')."'];\n")) throw new Exception('Unable to write installed marker.');
            $this->redirect('?controller=auth&action=login');
        } catch(Exception $e) {
            $_SESSION['setup_error']=$e->getMessage();
            $this->redirect('?controller=setup&action=index');
        }
    }

    private function runSchema($pdo) {
        $sql=file_get_contents(__DIR__.'/../../database/schema.sql');
        $sql=preg_replace('/^\s*--.*$/m','',$sql);
        foreach(explode(';',$sql) as $statement) {
            $statement=trim($statement);
            if($statement===''||preg_match('/^(DROP DATABASE|CREATE DATABASE|USE )/i',$statement)) continue;
            $pdo->exec($statement);
        }
    }

    private function configFile($host,$name,$user,$pass,$appName,$timezone) {
        return "<?php\nreturn ".var_export([
            'project_folder'=>'bbd',
            'base_name'=>$appName,
            'base_url'=>BASE_URL,
            'db_host'=>$host,
            'db_name'=>$name,
            'db_user'=>$user,
            'db_pass'=>$pass,
            'timezone'=>$timezone,
        ],true).";\n";
    }
}

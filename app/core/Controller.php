<?php
class Controller {
    protected function render($view, $data = []) {
        $settings = AppSettings::all();
        extract($data);
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/' . $view . '.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    protected function redirect($path) {
        $location = pretty_url_from_legacy($path);
        if (!parse_url($location, PHP_URL_SCHEME)) {
            $location = rtrim(BASE_URL, '/') . '/' . ltrim($location, '/');
        }
        header('Location: ' . $location);
        exit;
    }

    protected function authCheck() {
        if (empty($_SESSION['user'])) {
            $this->redirect('?controller=auth&action=login');
        }
    }

    protected function superAdminCheck() {
        $user=$_SESSION['user']??[];
        $isSuper=($user['role']??'')==='superadmin' || ($user['department_name']??'')==='Super Admin';
        if(!$isSuper) {
            $this->flash('error','Only Super Admin can access this page.');
            $this->redirect('?controller=dashboard&action=index');
        }
    }

    protected function flash($type, $msg) {
        $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
    }
}

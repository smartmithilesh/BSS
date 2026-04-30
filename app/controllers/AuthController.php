<?php
class AuthController extends Controller {
    public function login() {
        if(!empty($_SESSION['user'])) {
            $this->redirect('?controller=dashboard&action=index');
        }

        $error = '';
        if($_SERVER['REQUEST_METHOD']==='POST') {
            $user = (new User())->findByEmail($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user;
                $this->redirect('?controller=dashboard&action=index');
            }

            $error = 'Invalid email or password';
        }

        // Plain login page (no layout)
        require __DIR__.'/../views/auth/login.php';
    }
    public function logout() {
        session_destroy();
        $this->redirect('?controller=auth&action=login');
    }
}

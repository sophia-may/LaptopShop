<?php
// controllers/AuthController.php — Upgraded to use BaseModel-powered UserModel

require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel(); // auto-connects via BaseModel
    }

    // Handle login (session-based for FE monolith)
    public function login($username, $password) {
        $user = $this->userModel->authenticate($username, $password);

        if ($user) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['email'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['is_admin'] = $user['is_admin'];
            $_SESSION['role']     = $user['is_admin'] ? 'admin' : 'member';

            return $user;
        }
        return false;
    }

    // Handle signup
    public function signup($email, $password, $full_name = null) {
        if ($this->userModel->emailExists($email)) {
            return "Email already exists.";
        }

        $created = $this->userModel->createUser($email, $password, $full_name, 0);
        if ($created) {
            return true;
        }
        return "Signup failed. Please try again.";
    }

    // Handle logout
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header('Location: index.php?page=login_signup');
        exit();
    }

    // Check if user is logged in (session-based)
    public static function checkAuth($requireAdmin = false) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login_signup');
            exit();
        }

        if ($requireAdmin && (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1)) {
            header('Location: index.php?page=home');
            exit();
        }
    }
}

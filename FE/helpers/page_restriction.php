<?php

/**
 *  Restrict access to certain pages for guests or non-logged-in users.
 *
 *  $restrictedPages: List of page names to restrict
 *  $type: User type to restrict: 'guest', 'user', 'admin'
**/
function restrictPages(array $restrictedPages, string $type) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // If page is in restricted list
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 'home';

    if (in_array($currentPage, $restrictedPages)) {
        if ($type === 'guest' && isset($_SESSION['is_guest']) && $_SESSION['is_guest']) {
            header("Location: index.php?page=login_signup");
            exit();
        }

        if ($type === 'user' && !isset($_SESSION['user_id'])) {
            header("Location: index.php?page=home");
            exit();
        }

        if ($type === 'admin' && (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1)) {
            header("Location: index.php?page=home");
            exit();
        }
    }
}

?>
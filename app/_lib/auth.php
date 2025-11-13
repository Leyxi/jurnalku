<?php
session_start();

function check_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /jurnalku/public/login.php');
        exit();
    }
}

function check_role($allowed_roles = []) {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
        if ($_SESSION['role'] == 'admin') {
            header('Location: /jurnalku/public/admin/');
            exit();
        } elseif ($_SESSION['role'] == 'pembimbing') {
            header('Location: /jurnalku/public/pembimbing/');
            exit();
        } elseif ($_SESSION['role'] == 'siswa') {
            header('Location: /jurnalku/public/siswa/');
            exit();
        } else {
            header('Location: /jurnalku/public/login.php');
            exit();
        }
    }
}
?>

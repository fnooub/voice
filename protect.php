<?php
    /* Your password */
    $password = 'h5n1';

    if (empty($_COOKIE['password']) || $_COOKIE['password'] !== $password) {
        // Password not set or incorrect. Send to login.php.
        header('Location: protect_login.php');
        exit;
    }
?>
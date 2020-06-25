<?php
    /* Your password */
    $password = 'h5n1';

    /* Redirects here after login */
    $redirect_after_login = 'index.php';

    /* Will not ask password again for */
    $remember_password = strtotime('+30 days'); // 30 days

    if (isset($_POST['password']) && $_POST['password'] == $password) {
        setcookie("password", $password, $remember_password);
        header('Location: ' . $redirect_after_login);
        exit;
    }
?>
<title>Protect</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<div style="text-align:center;margin-top:50px;">
    You must enter the password to view this content.
    <form method="POST">
        <input type="text" name="password">
    </form>
</div>
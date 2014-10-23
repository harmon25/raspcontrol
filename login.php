<?php

session_start();

set_error_handler(function() {
            throw new Exception('Failed to open authentification file in <code>' . FILE_PASS . '</code>');
        });

require 'config.php';
require 'lib/password.php';

// logout
if (isset($_GET['logout'])) {
    unset($_SESSION['authentificated']);
    session_destroy();
}

// check identification
else if (isset($_POST['username']) && isset($_POST['password']) && !empty($_POST['username']) && !empty($_POST['password'])) {
    try {
        //*
        $db = json_decode(file_get_contents(FILE_PASS));
        $username = $db->{'user'};
        $password = $db->{'password'};

        if (password_needs_rehash($password, PASSWORD_BCRYPT)) {
            $password = password_hash($password, PASSWORD_BCRYPT);
            $db->{'password'} = $password;
            if (is_writable(FILE_PASS)) {
                $handler = fopen(FILE_PASS, 'w');
                fwrite($handler, json_encode($db));
                fclose($handler);
            } else {
                $_SESSION['message'] = "Database file \"" . FILE_PASS . "\" is not writable, please check the file owner and rights.";
            }
        }

        if ($_POST['username'] == $username && password_verify($_POST['password'], $password))
            $_SESSION['authentificated'] = true;
        else
            $_SESSION['message'] = 'Incorrect username or password.';
    } catch (Exception $e) {
        $_SESSION['message'] = $e->getMessage();
    }
}

header('Location: ' . INDEX);
exit();
?>
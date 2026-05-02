<?php
session_start();
session_destroy();
setcookie('remember_email', '', time() - 1, '/');
header('Location: login.php');
exit;

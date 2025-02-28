<?php
session_name("user_session");
session_start();

session_unset();
session_destroy();

// Remove remember me cookie
setcookie("remember_me", "", time() - 3600, "/");

// Redirect to the login page
header("Location: index?lang=tr");
exit();
?>

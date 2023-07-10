<?php
    session_start();

    require_once(__DIR__ . "/../config.php");
    require_once(__DIR__ . "/../include/db.php");
    require_once(__DIR__ . "/../include/UserDAO.php");

    $userDao = new UserDAO(createDatabaseConnection());

    if(isset($_SESSION["token"]))
    {
        $userDao->revokeToken($_SESSION["token"]);
    }

    $_SESSION = [];

    session_destroy();

    header("Location: /");
?>

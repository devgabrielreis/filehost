<?php
    session_start();

    require_once(__DIR__ . "/../config.php");
    require_once(__DIR__ . "/../include/Message.php");
    require_once(__DIR__ . "/../include/db.php");
    require_once(__DIR__ . "/../include/User.php");
    require_once(__DIR__ . "/../include/UserDAO.php");

    $userDao = new UserDAO(createDatabaseConnection());
    $message = new Message();

    if(!empty($_SESSION["token"]) && $userDao->getUserByToken($_SESSION["token"]))
    {
        header("Location: " . BASE_URL . "/home.php");
        exit();
    }

    $email = filter_input(INPUT_POST, "email");
    $password = filter_input(INPUT_POST, "password");

    $user = $userDao->getUserByEmail($email);

    if(!$user || !password_verify($password, $user->passwordHash))
    {
        $message->set("Invalid email or password", Message::TYPE_ERROR);

        header("Location: " . BASE_URL);
        exit();
    }

    $_SESSION["token"] = $userDao->createToken($user->id, date("Y-m-d H:i:s", strtotime("1 day")));

    $message->set("Welcome back!", Message::TYPE_SUCCESS);

    header("Location: " . BASE_URL . "/home.php");
?>

<?php
    session_start();

    require_once(__DIR__ . "/../../config.php");
    require_once(__DIR__ . "/../../include/Message.php");
    require_once(__DIR__ . "/../../include/db.php");
    require_once(__DIR__ . "/../../include/User.php");
    require_once(__DIR__ . "/../../include/UserDAO.php");

    $userDao = new UserDAO(createDatabaseConnection());
    $message = new Message();

    $loggedUser = $userDao->getLoggedUser();

    if(!$loggedUser)
    {
        $message->set("You are not logged in", Message::TYPE_ERROR);
        header("Location: /");
        exit();
    }

    $email = filter_input(INPUT_POST, "email");
    $password = filter_input(INPUT_POST, "password");

    if(empty($email) || empty($password))
    {
        $message->set("Invalid information", Message::TYPE_ERROR);

        header("Location: /account.php");
        exit();
    }

    if(!$loggedUser->comparePassword($password))
    {
        $message->set("Invalid password", Message::TYPE_ERROR);
        header("Location: /account.php");
        exit();
    }

    if(empty($email) || empty($password))
    {
        $message->set("Invalid information", Message::TYPE_ERROR);

        header("Location: /account.php");
        exit();
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $message->set("Invalid email", Message::TYPE_ERROR);

        header("Location: /account.php");
        exit();
    }

    if($userDao->getUserByEmail($email))
    {
        $message->set("This email is already in use", Message::TYPE_ERROR);

        header("Location: /account.php");
        exit();
    }

    $userDao->changeEmail($loggedUser->getId(), $email);

    $message->set("Email changed successfully", Message::TYPE_SUCCESS);
    header("Location: /home.php");
?>

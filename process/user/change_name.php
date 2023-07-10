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

    $name = filter_input(INPUT_POST, "name");
    $password = filter_input(INPUT_POST, "password");

    if(!$loggedUser->comparePassword($password))
    {
        $message->set("Invalid password", Message::TYPE_ERROR);
        header("Location: /account.php");
        exit();
    }

    if(empty($name) || empty($password))
    {
        $message->set("Invalid information", Message::TYPE_ERROR);

        header("Location: /account.php");
        exit();
    }

    if(strlen($name) < 3 || strlen($name) > 20)
    {
        $message->set("The username needs to have between 3 and 20 characters", Message::TYPE_ERROR);

        header("Location: /account.php");
        exit();
    }

    if($userDao->getUserByName($name))
    {
        $message->set("This username is already in use", Message::TYPE_ERROR);

        header("Location: /account.php");
        exit();
    }

    $userDao->changeName($loggedUser->getId(), $name);

    $message->set("Username changed successfully", Message::TYPE_SUCCESS);
    header("Location: /home.php");
?>

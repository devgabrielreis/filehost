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

    $oldPassword = filter_input(INPUT_POST, "password");
    $newPassword = filter_input(INPUT_POST, "newpassword");
    $confirmPassword = filter_input(INPUT_POST, "confirmpassword");

    if(empty($oldPassword) || empty($newPassword) || empty($confirmPassword))
    {
        $message->set("Invalid information", Message::TYPE_ERROR);

        header("Location: /account.php");
        exit();
    }

    if(!$loggedUser->comparePassword($oldPassword))
    {
        $message->set("Invalid password", Message::TYPE_ERROR);
        header("Location: /account.php");
        exit();
    }

    if(strlen($newPassword) < 8 || strlen($newPassword) > 20)
    {
        $message->set("The password needs to have between 8 and 20 characters", Message::TYPE_ERROR);

        header("Location: /account.php");
        exit();
    }

    if($newPassword !== $confirmPassword)
    {
        $message->set("The passwords you entered do not match", Message::TYPE_ERROR);

        header("Location: /account.php");
        exit();
    }

    $userDao->changePassword($loggedUser->getId(), password_hash($newPassword, PASSWORD_DEFAULT));

    $message->set("Password changed successfully", Message::TYPE_SUCCESS);
    header("Location: /home.php");
?>

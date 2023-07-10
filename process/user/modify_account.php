<?php
    session_start();

    require_once(__DIR__ . "/../config.php");
    require_once(__DIR__ . "/../include/Message.php");
    require_once(__DIR__ . "/../include/db.php");
    require_once(__DIR__ . "/../include/User.php");
    require_once(__DIR__ . "/../include/UserDAO.php");

    $userDao = new UserDAO(createDatabaseConnection());
    $message = new Message();

    $loggedUser = $userDao->getLoggedUser();

    if(!$loggedUser)
    {
        $message->set("You are not logged in", Message::TYPE_ERROR);
        header("Location: /");
        exit();
    }

    $type = filter_input(INPUT_POST, "type");

    if($type === "changename")
    {
        
    }
    elseif($type === "changeemail")
    {
        
    }
    elseif($type === "changepassword")
    {
        
    }
    else
    {
        header("Location: /");
        exit();
    }
?>

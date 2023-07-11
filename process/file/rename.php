<?php
    session_start();

    require_once(__DIR__ . "/../../config.php");
    require_once(__DIR__ . "/../../include/db.php");
    require_once(__DIR__ . "/../../include/UserDAO.php");
    require_once(__DIR__ . "/../../include/FileDAO.php");
    require_once(__DIR__ . "/../../include/Message.php");

    $userDao = new UserDAO(createDatabaseConnection());
    $fileDao = new FileDAO(createDatabaseConnection(), $userDao);
    $message = new Message();

    $loggedUser = $userDao->getLoggedUser();

    if(!$loggedUser)
    {
        header("Location: /");
        exit();
    }

    $fileId = filter_input(INPUT_POST, "id");
    $newName = basename(filter_input(INPUT_POST, "newname"));
    $password = filter_input(INPUT_POST, "password");

    if($fileId === null)
    {
        header("Location: /");
        exit();
    }

    if(strlen($newName) === 0)
    {
        header("Location: /");
        exit();
    }

    if(!$loggedUser->comparePassword($password))
    {
        $message->set("Invalid password", Message::TYPE_ERROR);
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit();

    }

    $file = $fileDao->getFile($fileId);

    if($file === null)
    {
        header("Location: /");
        exit();
    }

    if(!$file->isOwner($loggedUser))
    {
        header("Location: /");
        exit();
    }

    $fileDao->renameFile($fileId, $newName);
    $message->set("File renamed successfully", Message::TYPE_SUCCESS);
    header("Location: /home.php");
?>

<?php
    session_start();

    require_once(__DIR__ . "/../include/db.php");
    require_once(__DIR__ . "/../include/Message.php");
    require_once(__DIR__ . "/../include/File.php");
    require_once(__DIR__ . "/../include/FileDAO.php");
    require_once(__DIR__ . "/../include/UserDAO.php");

    $userDao = new UserDAO(createDatabaseConnection());
    $fileDao = new FileDAO(createDatabaseConnection(), $userDao);
    $message = new Message();

    $loggedUser = $userDao->getLoggedUser();

    if(!$loggedUser)
    {
        $message->set("You are not logged in", Message::TYPE_ERROR);
        header("Location: /");
        exit();
    }

    if(empty($_POST["visibility"] || !in_array($_POST["visibility"], ["private", "restrict", "public"]) || empty($_FILES["file"])))
    {
        $message->set("Invalid information", Message::TYPE_ERROR);

        header("Location: /upload.php");
        exit();
    }

    if(!$userDao->userHasEnoughStorageSpace($loggedUser->getId(), $_FILES["file"]["size"]))
    {
        $message->set("You don't have enough space to store this file", Message::TYPE_ERROR);

        header("Location: /upload.php");
        exit();
    }

    $fileDao->saveUploadedFIle($_FILES["file"], $_POST["visibility"], $loggedUser->getId());

    $message->set("File uploaded successfully", Message::TYPE_SUCCESS);

    header("Location: /home.php");
?>
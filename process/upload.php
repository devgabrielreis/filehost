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

    if(empty($_SESSION["token"]))
    {
        $message->set("You are not logged in", Message::TYPE_ERROR);
        header("Location: " . BASE_URL);
        exit();
    }

    $loggedUser = $userDao->getUserByToken($_SESSION["token"]);

    if(!$loggedUser)
    {
        $message->set("You are not logged in", Message::TYPE_ERROR);
        header("Location: " . BASE_URL);
        exit();
    }

    if(empty($_POST["visibility"] || !in_array($_POST["visibility"], ["private", "restricted", "public"]) || empty($_FILES["file"])))
    {
        $message->set("Invalid information", Message::TYPE_ERROR);

        header("Location: " . BASE_URL . "/upload.php");
        exit();
    }

    $fileDao->saveUploadedFIle($_FILES["file"], $_POST["visibility"], $loggedUser->id);

    $message->set("File uploaded successfully", Message::TYPE_SUCCESS);

    header("Location: " . BASE_URL . "/home.php");
?>
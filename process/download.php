<?php
    session_start();

    require_once(__DIR__ . "/../config.php");
    require_once(__DIR__ . "/../include/db.php");
    require_once(__DIR__ . "/../include/UserDAO.php");
    require_once(__DIR__ . "/../include/FileDAO.php");
    require_once(__DIR__ . "/../include/Message.php");

    $userDao = new UserDAO(createDatabaseConnection());
    $fileDao = new FileDAO(createDatabaseConnection(), $userDao);
    $message = new Message();

    $loggedUser = $userDao->getLoggedUser();

    $fileId = filter_input(INPUT_GET, "id");

    if($fileId === null)
    {
        // header("Location: " . BASE_URL);
        header("Location: /");
        exit();
    }

    $file = $fileDao->getFile($fileId);

    if($file === null)
    {
        $message->set("File not found", Message::TYPE_ERROR);

        if($loggedUser === null)
        {
            header("Location: " . BASE_URL);
        }
        else
        {
            header("Location: " . BASE_URL . "/home.php");
        }

        exit();
    }

    if(!$file->hasAccess($loggedUser))
    {
        $message->set("You don't have permission to access this file", Message::TYPE_ERROR);

        if($loggedUser === null)
        {
            header("Location: " . BASE_URL);
        }
        else
        {
            header("Location: " . BASE_URL . "/home.php");
        }

        exit();
    }

    header('Content-Disposition: attachment; filename="' . $file->getName() . '"');
    readfile(FILES_ROOT . $file->getPath());
?>